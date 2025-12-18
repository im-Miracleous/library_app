<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengguna::where('peran', 'anggota');

        // LOGIKA FILTER: Jika ada request 'status' di URL (misal ?status=aktif)
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Ambil data dengan pagination
        $pengguna = $query->orderBy('created_at', 'desc')->paginate(10);

        // Append query string ke pagination links (supaya saat pindah halaman, filter tidak hilang)
        $pengguna->appends($request->all());

        // HITUNG STATISTIK UNTUK SIDEBAR
        $totalAnggota = Pengguna::where('peran', 'anggota')->count();
        $totalAktif = Pengguna::where('peran', 'anggota')->where('status', 'aktif')->count();
        $totalNonaktif = Pengguna::where('peran', 'anggota')->where('status', 'nonaktif')->count();

        return view('pengguna.index', compact('pengguna', 'totalAnggota', 'totalAktif', 'totalNonaktif'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:pengguna',
            'password' => 'required|string|min:8|confirmed',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        Pengguna::create([
            'nama' => $validatedData['nama'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'peran' => 'anggota',
            'telepon' => $validatedData['telepon'],
            'alamat' => $validatedData['alamat'],
            'status' => 'aktif',
        ]);

        return redirect()->back()->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function show($id)
    {
        $user = Pengguna::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = Pengguna::findOrFail($id);

        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('pengguna')->ignore($user->id_pengguna, 'id_pengguna'),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $user->nama = $validatedData['nama'];
        $user->email = $validatedData['email'];
        if ($request->filled('password')) {
            $user->password = Hash::make($validatedData['password']);
        }
        $user->telepon = $validatedData['telepon'];
        $user->alamat = $validatedData['alamat'];
        $user->status = $validatedData['status'];
        
        $user->save();

        return redirect()->back()->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = Pengguna::findOrFail($id);
        $user->delete();
        return redirect()->back()->with('success', 'Anggota berhasil dihapus.');
    }
}