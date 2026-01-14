<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Peminjaman;
use App\Models\Denda;
use App\Models\DetailPeminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Pagination\LengthAwarePaginator;

class LaporanController extends Controller
{
    /**
     * Unified Report Dashboard
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $type = $request->input('type', 'transaksi'); // transaksi, denda, buku_top, anggota_top

        // Common Data
        $data = [];
        $stats = [];
        $chartData = [];

        // Determine Logic based on Type
        switch ($type) {
            case 'denda':
                [$data, $stats, $chartData] = $this->getLaporanDenda($request, $startDate, $endDate);
                break;
            case 'buku_top':
                [$data, $stats, $chartData] = $this->getBukuTerlaris($request, $startDate, $endDate);
                break;
            case 'anggota_top':
                [$data, $stats, $chartData] = $this->getAnggotaTeraktif($request, $startDate, $endDate);
                break;
            case 'transaksi':
            default:
                [$data, $stats, $chartData] = $this->getLaporanTransaksi($request, $startDate, $endDate);
                break;
        }

        if ($request->ajax()) {
            $viewName = match($type) {
                'denda' => 'admin.laporan.partials.rows-denda',
                'buku_top' => 'admin.laporan.partials.rows-buku_top',
                'anggota_top' => 'admin.laporan.partials.rows-anggota_top',
                default => 'admin.laporan.partials.rows-transaksi',
            };
            
            $html = view($viewName, compact('data'))->render();

            return response()->json([
                'html' => $html,
                'total' => method_exists($data, 'total') ? $data->total() : count($data),
                'stats' => $stats,
                'chartData' => $chartData
            ]);
        }

        return view('admin.laporan.index', compact(
            'startDate', 'endDate', 'type', 'data', 'stats', 'chartData'
        ));
    }


    // --- LOGIC: TRANSAKSI ---
    private function getLaporanTransaksi($request, $startDate, $endDate)
    {
        $status = $request->input('status'); // berjalan, selesai
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $offset = ($page - 1) * $limit;
        
        $search = $request->input('search');
        $sort = $request->input('sort');
        $direction = $request->input('direction');

        // Fetch Data via SP
        $rawData = DB::select('CALL sp_get_laporan_transaksi(?, ?, ?, ?, ?, ?, ?, ?, @total)', [
            $startDate, $endDate, $status, $search, $sort, $direction, $limit, $offset
        ]);
        $totalRaw = DB::select('SELECT @total as total')[0]->total;

        // Stats for Cards
        $query = Peminjaman::whereBetween('tanggal_pinjam', [$startDate, $endDate]);
        if ($status) $query->where('status_transaksi', $status);
        
        $stats = [
            'total_transaksi' => $query->count(),
            'total_buku' => DetailPeminjaman::whereIn('id_peminjaman', $query->pluck('id_peminjaman'))->sum('jumlah'),
            'selesai' => $query->clone()->where('status_transaksi', 'selesai')->count(),
            'berjalan' => $query->clone()->where('status_transaksi', 'berjalan')->count(),
        ];

        // Chart Data (Status Breakdown for Pie Chart)
        
        // Use whereDate to handle both DATE and DATETIME columns robustly
        $countDiajukan = Peminjaman::whereDate('tanggal_pinjam', '>=', $startDate)
            ->whereDate('tanggal_pinjam', '<=', $endDate)
            ->where('status_transaksi', 'menunggu_verifikasi')
            ->count();

        // 2. Berjalan (Not Overdue)
        $countBerjalan = Peminjaman::whereDate('tanggal_pinjam', '>=', $startDate)
            ->whereDate('tanggal_pinjam', '<=', $endDate)
            ->where('status_transaksi', 'berjalan')
            ->where('tanggal_jatuh_tempo', '>=', Carbon::today())
            ->count();

        // 3. Terlambat (Active Overdue)
        $countTerlambat = Peminjaman::whereDate('tanggal_pinjam', '>=', $startDate)
            ->whereDate('tanggal_pinjam', '<=', $endDate)
            ->where('status_transaksi', 'berjalan')
            ->where('tanggal_jatuh_tempo', '<', Carbon::today())
            ->count();

        // 4. Rusak
        $countRusak = Peminjaman::whereDate('tanggal_pinjam', '>=', $startDate)
            ->whereDate('tanggal_pinjam', '<=', $endDate)
            ->where('status_transaksi', 'selesai')
            ->whereHas('details', fn($q) => $q->where('status_buku', 'rusak'))
            ->count();

        // 5. Hilang
        $countHilang = Peminjaman::whereDate('tanggal_pinjam', '>=', $startDate)
            ->whereDate('tanggal_pinjam', '<=', $endDate)
            ->where('status_transaksi', 'selesai')
            ->whereHas('details', fn($q) => $q->where('status_buku', 'hilang'))
            ->count();

        // 6. Selesai (Clean)
        $countSelesai = Peminjaman::whereDate('tanggal_pinjam', '>=', $startDate)
            ->whereDate('tanggal_pinjam', '<=', $endDate)
            ->where('status_transaksi', 'selesai')
            ->whereDoesntHave('details', fn($q) => $q->whereIn('status_buku', ['rusak', 'hilang']))
            ->count();

        $chartData = [
            'labels' => ['Diajukan', 'Berjalan', 'Terlambat', 'Selesai', 'Rusak', 'Hilang'],
            'datasets' => [[
                'label' => 'Jumlah Transaksi',
                'data' => [$countDiajukan, $countBerjalan, $countTerlambat, $countSelesai, $countRusak, $countHilang],
                'backgroundColor' => [
                    '#f59e0b', // Diajukan (Orange)
                    '#3b82f6', // Berjalan (Blue)
                    '#ef4444', // Terlambat (Red)
                    '#10b981', // Selesai (Emerald)
                    '#71717a', // Rusak (Zinc-500 approx, or simplified color) -> Let's use Dark Orange or Brown? 
                    // Actually let's use:
                    // Rusak: #f97316 (Orange-500)
                    // Hilang: #64748b (Slate-500)
                    '#f97316', 
                    '#64748b'
                ],
            ]]
        ];

        // Pagination
        $paginator = new LengthAwarePaginator($rawData, $totalRaw, $limit, $page, ['path' => $request->url(), 'query' => $request->query()]);

        return [$paginator, $stats, $chartData];
    }

    // --- LOGIC: DENDA ---
    private function getLaporanDenda($request, $startDate, $endDate)
    {
        $statusBayar = $request->input('status_bayar'); // lunas, belum_bayar
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $search = $request->input('search');
        $sort = $request->input('sort');
        $direction = $request->input('direction', 'desc');

        // Build Query
        $query = Denda::query()
            ->select('denda.*', 'pengguna.nama as nama_anggota', 'peminjaman.id_peminjaman', 'detail_peminjaman.tanggal_kembali_aktual')
            ->join('detail_peminjaman', 'denda.id_detail_peminjaman', '=', 'detail_peminjaman.id_detail_peminjaman')
            ->join('peminjaman', 'detail_peminjaman.id_peminjaman', '=', 'peminjaman.id_peminjaman')
            ->join('pengguna', 'peminjaman.id_pengguna', '=', 'pengguna.id_pengguna')
            ->whereDate('denda.created_at', '>=', $startDate)
            ->whereDate('denda.created_at', '<=', $endDate);

        if ($statusBayar) {
            $query->where('denda.status_bayar', $statusBayar);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('denda.id_denda', 'like', "%{$search}%")
                  ->orWhere('pengguna.nama', 'like', "%{$search}%")
                  ->orWhere('peminjaman.id_peminjaman', 'like', "%{$search}%");
            });
        }

        if ($sort) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('denda.created_at', 'desc');
        }

        $paginator = $query->paginate($limit, ['*'], 'page', $page)->withQueryString();

        // Stats
        $statsQuery = Denda::whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate);
        if ($statusBayar) $statsQuery->where('status_bayar', $statusBayar);

        $stats = [
            'total_denda' => $statsQuery->sum('jumlah_denda'),
            'dibayar' => $statsQuery->clone()->where('status_bayar', 'lunas')->sum('jumlah_denda'),
            'belum_bayar' => $statsQuery->clone()->where('status_bayar', 'belum_bayar')->sum('jumlah_denda'),
        ];

        // Chart Data (Daily Fines - Grouped Bar)
        $daily = Denda::select(
                DB::raw('DATE(created_at) as date'), 
                'status_bayar',
                DB::raw('SUM(jumlah_denda) as total')
            )
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->groupBy('date', 'status_bayar')
            ->orderBy('date')
            ->get();

        $dates = $daily->pluck('date')->unique()->sort()->values();
        $lunasData = [];
        $belumBayarData = [];

        foreach ($dates as $date) {
            $lunasData[] = $daily->where('date', $date)->where('status_bayar', 'lunas')->sum('total');
            $belumBayarData[] = $daily->where('date', $date)->where('status_bayar', 'belum_bayar')->sum('total');
        }

        $chartData = [
            'labels' => $dates->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray(),
            'datasets' => [
                [
                    'label' => 'Sudah Dibayar',
                    'data' => $lunasData,
                    'backgroundColor' => '#10b981', // Emerald
                    'borderColor' => '#059669',
                    'borderWidth' => 1,
                    'borderRadius' => 4
                ],
                [
                    'label' => 'Belum Dibayar',
                    'data' => $belumBayarData,
                    'backgroundColor' => '#ef4444', // Red
                    'borderColor' => '#dc2626',
                    'borderWidth' => 1,
                    'borderRadius' => 4
                ]
            ]
        ];

        return [$paginator, $stats, $chartData];
    }

    // --- LOGIC: BUKU TERLARIS ---
    private function getBukuTerlaris($request, $startDate, $endDate)
    {
        // Get Top 10 Books
        $data = DetailPeminjaman::select('id_buku', DB::raw('SUM(jumlah) as total_dipinjam'))
            ->whereHas('peminjaman', function($q) use ($startDate, $endDate) {
                $q->whereDate('tanggal_pinjam', '>=', $startDate)
                  ->whereDate('tanggal_pinjam', '<=', $endDate);
            })
            ->with(['buku' => function($q) {
                $q->select('id_buku', 'judul', 'penulis', 'gambar_sampul');
            }])
            ->groupBy('id_buku')
            ->orderByDesc('total_dipinjam')
            ->take(10)
            ->get();

        $stats = [
            'total_buku_unik_dipinjam' => DetailPeminjaman::whereHas('peminjaman', fn($q) => $q->whereDate('tanggal_pinjam', '>=', $startDate)->whereDate('tanggal_pinjam', '<=', $endDate))->distinct('id_buku')->count('id_buku'),
            'top_1_judul' => $data->first() ? $data->first()->buku->judul : '-',
            'top_1_total' => $data->first() ? $data->first()->total_dipinjam : 0,
        ];

        // Horizontal Bar Chart
        $chartData = [
            'labels' => $data->pluck('buku.judul')->map(fn($t) => \Illuminate\Support\Str::limit($t, 20))->toArray(),
            'datasets' => [[
                'label' => 'Jumlah Dipinjam',
                'data' => $data->pluck('total_dipinjam')->toArray(),
                'backgroundColor' => [
                    '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
                    '#ec4899', '#6366f1', '#14b8a6', '#f97316', '#64748b'
                ],
            ]]
        ];

        return [$data, $stats, $chartData];
    }

    // --- LOGIC: ANGGOTA TERAKTIF ---
    private function getAnggotaTeraktif($request, $startDate, $endDate)
    {
        // Get Top 10 Members
        $data = Peminjaman::select('id_pengguna', DB::raw('count(*) as total_transaksi'))
            ->whereDate('tanggal_pinjam', '>=', $startDate)
            ->whereDate('tanggal_pinjam', '<=', $endDate)
            ->with(['pengguna' => function($q) {
                $q->select('id_pengguna', 'nama', 'email', 'foto_profil');
            }])
            ->groupBy('id_pengguna')
            ->orderByDesc('total_transaksi')
            ->take(10)
            ->get();

        $stats = [
            'total_anggota_aktif' => Peminjaman::whereDate('tanggal_pinjam', '>=', $startDate)->whereDate('tanggal_pinjam', '<=', $endDate)->distinct('id_pengguna')->count('id_pengguna'),
            'top_1_nama' => $data->first() ? $data->first()->pengguna->nama : '-',
            'top_1_total' => $data->first() ? $data->first()->total_transaksi : 0,
        ];

        // Bar Chart
        $chartData = [
            'labels' => $data->pluck('pengguna.nama')->map(fn($n) => \Illuminate\Support\Str::limit($n, 15))->toArray(),
            'datasets' => [[
                'label' => 'Jumlah Transaksi',
                'data' => $data->pluck('total_transaksi')->toArray(),
                'backgroundColor' => '#8b5cf6',
            ]]
        ];

        return [$data, $stats, $chartData];
    }
}
