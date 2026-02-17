<?php

use App\Http\Controllers\Anggaran\MonitoringAnggaranController;
use App\Http\Controllers\Anggaran\SPPController;
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
    Route::get('/profile', function () {
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

    // Anggaran
    Route::prefix('anggaran')->name('anggaran.')->middleware(['auth', 'has.role'])->group(function () {
        // Monitoring Anggaran
        Route::get('monitoring', [App\Http\Controllers\Anggaran\MonitoringAnggaranController::class, 'index'])
            ->name('monitoring.index');

        // SPP Management
        Route::resource('spp', App\Http\Controllers\Anggaran\SPPController::class);

        // AJAX routes untuk SPP - DIPERBAIKI
        Route::get('spp/ajax/subkomponen', [App\Http\Controllers\Anggaran\SPPController::class, 'getSubkomponen'])
            ->name('spp.ajax.subkomponen');
        Route::get('spp/ajax/akun', [App\Http\Controllers\Anggaran\SPPController::class, 'getAkun'])
            ->name('spp.ajax.akun');

        // Usulan Penarikan Dana
        Route::resource('usulan', App\Http\Controllers\Anggaran\UsulanPenarikanController::class);
        Route::get('usulan/ajax/subkomponen', [App\Http\Controllers\Anggaran\UsulanPenarikanController::class, 'getSubkomponen'])
            ->name('usulan.ajax.subkomponen');
        Route::post('usulan/{usulan}/approve', [App\Http\Controllers\Anggaran\UsulanPenarikanController::class, 'approve'])
            ->name('usulan.approve')
            ->middleware('role:superadmin,admin');
        Route::post('usulan/{usulan}/reject', [App\Http\Controllers\Anggaran\UsulanPenarikanController::class, 'reject'])
            ->name('usulan.reject')
            ->middleware('role:superadmin,admin');

        // Dokumen Capaian Output
        Route::resource('dokumen', App\Http\Controllers\Anggaran\DokumenCapaianController::class);
        Route::get('dokumen/ajax/subkomponen', [App\Http\Controllers\Anggaran\DokumenCapaianController::class, 'getSubkomponen'])
            ->name('dokumen.ajax.subkomponen');
        Route::get('dokumen/{dokumen}/download', [App\Http\Controllers\Anggaran\DokumenCapaianController::class, 'download'])
            ->name('dokumen.download');
        Route::get('dokumen/{dokumen}/download/{file}', [App\Http\Controllers\Anggaran\DokumenCapaianController::class, 'downloadSingle'])
            ->name('dokumen.download-single');

        // Revisi Anggaran
        Route::resource('revisi', App\Http\Controllers\Anggaran\RevisiAnggaranController::class);
        Route::get('revisi/{revisi}/download-dokumen', [App\Http\Controllers\Anggaran\RevisiAnggaranController::class, 'downloadDokumen'])
            ->name('revisi.download-dokumen');

        // Data Anggaran Management
        Route::resource('data', App\Http\Controllers\Anggaran\DataAnggaranController::class);

        // AJAX untuk Data Anggaran
        Route::get('data/ajax/subkomponen', [App\Http\Controllers\Anggaran\DataAnggaranController::class, 'getSubkomponen'])
            ->name('data.ajax.subkomponen');

        Route::get('data-import', [App\Http\Controllers\Anggaran\DataAnggaranController::class, 'importForm'])
            ->name('data.import-form');
        Route::post('data-import', [App\Http\Controllers\Anggaran\DataAnggaranController::class, 'import'])
            ->name('data.import');
        Route::get('data-export', [App\Http\Controllers\Anggaran\DataAnggaranController::class, 'export'])
            ->name('data.export');
    });

    // Inventaris
    Route::prefix('inventaris')->name('inventaris.')->group(function () {
        // Monitoring ATK
        Route::resource('monitoring-atk', App\Http\Controllers\Inventaris\MonitoringAtkController::class);

        // Route Export/Import untuk Monitoring ATK - TAMBAHKAN INI
        Route::get('monitoring-atk-export', [App\Http\Controllers\Inventaris\MonitoringAtkController::class, 'export'])
            ->name('monitoring-atk.export');
        Route::get('monitoring-atk-template', [App\Http\Controllers\Inventaris\MonitoringAtkController::class, 'downloadTemplate'])
            ->name('monitoring-atk.template');
        Route::get('monitoring-atk-import', [App\Http\Controllers\Inventaris\MonitoringAtkController::class, 'importForm'])
            ->name('monitoring-atk.import-form');
        Route::post('monitoring-atk-import', [App\Http\Controllers\Inventaris\MonitoringAtkController::class, 'import'])
            ->name('monitoring-atk.import');

        Route::post('monitoring-atk/{monitoringAtk}/update-stok', [App\Http\Controllers\Inventaris\MonitoringAtkController::class, 'updateStok'])
            ->name('monitoring-atk.update-stok');

        // Permintaan ATK
        Route::resource('permintaan-atk', App\Http\Controllers\Inventaris\PermintaanAtkController::class);
        Route::post('permintaan-atk/{permintaanAtk}/approve', [App\Http\Controllers\Inventaris\PermintaanAtkController::class, 'approve'])
            ->name('permintaan-atk.approve')
            ->middleware('role:superadmin,admin');
        Route::post('permintaan-atk/{permintaanAtk}/reject', [App\Http\Controllers\Inventaris\PermintaanAtkController::class, 'reject'])
            ->name('permintaan-atk.reject')
            ->middleware('role:superadmin,admin');
        Route::post('permintaan-atk/{permintaanAtk}/complete', [App\Http\Controllers\Inventaris\PermintaanAtkController::class, 'complete'])
            ->name('permintaan-atk.complete')
            ->middleware('role:superadmin,admin');

        // Aset End User
        Route::resource('aset-end-user', App\Http\Controllers\Inventaris\AsetEndUserController::class);

        // Route Export/Import untuk Aset End User - TAMBAHKAN INI
        Route::get('aset-end-user-export', [App\Http\Controllers\Inventaris\AsetEndUserController::class, 'export'])
            ->name('aset-end-user.export');
        Route::get('aset-end-user-template', [App\Http\Controllers\Inventaris\AsetEndUserController::class, 'downloadTemplate'])
            ->name('aset-end-user.template');
        Route::get('aset-end-user-import', [App\Http\Controllers\Inventaris\AsetEndUserController::class, 'importForm'])
            ->name('aset-end-user.import-form');
        Route::post('aset-end-user-import', [App\Http\Controllers\Inventaris\AsetEndUserController::class, 'import'])
            ->name('aset-end-user.import');

        Route::post('aset-end-user/{asetEndUser}/pinjam', [App\Http\Controllers\Inventaris\AsetEndUserController::class, 'pinjam'])
            ->name('aset-end-user.pinjam');
        Route::post('aset-end-user/{asetEndUser}/kembalikan', [App\Http\Controllers\Inventaris\AsetEndUserController::class, 'kembalikan'])
            ->name('aset-end-user.kembalikan');

        // Kategori ATK
        Route::resource('kategori-atk', App\Http\Controllers\Inventaris\KategoriAtkController::class);

        // Kategori Aset
        Route::resource('kategori-aset', App\Http\Controllers\Inventaris\KategoriAsetController::class);
    });

    // User Management (Admin & Superadmin only)
    Route::middleware('role:superadmin,admin')->group(function () {
        Route::resource('users', UserManagementController::class);
    });
});
