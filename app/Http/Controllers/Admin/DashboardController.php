<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Buku;
use App\Models\Pengguna;
use App\Models\Peminjaman;
use App\Models\Denda;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
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

        return view('admin.dashboard', compact('stats', 'peminjamanTerbaru'));
    }
}
