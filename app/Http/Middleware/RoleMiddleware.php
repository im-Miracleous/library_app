<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        // 1. Cek apakah user sudah login?
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 2. Cek apakah role user SESUAI dengan yang diminta?
        // Asumsi di database tabel 'users' ada kolom 'role' (admin/staff/user)
        if (Auth::user()->role !== $role) {
            abort(403, 'Akses Ditolak! Anda bukan ' . $role);
        }

        return $next($request);
    }
}