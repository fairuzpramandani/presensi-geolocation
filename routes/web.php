<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\KonfigurasiController;


// LOGIN & REGISTER KARYAWAN
Route::middleware(['guest:karyawan'])->group(function(){
    Route::get('/', [AuthController::class, 'showLoginKaryawan'])->name('login');
    Route::post('/proseslogin', [AuthController::class, 'proseslogin']);
    Route::get('/register', [AuthController::class, 'showRegisterPage']);
    Route::post('/prosesregister', [AuthController::class, 'prosesRegisterKaryawan']);
});

// ROUTE KARYAWAN
Route::middleware(['auth:karyawan'])->group(function(){
    Route::post('/proseslogout', [AuthController::class, 'proseslogout'])->name('karyawan.logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth:karyawan');
    Route::get('/settings', [PresensiController::class, 'settings']);

    //Presensi
    Route::get('/presensi/create', [PresensiController::class, 'create']);
    Route::post('/presensi/store', [PresensiController::class, 'store'])->name('presensi.store')->middleware('auth:karyawan');
    Route::post('/presensi/cekpengajuanizin', [PresensiController::class, 'cekPengajuanIzin']);

    //Edit Profile
    Route::get('/editprofile', [PresensiController::class,'editprofile']);
    Route::post('/presensi/{email}/updateprofile', [PresensiController::class, 'updateprofile'])->name('profile.update.web')->where('email', '.*');

    //Histori
    Route::get('/presensi/histori', [PresensiController::class,'histori']);
    Route::post('/gethistori', [PresensiController::class,'gethistori']);

    //Izin
    Route::get('/presensi/izin', [PresensiController::class, 'izin']);
    Route::get('/presensi/buatizin', [PresensiController::class, 'buatizin']);
    Route::post('/presensi/storeizin', [PresensiController::class, 'storeizin']);


});

    // LUPA PASSWORD KARYAWAN
    Route::get('/ubah-password-cepat', [AuthController::class, 'showDirectResetForm'])->name('password.direct.show');
    Route::post('/ubah-password-cepat', [AuthController::class, 'directResetPassword'])->name('password.direct.update');

    //Departemen Flutter
    Route::get('/departemen-list', [DepartemenController::class, 'listDepartemenJson']);

    //Jam Kerja Flutter
    Route::get('/jam-kerja-list', [KonfigurasiController::class, 'listJamKerjaJson']);

    //Profile Flutter
    Route::get('/getprofile/{email}', [PresensiController::class, 'getprofile'])->where('email', '.*');
    Route::post('/api/profile/update/{email}', [PresensiController::class, 'updateprofile'])->where('email', '.*');

    //Lokasi Flutter
    Route::get('/api/konfigurasi-lokasi', [PresensiController::class, 'getLokasiKantor']);

    //Izin Sakit
    Route::get('/api/presensi/izin', [PresensiController::class, 'getizin']);
    Route::post('/api/presensi/storeizin', [PresensiController::class, 'storeizin']);

    //Histori
    Route::post('/api/presensi/histori', [PresensiController::class, 'getHistoriApi']);

Route::middleware(['guest:user'])->group(function(){
    Route::get('/panel', function () { return view('auth.loginadmin'); })->name('loginadmin');
    Route::post('/prosesloginadmin', [AuthController::class, 'prosesloginadmin']);
    Route::post('/prosesregisteradmin', [AuthController::class, 'registerAdmin']);
});

// ROUTE ADMIN (Harus Login)
Route::middleware(['auth:user'])->group(function () {

    Route::get('/proseslogoutadmin',[AuthController::class, 'proseslogoutadmin']);
    Route::post('/proseslogoutadmin', [AuthController::class, 'proseslogoutadmin'])->name('admin.logout');
    Route::get('/panel/dashboardadmin',[DashboardController::class, 'dashboardadmin']);

    //Karyawan
    Route::get('/karyawan',[KaryawanController::class, 'index']);
    Route::post('/karyawan/store',[KaryawanController::class, 'store']);
    Route::post('/karyawan/edit', [KaryawanController::class, 'edit']);
    Route::post('/karyawan/{email}/update', [KaryawanController::class, 'update']);
    Route::post('/karyawan/{email}/delete', [KaryawanController::class, 'delete']);

    //Departemen
    Route::get('/departemen', [DepartemenController::class, 'index']);
    Route::post('/departemen/store', [DepartemenController::class, 'store']);
    Route::post('/departemen/edit', [DepartemenController::class, 'edit']);
    Route::post('/departemen/{kode_dept}/update', [DepartemenController::class, 'update']);
    Route::post('/departemen/{kode_dept}/delete', [DepartemenController::class, 'delete']);


    //Presensi
    Route::get('/presensi/monitoring', [PresensiController::class, 'monitoring']);
    Route::post('/getpresensi', [PresensiController::class, 'getpresensi']);
    Route::post('/tampilkanpeta', [PresensiController::class, 'tampilkanpeta']);
    Route::get('/presensi/laporan', [PresensiController::class, 'laporan']);
    Route::post('/presensi/cetaklaporan', [PresensiController::class, 'cetaklaporan']);
    Route::get('/presensi/rekap', [PresensiController::class, 'rekap']);
    Route::post('/presensi/cetakrekap', [PresensiController::class, 'cetakrekap']);
    Route::get('/presensi/izinsakit', [PresensiController::class, 'izinsakit']);
    Route::post('/presensi/aprrovedizinsakit', [PresensiController::class, 'approvedizinsakit']);
    Route::get('/presensi/{id}/batalkanizinsakit', [PresensiController::class, 'batalkanizinsakit']);

    //Konfigurasi
    Route::get('/konfigurasi/lokasikantor', [KonfigurasiController::class, 'lokasikantor']);
    Route::post('/konfigurasi/storelokasikantor', [KonfigurasiController::class, 'storeLokasiKantor']);
    Route::get('/konfigurasi/{id}/editlokasi', [KonfigurasiController::class, 'editLokasi']);
    Route::put('/konfigurasi/{id}/updatelokasikantor', [KonfigurasiController::class, 'updateLokasiKantor']);
    Route::delete('/konfigurasi/{id}/deletelokasi', [KonfigurasiController::class, 'deleteLokasi']);
    Route::post('/konfigurasi/updatelokasikantor', [KonfigurasiController::class, 'updateLokasiKantor']);
    Route::get('/konfigurasi/jamkerja', [KonfigurasiController::class, 'jamkerja']);
    Route::post('/konfigurasi/storejamkerja', [KonfigurasiController::class, 'storejamkerja']);
    Route::post('/konfigurasi/editjamkerja', [KonfigurasiController::class, 'editjamkerja']);
    Route::post('/konfigurasi/updatejamkerja', [KonfigurasiController::class, 'updatejamkerja']);
    Route::post('/konfigurasi/{kode_jam_kerja}/delete', [KonfigurasiController::class, 'deletejamkerja']);
});
