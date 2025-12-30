<?php

namespace App\Http\Controllers;

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
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $status = $request->input('status');

        // Common parameters
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $offset = ($page - 1) * $limit;
        $search = $request->input('search');

        // Fetch Data (used for both Ajax and Initial View)
        $data = DB::select('CALL sp_get_laporan_transaksi(?, ?, ?, ?, ?, ?, @total)', [
            $startDate,
            $endDate,
            $status,
            $search,
            $limit,
            $offset
        ]);
        $total = DB::select('SELECT @total as total')[0]->total;

        if ($request->ajax()) {
            return response()->json([
                'data' => $data,
                'total' => $total
            ]);
        }

        // Summary Calculations (Using simple Eloquent queries for stats)
        $query = Peminjaman::whereBetween('tanggal_pinjam', [$startDate, $endDate]);
        if ($status) {
            $query->where('status_transaksi', $status);
        }

        $totalTransaksi = $query->count();

        // Optimization: Get IDs first, then count details
        $peminjamanIds = $query->pluck('id_peminjaman');
        $totalBukuDipinjam = DetailPeminjaman::whereIn('id_peminjaman', $peminjamanIds)->count();

        $transaksiSelesai = $query->clone()->where('status_transaksi', 'selesai')->count();
        $transaksiBerjalan = $query->clone()->where('status_transaksi', 'berjalan')->count();

        // Create Paginator for Initial View
        $peminjaman = new LengthAwarePaginator(
            $data,
            $total,
            $limit,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('laporan.peminjaman', compact(
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
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $statusBayar = $request->input('status_bayar');

        // Common parameters
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $offset = ($page - 1) * $limit;
        $search = $request->input('search');

        // Fetch Data (used for both Ajax and Initial View)
        $data = DB::select('CALL sp_get_laporan_denda(?, ?, ?, ?, ?, ?, @total)', [
            $startDate,
            $endDate,
            $statusBayar,
            $search,
            $limit,
            $offset
        ]);
        $total = DB::select('SELECT @total as total')[0]->total;

        if ($request->ajax()) {
            return response()->json([
                'data' => $data,
                'total' => $total
            ]);
        }

        // Summary Calculations
        $query = Denda::whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
        if ($statusBayar) {
            $query->where('status_bayar', $statusBayar);
        }

        $totalDenda = $query->sum('jumlah_denda');
        $totalDibayar = $query->clone()->where('status_bayar', 'lunas')->sum('jumlah_denda');
        $totalBelumBayar = $query->clone()->where('status_bayar', 'belum_bayar')->sum('jumlah_denda');

        // Create Paginator for Initial View
        $denda = new LengthAwarePaginator(
            $data,
            $total,
            $limit,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('laporan.denda', compact(
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
