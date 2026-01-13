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
                'total' => $type === 'transaksi' || $type === 'denda' ? DB::select('SELECT @total as total')[0]->total ?? count($data) : count($data),
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

        // Chart Data (Daily Transactions)
        $daily = Peminjaman::select(DB::raw('DATE(tanggal_pinjam) as date'), DB::raw('count(*) as count'))
            ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
            ->when($status, fn($q) => $q->where('status_transaksi', $status))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartData = [
            'labels' => $daily->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray(),
            'datasets' => [[
                'label' => 'Jumlah Transaksi',
                'data' => $daily->pluck('count')->toArray(),
                'borderColor' => '#3b82f6',
                'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                'fill' => true,
                'tension' => 0.4
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
            ->select('denda.*', 'pengguna.nama as nama_anggota', 'peminjaman.id_peminjaman')
            ->join('detail_peminjaman', 'denda.id_detail_peminjaman', '=', 'detail_peminjaman.id_detail_peminjaman')
            ->join('peminjaman', 'detail_peminjaman.id_peminjaman', '=', 'peminjaman.id_peminjaman')
            ->join('pengguna', 'peminjaman.id_pengguna', '=', 'pengguna.id_pengguna')
            ->whereBetween('denda.created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);

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
        $statsQuery = Denda::whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
        if ($statusBayar) $statsQuery->where('status_bayar', $statusBayar);

        $stats = [
            'total_denda' => $statsQuery->sum('jumlah_denda'),
            'dibayar' => $statsQuery->clone()->where('status_bayar', 'lunas')->sum('jumlah_denda'),
            'belum_bayar' => $statsQuery->clone()->where('status_bayar', 'belum_bayar')->sum('jumlah_denda'),
        ];

        // Chart Data (Daily Fines)
        $daily = Denda::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(jumlah_denda) as total'))
            ->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
            ->when($statusBayar, fn($q) => $q->where('status_bayar', $statusBayar))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartData = [
            'labels' => $daily->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray(),
            'datasets' => [[
                'label' => 'Total Denda (Rp)',
                'data' => $daily->pluck('total')->toArray(),
                'borderColor' => '#ef4444',
                'backgroundColor' => 'rgba(239, 68, 68, 0.2)',
                'fill' => true,
                'tension' => 0.4
            ]]
        ];

        return [$paginator, $stats, $chartData];
    }

    // --- LOGIC: BUKU TERLARIS ---
    private function getBukuTerlaris($request, $startDate, $endDate)
    {
        // Get Top 10 Books
        $data = DetailPeminjaman::select('id_buku', DB::raw('SUM(jumlah) as total_dipinjam'))
            ->whereHas('peminjaman', function($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_pinjam', [$startDate, $endDate]);
            })
            ->with(['buku' => function($q) {
                $q->select('id_buku', 'judul', 'penulis', 'gambar_sampul');
            }])
            ->groupBy('id_buku')
            ->orderByDesc('total_dipinjam')
            ->take(10)
            ->get();

        $stats = [
            'total_buku_unik_dipinjam' => DetailPeminjaman::whereHas('peminjaman', fn($q) => $q->whereBetween('tanggal_pinjam', [$startDate, $endDate]))->distinct('id_buku')->count('id_buku'),
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
            ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
            ->with(['pengguna' => function($q) {
                $q->select('id_pengguna', 'nama', 'email', 'foto_profil');
            }])
            ->groupBy('id_pengguna')
            ->orderByDesc('total_transaksi')
            ->take(10)
            ->get();

        $stats = [
            'total_anggota_aktif' => Peminjaman::whereBetween('tanggal_pinjam', [$startDate, $endDate])->distinct('id_pengguna')->count('id_pengguna'),
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
