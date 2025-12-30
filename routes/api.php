<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BukuController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\AnggotaController;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- PUBLIC ROUTES ---
Route::post('/login', [AuthController::class, 'login']);

// System Check
Route::get('/system-status', function () {
    try {
        DB::connection()->getPdo();
        return response()->json(['db_status' => true, 'server_status' => true]);
    } catch (\Exception $e) {
        return response()->json(['db_status' => false, 'server_status' => true]);
    }
});

// Public Read Routes
Route::get('/buku', [BukuController::class, 'index']);
Route::get('/buku/{id}', [BukuController::class, 'show']);
Route::get('/kategori', [KategoriController::class, 'index']);
Route::get('/anggota', [AnggotaController::class, 'index']); // Public List User


// --- PROTECTED ROUTES ---
Route::middleware('auth:sanctum')->group(function () {

    // Auth & Profile
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // KELOLA PENGGUNA (Otomatis buat route store, show, update, destroy. Index sudah di public)
    Route::apiResource('anggota', AnggotaController::class)
        ->names([
            'index' => 'api.anggota.index',
            'store' => 'api.anggota.store',
            'show' => 'api.anggota.show',
            'update' => 'api.anggota.update',
            'destroy' => 'api.anggota.destroy',
        ]);

    // KELOLA BUKU (Create, Update, Delete)
    Route::post('/buku', [BukuController::class, 'store']);
    Route::put('/buku/{id}', [BukuController::class, 'update']);
    Route::delete('/buku/{id}', [BukuController::class, 'destroy']);

    // KELOLA KATEGORI (Create, Update, Delete - Read ada di public)
    Route::post('/kategori', [KategoriController::class, 'store']);
    Route::put('/kategori/{id}', [KategoriController::class, 'update']);
    Route::delete('/kategori/{id}', [KategoriController::class, 'destroy']);

});
