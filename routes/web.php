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
    Route::post('/login', [AuthController::class, 'login']);

    // Route Lupa Password
    Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

// 3. Rute Auth - Hanya bisa diakses jika SUDAH login
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard (Menggunakan DashboardController)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rute Resource untuk Pengguna (Anggota)
    Route::resource('pengguna', \App\Http\Controllers\PenggunaController::class);

    // Rute Resource untuk Kategori Buku
    Route::resource('kategori', \App\Http\Controllers\KategoriController::class);

    // Rute Resource untuk Buku
    Route::resource('buku', \App\Http\Controllers\BukuController::class);

});