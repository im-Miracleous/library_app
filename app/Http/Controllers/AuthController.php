<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengaturan;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    // 1. Tampilkan Halaman Login
    public function showLogin()
    {
        // Cek jika sudah login, lempar ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        $pengaturan = Pengaturan::first();
        return view('auth.login', compact('pengaturan'));
    }

    // 1b. Tampilkan Halaman Register
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    // 1c. PROSES REGISTER (LOGIKA: DAFTAR -> GENERATE OTP -> REDIRECT OTP)
    public function register(Request $request)
    {
        // Validasi Input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:pengguna'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 1. Generate ID Pengguna (Format: U-12345)
        $id_baru = 'U-' . rand(10000, 99999);

        // 2. Generate OTP 6 Digit
        $otp = rand(100000, 999999);

        // 3. Simpan ke Database
        $user = Pengguna::create([
            'id_pengguna' => $id_baru,
            'nama' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'peran' => 'anggota',
            'status' => 'aktif',
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10), // Expired 10 menit
            'login_attempts' => 0,
            'is_locked' => false
        ]);

        // 4. Kirim Email OTP
        try {
            Mail::raw("Halo $user->nama, Kode Verifikasi (OTP) Anda adalah: $otp. Kode ini berlaku selama 10 menit.", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Kode OTP Perpustakaan');
            });
        } catch (\Exception $e) {
            // Log error diam-diam
        }

        // 5. PENTING: JANGAN LOGIN DISINI (Auth::login HILANG)
        // Langsung lempar ke halaman verifikasi membawa ID Pengguna
        return redirect()->route('otp.verify', ['id' => $user->id_pengguna])
                         ->with('success', 'Registrasi berhasil! Cek email Anda untuk kode OTP.');
    }

    // ==========================================
    // BAGIAN VERIFIKASI OTP
    // ==========================================

    // Tampilkan Form Input OTP
    public function showVerifyOtp($id)
    {
        return view('auth.verify-otp', ['id' => $id]);
    }

    // Proses Cek OTP
    public function verifyOtp(Request $request, $id)
    {
        $request->validate([
            'otp' => 'required|numeric'
        ]);

        // 1. Bersihkan ID
        $clean_id = trim($id);

        // 2. Cari user
        $user = Pengguna::where('id_pengguna', $clean_id)->first();

        // Jika user tidak ada / URL ngaco
        if (!$user) {
            return redirect('/login')->with('error', 'User tidak ditemukan atau Link tidak valid.');
        }

        // Cek 2: OTP cocok gak?
        if ($user->otp_code != $request->otp) {
            return back()->with('error', 'Kode OTP salah!');
        }

        // Cek 3: OTP kadaluarsa gak?
        if (Carbon::now()->gt($user->otp_expires_at)) {
            return back()->with('error', 'Kode OTP sudah kadaluarsa. Silakan daftar ulang.');
        }

        // Jika Sukses:
        $user->update([
            'otp_code' => null,
            'otp_expires_at' => null
        ]);

        // BARU DISINI KITA LOGIN  USER
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Verifikasi berhasil! Selamat datang.');
    }

    // ==========================================
    // LOGIKA LOGIN BIASA
    // ==========================================
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = Pengguna::where('email', $request->email)->first();

        if ($user) {
            if ($user->is_locked) {
                return back()->withErrors(['email' => 'Akun terkunci. Hubungi Admin.'])->onlyInput('email');
            }
            if ($user->lockout_time && now()->lessThan($user->lockout_time)) {
                $seconds = now()->diffInSeconds($user->lockout_time);
                $minutes = ceil($seconds / 60);
                return back()->withErrors(['email' => "Tunggu $minutes menit lagi."])->onlyInput('email');
            }
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            if ($user) {
                $user->update(['login_attempts' => 0, 'lockout_time' => null, 'is_locked' => false]);
            }
            return redirect()->route('dashboard')->with('success', 'Selamat Datang!');
        }

        // Logic Lockout
        if ($user) {
            $user->increment('login_attempts');
        
            if ($user->login_attempts >= 3) {
            
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}