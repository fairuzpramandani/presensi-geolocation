<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\PresensiApiController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\Api\IzinApiController;
use App\Http\Controllers\Api\DepartemenController;
use App\Http\Controllers\Api\LokasiApiController;

// AUTH
Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/register', [AuthApiController::class, 'register']);
Route::get('/departemen', [DepartemenController::class, 'listDepartemen']);
Route::get('/lokasi', [LokasiApiController::class, 'index']);

// PROTECTED ROUTES (HARUS LOGIN)
Route::middleware('auth:sanctum')->group(function () {

    // PROFILE
    Route::get('/profile', [ProfileApiController::class, 'index']);
    Route::post('/profile/update', [ProfileApiController::class, 'update']);

    // AUTH USER INFO
    Route::get('/me', [AuthApiController::class, 'me']);
    Route::post('/logout', [AuthApiController::class, 'logout']);

    // PRESENSI
    Route::get('/presensi/create', [PresensiApiController::class, 'create']);
    Route::post('/presensi/store', [PresensiApiController::class, 'store']);
    Route::get('/presensi/histori', [PresensiApiController::class, 'histori']);
    Route::get('/presensi/settings', [PresensiApiController::class, 'settings']);

    // IZIN
    Route::get('/izin', [IzinApiController::class, 'index']);
    Route::post('/izin/store', [IzinApiController::class, 'store']);
});
