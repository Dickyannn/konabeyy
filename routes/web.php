<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master\MasterDashboardController;
use Illuminate\Support\Facades\Route;

/**
 * ── PUBLIC ROUTES ────────────────────────────────────
 * Akses tanpa login
 */

Route::get('/', function () {
    return redirect('/login');
})->name('home');

/**
 * ── AUTH ROUTES ──────────────────────────────────────
 * Login, Logout, Password Reset
 */
Route::middleware('guest')->group(function () {
    // Show login form
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    // Handle login submission
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // TODO: Password reset routes
    // Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    // Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    // Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    // Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

/**
 * ── PROTECTED ROUTES ─────────────────────────────────
 * Hanya untuk user yang sudah login
 */
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // TODO: Other protected routes
    // Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
});

/**
 * ── MASTER SYSTEM ROUTES ──────────────────────────────
 * Admin master configuration (Master System role only)
 */
Route::middleware(['auth', 'role:master_system'])->prefix('master')->name('master.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [MasterDashboardController::class, 'index'])->name('dashboard');

    // Golongan
    Route::post('/golongan', [MasterDashboardController::class, 'golonganStore'])->name('golongan.store');
    Route::put('/golongan/{golongan}', [MasterDashboardController::class, 'golonganUpdate'])->name('golongan.update');
    Route::delete('/golongan/{golongan}', [MasterDashboardController::class, 'golonganDestroy'])->name('golongan.destroy');

    // Car Allowance
    Route::post('/car-allowance', [MasterDashboardController::class, 'carAllowanceStore'])->name('car-allowance.store');
    Route::delete('/car-allowance/{carAllowance}', [MasterDashboardController::class, 'carAllowanceDestroy'])->name('car-allowance.destroy');

    // Parameter BPJS
    Route::put('/parameter-bpjs/{parameterBpjs}', [MasterDashboardController::class, 'parameterBpjsUpdate'])->name('parameter-bpjs.update');

    // Komponen Gaji
    Route::post('/komponen-gaji', [MasterDashboardController::class, 'komponenGajiStore'])->name('komponen-gaji.store');

    // Unit / PT
    Route::post('/unit-pt', [MasterDashboardController::class, 'unitPtStore'])->name('unit-pt.store');
    Route::delete('/unit-pt/{unitPt}', [MasterDashboardController::class, 'unitPtDestroy'])->name('unit-pt.destroy');

    // Users
    Route::post('/users', [MasterDashboardController::class, 'usersStore'])->name('users.store');
    Route::put('/users/{user}', [MasterDashboardController::class, 'usersUpdate'])->name('users.update');
    Route::delete('/users/{user}', [MasterDashboardController::class, 'usersDestroy'])->name('users.destroy');
    Route::put('/users/{user}/toggle-active', [MasterDashboardController::class, 'usersToggleActive'])->name('users.toggle-active');
});

