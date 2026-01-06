<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\PresensiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/test', function () {
        return response()->json(['message' => 'Server Jalan Bos!']);
    });

    // Presensi & Izin
    Route::get('/presensi/izin', [PresensiController::class, 'getizin']);
    Route::post('/presensi/storeizin', [PresensiController::class, 'storeizin']);
    Route::get('/konfigurasi-lokasi', [PresensiController::class, 'getLokasiKantor']);

    // Profile
    Route::post('/profile/update/{email}', [PresensiController::class, 'updateprofile']);
});
