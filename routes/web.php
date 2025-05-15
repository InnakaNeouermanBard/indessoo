<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\FormLemburController;
use App\Http\Controllers\JadwalKerjaController;
use App\Http\Controllers\AuthKaryawanController;
use App\Http\Controllers\LokasiKantorController;
use App\Http\Controllers\ShiftScheduleController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

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

    // routes/web.php
    Route::get('/tukar-jadwal/riwayat', [PresensiController::class, 'riwayatTukarJadwal'])->name('tukar-jadwal.riwayat');
    Route::get('/tukar-jadwal/detail/{id}', [PresensiController::class, 'detailTukarJadwal'])->name('tukar-jadwal.detail');
    Route::get('/tukar-jadwal/terima/{id}', [PresensiController::class, 'terimaAjuanTukarJadwal'])->name('tukar-jadwal.terima');
    Route::get('/tukar-jadwal/tolak/{id}', [PresensiController::class, 'tolakAjuanTukarJadwal'])->name('tukar-jadwal.tolak');







    // Update kuota cuti karyawan
    Route::put('/admin/kuota-cuti/{nik}', [PresensiController::class, 'updateKuotaCuti'])->name('admin.kuota-cuti.update');
    Route::get('/admin/kuota-cuti', [PresensiController::class, 'manajemenKuotaCuti'])->name('admin.kuota-cuti');

    Route::get('/form-lembur', [FormLemburController::class, 'index'])->name('form-lembur.index');
    Route::post('/form-lembur', [FormLemburController::class, 'store'])->name('form-lembur.store');
    Route::get('/form-lembur/{id}', [FormLemburController::class, 'show'])->name('form-lembur.show');
    Route::get('/form-lembur/{id}/edit', [FormLemburController::class, 'edit'])->name('form-lembur.edit');
    Route::put('/form-lembur/{id}', [FormLemburController::class, 'update'])->name('form-lembur.update');
    Route::post('/form-lembur/delete', [FormLemburController::class, 'destroy'])->name('form-lembur.delete');
    Route::get('/form-lembur/get-karyawan-data/{nik}', [FormLemburController::class, 'getKaryawanData'])->name('form-lembur.getKaryawanData');

    Route::get('jadwal-kerja', [JadwalKerjaController::class, 'index'])
        ->name('jadwalkerja.index');
    Route::post('jadwal-kerja/import', [JadwalKerjaController::class, 'import'])
        ->name('jadwalkerja.import');
    Route::get('jadwal-kerja/download/{id}', [JadwalKerjaController::class, 'download'])
        ->name('jadwalkerja.download');
    Route::delete('jadwal-kerja/{id}', [JadwalKerjaController::class, 'destroy'])
        ->name('jadwalkerja.destroy');

    // 1. Rute Index
    Route::get('jadwal-shift', [App\Http\Controllers\ShiftScheduleController::class, 'index'])
        ->name('jadwal-shift.index');

    // 2. Rute Create/Store biasa (tanpa parameter di URL)
    Route::get('jadwal-shift/create', [App\Http\Controllers\ShiftScheduleController::class, 'create'])
        ->name('jadwal-shift.create');
    Route::post('jadwal-shift', [App\Http\Controllers\ShiftScheduleController::class, 'store'])
        ->name('jadwal-shift.store');

    // 3. Rute untuk jadwal massal dan fitur khusus lainnya (tanpa parameter di URL)
    Route::get('jadwal-shift/create-massal', [App\Http\Controllers\ShiftScheduleController::class, 'createMassal'])
        ->name('jadwal-shift.create-massal');
    Route::post('jadwal-shift/store-massal', [App\Http\Controllers\ShiftScheduleController::class, 'storeMassal'])
        ->name('jadwal-shift.store-massal');
    Route::get('jadwal-shift/get-day', [App\Http\Controllers\ShiftScheduleController::class, 'getDaySchedule'])
        ->name('jadwal-shift.get-day');
    Route::post('jadwal-shift/update-single-day', [App\Http\Controllers\ShiftScheduleController::class, 'updateSingleDay'])
        ->name('jadwal-shift.update-single-day');

    // 4. Rute dengan parameter HARUS ditempatkan PALING TERAKHIR
    Route::get('jadwal-shift/karyawan/{nik}', [App\Http\Controllers\ShiftScheduleController::class, 'karyawanDetail'])
        ->name('jadwal-shift.karyawan-detail');
    Route::get('jadwal-shift/{id}/edit', [App\Http\Controllers\ShiftScheduleController::class, 'edit'])
        ->name('jadwal-shift.edit');
    Route::put('jadwal-shift/{id}', [App\Http\Controllers\ShiftScheduleController::class, 'update'])
        ->name('jadwal-shift.update');
    Route::delete('jadwal-shift/{id}', [App\Http\Controllers\ShiftScheduleController::class, 'destroy'])
        ->name('jadwal-shift.destroy');
    Route::get('jadwal-shift/{id}', [App\Http\Controllers\ShiftScheduleController::class, 'show'])
        ->name('jadwal-shift.show');


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
    Route::post('/lokasi-kantor/toggle-status', [LokasiKantorController::class, 'toggleStatus'])->name('admin.lokasi-kantor.toggle-status');

    Route::get('/administrasi-presensi', [PresensiController::class, 'indexAdmin'])->name('admin.administrasi-presensi');
    Route::post('/administrasi-presensi/status', [PresensiController::class, 'persetujuanPresensi'])->name('admin.administrasi-presensi.persetujuan');
});

