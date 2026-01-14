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

        // B. Pengunjung Count (Bar Chart)
        $pengunjungQuery = \App\Models\Pengunjung::select(
                DB::raw($filter === 'today' ? 'DATE_FORMAT(created_at, "%H:00") as date' : 'DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date');

        $pengunjungData = $pengunjungQuery->get()->pluck('count', 'date')->toArray();

        // 3. Fill Missing Dates/Hours
        $labels = [];
        $chartPeminjaman = [];
        $chartPengunjung = [];

        // 1. Diajukan
        $diajukan = Peminjaman::whereDate('tanggal_pinjam', '>=', $startDate)
            ->whereDate('tanggal_pinjam', '<=', $endDate)
            ->where('status_transaksi', 'menunggu_verifikasi')
            ->count();

        // 2. Berjalan (Not Overdue)
        $berjalan = Peminjaman::whereDate('tanggal_pinjam', '>=', $startDate)
            ->whereDate('tanggal_pinjam', '<=', $endDate)
            ->where('status_transaksi', 'berjalan')
            ->where('tanggal_jatuh_tempo', '>=', Carbon::today())
            ->count();

        // 3. Terlambat (Active Overdue)
        $terlambat = Peminjaman::whereDate('tanggal_pinjam', '>=', $startDate)
            ->whereDate('tanggal_pinjam', '<=', $endDate)
            ->where('status_transaksi', 'berjalan')
            ->where('tanggal_jatuh_tempo', '<', Carbon::today())
            ->count();

        // 4. Rusak (Finished with damaged items)
        $rusak = Peminjaman::whereDate('tanggal_pinjam', '>=', $startDate)
            ->whereDate('tanggal_pinjam', '<=', $endDate)
            ->where('status_transaksi', 'selesai')
            ->whereHas('details', fn($q) => $q->where('status_buku', 'rusak'))
            ->count();
        
        // 5. Hilang (Finished with lost items)
        $hilang = Peminjaman::whereDate('tanggal_pinjam', '>=', $startDate)
            ->whereDate('tanggal_pinjam', '<=', $endDate)
            ->where('status_transaksi', 'selesai')
            ->whereHas('details', fn($q) => $q->where('status_buku', 'hilang'))
            ->count();

        // 6. Selesai (Finished normally - exclude those counted as rusak/hilang to avoid double counting if prioritizing)
        // Generally, a loan might have mixed items. If we want strictly "Clean Selesai", we exclude those with issues.
        $selesai = Peminjaman::whereDate('tanggal_pinjam', '>=', $startDate)
            ->whereDate('tanggal_pinjam', '<=', $endDate)
            ->where('status_transaksi', 'selesai')
            ->whereDoesntHave('details', fn($q) => $q->whereIn('status_buku', ['rusak', 'hilang']))
            ->count();

        $labels = ['Diajukan', 'Berjalan', 'Terlambat', 'Selesai', 'Rusak', 'Hilang'];
        $chartPeminjaman = [$diajukan, $berjalan, $terlambat, $selesai, $rusak, $hilang];
        
        // We also need Pengunjung to stay as is (grouped by date/time) OR maybe remove it? 
        // User only mentioned "Grafik Tren Peminjaman" and "Laporan Transaksi".
        // Dashboard has 2 charts. "Tren Peminjaman" and "Kunjungan Perpustakaan".
        // Use generic date filling for Pengunjung ONLY.
        
        $chartPengunjung = [];
        $current = $minDate->copy();
        $pengunjungLabels = []; 
        // We'll separate the labels for visitors since Peminjaman is now Pie (Categories) and Visitors is Line/Bar (Time).
        // BUT the view expects `chartData` to have unified labels if they share the X-Axis?
        // Let's check dashboard.blade.php later. Ideally we separate them.
        
        while ($current <= $maxDate) {
            $key = $filter === 'today' ? $current->format('H:00') : $current->format('Y-m-d');
            $chartPengunjung[] = $pengunjungData[$key] ?? 0;
            $pengunjungLabels[] = $filter === 'today' ? $current->format('H:00') : $current->format($labelFormat);

            if ($filter === 'today') {
                $current->addHour();
            } else {
                $current->addDay();
            }
        }

        $chartData = [
            'peminjaman' => [
                'labels' => $labels,
                'data' => $chartPeminjaman,
            ],
            'pengunjung' => [
                'labels' => $pengunjungLabels,
                'data' => $chartPengunjung,
            ],
            // Old format fallback just in case, but we should update view
            'labels' => $pengunjungLabels, 
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
