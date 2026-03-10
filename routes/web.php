<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/**
 * ── PUBLIC ROUTES ────────────────────────────────────
 * Akses tanpa login
 */

Route::get('/', function () {
    return view('welcome');
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


