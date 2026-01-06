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
    // 1a. Tampilkan Halaman Login
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
        Pengguna::create([
            'id_pengguna' => 'TEMP-' . time(),
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

        // RE-FETCH User berdasarkan Email untuk mendapatkan ID ASLI dari Trigger Database
        $user = Pengguna::where('email', $request->email)->firstOrFail();

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
        // Langsung lempar ke halaman verifikasi membawa ID Pengguna yang BENAR
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

        // --- RATE LIMITING MANUAL ---
        $throttleKey = 'otp-verify:' . $clean_id . '|' . $request->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);
            return back()
                ->withErrors(['otp' => "Terlalu banyak percobaan. Tunggu $minutes menit ($seconds detik) lagi."])
                ->withInput();
        }

        // DEBUG: Cek Data Masuk
        \Illuminate\Support\Facades\Log::info("Verifikasi OTP. ID: $clean_id, Input: " . $request->otp);

        // 2. Cari user
        $user = Pengguna::where('id_pengguna', $clean_id)->first();

        // Jika user tidak ada / URL ngaco
        if (!$user) {
            \Illuminate\Support\Facades\Log::warning("Verifikasi Gagal: User tidak ditemukan. ID: $clean_id");
            return redirect()->route('login')->with('error', 'User tidak ditemukan atau Link tidak valid.');
        }

        // Cek 2: OTP cocok gak?
        if (strval($user->otp_code) !== strval($request->otp)) {
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);
            return back()->withErrors(['otp' => 'Kode OTP salah!'])->withInput();
        }

        // Cek 3: OTP kadaluarsa gak?
        if ($user->otp_expires_at && Carbon::now()->gt($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kadaluarsa. Silakan minta kode baru.']);
        }

        // Jika Sukses:
        \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);

        // Gunakan forceFill / save untuk memastikan update terjadi
        $user->otp_code = null;
        $user->otp_expires_at = null;

        // PENTING: Matikan timestamp update jika perlu, tapi save() aman
        if ($user->save()) {
            \Illuminate\Support\Facades\Log::info("Verifikasi Sukses. OTP dihapus untuk user: $clean_id");
        } else {
            \Illuminate\Support\Facades\Log::error("Verifikasi Gagal Save DB. User: $clean_id");
        }

        return redirect()->route('login')->with('success', 'Verifikasi berhasil! Silahkan lakukan login kembali.');
    }

    // Kirim Ulang OTP
    public function resendOtp($id)
    {
        $user = Pengguna::where('id_pengguna', $id)->firstOrFail();

        // Cek batasan waktu (Opsional: Cegah spamming tombol resend)
        // Disini kita langsung generate baru saja

        $otp = rand(100000, 999999);
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10)
        ]);

        try {
            Mail::raw("Halo $user->nama, Kode OTP Baru Anda: $otp.", function ($msg) use ($user) {
                $msg->to($user->email)->subject('Kode OTP Baru Perpustakaan');
            });
        } catch (\Exception $e) {
        }

        return back()->with('success', 'Kode OTP baru telah dikirim. Cek email Anda.');
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
            // CEK STATUS: Nonaktif
            if ($user->status === 'nonaktif') {
                return back()->withErrors(['email' => 'Akun Anda dinonaktifkan. Silahkan hubungi Administrator.'])->onlyInput('email');
            }
            if ($user->is_locked && $user->peran !== 'owner') {
                return back()->withErrors(['email' => 'Akun terkunci. Hubungi Administrator untuk membuka akses.'])->onlyInput('email');
            }
            if ($user->lockout_time && now()->lessThan($user->lockout_time) && $user->peran !== 'owner') {
                $seconds = now()->diffInSeconds($user->lockout_time);
                $minutes = ceil($seconds / 60);
                return back()->withErrors(['email' => "Tunggu $minutes menit lagi."])->onlyInput('email');
            }

            // CEK 3: Apakah User sudah verifikasi OTP?
            if (!empty($user->otp_code)) {
                return redirect()->route('otp.verify', ['id' => $user->id_pengguna])
                    ->with('error', 'Akun belum diverifikasi. Silakan masukkan kode OTP yang dikirim ke email.');
            }
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            if ($user) {
                $user->update(['login_attempts' => 0, 'lockout_time' => null, 'is_locked' => false]);
            }
            // Redirect berdasarkan peran
            // Redirect berdasarkan peran
            if ($user->peran === 'anggota') {
                return redirect()->route('member.dashboard');
            } else {
                // Admin, Petugas, Owner
                return redirect()->route('dashboard');
            }
        }

        // Logic Lockout
        if ($user && $user->peran !== 'owner') {
            $user->increment('login_attempts');

            if ($user->login_attempts >= 3) {
                $user->update(['lockout_time' => now()->addMinutes(5)]);

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