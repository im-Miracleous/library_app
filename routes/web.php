<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// 1. Halaman Utama (Root)
// Logika: Jika sudah login -> lempar ke dashboard. Jika belum -> lempar ke login.
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// 2. Rute Guest - Hanya bisa diakses jika BELUM login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
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
    
});