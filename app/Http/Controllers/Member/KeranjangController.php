<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Keranjang;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KeranjangController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->id_pengguna;
        $items = Keranjang::with('buku')->where('id_pengguna', $userId)->get();
        // Get current total (active loans + cart) from settings
        $pengaturan = \App\Models\Pengaturan::first();
        $maxBuku = $pengaturan->maksimal_buku_pinjam ?? 3;

        $activeBooksCount = DB::table('detail_peminjaman')
            ->join('peminjaman', 'detail_peminjaman.id_peminjaman', '=', 'peminjaman.id_peminjaman')
            ->where('peminjaman.id_pengguna', $userId)
            ->whereIn('peminjaman.status_transaksi', ['berjalan', 'menunggu_verifikasi'])
            ->where('detail_peminjaman.status_buku', 'dipinjam')
            ->sum('detail_peminjaman.jumlah');

        $limitReached = ($activeBooksCount + $items->count()) >= $maxBuku;

        return view('member.keranjang.index', compact('items', 'limitReached'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_buku' => 'required|exists:buku,id_buku'
        ]);

        $userId = Auth::user()->id_pengguna;
        $bookId = $request->id_buku;

        // Check duplicates in cart
        $exists = Keranjang::where('id_pengguna', $userId)->where('id_buku', $bookId)->exists();
        if ($exists) {
            return response()->json(['status' => 'error', 'message' => 'Buku sudah ada di keranjang']);
        }

        // Check if already borrowed (active/pending)
        $isBorrowed = DB::table('detail_peminjaman')
            ->join('peminjaman', 'detail_peminjaman.id_peminjaman', '=', 'peminjaman.id_peminjaman')
            ->where('peminjaman.id_pengguna', $userId)
            ->where('detail_peminjaman.id_buku', $bookId)
            ->whereIn('peminjaman.status_transaksi', ['berjalan', 'menunggu_verifikasi'])
            ->where('detail_peminjaman.status_buku', 'dipinjam')
            ->exists();

        if ($isBorrowed) {
            return response()->json(['status' => 'error', 'message' => 'Anda sedang meminjam buku ini. Harap kembalikan terlebih dahulu sebelum meminjam ulang.']);
        } // Check stock
        $buku = Buku::find($bookId);
        if ($buku->stok_tersedia <= 0) {
            return response()->json(['status' => 'error', 'message' => 'Stok buku habis']);
        }

        // Limit active loans + cart (Max 3)
        $activeBooksCount = DB::table('detail_peminjaman')
            ->join('peminjaman', 'detail_peminjaman.id_peminjaman', '=', 'peminjaman.id_peminjaman')
            ->where('peminjaman.id_pengguna', $userId)
            ->whereIn('peminjaman.status_transaksi', ['berjalan', 'menunggu_verifikasi'])
            ->where('detail_peminjaman.status_buku', 'dipinjam')
            ->sum('detail_peminjaman.jumlah');

        $currentCartCount = Keranjang::where('id_pengguna', $userId)->count();

        $pengaturan = \App\Models\Pengaturan::first();
        $maxBuku = $pengaturan->maksimal_buku_pinjam ?? 3;

        if (($activeBooksCount + $currentCartCount) >= $maxBuku) {
            return response()->json(['status' => 'error', 'message' => "Batas peminjaman maks $maxBuku buku tercapai."]);
        }

        Keranjang::create([
            'id_pengguna' => $userId,
            'id_buku' => $bookId
        ]);

        return response()->json(['status' => 'success', 'message' => 'Buku masuk keranjang']);
    }

    public function destroy($id)
    {
        $item = Keranjang::where('id_keranjang', $id)->where('id_pengguna', Auth::user()->id_pengguna)->first();
        if ($item) {
            $item->delete();
            return back()->with('success', 'Buku dihapus dari keranjang');
        }
        return back()->with('error', 'Item tidak ditemukan');
    }

    public function clear()
    {
        Keranjang::where('id_pengguna', Auth::user()->id_pengguna)->delete();
        return back()->with('success', 'Keranjang berhasil dikosongkan.');
    }
}
