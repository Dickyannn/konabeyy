<?php
// FILE: routes/personalia_routes.php
// Tambahkan ke routes/web.php

use App\Http\Controllers\Personalia\PersonaliaController;

Route::middleware(['auth', 'role:personalia,master_system'])
    ->prefix('personalia')
    ->name('personalia.')
    ->group(function () {

    Route::get('/dashboard', [PersonaliaController::class, 'index'])->name('dashboard');

    // Absensi
    Route::post  ('/absensi',              [PersonaliaController::class, 'absensiStore'])  ->name('absensi.store');
    Route::put   ('/absensi/{attendance}', [PersonaliaController::class, 'absensiUpdate']) ->name('absensi.update');
    Route::delete('/absensi/{attendance}', [PersonaliaController::class, 'absensiDestroy'])->name('absensi.destroy');

    // Cuti & Izin
    Route::post  ('/cuti',                    [PersonaliaController::class, 'cutiStore'])  ->name('cuti.store');
    Route::put   ('/cuti/{cuti}/approve',     [PersonaliaController::class, 'cutiApprove'])->name('cuti.approve');
    Route::delete('/cuti/{cuti}',             [PersonaliaController::class, 'cutiDestroy'])->name('cuti.destroy');

    // Lembur
    Route::post  ('/lembur',                  [PersonaliaController::class, 'lemburStore'])  ->name('lembur.store');
    Route::put   ('/lembur/{lembur}/approve', [PersonaliaController::class, 'lemburApprove'])->name('lembur.approve');
    Route::delete('/lembur/{lembur}',         [PersonaliaController::class, 'lemburDestroy'])->name('lembur.destroy');
});
