<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Pengguna;

class AuthController extends Controller
{
    // 1. Tampilkan Halaman Login
    public function showLogin()
    {
        return view('auth.login');
    }

    // 2. Proses Login dengan Advanced Rate Limiting
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Cek User Manual untuk logika Lockout
        $user = Pengguna::where('email', $request->email)->first();

        if ($user) {
            // Cek Status Kunci/Blokir
            if ($user->is_locked) {
                return back()->withErrors(['email' => 'Akun terkunci karena aktivitas mencurigakan. Silahkan hubungi Administrator.'])->onlyInput('email');
            }

            // Cek Status Lockout Sementara
            if ($user->lockout_time && now()->lessThan($user->lockout_time)) {
                $seconds = now()->diffInSeconds($user->lockout_time);
                $minutes = ceil($seconds / 60); // Bulatkan ke atas
                return back()->withErrors(['email' => "Terlalu banyak percobaan Login. Silakan coba lagi dalam $minutes menit."])->onlyInput('email');
            }
        }

        // Coba Login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Reset Counter Kunci jika Login Sukses
            if ($user) {
                $user->update([
                    'login_attempts' => 0,
                    'lockout_time' => null,
                    'is_locked' => false
                ]);
            }

            return redirect()->route('dashboard')->with('success', 'Selamat Datang!');
        }

        // Jika Gagal Login: Terapkan Penalti
        if ($user) {
            $user->increment('login_attempts');
            $attempts = $user->login_attempts; // Ambil nilai terbaru

            $lockData = [];
            $pesanKunci = null;

            // ATURAN 1: ADMINISTRATOR (Kelipatan 3 -> Lock 1 Menit)
            // Asumsi peran admin bisa 'administrator' atau 'admin' (case insensitive biar aman)
            if (in_array(strtolower($user->peran), ['administrator', 'admin'])) {
                if ($attempts % 3 == 0) {
                    $lockData['lockout_time'] = now()->addMinute();
                    $pesanKunci = "Akun dibekukan sementara (1 menit).";
                }
            }
            // ATURAN 2: USER BIASA (Progressive Timeout)
            else {
                if ($attempts == 3) {
                    $lockData['lockout_time'] = now()->addMinutes();
                    $pesanKunci = "Akun dibekukan selama 1 menit.";
                } elseif ($attempts == 6) {
                    $lockData['lockout_time'] = now()->addHour();
                    $pesanKunci = "Akun dibekukan selama 1 jam.";
                } elseif ($attempts == 9) {
                    $lockData['lockout_time'] = now()->addDay();
                    $pesanKunci = "Akun dibekukan selama 24 jam.";
                } elseif ($attempts >= 12) {
                    $lockData['is_locked'] = true;
                    $lockData['lockout_time'] = null;
                    $pesanKunci = "Akun telah diblokir. Silahkan hubungi admin untuk membuka blokir.";
                }
            }

            // Simpan Perubahan jika ada Lock
            if (!empty($lockData)) {
                $user->update($lockData);

                if ($pesanKunci) {
                    return back()->withErrors(['email' => "Login gagal. $pesanKunci"])->onlyInput('email');
                }
            }

            // Info sisa kesempatan
            return back()->withErrors(['email' => "Password salah. Percobaan ke-$attempts."])->onlyInput('email');
        }

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

        // Redirect ke login dengan pesan sukses
        return redirect()->route('login')->with('success', 'Anda berhasil keluar dari sistem.');
    }
}