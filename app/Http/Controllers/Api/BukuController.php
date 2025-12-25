<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Buku;
use Illuminate\Support\Facades\Validator;

class BukuController extends Controller
{
    // GET /api/buku
    public function index(Request $request)
    {
        // Urutkan Buku Terbaru di Atas
        $query = Buku::query()->with('kategori')->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('penulis', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->get()
        ]);
    }

    // GET /api/buku/{id}
    public function show($id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json(['status' => 'error', 'message' => 'Buku tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $buku
        ]);
    }

    // POST /api/buku
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required',
            'penulis' => 'required',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'stok_total' => 'required|integer',
            'tahun_terbit' => 'required|integer',
            'isbn' => 'nullable|string',
            'kode_dewey' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $buku = Buku::create([
                'judul' => $request->judul,
                'penulis' => $request->penulis,
                'id_kategori' => $request->id_kategori,
                'tahun_terbit' => $request->tahun_terbit,
                'stok_total' => $request->stok_total,
                'stok_tersedia' => $request->stok_total,
                'penerbit' => $request->penerbit ?? null,
                'isbn' => $request->isbn ?? null,
                'kode_dewey' => $request->kode_dewey ?? null,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Buku berhasil ditambahkan via API',
                'data' => $buku
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan buku: ' . $e->getMessage()
            ], 500);
        }
    }

    // PUT /api/buku/{id}
    public function update(Request $request, $id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json(['status' => 'error', 'message' => 'Buku tidak ditemukan'], 404);
        }

        // Kita bisa tambahkan validasi parsial disini jika mau
        $buku->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Buku berhasil diperbarui',
            'data' => $buku
        ]);
    }

    // DELETE /api/buku/{id}
    public function destroy($id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json(['status' => 'error', 'message' => 'Buku tidak ditemukan'], 404);
        }

        $buku->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Buku berhasil dihapus'
        ]);
    }
}
