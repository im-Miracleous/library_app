<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Denda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

        $query = Peminjaman::with(['pengguna', 'details.buku'])
            ->whereBetween('tanggal_pinjam', [$startDate, $endDate]);

        if ($status) {
            $query->where('status_transaksi', $status);
        }

        $peminjaman = $query->latest()->get();

        // Calculations for Summary Cards
        $totalTransaksi = $peminjaman->count();
        $totalBukuDipinjam = $peminjaman->sum(function ($t) {
            return $t->details->count();
        });
        $transaksiSelesai = $peminjaman->where('status_transaksi', 'selesai')->count();
        $transaksiBerjalan = $peminjaman->where('status_transaksi', 'berjalan')->count();

        return view('laporan.peminjaman', compact(
            'peminjaman',
            'startDate',
            'endDate',
            'status',
            'totalTransaksi',
            'totalBukuDipinjam',
            'transaksiSelesai',
            'transaksiBerjalan'
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

        $query = Denda::with(['detail.peminjaman.pengguna', 'detail.buku'])
            ->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);

        if ($statusBayar) {
            $query->where('status_bayar', $statusBayar);
        }

        $denda = $query->latest()->get();

        // Calculations
        $totalDenda = $denda->sum('jumlah_denda');
        $totalDibayar = $denda->where('status_bayar', 'lunas')->sum('jumlah_denda');
        $totalBelumBayar = $denda->where('status_bayar', 'belum_bayar')->sum('jumlah_denda');

        return view('laporan.denda', compact(
            'denda',
            'startDate',
            'endDate',
            'statusBayar',
            'totalDenda',
            'totalDibayar',
            'totalBelumBayar'
        ));
    }
}
