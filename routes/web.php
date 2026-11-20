<?php

use App\Http\Controllers\Auth\KaderAuthController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ParentAuthController;
use App\Http\Controllers\Kader\BalitaController;
use App\Http\Controllers\Kader\DashboardController as KaderDashboardController;
use App\Http\Controllers\Kader\ExaminationController;
use App\Http\Controllers\Kader\GuardianController;
use App\Http\Controllers\Kader\ImmunizationController;
use App\Http\Controllers\Kader\ParentAccountController;
use App\Http\Controllers\Kader\ReportController;
use App\Http\Controllers\OrangTua\DashboardController as ParentDashboardController;
use App\Http\Controllers\OrangTua\GrowthController;
use App\Http\Controllers\OrangTua\HistoryController;
use App\Http\Controllers\OrangTua\ImmunizationHistoryController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->to(Auth::user()->homeRoute());
    }

    return redirect()->route('kader.login');
})->name('home');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/kader/masuk', [KaderAuthController::class, 'create'])->name('kader.login');
    Route::post('/kader/masuk', [KaderAuthController::class, 'store'])->name('kader.login.store');

    Route::get('/orang-tua/masuk', [ParentAuthController::class, 'create'])->name('orang-tua.login');
    Route::post('/orang-tua/masuk', [ParentAuthController::class, 'store'])->name('orang-tua.login.store');
});

Route::post('/keluar', LogoutController::class)
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Kader
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:kader'])->prefix('kader')->name('kader.')->group(function () {
    Route::get('/', KaderDashboardController::class)->name('dashboard');

    Route::get('/balita', [BalitaController::class, 'index'])->name('balita.index');
    Route::get('/balita/baru', [BalitaController::class, 'create'])->name('balita.create');
    Route::post('/balita', [BalitaController::class, 'store'])->name('balita.store');
    Route::get('/balita/{balita}', [BalitaController::class, 'show'])->name('balita.show');
    Route::get('/balita/{balita}/ubah', [BalitaController::class, 'edit'])->name('balita.edit');
    Route::put('/balita/{balita}', [BalitaController::class, 'update'])->name('balita.update');

    Route::get('/balita/{balita}/pemeriksaan/baru', [ExaminationController::class, 'create'])->name('pemeriksaan.create');
    Route::post('/balita/{balita}/pemeriksaan', [ExaminationController::class, 'store'])->name('pemeriksaan.store');
    Route::get('/pemeriksaan/{examination}/berhasil', [ExaminationController::class, 'success'])->name('pemeriksaan.success');
    Route::get('/pemeriksaan/{examination}/ubah', [ExaminationController::class, 'edit'])->name('pemeriksaan.edit');
    Route::put('/pemeriksaan/{examination}', [ExaminationController::class, 'update'])->name('pemeriksaan.update');

    Route::post('/balita/{balita}/imunisasi', [ImmunizationController::class, 'store'])->name('imunisasi.store');
    Route::get('/imunisasi/{immunization}/ubah', [ImmunizationController::class, 'edit'])->name('imunisasi.edit');
    Route::put('/imunisasi/{immunization}', [ImmunizationController::class, 'update'])->name('imunisasi.update');

    Route::post('/balita/{balita}/wali', [GuardianController::class, 'store'])->name('wali.store');
    Route::post('/balita/{balita}/wali/{parent}/verifikasi', [GuardianController::class, 'verify'])->name('wali.verify');
    Route::delete('/balita/{balita}/wali/{parent}', [GuardianController::class, 'destroy'])->name('wali.destroy');

    Route::get('/orang-tua', [ParentAccountController::class, 'index'])->name('orang-tua.index');
    Route::get('/orang-tua/baru', [ParentAccountController::class, 'create'])->name('orang-tua.create');
    Route::post('/orang-tua', [ParentAccountController::class, 'store'])->name('orang-tua.store');
    Route::get('/orang-tua/{parent}/ubah', [ParentAccountController::class, 'edit'])->name('orang-tua.edit');
    Route::put('/orang-tua/{parent}', [ParentAccountController::class, 'update'])->name('orang-tua.update');

    Route::get('/laporan', [ReportController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/cetak', [ReportController::class, 'print'])->name('laporan.print');
    Route::get('/laporan/csv', [ReportController::class, 'csv'])->name('laporan.csv');
});

/*
|--------------------------------------------------------------------------
| Orang Tua / Wali
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:parent'])->prefix('orang-tua')->name('orang-tua.')->group(function () {
    Route::get('/', ParentDashboardController::class)->name('dashboard');

    Route::get('/anak/{balita}/grafik', GrowthController::class)->name('grafik');
    Route::get('/anak/{balita}/riwayat', HistoryController::class)->name('riwayat');
    Route::get('/anak/{balita}/imunisasi', ImmunizationHistoryController::class)->name('imunisasi');
});
