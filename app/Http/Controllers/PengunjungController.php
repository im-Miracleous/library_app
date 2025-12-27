<?php

namespace App\Http\Controllers;

use App\Models\Pengunjung;
use Illuminate\Http\Request;

class PengunjungController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pengunjung::latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_pengunjung', 'like', "%{$search}%")
                ->orWhere('keperluan', 'like', "%{$search}%")
                ->orWhere('jenis_pengunjung', 'like', "%{$search}%");
        }

        $pengunjung = $query->paginate(10)->withQueryString();

        return view('sirkulasi.pengunjung.index', compact('pengunjung'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pengunjung' => 'required|string|max:255',
            'jenis_pengunjung' => 'required|in:umum,anggota,petugas,admin',
            'keperluan' => 'nullable|string',
            'id_pengguna' => 'nullable|exists:pengguna,id_pengguna',
        ]);

        Pengunjung::create($request->all());

        return redirect()->route('pengunjung.index')->with('success', 'Data pengunjung berhasil dicatat.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pengunjung' => 'required|string|max:255',
            'jenis_pengunjung' => 'required|in:umum,anggota,petugas,admin',
            'keperluan' => 'nullable|string',
        ]);

        $pengunjung = Pengunjung::findOrFail($id);
        $pengunjung->update($request->all());

        return redirect()->route('pengunjung.index')->with('success', 'Data pengunjung berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pengunjung = Pengunjung::findOrFail($id);
        $pengunjung->delete();

        return redirect()->route('pengunjung.index')->with('success', 'Data pengunjung berhasil dihapus.');
    }
}
