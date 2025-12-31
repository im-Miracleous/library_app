<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Koleksi;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->id_pengguna;

        // 1. Statistik
        // Hitung peminjaman yang sedang aktif (berjalan / terlambat)
        // Note: status_transaksi 'berjalan' means active. 
        // We need to check if we should count 'menunggu_verifikasi' as separate or not. 
        // User asked for "jumlah buku yang sedang dipinjam".
        $activeLoansCount = Peminjaman::where('id_pengguna', $userId)
            ->where('status_transaksi', 'berjalan')
            ->count();

        // Count waiting verification
        $pendingLoansCount = Peminjaman::where('id_pengguna', $userId)
            ->where('status_transaksi', 'menunggu_verifikasi')
            ->count();

        $bookmarksCount = Koleksi::where('id_pengguna', $userId)->count();

        // 2. Buku Terbaru / Rekomendasi (Active books only)
        // Get 5 latest active books
        $recentBooks = Buku::with('kategori')
            ->where('status', 'tersedia')
            ->latest()
            ->take(5)
            ->get();

        // 3. User info passed via Auth facade in view, but we can pass extra if needed.

        return view('member.dashboard.index', compact(
            'activeLoansCount',
            'pendingLoansCount',
            'bookmarksCount',
            'recentBooks'
        ));
    }
}
