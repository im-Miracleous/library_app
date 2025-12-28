<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login?
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 2. Cek apakah role user SESUAI dengan yang diminta?
        // Kita gunakan 'peran' sesuai kolom di tabel pengguna, dan support multiple roles
        if (!in_array(Auth::user()->peran, $roles)) {
            abort(403, 'Akses Ditolak! Anda bukan ' . implode(' atau ', $roles));
        }

        return $next($request);
    }
}