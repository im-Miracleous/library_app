<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Peminjaman;
use App\Models\Denda;
use App\Models\DetailPeminjaman;
use App\Models\Buku;
use App\Models\Pengunjung;
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
        $startDate = $request->input('start_date', Carbon::create(2000, 1, 1)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $type = $request->input('type'); // Defaults to null

        // Common Data
        $data = [];
        $stats = [];
        $chartData = [];

        if ($type) {
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
                    [$data, $stats, $chartData] = $this->getLaporanTransaksi($request, $startDate, $endDate);
                    break;
                case 'kunjungan':
                    [$data, $stats, $chartData] = $this->getLaporanKunjungan($request, $startDate, $endDate);
                    break;
                case 'inventaris':
                     // Inventaris is a snapshot, but we might arguably allow date filtering for "created_at" of books?
                     // Usually inventory is "Current State". Let's treat it as current state usually, but maybe allow filtering by category.
                     // usage of start/end date for inventory is weak. Let's pass them but mostly ignore for the snapshot stats.
                    [$data, $stats, $chartData] = $this->getLaporanInventaris($request);
                    break;
                default:
                    [$data, $stats, $chartData] = $this->getLaporanTransaksi($request, $startDate, $endDate);
                    break;
            }
        }

        if ($request->ajax()) {
            $viewName = match($type) {
                'denda' => 'admin.laporan.partials.rows-denda',
                'buku_top' => 'admin.laporan.partials.rows-buku_top',
                'anggota_top' => 'admin.laporan.partials.rows-anggota_top',
                'kunjungan' => 'admin.laporan.partials.rows-kunjungan',
                'inventaris' => 'admin.laporan.partials.rows-inventaris',
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

    public function cetak(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::create(2000, 1, 1)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $type = $request->input('type', 'transaksi');
        $status = $request->input('status');
        $statusBayar = $request->input('status_bayar');
        $search = $request->input('search');
        $kategori = $request->input('kategori');
        $withSignature = $request->input('signature', 1);

        $data = [];

        switch ($type) {
            case 'transaksi':
                $data = DB::select('CALL sp_get_laporan_transaksi_cetak(?, ?, ?, ?)', [
                    $startDate, $endDate, $status, $search
                ]);
                break;
            case 'denda':
                 $data = DB::select('CALL sp_get_laporan_denda_cetak(?, ?, ?, ?)', [
                    $startDate, $endDate, $statusBayar, $search
                ]);
                break;
            case 'kunjungan':
                 $data = DB::select('CALL sp_get_laporan_kunjungan_cetak(?, ?, ?)', [
                    $startDate, $endDate, $search
                ]);
                break;
            case 'inventaris':
                 $data = DB::select('CALL sp_get_laporan_inventaris_cetak(?, ?)', [
                    $search, $kategori
                ]);
                break;
            case 'buku_top':
                 $data = DB::select('CALL sp_get_buku_terpopuler_cetak(?, ?)', [
                    $startDate, $endDate
                ]);
                break;
            case 'anggota_top':
                 $data = DB::select('CALL sp_get_anggota_teraktif_cetak(?, ?)', [
                    $startDate, $endDate
                ]);
                break;
            default:
                 $data = [];
                 break;
        }

        return view('admin.laporan.print', compact('data', 'startDate', 'endDate', 'type', 'withSignature'));
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

        // Fix Ambiguity & Validate Sort Column
        $allowedSorts = [
            'id_peminjaman', 'tanggal_pinjam', 'tanggal_jatuh_tempo', 'status_transaksi', 
            'created_at', 'p.created_at', 'nama_anggota', 'total_buku'
        ];

        // Map frontend alias to actual column if needed
        if ($sort === 'created_at') {
            $sort = 'p.created_at';
        }
        if ($sort === 'nama_anggota') {
             $sort = 'u.nama';
        }

        // If sort is provided but not in allowed list (e.g. leftover from other report), reset it
        if ($sort && !in_array($sort, $allowedSorts) && !in_array($sort, ['u.nama', 'p.created_at'])) {
            $sort = 'p.created_at';
        }

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
            $query->orderBy('denda.id_denda', 'desc');
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

    // --- LOGIC: KUNJUNGAN ---
    private function getLaporanKunjungan($request, $startDate, $endDate)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $search = $request->input('search');
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');

        $query = Pengunjung::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($search) {
             $query->where(function($q) use ($search) {
                 $q->where('nama_pengunjung', 'like', "%{$search}%")
                   ->orWhere('keperluan', 'like', "%{$search}%")
                   ->orWhere('jenis_pengunjung', 'like', "%{$search}%");
             });
        }

        // Apply sorting
        $allowedSorts = ['created_at', 'nama_pengunjung', 'jenis_pengunjung'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $paginator = $query->paginate($limit, ['*'], 'page', $page)->withQueryString();

        // Stats
        $statsData = Pengunjung::whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->get();
        $totalPengunjung = $statsData->count();

        // Top Kategori
        $topKategori = $statsData->groupBy('jenis_pengunjung')
            ->sortByDesc(fn($g) => $g->count())
            ->keys()
            ->first();
        
        $stats = [
            'total_pengunjung' => $totalPengunjung,
            'avg_daily' => $totalPengunjung > 0 ? round($totalPengunjung / max(1, Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1), 1) : 0,
            'top_kategori' => $topKategori ?? '-',
        ];

        // Chart 1: Daily Trend (Line)
        $daily = $statsData->groupBy(fn($item) => $item->created_at->format('Y-m-d'))
            ->map(fn($group) => $group->count())
            ->sortKeys();
        
        // Chart 2: Category Breakdown (Pie) - We can only pass 1 chart structure easily to the main view logic unless we allow multiple.
        // The current view supports mainly 1 'mainChart'.
        
        $chartData = [
            'labels' => $daily->keys()->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray(),
            'datasets' => [[
                'label' => 'Jumlah Pengunjung',
                'data' => $daily->values()->toArray(),
                // Initial colors (Light Mode default), wil be overridden by JS if needed or handled there
                'backgroundColor' => 'rgba(59, 130, 246, 0.1)', // Blue-500 very low opacity for fill
                'borderColor' => '#3b82f6',
                'borderWidth' => 2,
                'fill' => true,
                'tension' => 0.4
            ]]
        ];

        return [$paginator, $stats, $chartData];
    }

    // --- LOGIC: INVENTARIS ---
    private function getLaporanInventaris($request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $search = $request->input('search');
        $kategori = $request->input('kategori');
        $sort = $request->input('sort', 'judul');
        $direction = $request->input('direction', 'asc');

        // Query
        $query = Buku::with('kategori');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%");
            });
        }
        
        // Filter by Category if needed (though not in UI yet, good to have)
        if ($kategori) {
             $query->where('id_kategori', $kategori);
        }

        // Apply sorting
        $allowedSorts = ['stok_total', 'stok_tersedia', 'stok_rusak', 'stok_hilang', 'judul'];
        if ($sort === 'kategori') {
            // Join with kategori table for sorting
            $query->join('kategori', 'buku.id_kategori', '=', 'kategori.id_kategori')
                  ->orderBy('kategori.nama_kategori', $direction)
                  ->select('buku.*'); // Ensure we only select buku columns
        } elseif (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('judul', 'asc');
        }

        $paginator = $query->paginate($limit, ['*'], 'page', $page)->withQueryString();

        // Stats (Overall Snapshot)
        $allBooks = Buku::all(); // Memory efficient? If many books, use aggregates.
        
        $stats = [
            'total_judul' => Buku::count(),
            'total_eksemplar' => Buku::sum('stok_total'),
            'total_rusak' => Buku::sum('stok_rusak'),
            'total_hilang' => Buku::sum('stok_hilang'),
        ];

        // Chart: Stock Composition (Doughnut)
        // Tersedia vs Dipinjam vs Rusak vs Hilang
        // Tersedia = sum(stok_tersedia)
        // Dipinjam = sum(stok_total) - sum(stok_tersedia) - sum(stok_rusak) - sum(stok_hilang)
        // Rusak = sum(stok_rusak)
        // Hilang = sum(stok_hilang)
        
        $totalTersedia = Buku::sum('stok_tersedia');
        $totalRusak = $stats['total_rusak'];
        $totalHilang = $stats['total_hilang'];
        $totalDipinjam = $stats['total_eksemplar'] - $totalTersedia - $totalRusak - $totalHilang;

        $chartData = [
            'labels' => ['Tersedia', 'Dipinjam', 'Rusak', 'Hilang'],
            'datasets' => [[
                'label' => 'Komposisi Stok',
                'data' => [$totalTersedia, $totalDipinjam, $totalRusak, $totalHilang],
                'backgroundColor' => [
                    '#10b981', // Emerald (Tersedia)
                    '#3b82f6', // Blue (Dipinjam)
                    '#f97316', // Orange (Rusak)
                    '#64748b'  // Slate (Hilang)
                ],
            ]]
        ];

        return [$paginator, $stats, $chartData];
    }
}
