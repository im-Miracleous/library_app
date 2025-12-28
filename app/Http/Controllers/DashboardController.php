<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Pengguna;
use App\Models\Peminjaman;
use App\Models\Denda;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $dbStatus = false;
        $serverStatus = true; // Asumsi server web berjalan jika kode ini dieksekusi

        try {
            // Cek koneksi database eksplisit
            \Illuminate\Support\Facades\DB::connection()->getPdo();
            $dbStatus = true;

            $stats = [
                'total_buku' => Buku::count(),
                'total_anggota' => Pengguna::where('peran', 'anggota')->count(),
                // 'peminjaman_aktif' => Peminjaman::where('status_transaksi', 'berjalan')->count(),
                // Updated: Use View for calculation
                'peminjaman_aktif' => \Illuminate\Support\Facades\DB::table('v_peminjaman_aktif')->count(),
                'total_denda' => Denda::where('status_bayar', 'belum_bayar')->sum('jumlah_denda'),
            ];

            $peminjamanTerbaru = Peminjaman::with('pengguna')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            // Fallback jika database mati/error
            $dbStatus = false;
            $stats = [
                'total_buku' => 0,
                'total_anggota' => 0,
                'peminjaman_aktif' => 0,
                'total_denda' => 0,
            ];
            $peminjamanTerbaru = [];
            // Opsional: Log error message
        }

        return view('dashboard', compact('stats', 'peminjamanTerbaru', 'dbStatus', 'serverStatus'));
    }
}
