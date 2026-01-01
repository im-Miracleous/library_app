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
     * Dashboard Laporan or Redirect to Default Report
     */
    public function index()
    {
        return redirect()->route('laporan.peminjaman');
    }

    /**
     * Laporan Peminjaman (Transactions)
     */
    public function peminjaman(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $status = $request->input('status');

        // Common parameters
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $offset = ($page - 1) * $limit;
        $search = $request->input('search');

        $sort = $request->input('sort');
        $direction = $request->input('direction');

        // Fetch Data (used for both Ajax and Initial View)
        $data = DB::select('CALL sp_get_laporan_transaksi(?, ?, ?, ?, ?, ?, ?, ?, @total)', [
            $startDate,
            $endDate,
            $status,
            $search,
            $sort,
            $direction,
            $limit,
            $offset
        ]);
        $total = DB::select('SELECT @total as total')[0]->total;

        // Summary Calculations (Move here to use in AJAX too)
        $query = Peminjaman::whereBetween('tanggal_pinjam', [$startDate, $endDate]);
        if ($status) {
            $query->where('status_transaksi', $status);
        }

        $totalTransaksi = $query->count();
        $peminjamanIds = $query->pluck('id_peminjaman');
        $totalBukuDipinjam = DetailPeminjaman::whereIn('id_peminjaman', $peminjamanIds)->sum('jumlah');

        $transaksiSelesai = $query->clone()->where('status_transaksi', 'selesai')->count();
        $transaksiBerjalan = $query->clone()->where('status_transaksi', 'berjalan')->count();

        if ($request->ajax()) {
            return response()->json([
                'data' => $data,
                'total' => $total,
                'stats' => [
                    'total_transaksi' => $totalTransaksi,
                    'total_buku' => $totalBukuDipinjam,
                    'selesai' => $transaksiSelesai,
                    'berjalan' => $transaksiBerjalan
                ]
            ]);
        }

        // Create Paginator for Initial View
        $peminjaman = new LengthAwarePaginator(
            $data,
            $total,
            $limit,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.laporan.peminjaman', compact(
            'startDate',
            'endDate',
            'status',
            'totalTransaksi',
            'totalBukuDipinjam',
            'transaksiSelesai',
            'transaksiBerjalan',
            'peminjaman'
        ));
    }

    /**
     * Laporan Denda (Fines)
     */
    public function denda(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $statusBayar = $request->input('status_bayar');

        // Common parameters
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $offset = ($page - 1) * $limit;
        $search = $request->input('search');

        $sort = $request->input('sort');
        $direction = $request->input('direction');

        // Fetch Data (used for both Ajax and Initial View)
        $data = DB::select('CALL sp_get_laporan_denda(?, ?, ?, ?, ?, ?, ?, ?, @total)', [
            $startDate,
            $endDate,
            $statusBayar,
            $search,
            $sort,
            $direction,
            $limit,
            $offset
        ]);
        $total = DB::select('SELECT @total as total')[0]->total;

        // Summary Calculations (Move here for AJAX)
        $query = Denda::whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
        if ($statusBayar) {
            $query->where('status_bayar', $statusBayar);
        }

        $totalDenda = $query->sum('jumlah_denda');
        $totalDibayar = $query->clone()->where('status_bayar', 'lunas')->sum('jumlah_denda');
        $totalBelumBayar = $query->clone()->where('status_bayar', 'belum_bayar')->sum('jumlah_denda');

        if ($request->ajax()) {
            return response()->json([
                'data' => $data,
                'total' => $total,
                'stats' => [
                    'total_denda' => $totalDenda,
                    'total_dibayar' => $totalDibayar,
                    'total_belum_bayar' => $totalBelumBayar
                ]
            ]);
        }

        // Create Paginator for Initial View
        $denda = new LengthAwarePaginator(
            $data,
            $total,
            $limit,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.laporan.denda', compact(
            'startDate',
            'endDate',
            'statusBayar',
            'totalDenda',
            'totalDibayar',
            'totalBelumBayar',
            'denda'
        ));
    }
}
