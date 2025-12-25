<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kategori;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    // GET /api/kategori
    // GET /api/kategori
    public function index(Request $request)
    {
        // Urutkan ID Descending (Terbaru paling atas)
        $query = Kategori::query()->orderBy('id_kategori', 'desc');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nama_kategori', 'like', "%{$search}%");
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->get()
        ]);
    }

    // GET /api/kategori/{id}
    public function show($id)
    {
        $kategori = Kategori::find($id);

        if (!$kategori) {
            return response()->json(['status' => 'error', 'message' => 'Kategori tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $kategori
        ]);
    }

    // POST /api/kategori
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $kategori = Kategori::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berhasil ditambahkan',
            'data' => $kategori
        ], 201);
    }

    // PUT /api/kategori/{id}
    public function update(Request $request, $id)
    {
        $kategori = Kategori::find($id);

        if (!$kategori) {
            return response()->json(['status' => 'error', 'message' => 'Kategori tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $kategori->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berhasil diperbarui',
            'data' => $kategori
        ]);
    }

    // DELETE /api/kategori/{id}
    public function destroy($id)
    {
        $kategori = Kategori::find($id);

        if (!$kategori) {
            return response()->json(['status' => 'error', 'message' => 'Kategori tidak ditemukan'], 404);
        }

        // Opsional: Cek apakah kategori dipakai buku sebelum delete
        if ($kategori->buku()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori tidak bisa dihapus karena masih memiliki buku.'
            ], 400);
        }

        $kategori->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berhasil dihapus'
        ]);
    }
}
