<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/system-status', function () {
    $dbStatus = false;
    try {
        DB::connection()->getPdo();
        $dbStatus = true;
    } catch (\Exception $e) {
        $dbStatus = false;
    }

    return response()->json([
        'db_status' => $dbStatus,
        'server_status' => true
    ]);
});
