<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    // 1. Tampilkan Halaman Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // 2. Proses Login
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Coba Login (Laravel otomatis mengecek password hash)
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('dashboard')->with('success', 'Selamat Datang!');
        }

        // Jika Gagal
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // 3. Proses Logout
    public function logout(Request $request)
    {
        Auth::logout(); // Hapus sesi otentikasi

        $request->session()->invalidate(); // Hancurkan file sesi lama
        $request->session()->regenerateToken(); // Buat token CSRF baru (security)

        return redirect('/login');

        // Redirect ke login dengan pesan sukses
        return redirect()->route('login')->with('success', 'Anda berhasil keluar dari sistem.');
    }
}