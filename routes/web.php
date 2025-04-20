<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AuthKaryawanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\LokasiKantorController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

// Route untuk login
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// Route untuk logout (menggabungkan kedua guard)
Route::middleware('auth:web,karyawan')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Route untuk profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routing untuk admin (guard web)
Route::middleware(['auth:web'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'indexAdmin'])->name('admin.dashboard');

    Route::get('/karyawan', [KaryawanController::class, 'indexAdmin'])->name('admin.karyawan');
    Route::post('/karyawan/tambah', [KaryawanController::class, 'store'])->name('admin.karyawan.store');
    Route::get('/karyawan/perbarui', [KaryawanController::class, 'edit'])->name('admin.karyawan.edit');
    Route::post('/karyawan/perbarui', [KaryawanController::class, 'updateAdmin'])->name('admin.karyawan.update');
    Route::post('/karyawan/hapus', [KaryawanController::class, 'delete'])->name('admin.karyawan.delete');

    Route::get('/admin-management', [AdminController::class, 'index'])->name('admin-management');
    Route::post('/admin-management/store', [AdminController::class, 'store'])->name('admin-management.store');
    Route::get('/admin-management/edit', [AdminController::class, 'edit'])->name('admin-management.edit');
    Route::post('/admin-management/update', [AdminController::class, 'update'])->name('admin-management.update');
    Route::post('/admin-management/delete', [AdminController::class, 'delete'])->name('admin-management.delete');

    Route::get('/departemen', [DepartemenController::class, 'index'])->name('admin.departemen');
    Route::post('/departemen/tambah', [DepartemenController::class, 'store'])->name('admin.departemen.store');
    Route::get('/departemen/perbarui', [DepartemenController::class, 'edit'])->name('admin.departemen.edit');
    Route::post('/departemen/perbarui', [DepartemenController::class, 'update'])->name('admin.departemen.update');
    Route::post('/departemen/hapus', [DepartemenController::class, 'delete'])->name('admin.departemen.delete');

    Route::get('/monitoring-presensi', [PresensiController::class, 'monitoringPresensi'])->name('admin.monitoring-presensi');
    Route::post('/monitoring-presensi', [PresensiController::class, 'viewLokasi'])->name('admin.monitoring-presensi.lokasi');

    Route::get('/laporan/presensi', [PresensiController::class, 'laporan'])->name('admin.laporan.presensi');
    Route::post('/laporan/presensi/karyawan', [PresensiController::class, 'laporanPresensiKaryawan'])->name('admin.laporan.presensi.karyawan');
    Route::post('/laporan/presensi/semua-karyawan', [PresensiController::class, 'laporanPresensiSemuaKaryawan'])->name('admin.laporan.presensi.semua-karyawan');

    Route::get('/lokasi', [LokasiKantorController::class, 'index'])->name('admin.lokasi-kantor');
    Route::post('/lokasi/tambah', [LokasiKantorController::class, 'store'])->name('admin.lokasi-kantor.store');
    Route::get('/lokasi/perbarui', [LokasiKantorController::class, 'edit'])->name('admin.lokasi-kantor.edit');
    Route::post('/lokasi/perbarui', [LokasiKantorController::class, 'update'])->name('admin.lokasi-kantor.update');
    Route::post('/lokasi/hapus', [LokasiKantorController::class, 'delete'])->name('admin.lokasi-kantor.delete');

    Route::get('/administrasi-presensi', [PresensiController::class, 'indexAdmin'])->name('admin.administrasi-presensi');
    Route::post('/administrasi-presensi/status', [PresensiController::class, 'persetujuanPresensi'])->name('admin.administrasi-presensi.persetujuan');
});

// Routing untuk karyawan (guard karyawan)
Route::middleware(['auth:karyawan'])->prefix('karyawan')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('karyawan.dashboard');

    Route::prefix('presensi')->group(function () {
        Route::get('/', [PresensiController::class, 'index'])->name('karyawan.presensi');
        Route::post('/', [PresensiController::class, 'store'])->name('karyawan.presensi.store');

        Route::prefix('history')->group(function () {
            Route::get('/', [PresensiController::class, 'history'])->name('karyawan.history');
            Route::post('/search-history', [PresensiController::class, 'searchHistory'])->name('karyawan.history.search');
        });

        Route::prefix('izin')->group(function () {
            Route::get('/', [PresensiController::class, 'pengajuanPresensi'])->name('karyawan.izin');
            Route::get('/pengajuan-presensi', [PresensiController::class, 'pengajuanPresensiCreate'])->name('karyawan.izin.create');
            Route::post('/pengajuan-presensi', [PresensiController::class, 'pengajuanPresensiStore'])->name('karyawan.izin.store');
            Route::post('/search-history', [PresensiController::class, 'searchPengajuanHistory'])->name('karyawan.izin.search');
        });
    });

    Route::prefix('profile')->group(function () {
        Route::get('/', [KaryawanController::class, 'index'])->name('karyawan.profile');
        Route::post('/update', [KaryawanController::class, 'update'])->name('karyawan.profile.update');
    });
});

require __DIR__ . '/auth.php';
