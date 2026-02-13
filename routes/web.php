<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Kepegawaian\SebaranPegawaiController;
use App\Http\Controllers\Kepegawaian\KenaikanGradingController;
use App\Http\Controllers\Kepegawaian\ProyeksiMutasiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

// Authenticated routes
Route::middleware(['auth', 'has.role'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Profile
    Route::get('/profile', function() {
        return view('profile.index');
    })->name('profile');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Logout
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Kepegawaian - Sebaran Pegawai
    Route::prefix('kepegawaian')->name('kepegawaian.')->group(function () {
        Route::get('sebaran', [SebaranPegawaiController::class, 'index'])->name('sebaran');
        Route::get('sebaran/{pegawai}', [SebaranPegawaiController::class, 'show'])->name('sebaran.show');

        // Kenaikan Grading
        Route::get('grading', [KenaikanGradingController::class, 'index'])->name('grading');
        Route::get('grading/{pegawai}', [KenaikanGradingController::class, 'show'])->name('grading.show');

        // Proyeksi Mutasi
        Route::get('mutasi', [ProyeksiMutasiController::class, 'index'])->name('mutasi');
        Route::get('mutasi/{pegawai}', [ProyeksiMutasiController::class, 'show'])->name('mutasi.show');
    });

    // User Management (Admin & Superadmin only)
    Route::middleware('role:superadmin,admin')->group(function () {
        Route::resource('users', UserManagementController::class);
    });
});