// Routing untuk karyawan (guard karyawan)
Route::middleware(['auth:karyawan'])->prefix('karyawan')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('karyawan.dashboard');

    // Ganti resource route dengan route custom untuk form-lembur
    Route::prefix('form-lembur')->group(function () {
        Route::get('/', [FormLemburController::class, 'karyawanIndex'])->name('karyawan.form-lembur.index');
        Route::post('/', [FormLemburController::class, 'karyawanStore'])->name('karyawan.form-lembur.store');
    });
    Route::prefix('jadwal-kerja')->group(function () {
        Route::get('jadwal-kerja', [ShiftScheduleController::class, 'jadwalKaryawan'])
            ->name('karyawan.jadwalkerja.index');  // Menampilkan jadwal kazryawan dengan filter bulan dan tahun
        Route::get('jadwal-excel', [JadwalKerjaController::class, 'indexKaryawanExcel'])
            ->name('karyawan.jadwalkerja.indexExcel');
        Route::get('jadwal-kerja/download/{id}', [JadwalKerjaController::class, 'downloadKaryawan'])
            ->name('karyawan.jadwalkerja.download');
            // Route untuk karyawan melihat file Excel yang di-upload admin
        // Route untuk karyawan melihat file Excel yang di-upload admin
    });

    Route::prefix('presensi')->group(function () {
        Route::get('/', [PresensiController::class, 'index'])->name('karyawan.presensi');
        Route::post('/', [PresensiController::class, 'store'])->name('karyawan.presensi.store');
        // routes/web.php
        Route::post('/presensi/tukar-jadwal', [PresensiController::class, 'ajukanTukarJadwal'])->name('presensi.tukar-jadwal');


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

    Route::prefix('laporan')->group(function () {
        Route::get('/', [PresensiController::class, 'laporankaryawan'])->name('karyawan.laporan');
        Route::get('/laporan/presensi', [PresensiController::class, 'laporankaryawan'])->name('karyawan.laporan.presensi');
        Route::post('/laporan/presensi/karyawan', [PresensiController::class, 'laporanPresensiKaryawanKaryawan'])->name('karyawan.laporan.presensi.karyawan');
        Route::post('/laporan/presensi/semua-karyawan', [PresensiController::class, 'laporanPresensiSemuaKaryawanKaryawan'])->name('karyawan.laporan.presensi.semua-karyawan');
    });
});

require __DIR__ . '/auth.php';
