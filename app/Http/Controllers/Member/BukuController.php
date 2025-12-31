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

        return view('member.buku.index', compact('buku', 'kategori_list', 'bookmarkedIds'));
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

        return view('member.buku.show', compact('buku', 'isBookmarked'));
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
