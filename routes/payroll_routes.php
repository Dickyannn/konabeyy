<?php
// FILE: routes/payroll_routes.php
// Tambahkan isi route group ini ke dalam routes/web.php

use App\Http\Controllers\Payroll\PayrollController;

Route::middleware(['auth', 'role:payroll,master_system'])
    ->prefix('payroll')
    ->name('payroll.')
    ->group(function () {

    // Dashboard utama
    Route::get('/dashboard', [PayrollController::class, 'index'])->name('dashboard');

    // Penggajian — semua modal ada di dashboard, tidak perlu GET terpisah
    Route::post('/penggajian/proses',   [PayrollController::class, 'penggajianProses'])  ->name('penggajian.proses');
    Route::post('/penggajian/approve',  [PayrollController::class, 'penggajianApprove']) ->name('penggajian.approve');

    // THR
    Route::post  ('/thr/hitung',       [PayrollController::class, 'thrHitung'])  ->name('thr.hitung');
    Route::post  ('/thr/{thr}/bayar',  [PayrollController::class, 'thrBayar'])   ->name('thr.bayar');
    Route::delete('/thr/{thr}',        [PayrollController::class, 'thrDestroy']) ->name('thr.destroy');

    // Insentif
    Route::post  ('/insentif',           [PayrollController::class, 'insentifStore'])   ->name('insentif.store');
    Route::put   ('/insentif/{insentif}',[PayrollController::class, 'insentifUpdate'])  ->name('insentif.update');
    Route::delete('/insentif/{insentif}',[PayrollController::class, 'insentifDestroy']) ->name('insentif.destroy');

    // BPJS Ketenagakerjaan
    Route::post  ('/bpjs-tk/generate',  [PayrollController::class, 'bpjsTkGenerate']) ->name('bpjs-tk.generate');
    Route::delete('/bpjs-tk/{bpjsTk}',  [PayrollController::class, 'bpjsTkDestroy'])  ->name('bpjs-tk.destroy');

    // BPJS Kesehatan
    Route::post  ('/bpjs-kes/generate',         [PayrollController::class, 'bpjsKesGenerate']) ->name('bpjs-kes.generate');
    Route::delete('/bpjs-kes/{bpjsKesehatan}',  [PayrollController::class, 'bpjsKesDestroy'])  ->name('bpjs-kes.destroy');
});
