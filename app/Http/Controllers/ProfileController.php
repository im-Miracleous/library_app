<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Pengguna;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('pengguna', 'email')->ignore($user->id_pengguna, 'id_pengguna'),
            ],
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        // Simpan data text
        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->telepon = $request->telepon;
        $user->alamat = $request->alamat;

        // Handle File Upload or Removal
        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama jika ada
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            // Simpan foto baru
            $path = $request->file('foto_profil')->store('profiles', 'public');
            $user->foto_profil = $path;
        } elseif ($request->input('remove_foto_profil') == '1') {
            // Hapus foto jika diminta (Draft Delete)
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $user->foto_profil = null;
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
