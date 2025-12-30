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

        // Parameter Default
        $search = $request->input('search', '');
        $sortCol = $request->input('sort', 'created_at');
        $sortDir = $request->input('direction', 'desc');
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $limit;
        $peran = $request->input('peran', ''); // Add peran filter

        // Panggil Stored Procedure
        $results = \Illuminate\Support\Facades\DB::select(
            'CALL sp_get_kepegawaian(?, ?, ?, ?, ?, ?, @total)',
            [$search, $sortCol, $sortDir, $limit, $offset, $peran]
        );

        // Ambil Total Data
        $totalResult = \Illuminate\Support\Facades\DB::select('SELECT @total as total');
        $total = $totalResult[0]->total ?? 0;

        // Buat Paginator
        $items = Pengguna::hydrate($results);
        $pegawai = new \Illuminate\Pagination\LengthAwarePaginator(
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
                'links' => (string) $pegawai->links()
            ]);
        }

        // Statistik
        $totalPegawai = Pengguna::whereIn('peran', ['admin', 'petugas'])->count();
        $totalAdmin = Pengguna::where('peran', 'admin')->count();
        $totalPetugas = Pengguna::where('peran', 'petugas')->count();

        return view('administrator.kepegawaian.index', compact('pegawai', 'totalPegawai', 'totalAdmin', 'totalPetugas'));
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

        // PROTEKSI ADMIN: Tidak boleh edit sesama Admin (kecuali diri sendiri)
        if ($user->peran === 'admin' && $user->id_pengguna !== auth()->user()->id_pengguna) {
            abort(403, 'Anda tidak dapat mengubah data sesama Admin.');
        }

        // UNLOCK ACCOUNT FEATURE
        if ($request->boolean('unlock_account')) {
            $user->update([
                'is_locked' => false,
                'login_attempts' => 0,
                'lockout_time' => null
            ]);
            return redirect()->back()->with('success', 'Akun berhasil dibuka kuncinya.');
        }

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

        // PROTEKSI DIRI SENDIRI: Admin tidak boleh ubah Role & Status diri sendiri
        if ($user->id_pengguna === auth()->user()->id_pengguna) {
            unset($validatedData['peran']);
            unset($validatedData['status']);
        }

        $user->nama = $validatedData['nama'];
        $user->email = $validatedData['email'];
        if ($request->filled('password')) {
            $user->password = Hash::make($validatedData['password']);
        }

        // Update peran & status hanya jika key masih ada (tidak di-unset)
        if (isset($validatedData['peran'])) {
            $user->peran = $validatedData['peran'];
        }
        $user->telepon = $validatedData['telepon'];
        $user->alamat = $validatedData['alamat'];

        if (isset($validatedData['status'])) {
            $user->status = $validatedData['status'];
        }

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

        // PROTEKSI: Tidak boleh hapus Admin
        if ($user->peran === 'admin') {
            abort(403, 'Anda tidak dapat menghapus akun Admin.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'Pegawai berhasil dihapus.');
    }
}
