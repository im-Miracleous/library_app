<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasswordResetController;

// 1. Halaman Utama (Root)
// Logika: Jika sudah login -> lempar ke dashboard. Jika belum -> lempar ke login.
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// 2. Rute Guest - Hanya bisa diakses jika BELUM login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login.process');

    // Route Register
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Route Lupa Password
    Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

// 3. Rute Auth - Hanya bisa diakses jika SUDAH login
Route::middleware(['auth'])->group(function () {

    // --- AREA BEBAS (Semua user login bisa akses) ---
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


    // --- AREA KHUSUS ADMIN (Pakai Middleware Role) ---
    // Hanya user dengan role 'admin' yang bisa masuk sini
    Route::middleware(['role:admin'])->group(function () {
        // Kelola data pegawai (Sangat Rahasia)
        Route::resource('kepegawaian', \App\Http\Controllers\KepegawaianController::class);
        
        // Pengaturan Aplikasi
        Route::get('/pengaturan', [\App\Http\Controllers\PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::put('/pengaturan', [\App\Http\Controllers\PengaturanController::class, 'update'])->name('pengaturan.update');
    });


    // --- AREA PETUGAS & ADMIN ---
    // User role 'petugas' DAN 'admin' bisa akses (mengelola buku & anggota)
    // Catatan: Pastikan Middleware kamu support multi-role atau buat logic 'admin' boleh akses area 'petugas'.
    // Kalau middleware kamu simple, bisa buat group terpisah atau pakai logic OR.
    
    Route::middleware(['role:admin,petugas'])->group(function () {
        Route::resource('buku', \App\Http\Controllers\BukuController::class);
        Route::resource('kategori', \App\Http\Controllers\KategoriController::class);
        Route::resource('pengguna', \App\Http\Controllers\PenggunaController::class);
    });

});