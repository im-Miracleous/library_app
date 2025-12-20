<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use App\Models\Pengguna;
use Illuminate\Auth\Events\PasswordReset;

class PasswordResetController extends Controller
{
    /**
     * Tampilkan form request link reset password.
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Proses pengiriman link reset password ke email.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Cek apakah akun terkunci (Advanced Rate Limiting Rule)
        $user = Pengguna::where('email', $request->email)->first();
        if ($user) {
            if ($user->is_locked) {
                return back()->withErrors(['email' => 'Akun terkunci permanen. Fitur reset password dinonaktifkan.']);
            }
            if ($user->lockout_time && now()->lessThan($user->lockout_time)) {
                $diff = now()->diffForHumans($user->lockout_time, ['syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW]);
                return back()->withErrors(['email' => "Akun sedang dibekukan. Silakan tunggu hingga sesi kunci berakhir."]);
            }
        }

        // Kirim link reset password (menggunakan default Laravel Password Broker)
        $status = Password::sendResetLink($request->only('email'));

        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
    }

    /**
     * Tampilkan form reset password (input password baru).
     */
    public function edit(Request $request)
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Proses update password baru.
     */
    public function update(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Lakukan reset password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Callback jika token valid
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
    }
}
