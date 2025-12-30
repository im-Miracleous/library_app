<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AnggotaController extends Controller
{
    // GET /api/anggota
    public function index(Request $request)
    {
        // Fitur search sederhana untuk anggota, urutkan terbaru
        $query = Pengguna::query()->orderBy('id_pengguna', 'desc');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('id_pengguna', 'like', "%{$search}%");
            });
        }

        // Filter Param 'peran' (Opsional) - Agar bisa request khusus 'anggota'
        if ($request->has('peran')) {
            $query->where('peran', $request->peran);
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->get()
        ]);
    }

    // GET /api/anggota/{id}
    public function show($id)
    {
        $pengguna = Pengguna::find($id);

        if (!$pengguna) {
            return response()->json(['status' => 'error', 'message' => 'Pengguna tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $pengguna
        ]);
    }

    // POST /api/anggota
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:pengguna,email',
            'password' => 'required|min:6',
            'peran' => 'required|in:admin,petugas,anggota',
            'telepon' => 'nullable|string',
            'alamat' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        // Buat data baru
        $pengguna = Pengguna::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash Password!
            'peran' => $request->peran,
            'telepon' => $request->telepon,
            'alamat' => $request->alamat,
            'status' => 'aktif'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengguna berhasil ditambahkan',
            'data' => $pengguna
        ], 201);
    }

    // PUT /api/anggota/{id}
    public function update(Request $request, $id)
    {
        $pengguna = Pengguna::find($id);

        if (!$pengguna) {
            return response()->json(['status' => 'error', 'message' => 'Pengguna tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:pengguna,email,' . $id . ',id_pengguna',
            'peran' => 'required|in:admin,petugas,anggota',
            'password' => 'nullable|min:6', // Password opsional saat edit
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $data = $request->except(['password']);

        // Jika password diisi, kita hash baru. Jika kosong, biarkan password lama.
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $pengguna->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data pengguna berhasil diperbarui',
            'data' => $pengguna
        ]);
    }

    // DELETE /api/anggota/{id}
    public function destroy($id)
    {
        $pengguna = Pengguna::find($id);

        if (!$pengguna) {
            return response()->json(['status' => 'error', 'message' => 'Pengguna tidak ditemukan'], 404);
        }

        $pengguna->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pengguna berhasil dihapus'
        ]);
    }
}
