<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasswordResetController;

// 1. Halaman Utama (Root)
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// 2. Rute Guest - Hanya bisa diakses jika BELUM login
Route::middleware('guest')->group(function () {

    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.process');

    // Register
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // OTP Routes
    Route::get('/verify-otp/{id}', [AuthController::class, 'showVerifyOtp'])->name('otp.verify');
    Route::post('/verify-otp/{id}', [AuthController::class, 'verifyOtp'])
        // ->middleware('throttle:5,1')  <-- Handled manually in Controller now
        ->name('otp.action');

    // Resend OTP
    Route::post('/resend-otp/{id}', [AuthController::class, 'resendOtp'])
        ->middleware('throttle:3,1') // Limit 3 kali per menit
        ->name('otp.resend');

    // === INI YANG TADI KURANG (PEMICU ERROR ANDA) ===
    Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
    // ================================================
});

// 3. Rute Auth - Hanya bisa diakses jika SUDAH login
Route::middleware(['auth'])->group(function () {

    // --- AREA BEBAS ---
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- AREA BEBAS ---
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/global-search', [\App\Http\Controllers\Api\GlobalSearchController::class, 'search'])->name('global.search');

    // Notifikasi Routes
    Route::get('/notifikasi', [\App\Http\Controllers\NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{id}/read', [\App\Http\Controllers\NotifikasiController::class, 'markAsRead'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [\App\Http\Controllers\NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.readAll');

    // Profile Routes
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // --- AREA KHUSUS ADMIN & OWNER ---
    Route::middleware(['role:admin,owner'])->group(function () {
        Route::resource('kepegawaian', \App\Http\Controllers\KepegawaianController::class);
        Route::get('/pengaturan', [\App\Http\Controllers\PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::put('/pengaturan', [\App\Http\Controllers\PengaturanController::class, 'update'])->name('pengaturan.update');

        Route::resource('buku', \App\Http\Controllers\BukuController::class);
        Route::resource('kategori', \App\Http\Controllers\KategoriController::class);
        Route::resource('anggota', \App\Http\Controllers\AnggotaController::class);

        // Modul Laporan
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [\App\Http\Controllers\LaporanController::class, 'index'])->name('index');
            Route::get('/peminjaman', [\App\Http\Controllers\LaporanController::class, 'peminjaman'])->name('peminjaman');
            Route::get('/denda', [\App\Http\Controllers\LaporanController::class, 'denda'])->name('denda');
            Route::post('/denda/{id}/bayar', [\App\Http\Controllers\DendaController::class, 'update'])->name('denda.bayar');
        });

        // Route Update Status Denda (Outside Laporan Prefix)
        Route::put('/denda/{id}', [\App\Http\Controllers\DendaController::class, 'update'])->name('denda.update');
    });

    // --- AREA PETUGAS, ADMIN & OWNER (Sirkulasi) ---
    Route::middleware(['role:admin,petugas,owner'])->group(function () {
        // Rute Resource untuk Pengunjung (Sirkulasi)
        Route::resource('pengunjung', \App\Http\Controllers\PengunjungController::class);

        // Rute Resource untuk Peminjaman
        Route::resource('peminjaman', \App\Http\Controllers\PeminjamanController::class);

        // Rute Resource untuk Pengembalian
        Route::get('/pengembalian', [\App\Http\Controllers\PengembalianController::class, 'index'])->name('pengembalian.index');
        Route::get('/pengembalian/{id}', [\App\Http\Controllers\PengembalianController::class, 'show'])->name('pengembalian.show');
        Route::post('/pengembalian', [\App\Http\Controllers\PengembalianController::class, 'store'])->name('pengembalian.store');
    });

});