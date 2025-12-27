<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    public function index(Request $request)
    {
        $query = Buku::with('kategori');

        // Fitur Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%$search%")
                    ->orWhere('penulis', 'like', "%$search%")
                    ->orWhere('isbn', 'like', "%$search%");
            });
        }

        $buku = $query->orderBy('id_buku', 'desc')->paginate(10);
        $buku->appends($request->all());

        // Helper untuk API Search (AJAX) - Digunakan di Peminjaman
        if ($request->ajax()) {
            // Re-query with limit for autocomplete
            return response()->json([
                'status' => 'success',
                'data' => $query->orderBy('id_buku', 'desc')->limit(20)->get()
            ]);
        }

        $kategoriList = Kategori::all();

        $totalBuku = Buku::count();
        $totalStok = Buku::sum('stok_total');

        return view('buku.index', compact('buku', 'kategoriList', 'totalBuku', 'totalStok'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'nullable|string|max:255',
            'tahun_terbit' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'isbn' => 'nullable|string|unique:buku,isbn',
            'stok_total' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'kode_dewey' => 'nullable|string',
        ]);

        // Stok tersedia awal = stok total
        $validated['stok_tersedia'] = $validated['stok_total'];

        Buku::create($validated);

        return redirect()->back()->with('success', 'Buku berhasil ditambahkan.');
    }

    public function show($id)
    {
        $buku = Buku::findOrFail($id);
        return response()->json($buku);
    }

    public function update(Request $request, $id)
    {
        $buku = Buku::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'nullable|string|max:255',
            'tahun_terbit' => 'required|integer',
            'isbn' => 'nullable|string|unique:buku,isbn,' . $id . ',id_buku',
            'stok_total' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:tersedia,rusak,hilang',
            'kode_dewey' => 'nullable|string',
        ]);

        // Update data
        $buku->update($validated);

        return redirect()->back()->with('success', 'Data buku berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $buku = Buku::findOrFail($id);
        $buku->delete();
        return redirect()->back()->with('success', 'Buku berhasil dihapus.');
    }
}