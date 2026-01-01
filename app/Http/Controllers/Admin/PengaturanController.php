<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Pengaturan;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    /**
     * Display the settings form.
     */
    public function index()
    {
        $pengaturan = Pengaturan::first();

        // Buat data default jika belum ada (safety check)
        if (!$pengaturan) {
            $pengaturan = Pengaturan::create([
                'nama_perpustakaan' => 'Perpustakaan Digital',
                'denda_per_hari' => 1000,
                'denda_rusak' => 50000,
                'denda_hilang' => 100000,
                'batas_peminjaman_hari' => 7,
                'maksimal_buku_pinjam' => 3,
            ]);
        }

        return view('admin.pengaturan.index', compact('pengaturan'));
    }

    /**
     * Update the settings in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'nama_perpustakaan' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'denda_per_hari' => 'required|numeric|min:0',
            'denda_rusak' => 'required|numeric|min:0',
            'denda_hilang' => 'required|numeric|min:0',
            'batas_peminjaman_hari' => 'required|integer|min:1',
            'maksimal_buku_pinjam' => 'required|integer|min:1',
        ]);

        $pengaturan = Pengaturan::first();
        if (!$pengaturan) {
            $pengaturan = new Pengaturan();
        }

        $data = $request->except('logo');

        // Handle File Upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($pengaturan->logo_path && \Illuminate\Support\Facades\Storage::exists('public/' . $pengaturan->logo_path)) {
                \Illuminate\Support\Facades\Storage::delete('public/' . $pengaturan->logo_path);
            }

            // Store new logo
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo_path'] = $path;
        }

        if ($pengaturan->exists) {
            $pengaturan->update($data);
        } else {
            // If creating for first time (rare case as index creates it)
            Pengaturan::create($data);
        }

        return redirect()->route('pengaturan.index')->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
