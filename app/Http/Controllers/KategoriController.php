<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $query = Kategori::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('nama_kategori', 'like', '%' . $request->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
        }

        $kategori = $query->orderBy('id_kategori', 'desc')->paginate(10);
        $kategori->appends($request->all());

        $totalKategori = Kategori::count();
        $kategoriBaru = Kategori::whereMonth('created_at', date('m'))->count();

        return view('kategori.index', compact('kategori', 'totalKategori', 'kategoriBaru'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori',
            'deskripsi' => 'nullable|string',
        ]);

        Kategori::create([
            'nama_kategori' => $validated['nama_kategori'],
            'deskripsi' => $validated['deskripsi'],
        ]);

        return redirect()->back()->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function show($id)
    {
        $kategori = Kategori::findOrFail($id);
        return response()->json($kategori);
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori,' . $id . ',id_kategori',
            'deskripsi' => 'nullable|string',
        ]);

        $kategori->update([
            'nama_kategori' => $validated['nama_kategori'],
            'deskripsi' => $validated['deskripsi'],
        ]);

        return redirect()->back()->with('success', 'Data kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);
        
        // Cek apakah kategori ini dipakai di tabel Buku?
        // Jika ya, sebaiknya dicegah hapus (Opsional, tapi aman)
        if ($kategori->buku()->count() > 0) {
            return redirect()->back()->with('error', 'Gagal hapus! Kategori ini masih digunakan oleh data buku.');
        }

        $kategori->delete();
        return redirect()->back()->with('success', 'Kategori berhasil dihapus.');
    }
}
