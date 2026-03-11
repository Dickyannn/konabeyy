<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Master\MasterDashboardController;
use App\Http\Controllers\PersonalAdmin\PersonalAdminController;
use App\Http\Controllers\ProfileController;
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
    // Dashboard - fallback untuk roles lainnya
    Route::get('/dashboard', function () {
        return redirect('/master/dashboard');
    })->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
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
    Route::put('/car-allowance/{carAllowance}', [MasterDashboardController::class, 'carAllowanceUpdate'])->name('car-allowance.update');
    Route::delete('/car-allowance/{carAllowance}', [MasterDashboardController::class, 'carAllowanceDestroy'])->name('car-allowance.destroy');

    // Parameter BPJS
    Route::put('/parameter-bpjs/{parameterBpjs}', [MasterDashboardController::class, 'parameterBpjsUpdate'])->name('parameter-bpjs.update');

    // Komponen Gaji
    Route::post('/komponen-gaji', [MasterDashboardController::class, 'komponenGajiStore'])->name('komponen-gaji.store');
    Route::put('/komponen-gaji/{komponenGaji}', [MasterDashboardController::class, 'komponenGajiUpdate'])->name('komponen-gaji.update');
    Route::delete('/komponen-gaji/{komponenGaji}', [MasterDashboardController::class, 'komponenGajiDestroy'])->name('komponen-gaji.destroy');

    // Unit / PT
    Route::post('/unit-pt', [MasterDashboardController::class, 'unitPtStore'])->name('unit-pt.store');
    Route::put('/unit-pt/{unitPt}', [MasterDashboardController::class, 'unitPtUpdate'])->name('unit-pt.update');
    Route::delete('/unit-pt/{unitPt}', [MasterDashboardController::class, 'unitPtDestroy'])->name('unit-pt.destroy');

    // Users
    Route::post('/users', [MasterDashboardController::class, 'usersStore'])->name('users.store');
    Route::put('/users/{user}', [MasterDashboardController::class, 'usersUpdate'])->name('users.update');
    Route::delete('/users/{user}', [MasterDashboardController::class, 'usersDestroy'])->name('users.destroy');
    Route::put('/users/{user}/toggle-active', [MasterDashboardController::class, 'usersToggleActive'])->name('users.toggle-active');
});

/**
 * ── PERSONAL ADMIN ROUTES ────────────────────────────
 * HR Administration & Employee Management
 */
Route::middleware(['auth', 'role:personal_admin'])->prefix('personal-admin')->name('personal-admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [PersonalAdminController::class, 'index'])->name('dashboard');

    // Data Karyawan
    Route::get('/karyawan', [PersonalAdminController::class, 'karyawanIndex'])->name('karyawan.index');
    Route::get('/karyawan/{karyawan}', [PersonalAdminController::class, 'karyawanShow'])->name('karyawan.show');
    Route::post('/karyawan', [PersonalAdminController::class, 'karyawanStore'])->name('karyawan.store');
    Route::put('/karyawan/{karyawan}', [PersonalAdminController::class, 'karyawanUpdate'])->name('karyawan.update');
    Route::delete('/karyawan/{karyawan}', [PersonalAdminController::class, 'karyawanDestroy'])->name('karyawan.destroy');

    // Struktur Organisasi / Posisi
    Route::get('/posisi/{posisi}', [PersonalAdminController::class, 'posisiShow'])->name('posisi.show');
    Route::post('/posisi', [PersonalAdminController::class, 'posisiStore'])->name('posisi.store');
    Route::put('/posisi/{posisi}', [PersonalAdminController::class, 'posisiUpdate'])->name('posisi.update');
    Route::delete('/posisi/{posisi}', [PersonalAdminController::class, 'posisiDestroy'])->name('posisi.destroy');

    // Fasilitas Kendaraan
    Route::get('/kendaraan/{kendaraan}', [PersonalAdminController::class, 'kendaraanShow'])->name('kendaraan.show');
    Route::post('/kendaraan', [PersonalAdminController::class, 'kendaraanStore'])->name('kendaraan.store');
    Route::put('/kendaraan/{kendaraan}', [PersonalAdminController::class, 'kendaraanUpdate'])->name('kendaraan.update');
    Route::delete('/kendaraan/{kendaraan}', [PersonalAdminController::class, 'kendaraanDestroy'])->name('kendaraan.destroy');

    // Riwayat Jabatan
    Route::get('/riwayat/{riwayat}', [PersonalAdminController::class, 'riwayatShow'])->name('riwayat.show');
    Route::post('/riwayat', [PersonalAdminController::class, 'riwayatStore'])->name('riwayat.store');
    Route::put('/riwayat/{riwayat}', [PersonalAdminController::class, 'riwayatUpdate'])->name('riwayat.update');
    Route::delete('/riwayat/{riwayat}', [PersonalAdminController::class, 'riwayatDestroy'])->name('riwayat.destroy');
});

