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
        $stats = [
            'total_buku' => Buku::count(),
            'total_anggota' => Pengguna::where('peran', 'anggota')->count(),
            'peminjaman_aktif' => Peminjaman::where('status_transaksi', 'berjalan')->count(),
            'total_denda' => Denda::where('status_bayar', 'belum_bayar')->sum('jumlah_denda'),
        ];

        $peminjamanTerbaru = Peminjaman::with('pengguna')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('dashboard', compact('stats', 'peminjamanTerbaru'));
    }
}
