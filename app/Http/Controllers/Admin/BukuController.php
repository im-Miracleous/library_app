<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    public function index(Request $request)
    {
        // Parameter Default
        $search = $request->input('search', '');
        $sortCol = $request->input('sort', 'created_at');
        $sortDir = $request->input('direction', 'desc');
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $limit;

        // Panggil Stored Procedure
        $results = \Illuminate\Support\Facades\DB::select(
            'CALL sp_get_buku(?, ?, ?, ?, ?, @total)',
            [$search, $sortCol, $sortDir, $limit, $offset]
        );

        // Ambil Total Data
        $totalResult = \Illuminate\Support\Facades\DB::select('SELECT @total as total');
        $total = $totalResult[0]->total ?? 0;

        // Hydrate
        $items = Buku::hydrate($results);

        // Manual Paginator
        $buku = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $limit,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // API Response (AJAX)
        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => $items,
                'total' => $total,
                'links' => (string) $buku->links()
            ]);
        }

        $kategoriList = Kategori::all();
        $totalBuku = Buku::count();
        $totalStok = Buku::sum('stok_total');

        return view('admin.buku.index', compact('buku', 'kategoriList', 'totalBuku', 'totalStok'));
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