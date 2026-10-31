<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Asesi\DashboardController as AsesiDashboardController;
use App\Http\Controllers\Asesor\DashboardController as AsesorDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Guest Only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'store'])->name('password.email');

    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes (All Roles)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    /*
    |--------------------------------------------------------------------------
    | Role-Specific Dashboards & Area Guards
    |--------------------------------------------------------------------------
    */
    // 1. Admin LSP Area
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Master Data Engine (Fase 2)
        Route::get('/lsp-profile', [\App\Http\Controllers\Admin\LspProfileController::class, 'edit'])->name('lsp-profile.edit');
        Route::put('/lsp-profile', [\App\Http\Controllers\Admin\LspProfileController::class, 'update'])->name('lsp-profile.update');

        Route::resource('/standards', \App\Http\Controllers\Admin\StandardController::class)->except(['create', 'show', 'edit']);
        Route::resource('/schemes', \App\Http\Controllers\Admin\CertificationSchemeController::class);
        
        Route::post('/schemes/{scheme}/requirements', [\App\Http\Controllers\Admin\CertificationSchemeController::class, 'storeRequirement'])->name('schemes.requirements.store');
        Route::delete('/requirements/{requirement}', [\App\Http\Controllers\Admin\CertificationSchemeController::class, 'destroyRequirement'])->name('schemes.requirements.destroy');

        Route::post('/schemes/{scheme}/units', [\App\Http\Controllers\Admin\CompetencyUnitController::class, 'store'])->name('schemes.units.store');
        Route::put('/units/{unit}', [\App\Http\Controllers\Admin\CompetencyUnitController::class, 'update'])->name('schemes.units.update');
        Route::delete('/units/{unit}', [\App\Http\Controllers\Admin\CompetencyUnitController::class, 'destroy'])->name('schemes.units.destroy');

        Route::post('/units/{unit}/elements', [\App\Http\Controllers\Admin\CompetencyUnitController::class, 'storeElement'])->name('schemes.elements.store');
        Route::delete('/elements/{element}', [\App\Http\Controllers\Admin\CompetencyUnitController::class, 'destroyElement'])->name('schemes.elements.destroy');

        Route::post('/elements/{element}/criterias', [\App\Http\Controllers\Admin\CompetencyUnitController::class, 'storeCriteria'])->name('schemes.criterias.store');
        Route::delete('/criterias/{criteria}', [\App\Http\Controllers\Admin\CompetencyUnitController::class, 'destroyCriteria'])->name('schemes.criterias.destroy');

        Route::resource('/tuks', \App\Http\Controllers\Admin\TukController::class)->except(['create', 'show', 'edit']);
        Route::patch('/tuks/{tuk}/toggle-status', [\App\Http\Controllers\Admin\TukController::class, 'toggleStatus'])->name('tuks.toggle-status');

        Route::resource('/asesors', \App\Http\Controllers\Admin\AsesorManagementController::class)->except(['create', 'show', 'edit']);

        // Event & Manajemen Jadwal (Fase 3)
        Route::resource('/events', \App\Http\Controllers\Admin\EventController::class);
        Route::resource('/schedules', \App\Http\Controllers\Admin\AssessmentScheduleController::class);
        Route::patch('/schedules/{schedule}/status', [\App\Http\Controllers\Admin\AssessmentScheduleController::class, 'updateStatus'])->name('schedules.update-status');

        // Verifikasi Permohonan & Berkas APL-01 (Fase 4)
        Route::get('/verifications', [\App\Http\Controllers\Admin\VerificationController::class, 'index'])->name('verifications.index');
        Route::get('/verifications/{application}', [\App\Http\Controllers\Admin\VerificationController::class, 'show'])->name('verifications.show');
        Route::post('/verifications/{application}/status', [\App\Http\Controllers\Admin\VerificationController::class, 'updateStatus'])->name('verifications.update-status');
        Route::post('/verifications/{application}/plot', [\App\Http\Controllers\Admin\VerificationController::class, 'plotSchedule'])->name('verifications.plot-schedule');
        Route::post('/verifications/documents/{document}', [\App\Http\Controllers\Admin\VerificationController::class, 'verifyDocument'])->name('verifications.verify-document');
    });

    // 2. Asesor Kompetensi Area
    Route::middleware('role:asesor')->prefix('asesor')->name('asesor.')->group(function () {
        Route::get('/dashboard', [AsesorDashboardController::class, 'index'])->name('dashboard');
    });

    // 3. Asesi Mandiri Area (Fase 4)
    Route::middleware('role:asesi')->prefix('asesi')->name('asesi.')->group(function () {
        Route::get('/dashboard', [AsesiDashboardController::class, 'index'])->name('dashboard');
        Route::resource('/applications', \App\Http\Controllers\Asesi\ApplicationController::class)->except(['destroy']);
    });
});

/*
|--------------------------------------------------------------------------
| Wilayah Indonesia API (Cascading Dropdown)
|--------------------------------------------------------------------------
*/
Route::prefix('api/wilayah')->name('api.wilayah.')->group(function () {
    Route::get('/provinces', [\App\Http\Controllers\Api\WilayahController::class, 'provinces'])->name('provinces');
    Route::get('/regencies', [\App\Http\Controllers\Api\WilayahController::class, 'regencies'])->name('regencies');
    Route::get('/districts', [\App\Http\Controllers\Api\WilayahController::class, 'districts'])->name('districts');
    Route::get('/villages', [\App\Http\Controllers\Api\WilayahController::class, 'villages'])->name('villages');
});
