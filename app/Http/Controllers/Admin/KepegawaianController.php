<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class KepegawaianController extends Controller
{
    public function index(Request $request)
    {
        // Hanya Admin & Owner yang boleh akses (Handling di Controller level selain Middleware)
        if (auth()->user()->peran !== 'admin' && auth()->user()->peran !== 'owner') {
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

        // Panggil Stored Procedure (Pass Caller Role)
        $results = \Illuminate\Support\Facades\DB::select(
            'CALL sp_get_kepegawaian(?, ?, ?, ?, ?, ?, ?, @total)',
            [$search, $sortCol, $sortDir, $limit, $offset, $peran, auth()->user()->peran]
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

        // Statistik (Respect Visibility)
        $currentUserPeran = auth()->user()->peran;
        // Total Pegawai hanya menghitung Admin & Petugas (Owner tidak termasuk)
        $totalPegawai = Pengguna::whereIn('peran', ['admin', 'petugas'])->count();
        $totalOwner = $currentUserPeran === 'owner' ? Pengguna::where('peran', 'owner')->count() : 0;
        $totalAdmin = Pengguna::where('peran', 'admin')->count();
        $totalPetugas = Pengguna::where('peran', 'petugas')->count();

        return view('admin.kepegawaian.index', compact('pegawai', 'totalPegawai', 'totalOwner', 'totalAdmin', 'totalPetugas'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->peran !== 'admin' && auth()->user()->peran !== 'owner') {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:pengguna',
            'password' => 'required|string|min:8|confirmed',
            'peran' => 'required|in:admin,petugas',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'foto_profil' => 'nullable|image|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto_profil')) {
            $fotoPath = $request->file('foto_profil')->store('profile_photos', 'public');
        }

        Pengguna::create([
            'nama' => $validatedData['nama'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'peran' => $validatedData['peran'],
            'telepon' => $validatedData['telepon'],
            'alamat' => $validatedData['alamat'],
            'foto_profil' => $fotoPath,
            'status' => 'aktif',
        ]);

        return redirect()->back()->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function show($id)
    {
        if (auth()->user()->peran !== 'admin' && auth()->user()->peran !== 'owner') {
            abort(403, 'Anda tidak memiliki akses.');
        }
        $user = Pengguna::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->peran !== 'admin' && auth()->user()->peran !== 'owner') {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $user = Pengguna::findOrFail($id);

        // PROTEKSI ADMIN & OWNER
        $targetUser = Pengguna::findOrFail($id);
        $currentUser = auth()->user();

        // 1. Admin/Petugas TIDAK BOLEH edit Owner
        if ($targetUser->peran === 'owner' && $currentUser->peran !== 'owner') {
            abort(403, 'Akses Ditolak: Anda tidak dapat mengubah data Owner.');
        }

        // 2. Admin TIDAK BOLEH edit sesama Admin (kecuali diri sendiri)
        // Owner BOLEH edit Admin
        if ($currentUser->peran === 'admin' && $targetUser->peran === 'admin' && $targetUser->id_pengguna !== $currentUser->id_pengguna) {
            abort(403, 'Akses Ditolak: Admin tidak dapat mengubah data sesama Admin.');
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
            'foto_profil' => 'nullable|image|max:2048',
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

        // Handle Photo Upload
        if ($request->hasFile('foto_profil')) {
            // Delete old photo
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $user->foto_profil = $request->file('foto_profil')->store('profile_photos', 'public');
        }
        // Handle Draft Delete
        elseif ($request->input('remove_foto_profil') == '1') {
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $user->foto_profil = null;
        }

        if (isset($validatedData['status'])) {
            $user->status = $validatedData['status'];
        }

        $user->save();

        return redirect()->back()->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $user = Pengguna::findOrFail($id);
        $currentUser = auth()->user();

        // 1. Owner tidak boleh dihapus oleh siapapun (termasuk sesama owner jika ada, tapi karena single owner, aman)
        if ($user->peran === 'owner') {
            abort(403, 'AKSI ILEGAL: Akun Root/Owner tidak dapat dihapus.');
        }

        // 2. Admin tidak boleh menghapus Admin lain
        // Owner BOLEH menghapus Admin
        if ($currentUser->peran === 'admin' && $user->peran === 'admin') {
            abort(403, 'Anda tidak dapat menghapus akun Admin.');
        }

        // Hapus foto profil jika ada
        if ($user->foto_profil) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        $user->delete();

        return redirect()->route('kepegawaian.index')->with('success', 'Data pegawai berhasil dihapus');
    }
}
