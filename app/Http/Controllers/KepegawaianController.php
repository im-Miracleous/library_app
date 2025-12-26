<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class KepegawaianController extends Controller
{
    public function index(Request $request)
    {
        // Hanya Admin yang boleh akses (Handling di Controller level selain Middleware)
        if (auth()->user()->peran !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $query = Pengguna::whereIn('peran', ['admin', 'petugas']);

        // Filter Status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Helper untuk API Search (AJAX)
        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => $query->orderBy('id_pengguna', 'desc')->get()
            ]);
        }

        // Pagination
        $pegawai = $query->orderBy('id_pengguna', 'desc')->paginate(10);
        $pegawai->appends($request->all());

        // Statistik
        $totalPegawai = Pengguna::whereIn('peran', ['admin', 'petugas'])->count();
        $totalAdmin = Pengguna::where('peran', 'admin')->count();
        $totalPetugas = Pengguna::where('peran', 'petugas')->count();

        return view('kepegawaian.index', compact('pegawai', 'totalPegawai', 'totalAdmin', 'totalPetugas'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->peran !== 'admin') {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:pengguna',
            'password' => 'required|string|min:8|confirmed',
            'peran' => 'required|in:admin,petugas',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        Pengguna::create([
            'nama' => $validatedData['nama'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'peran' => $validatedData['peran'],
            'telepon' => $validatedData['telepon'],
            'alamat' => $validatedData['alamat'],
            'status' => 'aktif',
        ]);

        return redirect()->back()->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function show($id)
    {
        if (auth()->user()->peran !== 'admin') {
            abort(403, 'Anda tidak memiliki akses.');
        }
        $user = Pengguna::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->peran !== 'admin') {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $user = Pengguna::findOrFail($id);

        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('pengguna')->ignore($user->id_pengguna, 'id_pengguna'),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'peran' => 'required|in:admin,petugas',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $user->nama = $validatedData['nama'];
        $user->email = $validatedData['email'];
        if ($request->filled('password')) {
            $user->password = Hash::make($validatedData['password']);
        }
        $user->peran = $validatedData['peran'];
        $user->telepon = $validatedData['telepon'];
        $user->alamat = $validatedData['alamat'];
        $user->status = $validatedData['status'];

        $user->save();

        return redirect()->back()->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (auth()->user()->peran !== 'admin') {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $user = Pengguna::findOrFail($id);

        // Prevent deleting self
        if ($user->id_pengguna == auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'Pegawai berhasil dihapus.');
    }
}
