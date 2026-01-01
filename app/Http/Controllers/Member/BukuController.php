<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Koleksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BukuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Buku::query()->with('kategori');

        // Filter by Category
        if ($request->has('kategori') && $request->kategori != '') {
            $query->where('id_kategori', $request->kategori);
        }

        // Filter by Bookmarks
        if ($request->get('filter') === 'bookmarks') {
            $bookmarkedIds = Koleksi::where('id_pengguna', Auth::user()->id_pengguna)
                ->pluck('id_buku');
            $query->whereIn('id_buku', $bookmarkedIds);
        }

        // Search by Title or Author
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('penulis', 'like', "%{$search}%");
            });
        }

        // Only show available books or all? Let's show all but indicate stock.
        // Pagination
        $buku = $query->paginate(12)->withQueryString();

        $kategori_list = Kategori::all();

        // Get bookmarked IDs for initial UI state
        $bookmarkedIds = Auth::check()
            ? Koleksi::where('id_pengguna', Auth::user()->id_pengguna)->pluck('id_buku')->toArray()
            : [];

        // Get cart item IDs for "Added to Cart" indicator
        $cartItems = \App\Models\Keranjang::where('id_pengguna', Auth::user()->id_pengguna)->get();
        $cartItemIds = $cartItems->pluck('id_buku')->toArray();

        $borrowedBooksQuery = \Illuminate\Support\Facades\DB::table('detail_peminjaman')
            ->join('peminjaman', 'detail_peminjaman.id_peminjaman', '=', 'peminjaman.id_peminjaman')
            ->where('peminjaman.id_pengguna', Auth::user()->id_pengguna)
            ->whereIn('peminjaman.status_transaksi', ['berjalan', 'menunggu_verifikasi'])
            ->whereIn('detail_peminjaman.status_buku', ['dipinjam', 'diajukan']);

        $borrowedBookIds = (clone $borrowedBooksQuery)->where('detail_peminjaman.status_buku', 'dipinjam')->pluck('detail_peminjaman.id_buku')->toArray();
        $pendingBookIds = (clone $borrowedBooksQuery)->where('detail_peminjaman.status_buku', 'diajukan')->pluck('detail_peminjaman.id_buku')->toArray();

        $activeBooksCount = $borrowedBooksQuery->sum('detail_peminjaman.jumlah');

        // Calculate limit from settings
        $pengaturan = \App\Models\Pengaturan::first();
        $maxBuku = $pengaturan->maksimal_buku_pinjam ?? 3;

        $limitReached = ($activeBooksCount + $cartItems->count()) >= $maxBuku;

        return view('member.buku.index', compact('buku', 'kategori_list', 'bookmarkedIds', 'cartItemIds', 'borrowedBookIds', 'pendingBookIds', 'limitReached'));
    }

    public function show($id)
    {
        $buku = Buku::with('kategori')->findOrFail($id);

        // Check if book is bookmarked by user
        $isBookmarked = false;
        if (Auth::check()) {
            $isBookmarked = Koleksi::where('id_pengguna', Auth::user()->id_pengguna)
                ->where('id_buku', $id)
                ->exists();
        }

        // Check if book is in cart
        $cartItems = \App\Models\Keranjang::where('id_pengguna', Auth::user()->id_pengguna)->get();
        $isInCart = $cartItems->where('id_buku', $id)->first() ? true : false;

        // Check if book is already borrowed or pending
        $borrowedBooksQuery = \Illuminate\Support\Facades\DB::table('detail_peminjaman')
            ->join('peminjaman', 'detail_peminjaman.id_peminjaman', '=', 'peminjaman.id_peminjaman')
            ->where('peminjaman.id_pengguna', Auth::user()->id_pengguna)
            ->whereIn('peminjaman.status_transaksi', ['berjalan', 'menunggu_verifikasi'])
            ->whereIn('detail_peminjaman.status_buku', ['dipinjam', 'diajukan']);

        $isBorrowed = (clone $borrowedBooksQuery)->where('detail_peminjaman.id_buku', $id)->where('detail_peminjaman.status_buku', 'dipinjam')->exists();
        $isPending = (clone $borrowedBooksQuery)->where('detail_peminjaman.id_buku', $id)->where('detail_peminjaman.status_buku', 'diajukan')->exists();

        $activeBooksCount = $borrowedBooksQuery->sum('detail_peminjaman.jumlah');

        // Calculate limit from settings
        $pengaturan = \App\Models\Pengaturan::first();
        $maxBuku = $pengaturan->maksimal_buku_pinjam ?? 3;

        $limitReached = ($activeBooksCount + $cartItems->count()) >= $maxBuku;

        return view('member.buku.show', compact('buku', 'isBookmarked', 'isInCart', 'isBorrowed', 'isPending', 'limitReached'));
    }

    public function toggleBookmark($id)
    {
        $userId = Auth::user()->id_pengguna;

        $bookmark = Koleksi::where('id_pengguna', $userId)
            ->where('id_buku', $id)
            ->first();

        if ($bookmark) {
            $bookmark->delete();
            return response()->json(['status' => 'removed', 'message' => 'Buku dihapus dari koleksi']);
        } else {
            Koleksi::create([
                'id_pengguna' => $userId,
                'id_buku' => $id
            ]);
            return response()->json(['status' => 'added', 'message' => 'Buku ditambahkan ke koleksi']);
        }
    }
}
