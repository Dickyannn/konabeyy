<?php
// ── routes/personal_admin_routes.php ──────────────────────
// Tambahkan isi ini ke routes/web.php

use App\Http\Controllers\PersonalAdmin\PersonalAdminController;

Route::middleware(['auth', 'role:personal_admin,master_system'])
    ->prefix('personal-admin')
    ->name('personal-admin.')
    ->group(function () {

    // Dashboard
    Route::get('/dashboard', [PersonalAdminController::class, 'index'])->name('dashboard');

    // Data Karyawan
    Route::get   ('/karyawan',           [PersonalAdminController::class, 'karyawanIndex'])  ->name('karyawan.index');
    Route::post  ('/karyawan',           [PersonalAdminController::class, 'karyawanStore'])  ->name('karyawan.store');
    Route::put   ('/karyawan/{karyawan}',[PersonalAdminController::class, 'karyawanUpdate']) ->name('karyawan.update');
    Route::delete('/karyawan/{karyawan}',[PersonalAdminController::class, 'karyawanDestroy'])->name('karyawan.destroy');

    // Kontrak Karyawan
    Route::post  ('/kontrak',           [PersonalAdminController::class, 'kontrakStore'])  ->name('kontrak.store');
    Route::put   ('/kontrak/{kontrak}', [PersonalAdminController::class, 'kontrakUpdate']) ->name('kontrak.update');
    Route::delete('/kontrak/{kontrak}', [PersonalAdminController::class, 'kontrakDestroy'])->name('kontrak.destroy');

    // Posisi / Struktur Organisasi
    Route::post  ('/posisi',          [PersonalAdminController::class, 'posisiStore'])  ->name('posisi.store');
    Route::delete('/posisi/{posisi}', [PersonalAdminController::class, 'posisiDestroy'])->name('posisi.destroy');

    // Fasilitas Kendaraan
    Route::post  ('/kendaraan',              [PersonalAdminController::class, 'kendaraanStore'])  ->name('kendaraan.store');
    Route::delete('/kendaraan/{fasilitas}',  [PersonalAdminController::class, 'kendaraanDestroy'])->name('kendaraan.destroy');

    // Data BPJS (View Only)
    Route::get('/bpjs', [PersonalAdminController::class, 'bpjsIndex'])->name('bpjs.index');

    // Riwayat Jabatan
    Route::post  ('/riwayat',           [PersonalAdminController::class, 'riwayatStore'])  ->name('riwayat.store');
    Route::put   ('/riwayat/{riwayat}', [PersonalAdminController::class, 'riwayatUpdate']) ->name('riwayat.update');
    Route::delete('/riwayat/{riwayat}', [PersonalAdminController::class, 'riwayatDestroy'])->name('riwayat.destroy');
});
