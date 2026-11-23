<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JamKerjaController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\GeoSettingController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ManajerController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\AbsensiKaryawanController;
use App\Http\Controllers\IzinKaryawanController;

// Route Tamu (Belum Login)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Profil Umum (Bisa diakses Admin, Manager, maupun Karyawan)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Route khusus Admin
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // Manajemen Pengguna
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');

    // Kelola Jam Kerja
    Route::get('/jam-kerja', [JamKerjaController::class, 'index'])->name('jam-kerja.index');
    Route::post('/jam-kerja', [JamKerjaController::class, 'store'])->name('jam-kerja.store');
    Route::put('/jam-kerja/{jamKerja}', [JamKerjaController::class, 'update'])->name('jam-kerja.update');
    Route::delete('/jam-kerja/{jamKerja}', [JamKerjaController::class, 'destroy'])->name('jam-kerja.destroy');

    // Generator QR Code
    Route::get('/qr-code', [QrCodeController::class, 'index'])->name('qr.index');
    Route::post('/qr-code/generate', [QrCodeController::class, 'generate'])->name('qr.generate');
    Route::get('/qr-code/display', [QrCodeController::class, 'display'])->name('qr.display');

    // Geo-fencing & Setting Lokasi Kantor
    Route::get('/settings', [GeoSettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [GeoSettingController::class, 'update'])->name('settings.update');

    // Laporan & Ekspor Kehadiran
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export-csv', [LaporanController::class, 'exportCsv'])->name('laporan.export');
    Route::get('/laporan/print', [LaporanController::class, 'printPdf'])->name('laporan.print');
    Route::put('/laporan/{absensi}/update-status', [LaporanController::class, 'updateStatus'])->name('laporan.updateStatus');
});

// Route khusus Manajer
Route::middleware(['auth', 'role:Manajer'])->prefix('manajer')->name('manajer.')->group(function () {
    Route::get('/dashboard', [ManajerController::class, 'dashboard'])->name('dashboard');
    Route::get('/laporan', [ManajerController::class, 'laporan'])->name('laporan');
    
    // Menu Approval Perizinan
    Route::get('/perizinan', [ManajerController::class, 'perizinan'])->name('perizinan');
    Route::put('/perizinan/{izin}', [ManajerController::class, 'updateApproval'])->name('perizinan.update');
});

// Route khusus Karyawan
Route::middleware(['auth'])->prefix('karyawan')->name('karyawan.')->group(function () {
    Route::get('/dashboard', [KaryawanController::class, 'dashboard'])->name('dashboard');
    
    // Absensi (Scan QR & GPS)
    Route::get('/absensi', [AbsensiKaryawanController::class, 'index'])->name('absensi.index');
    Route::post('/absensi/store', [AbsensiKaryawanController::class, 'store'])->name('absensi.store');

    // Pengajuan Izin / Cuti
    Route::get('/izin', [IzinKaryawanController::class, 'index'])->name('izin.index');
    Route::post('/izin', [IzinKaryawanController::class, 'store'])->name('izin.store');
});