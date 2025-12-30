<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
        // Tapi stdClass pun bisa, cukup cast ke Array atau Object.
        // Untuk amannya kita hydrate jika hasil tidak kosong.
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
                'links' => (string) $pengguna->links() // Render pagination links jika perlu
            ]);
        }

        // Stats (Bisa juga dibuatkan SP terpisah jika ingin full SP, tapi Eloquent simple count ok)
        // Atau buat query simple count agar konsisten
        $totalAnggota = Pengguna::where('peran', 'anggota')->count();
        $totalAktif = Pengguna::where('peran', 'anggota')->where('status', 'aktif')->count();
        $totalNonaktif = Pengguna::where('peran', 'anggota')->where('status', 'nonaktif')->count();

        return view('administrator.anggota.index', compact('pengguna', 'totalAnggota', 'totalAktif', 'totalNonaktif'));
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
            ]);

            \Illuminate\Support\Facades\Log::info('Validation passed');

            Pengguna::create([
                'nama' => $validatedData['nama'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'peran' => 'anggota',
                'telepon' => $validatedData['telepon'],
                'alamat' => $validatedData['alamat'],
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
        \Illuminate\Support\Facades\Log::info('AnggotaController::destroy triggered for ID: ' . $id);
        try {
            $user = Pengguna::findOrFail($id);
            $user->delete();
            \Illuminate\Support\Facades\Log::info('User deleted successfully');
            return redirect()->back()->with('success', 'Anggota berhasil dihapus.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in destroy: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus anggota: ' . $e->getMessage());
        }
    }
}
