<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Master\MasterDashboardController;
use App\Http\Controllers\PersonalAdmin\PersonalAdminController;
use App\Http\Controllers\Personalia\PersonaliaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/**
 * ── PUBLIC ROUTES ────────────────────────────────────
 * Akses tanpa login
 */

Route::get('/', function () {
    if (Auth::check()) {
        // Jika sudah login, redirect sesuai role
        $user = Auth::user();
        $user->load('role');
        $roleCode = $user->role->kode_role ?? null;
        
        switch ($roleCode) {
            case 'master_system':
                return redirect()->route('master.dashboard');
            case 'personal_admin':
                return redirect()->route('personal-admin.dashboard');
            case 'payroll':
                return redirect()->route('payroll.dashboard');
            case 'personalia':
                return redirect()->route('personalia.dashboard');
            default:
                return redirect()->route('login');
        }
    }
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
    // Dashboard - fallback redirect sesuai role
    Route::get('/dashboard', function () {
        $user = Auth::user();
        $user->load('role');
        $roleCode = $user->role->kode_role ?? null;
        
        switch ($roleCode) {
            case 'master_system':
                return redirect()->route('master.dashboard');
            case 'personal_admin':
                return redirect()->route('personal-admin.dashboard');
            case 'payroll':
                return redirect()->route('payroll.dashboard');
            case 'personalia':
                return redirect()->route('personalia.dashboard');
            default:
                return redirect()->route('login');
        }
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
    Route::get('/karyawan/{karyawan}/view', [PersonalAdminController::class, 'karyawanView'])->name('karyawan.view');
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
    // Riwayat Jabatan
    Route::get('/riwayat/karyawan/{karyawan}/data', [PersonalAdminController::class, 'riwayatGetKaryawanData'])->name('riwayat.karyawan.data'); // ← TAMBAH INI PALING ATAS
    Route::get('/riwayat/{riwayat}/view', [PersonalAdminController::class, 'riwayatShow'])->name('riwayat.view');  // ← TAMBAH INI
    Route::get('/riwayat/{riwayat}', [PersonalAdminController::class, 'riwayatShow'])->name('riwayat.show');
    Route::post('/riwayat', [PersonalAdminController::class, 'riwayatStore'])->name('riwayat.store');
    Route::put('/riwayat/{riwayat}', [PersonalAdminController::class, 'riwayatUpdate'])->name('riwayat.update');
    Route::delete('/riwayat/{riwayat}', [PersonalAdminController::class, 'riwayatDestroy'])->name('riwayat.destroy');
    
    
    // Get employee current data for riwayat form
    Route::get('/employee-current/{karyawan}', [PersonalAdminController::class, 'getEmployeeCurrentData'])->name('employee.current');

    // Data Keluarga
    Route::get('/keluarga', [PersonalAdminController::class, 'keluargaIndex'])->name('keluarga.index');
    Route::get('/keluarga/{karyawan}/view', [PersonalAdminController::class, 'keluargaView'])->name('keluarga.view');
    Route::get('/keluarga/{karyawan}/edit', [PersonalAdminController::class, 'keluargaEdit'])->name('keluarga.edit');
    Route::post('/keluarga/{karyawan}/update', [PersonalAdminController::class, 'keluargaUpdate'])->name('keluarga.update');
    Route::post('/keluarga', [PersonalAdminController::class, 'keluargaStore'])->name('keluarga.store');
    Route::delete('/keluarga/{anggotaKeluarga}', [PersonalAdminController::class, 'keluargaDestroy'])->name('keluarga.destroy');
});


/**
 * ── PAYROLL ROUTES ───────────────────────────────────
 * Payroll & BPJS Management
 */
Route::middleware(['auth', 'role:payroll'])->prefix('payroll')->name('payroll.')->group(function () {
    // Dashboard utama
    Route::get('/dashboard', [\App\Http\Controllers\Payroll\PayrollController::class, 'index'])->name('dashboard');

    // Penggajian
    Route::post('/penggajian/proses',   [\App\Http\Controllers\Payroll\PayrollController::class, 'penggajianProses'])  ->name('penggajian.proses');
    Route::post('/penggajian/approve',  [\App\Http\Controllers\Payroll\PayrollController::class, 'penggajianApprove']) ->name('penggajian.approve');
    Route::post('/penggajian/reject',   [\App\Http\Controllers\Payroll\PayrollController::class, 'penggajianReject'])  ->name('penggajian.reject');

    // THR
    Route::post  ('/thr/hitung',       [\App\Http\Controllers\Payroll\PayrollController::class, 'thrHitung'])  ->name('thr.hitung');
    Route::post  ('/thr/{thr}/bayar',  [\App\Http\Controllers\Payroll\PayrollController::class, 'thrBayar'])   ->name('thr.bayar');
    Route::delete('/thr/{thr}',        [\App\Http\Controllers\Payroll\PayrollController::class, 'thrDestroy']) ->name('thr.destroy');

    // Insentif
    Route::post  ('/insentif',           [\App\Http\Controllers\Payroll\PayrollController::class, 'insentifStore'])   ->name('insentif.store');
    Route::put   ('/insentif/{insentif}',[\App\Http\Controllers\Payroll\PayrollController::class, 'insentifUpdate'])  ->name('insentif.update');
    Route::delete('/insentif/{insentif}',[\App\Http\Controllers\Payroll\PayrollController::class, 'insentifDestroy']) ->name('insentif.destroy');

    // BPJS Ketenagakerjaan
    Route::post  ('/bpjs-tk/generate',  [\App\Http\Controllers\Payroll\PayrollController::class, 'bpjsTkGenerate']) ->name('bpjs-tk.generate');
    Route::delete('/bpjs-tk/{bpjsTk}',  [\App\Http\Controllers\Payroll\PayrollController::class, 'bpjsTkDestroy'])  ->name('bpjs-tk.destroy');

    // BPJS Kesehatan
    Route::post  ('/bpjs-kes/generate',         [\App\Http\Controllers\Payroll\PayrollController::class, 'bpjsKesGenerate']) ->name('bpjs-kes.generate');
    Route::delete('/bpjs-kes/{bpjsKesehatan}',  [\App\Http\Controllers\Payroll\PayrollController::class, 'bpjsKesDestroy'])  ->name('bpjs-kes.destroy');
});

/**
 * ── PERSONALIA ROUTES ────────────────────────────────
 * HR Personnel & Attendance Management
 */
Route::middleware(['auth', 'role:personalia,master_system'])->prefix('personalia')->name('personalia.')->group(function () {
    // Dashboard
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
