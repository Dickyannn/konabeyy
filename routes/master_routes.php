<?php
// Tambahkan di dalam routes/web.php
// Di dalam group: Route::middleware(['auth', 'role:master_system'])->prefix('master')->name('master.')

use App\Http\Controllers\Master\MasterDashboardController;

Route::middleware(['auth', 'role:master_system'])->prefix('master')->name('master.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [MasterDashboardController::class, 'index'])->name('dashboard');

    // Golongan
    Route::post('/golongan',          [MasterDashboardController::class, 'golonganStore'])  ->name('golongan.store');
    Route::put('/golongan/{golongan}',[MasterDashboardController::class, 'golonganUpdate']) ->name('golongan.update');
    Route::delete('/golongan/{golongan}', [MasterDashboardController::class, 'golonganDestroy'])->name('golongan.destroy');

    // Car Allowance
    Route::post('/car-allowance',              [MasterDashboardController::class, 'carAllowanceStore'])  ->name('car-allowance.store');
    Route::delete('/car-allowance/{carAllowance}', [MasterDashboardController::class, 'carAllowanceDestroy'])->name('car-allowance.destroy');

    // Parameter BPJS
    Route::put('/parameter-bpjs/{parameterBpjs}', [MasterDashboardController::class, 'parameterBpjsUpdate'])->name('parameter-bpjs.update');

    // Komponen Gaji
    Route::post('/komponen-gaji', [MasterDashboardController::class, 'komponenGajiStore'])->name('komponen-gaji.store');

    // Unit / PT
    Route::post('/unit-pt',              [MasterDashboardController::class, 'unitPtStore'])  ->name('unit-pt.store');
    Route::delete('/unit-pt/{unitPt}',   [MasterDashboardController::class, 'unitPtDestroy'])->name('unit-pt.destroy');

    // User Management (dari UserManagementController yang sudah ada)
    Route::get('/users',                          [UserManagementController::class, 'index'])        ->name('users.index');
    Route::post('/users',                         [UserManagementController::class, 'store'])        ->name('users.store');
    Route::put('/users/{user}',                   [UserManagementController::class, 'update'])       ->name('users.update');
    Route::patch('/users/{user}/toggle-active',   [UserManagementController::class, 'toggleActive'])->name('users.toggle-active');
    Route::post('/users/{user}/reset-password',   [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
});
