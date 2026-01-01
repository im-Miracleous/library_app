<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class AnggotaController extends Controller
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
        $status = $request->input('status', ''); // Add status filter

        // Panggil Stored Procedure
        $results = \Illuminate\Support\Facades\DB::select(
            'CALL sp_get_anggota(?, ?, ?, ?, ?, ?, @total)',
            [$search, $sortCol, $sortDir, $limit, $offset, $status]
        );

        // Ambil Total Data
        $totalResult = \Illuminate\Support\Facades\DB::select('SELECT @total as total');
        $total = $totalResult[0]->total ?? 0;

        // Buat Paginator Manual
        // Kita hydrate ke model supaya fitur aksesors/mutators tetap jalan jika ada (opsional)
        $items = Pengguna::hydrate($results);

        $pengguna = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $limit,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // API Response (untuk Live Search AJAX dll)
        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => $items,
                'total' => $total,
                'links' => (string) $pengguna->links()
            ]);
        }

        // Stats
        $totalAnggota = Pengguna::where('peran', 'anggota')->count();
        $totalAktif = Pengguna::where('peran', 'anggota')->where('status', 'aktif')->count();
        $totalNonaktif = Pengguna::where('peran', 'anggota')->where('status', 'nonaktif')->count();

        return view('admin.anggota.index', compact('pengguna', 'totalAnggota', 'totalAktif', 'totalNonaktif'));
    }

    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('AnggotaController::store triggered', $request->all());

        try {
            $validatedData = $request->validate([
                'nama' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:pengguna',
                'password' => 'required|string|min:8|confirmed',
                'telepon' => 'nullable|string|max:20',
                'alamat' => 'nullable|string',
                'foto_profil' => 'nullable|image|max:2048', // Validation for photo
            ]);

            \Illuminate\Support\Facades\Log::info('Validation passed');

            $fotoPath = null;
            if ($request->hasFile('foto_profil')) {
                $fotoPath = $request->file('foto_profil')->store('profile_photos', 'public');
            }

            Pengguna::create([
                'nama' => $validatedData['nama'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'peran' => 'anggota',
                'telepon' => $validatedData['telepon'],
                'alamat' => $validatedData['alamat'],
                'foto_profil' => $fotoPath,
                'status' => 'aktif',
            ]);

            \Illuminate\Support\Facades\Log::info('User created successfully');

            return redirect()->back()->with('success', 'Anggota berhasil ditambahkan.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in store: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $user = Pengguna::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = Pengguna::findOrFail($id);

        if ($user->peran === 'admin' && $user->id_pengguna !== auth()->user()->id_pengguna) {
            abort(403, 'Anda tidak dapat mengubah data sesama Admin.');
        }

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
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
            'foto_profil' => 'nullable|image|max:2048',
        ]);

        if ($user->id_pengguna === auth()->user()->id_pengguna) {
            unset($validatedData['status']);
        }

        $user->nama = $validatedData['nama'];
        $user->email = $validatedData['email'];
        if ($request->filled('password')) {
            $user->password = Hash::make($validatedData['password']);
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

        return redirect()->back()->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy($id)
    {
        \Illuminate\Support\Facades\Log::info('AnggotaController::destroy triggered for ID: ' . $id);
        try {
            $user = Pengguna::findOrFail($id);

            if ($user->peran === 'admin') {
                abort(403, 'Anda tidak dapat menghapus akun Admin.');
            }

            // Delete photo if exists
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $user->delete();
            \Illuminate\Support\Facades\Log::info('User deleted successfully');
            return redirect()->back()->with('success', 'Anggota berhasil dihapus.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in destroy: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus anggota: ' . $e->getMessage());
        }
    }
}
