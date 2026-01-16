<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Buku;
use App\Models\Pengguna;
use App\Models\Peminjaman;
use App\Models\Denda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'today'); // today, week, month

        // 1. Determine Date Range
        $endDate = Carbon::now();
        if ($filter === 'today') {
            $startDate = Carbon::today();
            $dateFormat = 'H:00'; // Group by Hour
            $labelFormat = 'H:i';
        } elseif ($filter === 'month') {
            $startDate = Carbon::now()->subDays(30);
            $dateFormat = 'Y-m-d'; // Group by Day
            $labelFormat = 'd M';
        } else { // week
            $startDate = Carbon::now()->subDays(7);
            $dateFormat = 'Y-m-d';
            $labelFormat = 'D, d M';
        }

        // 2. Fetch Chart Data (Generic Helper)
        $minDate = $startDate->copy();
        $maxDate = $endDate->copy();

        // A. Peminjaman Trend (Calculated in loop below specific to statuses)
        // Removed old query.

        // B. Pengunjung Count (Categorical Bar Chart)
        $pengunjungQuery = \App\Models\Pengunjung::select(
                'jenis_pengunjung',
                DB::raw('count(*) as count')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('jenis_pengunjung');

        $rawPengunjungData = $pengunjungQuery->get();

        // Mapping Logic
        $categoryMapping = [
            'Personal & Akademik' => [
                'Umum / Tamu', 'Anggota / Mahasiswa', 'Pelajar / Siswa Sekolah',
                'Dosen / Staff Pengajar', 'Peneliti / Riset'
            ],
            'Organisasi & Komunitas' => [
                'Organisasi Internal Kampus', 'Organisasi / Komunitas Luar', 'Yayasan / Nonprofit / NGO'
            ],
            'Instansi & Perusahaan' => [
                'Pemerintahan / Dinas', 'Korporasi / Perusahaan Swasta'
            ],
            'Kunjungan Khusus' => [
                'Tamu Undangan / VIP', 'Media / Jurnalis', 'Lainnya'
            ]
        ];

        // Initialize Counters
        $groupedCounts = [
            'Personal & Akademik' => 0,
            'Organisasi & Komunitas' => 0,
            'Instansi & Perusahaan' => 0,
            'Kunjungan Khusus' => 0
        ];

        foreach ($rawPengunjungData as $row) {
            $foundGroup = 'Kunjungan Khusus'; // Default
            foreach ($categoryMapping as $group => $subCategories) {
                if (in_array($row->jenis_pengunjung, $subCategories)) {
                    $foundGroup = $group;
                    break;
                }
            }
            $groupedCounts[$foundGroup] += $row->count;
        }

        // Prepare Data for Chart
        $pengunjungLabels = array_keys($groupedCounts);
        $pengunjungDataPoints = array_values($groupedCounts);

        $colors = ['#3b82f6', '#10b981', '#f59e0b', '#6366f1'];

        // Construct Dataset
        $pengunjungDataset = [
            [
                'label' => 'Total Kunjungan',
                'data' => $pengunjungDataPoints,
                'backgroundColor' => $colors,
                'borderRadius' => 6,
                'barPercentage' => 0.6,
            ]
        ];

        $diajukan = Peminjaman::whereDate('tanggal_pinjam', '>=', $startDate)
            ->whereDate('tanggal_pinjam', '<=', $endDate)
            ->where('status_transaksi', 'menunggu_verifikasi')
            ->count();

        // ... (Existing Peminjaman Queries - Keeping them but just ensuring variable scope)
        $berjalan = Peminjaman::whereDate('tanggal_pinjam', '>=', $startDate)
            ->whereDate('tanggal_pinjam', '<=', $endDate)
            ->where('status_transaksi', 'berjalan')
            ->where('tanggal_jatuh_tempo', '>=', Carbon::today())
            ->count();

        $terlambat = Peminjaman::whereDate('tanggal_pinjam', '>=', $startDate)
            ->whereDate('tanggal_pinjam', '<=', $endDate)
            ->where('status_transaksi', 'berjalan')
            ->where('tanggal_jatuh_tempo', '<', Carbon::today())
            ->count();

        $rusak = Peminjaman::whereDate('tanggal_pinjam', '>=', $startDate)
            ->whereDate('tanggal_pinjam', '<=', $endDate)
            ->where('status_transaksi', 'selesai')
            ->whereHas('details', fn($q) => $q->where('status_buku', 'rusak'))
            ->count();

        $hilang = Peminjaman::whereDate('tanggal_pinjam', '>=', $startDate)
            ->whereDate('tanggal_pinjam', '<=', $endDate)
            ->where('status_transaksi', 'selesai')
            ->whereHas('details', fn($q) => $q->where('status_buku', 'hilang'))
            ->count();

        $selesai = Peminjaman::whereDate('tanggal_pinjam', '>=', $startDate)
            ->whereDate('tanggal_pinjam', '<=', $endDate)
            ->where('status_transaksi', 'selesai')
            ->whereDoesntHave('details', fn($q) => $q->whereIn('status_buku', ['rusak', 'hilang']))
            ->count();

        $labels = ['Diajukan', 'Berjalan', 'Terlambat', 'Selesai', 'Rusak', 'Hilang'];
        $chartPeminjaman = [$diajukan, $berjalan, $terlambat, $selesai, $rusak, $hilang];

        $chartData = [
            'peminjaman' => [
                'labels' => $labels,
                'data' => $chartPeminjaman,
            ],
            'pengunjung' => [
                'labels' => $pengunjungLabels,
                'datasets' => $pengunjungDataset,
            ],
        ];

        if ($request->ajax()) {
            return response()->json($chartData);
        }

        try {
            $stats = [
                'total_buku' => Buku::count(),
                'total_anggota' => Pengguna::where('peran', 'anggota')->count(),
                'peminjaman_aktif' => Peminjaman::where('status_transaksi', 'berjalan')->count(),
                'total_denda' => Denda::where('status_bayar', 'belum_bayar')->sum('jumlah_denda'),
                'pending_verifications' => Peminjaman::where('status_transaksi', 'menunggu_verifikasi')->count(),
            ];

            $peminjamanTerbaru = Peminjaman::with('pengguna')
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
        } catch (\Exception $e) {
            $stats = [
                'total_buku' => 0,
                'total_anggota' => 0,
                'peminjaman_aktif' => 0,
                'total_denda' => 0,
            ];
            $peminjamanTerbaru = [];
        }

        return view('admin.dashboard', compact('stats', 'peminjamanTerbaru', 'chartData', 'filter'));
    }
}
