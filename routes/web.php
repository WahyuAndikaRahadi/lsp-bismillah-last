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
Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();
    if ($user->isAdmin() || $user->isRole('ADMIN_LSP') || $user->isRole('SUPER_ADMIN')) {
        return redirect()->route('admin.dashboard');
    }
    if ($user->isAsesor() || $user->isRole('ASESOR')) {
        return redirect()->route('asesor.dashboard');
    }
    return redirect()->route('asesi.dashboard');
})->name('home');

// Layanan Pengaduan & Aspirasi Publik (Fase 8)
Route::get('/pengaduan', [\App\Http\Controllers\ComplaintController::class, 'create'])->name('pengaduan.create');
Route::post('/pengaduan', [\App\Http\Controllers\ComplaintController::class, 'store'])->name('pengaduan.store');
Route::get('/pengaduan/tracking', [\App\Http\Controllers\ComplaintController::class, 'tracking'])->name('pengaduan.tracking');

// Layanan Surveilans Pemegang Sertifikat (Fase 8)
Route::get('/surveillance', [\App\Http\Controllers\SurveillanceController::class, 'create'])->name('surveillance.create');
Route::post('/surveillance', [\App\Http\Controllers\SurveillanceController::class, 'store'])->name('surveillance.store');

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
    Route::post('/proxy/switch-back', [LoginController::class, 'proxySwitchBack'])->name('proxy.switch-back');
    Route::post('/admin/proxy/switch/{user}', [\App\Http\Controllers\Admin\ProxyController::class, 'impersonate'])
        ->middleware('role:admin,ADMIN_LSP,SUPER_ADMIN')
        ->name('proxy.switch');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    /*
    |--------------------------------------------------------------------------
    | Role-Specific Dashboards & Area Guards
    |--------------------------------------------------------------------------
    */
    // 1. Admin LSP Area
    Route::middleware('role:admin,ADMIN_LSP,SUPER_ADMIN,KOMITE_TEKNIS,TUK_ADMIN')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // ProIndo Jadwal Management
        Route::get('/jadwal', [\App\Http\Controllers\Admin\JadwalController::class, 'index'])->name('jadwal.index');
        Route::get('/jadwal/create', [\App\Http\Controllers\Admin\JadwalController::class, 'create'])->name('jadwal.create');
        Route::post('/jadwal', [\App\Http\Controllers\Admin\JadwalController::class, 'store'])->name('jadwal.store');
        Route::get('/jadwal/{jadwal}', [\App\Http\Controllers\Admin\JadwalController::class, 'show'])->name('jadwal.show');
        Route::get('/jadwal/{jadwal}/edit', [\App\Http\Controllers\Admin\JadwalController::class, 'edit'])->name('jadwal.edit');
        Route::match(['put', 'patch'], '/jadwal/{jadwal}', [\App\Http\Controllers\Admin\JadwalController::class, 'update'])->name('jadwal.update');
        Route::delete('/jadwal/{jadwal}', [\App\Http\Controllers\Admin\JadwalController::class, 'destroy'])->name('jadwal.destroy');

        Route::post('/assessment/{assessment}/review-registration', [\App\Http\Controllers\Admin\JadwalController::class, 'reviewRegistration'])->name('assessment.review-registration');
        Route::post('/jadwal/{jadwal}/batch-review-registration', [\App\Http\Controllers\Admin\JadwalController::class, 'batchReviewRegistration'])->name('jadwal.batch-review-registration');
        Route::post('/assessment/{assessment}/mark-absent', [\App\Http\Controllers\Admin\JadwalController::class, 'markAbsent'])->name('assessment.mark-absent');

        // ProIndo Dokumen MUK Center & Exports
        Route::get('/dokumen-muk', [\App\Http\Controllers\Admin\DokumenMukController::class, 'index'])->name('dokumen-muk.index');
        Route::get('/dokumen-muk/{schedule}/export/{type}', [\App\Http\Controllers\Admin\DokumenMukController::class, 'exportSchedule'])->name('dokumen-muk.export');
        Route::get('/dokumen-muk/asesi/{assessment}/export/{type}', [\App\Http\Controllers\Admin\DokumenMukController::class, 'exportAsesi'])->name('dokumen-muk.export-asesi');
        Route::get('/dokumen-muk/{schedule}/export-sertifikat', [\App\Http\Controllers\Admin\DokumenMukController::class, 'exportSertifikat'])->name('dokumen-muk.export-sertifikat');
        Route::get('/dokumen-muk/{schedule}/export-ba', [\App\Http\Controllers\Admin\DokumenMukController::class, 'exportBa'])->name('dokumen-muk.export-ba');
        Route::get('/dokumen-muk/{schedule}/export-hadir-asesor', [\App\Http\Controllers\Admin\DokumenMukController::class, 'exportHadirAsesor'])->name('dokumen-muk.export-hadir-asesor');
        Route::get('/dokumen-muk/{schedule}/export-hadir-asesi', [\App\Http\Controllers\Admin\DokumenMukController::class, 'exportHadirAsesi'])->name('dokumen-muk.export-hadir-asesi');
        Route::get('/dokumen-muk/{schedule}/export-excel-biodata', [\App\Http\Controllers\Admin\DokumenMukController::class, 'exportExcelBiodata'])->name('dokumen-muk.export-excel-biodata');
        Route::get('/dokumen-muk/{schedule}/export-excel-hadir-asesi', [\App\Http\Controllers\Admin\DokumenMukController::class, 'exportExcelHadirAsesi'])->name('dokumen-muk.export-excel-hadir-asesi');
        Route::get('/dokumen-muk/{schedule}/export-excel-hadir-asesor', [\App\Http\Controllers\Admin\DokumenMukController::class, 'exportExcelHadirAsesor'])->name('dokumen-muk.export-excel-hadir-asesor');
        Route::get('/dokumen-muk/individual/{assessment}', [\App\Http\Controllers\Admin\DokumenMukController::class, 'exportIndividual'])->name('dokumen-muk.export-individual');

        // Proxy Impersonation
        Route::get('/proxy', [\App\Http\Controllers\Admin\ProxyController::class, 'index'])->name('proxy.index');
        Route::post('/proxy/{user}', [\App\Http\Controllers\Admin\ProxyController::class, 'impersonate'])->name('proxy.impersonate');

        // Master Data ProIndo
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::resource('schemes', \App\Http\Controllers\Admin\SchemeController::class);
        Route::resource('question-banks', \App\Http\Controllers\Admin\QuestionBankController::class);

        // Master Data Engine (Fase 2)
        Route::get('/lsp-profile', [\App\Http\Controllers\Admin\LspProfileController::class, 'edit'])->name('lsp-profile.edit');
        Route::put('/lsp-profile', [\App\Http\Controllers\Admin\LspProfileController::class, 'update'])->name('lsp-profile.update');

        Route::resource('/standards', \App\Http\Controllers\Admin\StandardController::class)->except(['create', 'show', 'edit']);
        Route::resource('/legacy-schemes', \App\Http\Controllers\Admin\CertificationSchemeController::class)->names('legacy_schemes');
        
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

        // Event & Manajemen Jadwal (Fase 3 & 5)
        Route::resource('/events', \App\Http\Controllers\Admin\EventController::class);
        Route::resource('/schedules', \App\Http\Controllers\Admin\AssessmentScheduleController::class);
        Route::patch('/schedules/{schedule}/status', [\App\Http\Controllers\Admin\AssessmentScheduleController::class, 'updateStatus'])->name('schedules.update-status');
        Route::post('/schedules/{schedule}/asesors', [\App\Http\Controllers\Admin\AssessmentScheduleController::class, 'assignAsesor'])->name('schedules.asesors.assign');
        Route::delete('/schedules/{schedule}/asesors/{scheduleAsesor}', [\App\Http\Controllers\Admin\AssessmentScheduleController::class, 'removeAsesor'])->name('schedules.asesors.remove');
        Route::post('/schedules/{schedule}/plot-asesor', [\App\Http\Controllers\Admin\AssessmentScheduleController::class, 'plotAsesiToAsesor'])->name('schedules.plot-asesor');

        // Verifikasi Permohonan & Berkas APL-01 (Fase 4)
        Route::get('/verifications', [\App\Http\Controllers\Admin\VerificationController::class, 'index'])->name('verifications.index');
        Route::get('/verifications/{application}', [\App\Http\Controllers\Admin\VerificationController::class, 'show'])->name('verifications.show');
        Route::post('/verifications/{application}/status', [\App\Http\Controllers\Admin\VerificationController::class, 'updateStatus'])->name('verifications.update-status');
        Route::post('/verifications/{application}/plot', [\App\Http\Controllers\Admin\VerificationController::class, 'plotSchedule'])->name('verifications.plot-schedule');
        Route::post('/verifications/documents/{document}', [\App\Http\Controllers\Admin\VerificationController::class, 'verifyDocument'])->name('verifications.verify-document');

        // Penetapan Rapat Pleno & Kelulusan (Fase 7)
        Route::get('/pleno', [\App\Http\Controllers\Admin\PlenoController::class, 'index'])->name('pleno.index');
        Route::get('/pleno/{schedule}', [\App\Http\Controllers\Admin\PlenoController::class, 'show'])->name('pleno.show');
        Route::post('/pleno/{schedule}', [\App\Http\Controllers\Admin\PlenoController::class, 'store'])->name('pleno.store');

        // Ekspor Format BNSP, Pengaduan, & Surveilans (Fase 8)
        Route::get('/exports/bnsp', [\App\Http\Controllers\Admin\BnspExportController::class, 'index'])->name('exports.bnsp.index');
        Route::get('/exports/bnsp/download', [\App\Http\Controllers\Admin\BnspExportController::class, 'export'])->name('exports.bnsp.download');
        Route::get('/exports/bnsp/schedule/{schedule}', [\App\Http\Controllers\Admin\BnspExportController::class, 'exportSchedule'])->name('exports.bnsp.schedule');

        Route::get('/complaints', [\App\Http\Controllers\Admin\ComplaintController::class, 'index'])->name('complaints.index');
        Route::get('/complaints/{complaint}', [\App\Http\Controllers\Admin\ComplaintController::class, 'show'])->name('complaints.show');
        Route::patch('/complaints/{complaint}', [\App\Http\Controllers\Admin\ComplaintController::class, 'updateStatus'])->name('complaints.update-status');

        Route::get('/surveillance', [\App\Http\Controllers\Admin\SurveillanceController::class, 'index'])->name('surveillance.index');
        Route::patch('/surveillance/{surveillance}/verify', [\App\Http\Controllers\Admin\SurveillanceController::class, 'verify'])->name('surveillance.verify');

        // Layanan Banding Asesmen
        Route::get('/appeals', [\App\Http\Controllers\Admin\AppealController::class, 'index'])->name('appeals.index');

        // Laporan & Ekspor Lengkap Parity Demo
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/general', [\App\Http\Controllers\Admin\ReportController::class, 'general'])->name('general');
            Route::get('/monthly', [\App\Http\Controllers\Admin\ReportController::class, 'monthly'])->name('monthly');
            Route::get('/annual', [\App\Http\Controllers\Admin\ReportController::class, 'annual'])->name('annual');
            Route::get('/asesor-performance', [\App\Http\Controllers\Admin\ReportController::class, 'asesorPerformance'])->name('asesor-performance');
            Route::get('/tuk-performance', [\App\Http\Controllers\Admin\ReportController::class, 'tukPerformance'])->name('tuk-performance');
            Route::get('/certificates', [\App\Http\Controllers\Admin\ReportController::class, 'certificates'])->name('certificates');
        });

        // Penjaminan Mutu & Audit Internal
        Route::prefix('quality')->name('quality.')->group(function () {
            Route::get('/documents', [\App\Http\Controllers\Admin\QualityController::class, 'documents'])->name('documents.index');
            Route::post('/documents', [\App\Http\Controllers\Admin\QualityController::class, 'storeDocument'])->name('documents.store');
            Route::get('/audits', [\App\Http\Controllers\Admin\QualityController::class, 'audits'])->name('audits.index');
            Route::post('/audits', [\App\Http\Controllers\Admin\QualityController::class, 'storeAudit'])->name('audits.store');
        });

        // Keuangan, Biaya & Rekening Bank
        Route::prefix('finance')->name('finance.')->group(function () {
            Route::get('/bank-accounts', [\App\Http\Controllers\Admin\FinanceController::class, 'bankAccounts'])->name('bank-accounts.index');
            Route::post('/bank-accounts', [\App\Http\Controllers\Admin\FinanceController::class, 'storeBankAccount'])->name('bank-accounts.store');
            Route::get('/pricing', [\App\Http\Controllers\Admin\FinanceController::class, 'pricing'])->name('pricing');
            Route::get('/transactions', [\App\Http\Controllers\Admin\FinanceController::class, 'transactions'])->name('transactions.index');
            Route::post('/transactions', [\App\Http\Controllers\Admin\FinanceController::class, 'storeTransaction'])->name('transactions.store');
            Route::get('/balance-sheet', [\App\Http\Controllers\Admin\FinanceController::class, 'balanceSheet'])->name('balance-sheet');
            Route::get('/income-statement', [\App\Http\Controllers\Admin\FinanceController::class, 'incomeStatement'])->name('income-statement');
        });

        // Persuratan Resmi LSP
        Route::get('/letters', [\App\Http\Controllers\Admin\LetterController::class, 'index'])->name('letters.index');
        Route::post('/letters', [\App\Http\Controllers\Admin\LetterController::class, 'store'])->name('letters.store');

        // Log SMS & Notifikasi Masuk/Keluar
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/sent', [\App\Http\Controllers\Admin\MessageController::class, 'sent'])->name('sent');
            Route::get('/inbox', [\App\Http\Controllers\Admin\MessageController::class, 'inbox'])->name('inbox');
        });

        // Asesi Submodules
        Route::prefix('asesi')->name('asesi.')->group(function () {
            Route::get('/unscheduled', [\App\Http\Controllers\Admin\AsesiSubmoduleController::class, 'unscheduled'])->name('unscheduled');
            Route::get('/scheduled', [\App\Http\Controllers\Admin\AsesiSubmoduleController::class, 'scheduled'])->name('scheduled');
            Route::get('/competent', [\App\Http\Controllers\Admin\AsesiSubmoduleController::class, 'competent'])->name('competent');
            Route::get('/incompetent', [\App\Http\Controllers\Admin\AsesiSubmoduleController::class, 'incompetent'])->name('incompetent');
        });
    });

    // 2. Asesor Kompetensi Area
    Route::middleware('role:asesor,ASESOR')->prefix('asesor')->name('asesor.')->group(function () {
        Route::get('/dashboard', function () {
            return redirect()->route('asesor.jadwal.index');
        })->name('dashboard');

        Route::get('/profile', [\App\Http\Controllers\Asesor\ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile', [\App\Http\Controllers\Asesor\ProfileController::class, 'update'])->name('profile.update');

        Route::get('/jadwal', [\App\Http\Controllers\Asesor\JadwalController::class, 'index'])->name('jadwal.index');
        Route::get('/jadwal/{schedule}/asesi', [\App\Http\Controllers\Asesor\JadwalController::class, 'asesiList'])->name('jadwal.asesi');

        // Review APL (both aliases supported)
        Route::get('/assessment/{assessment}/review-apl', [\App\Http\Controllers\Asesor\AplReviewController::class, 'show'])->name('apl.review');
        Route::post('/assessment/{assessment}/review-apl', [\App\Http\Controllers\Asesor\AplReviewController::class, 'decide'])->name('apl.decide');
        Route::get('/review-apl/{assessment}', [\App\Http\Controllers\Asesor\AplReviewController::class, 'show'])->name('review-apl');
        Route::post('/review-apl/{assessment}', [\App\Http\Controllers\Asesor\AplReviewController::class, 'decide'])->name('review-apl.update');

        // AK01
        Route::get('/schedule/{schedule}/ak01', [\App\Http\Controllers\Asesor\InstrumentController::class, 'ak01Schedule'])->name('schedule.ak01');
        Route::post('/assessment/{assessment}/ak01/sign', [\App\Http\Controllers\Asesor\InstrumentController::class, 'ak01Sign'])->name('ak01.sign');

        // AK07 Penyesuaian Wajar (Single & Batch)
        Route::get('/schedule/{schedule}/ak07', [\App\Http\Controllers\Asesor\Ak07Controller::class, 'index'])->name('schedule.ak07');
        Route::post('/schedule/{schedule}/ak07', [\App\Http\Controllers\Asesor\Ak07Controller::class, 'store'])->name('schedule.ak07.store');
        Route::get('/schedule/{schedule}/ak07/create', [\App\Http\Controllers\Asesor\Ak07Controller::class, 'index'])->name('ak07.create');
        Route::post('/schedule/{schedule}/ak07/create', [\App\Http\Controllers\Asesor\Ak07Controller::class, 'store'])->name('ak07.store');

        // Ruang Uji Jadwal (Schedule-level: Displays Selected Instruments)
        Route::get('/schedule/{schedule}/ruang-uji', [\App\Http\Controllers\Asesor\InstrumentController::class, 'scheduleRuangUji'])->name('schedule.ruang-uji');

        // Instrument Asesi List (Displays Asesis for a Selected Instrument)
        Route::get('/schedule/{schedule}/instrument/{code}', [\App\Http\Controllers\Asesor\InstrumentController::class, 'scheduleInstrument'])->name('schedule.instrument');

        // Ruang Uji Asesor per Assessment
        Route::get('/assessment/{assessment}/ruang-uji', [\App\Http\Controllers\Asesor\InstrumentController::class, 'hub'])->name('assessment.ruang-uji');

        // Generic Instrument routes for AK01, IA01, IA01B, IA02, IA03, IA05
        Route::get('/assessment/{assessment}/instrument/{code}', [\App\Http\Controllers\Asesor\InstrumentController::class, 'instrumentShow'])
            ->whereIn('code', ['ak01', 'ia01', 'ia01b', 'ia02', 'ia03', 'ia05', 'AK01', 'IA01', 'IA01B', 'IA02', 'IA03', 'IA05'])
            ->name('assessment.instrument');
        Route::post('/assessment/{assessment}/instrument/{code}', [\App\Http\Controllers\Asesor\InstrumentController::class, 'instrumentStore'])
            ->whereIn('code', ['ak01', 'ia01', 'ia01b', 'ia02', 'ia03', 'ia05', 'AK01', 'IA01', 'IA01B', 'IA02', 'IA03', 'IA05'])
            ->name('assessment.instrument.store');

        // AK02 Rekaman Asesmen
        Route::get('/assessment/{assessment}/ak02', [\App\Http\Controllers\Asesor\FinalizationController::class, 'showAk02'])->name('assessment.ak02');
        Route::post('/assessment/{assessment}/ak02', [\App\Http\Controllers\Asesor\FinalizationController::class, 'updateAk02'])->name('assessment.ak02.update');

        // AK05 & AK06 Reports
        Route::get('/schedule/{schedule}/reports', [\App\Http\Controllers\Asesor\FinalizationController::class, 'showReports'])->name('schedule.reports');
        Route::post('/schedule/{schedule}/reports', [\App\Http\Controllers\Asesor\FinalizationController::class, 'submitReports'])->name('schedule.reports.submit');

        // Finalize
        Route::post('/schedule/{schedule}/finalize', [\App\Http\Controllers\Asesor\FinalizationController::class, 'finalize'])->name('schedule.finalize');
        Route::post('/assessment/{assessment}/complete', [\App\Http\Controllers\Asesor\FinalizationController::class, 'completeAssessment'])->name('assessment.complete');
        Route::post('/assessment/{assessment}/mark-absent', [\App\Http\Controllers\Asesor\FinalizationController::class, 'markAbsent'])->name('assessment.mark-absent');

        // Extended parity submodules
        Route::get('/schedules', [\App\Http\Controllers\Asesor\ScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/schedules/{schedule}', [\App\Http\Controllers\Asesor\ScheduleController::class, 'show'])->name('schedules.show');
        Route::get('/tuks', [\App\Http\Controllers\Asesor\TukVerificationController::class, 'index'])->name('tuks.index');
        Route::post('/tuks', [\App\Http\Controllers\Asesor\TukVerificationController::class, 'store'])->name('tuks.store');
        Route::get('/instruments', [\App\Http\Controllers\Asesor\InstrumentReviewController::class, 'index'])->name('instruments.index');
        Route::post('/instruments', [\App\Http\Controllers\Asesor\InstrumentReviewController::class, 'store'])->name('instruments.store');
        Route::get('/mkva', [\App\Http\Controllers\Asesor\MkvaController::class, 'index'])->name('mkva.index');
        Route::post('/mkva', [\App\Http\Controllers\Asesor\MkvaController::class, 'store'])->name('mkva.store');
        Route::get('/messages', [\App\Http\Controllers\Asesor\NotificationController::class, 'index'])->name('messages.index');
        Route::get('/apl-02', [\App\Http\Controllers\Asesor\Apl02ReviewController::class, 'index'])->name('apl02.index');
        Route::get('/apl-02/{apl02}/review', [\App\Http\Controllers\Asesor\Apl02ReviewController::class, 'review'])->name('apl02.review');
        Route::put('/apl-02/{apl02}', [\App\Http\Controllers\Asesor\Apl02ReviewController::class, 'update'])->name('apl02.update');
    });

    // Secure Document & Evidence Access
    Route::get('/secure-file/proof/{proof}', [\App\Http\Controllers\SecureFileController::class, 'showProof'])->name('secure-file.proof');
    Route::get('/secure-file/evidence/{response}', [\App\Http\Controllers\SecureFileController::class, 'showEvidence'])->name('secure-file.evidence');

    // 3. Asesi Mandiri Area (Fase 4, 5, 6)
    Route::middleware('role:asesi,ASESI')->prefix('asesi')->name('asesi.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Asesi\JadwalController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [\App\Http\Controllers\Asesi\ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile', [\App\Http\Controllers\Asesi\ProfileController::class, 'update'])->name('profile.update');

        // Jadwal
        Route::get('/jadwal', [\App\Http\Controllers\Asesi\JadwalController::class, 'index'])->name('jadwal.index');
        Route::post('/jadwal/join', [\App\Http\Controllers\Asesi\JadwalController::class, 'join'])->middleware('throttle:10,1')->name('jadwal.join');

        // Asesmen & APL
        Route::get('/assessment/{assessment}/apl', [\App\Http\Controllers\Asesi\AplController::class, 'show'])->name('assessment.apl');
        Route::post('/assessment/{assessment}/apl', [\App\Http\Controllers\Asesi\AplController::class, 'store'])->name('assessment.apl.store');

        // Ruang Uji (Protected by APL Approval Gate)
        Route::middleware(['apl.approved'])->prefix('assessment/{assessment}')->name('assessment.')->group(function () {
            Route::get('/ak01', [\App\Http\Controllers\Asesi\RuangUjiController::class, 'ak01'])->name('ak01');
            Route::post('/ak01', [\App\Http\Controllers\Asesi\RuangUjiController::class, 'signAk01'])->name('ak01.sign');
            Route::post('/consent-esign', [\App\Http\Controllers\Asesi\RuangUjiController::class, 'consentEsign'])->name('consent-esign');

            Route::get('/ruang-uji', [\App\Http\Controllers\Asesi\RuangUjiController::class, 'hub'])->name('ruang-uji');

            // FR.IA Instruments
            Route::get('/instrument/IA05', [\App\Http\Controllers\Asesi\RuangUjiController::class, 'ia05'])->name('instrument.ia05');
            Route::post('/instrument/IA05', [\App\Http\Controllers\Asesi\RuangUjiController::class, 'submitIa05'])->name('instrument.ia05.submit');

            Route::post('/instrument/{code}/upload-evidence', [\App\Http\Controllers\Asesi\RuangUjiController::class, 'uploadEvidence'])->name('instrument.upload-evidence');

            Route::get('/ak03', [\App\Http\Controllers\Asesi\RuangUjiController::class, 'showAk03'])->name('ak03');
            Route::post('/ak03', [\App\Http\Controllers\Asesi\RuangUjiController::class, 'submitAk03'])->name('ak03.submit');
            Route::get('/appeal', [\App\Http\Controllers\Asesi\RuangUjiController::class, 'showAppeal'])->name('appeal');
            Route::post('/appeal', [\App\Http\Controllers\Asesi\RuangUjiController::class, 'submitAppeal'])->name('appeal.submit');
        });

        // Backward compatibility routes for legacy application links
        Route::resource('/applications', \App\Http\Controllers\Asesi\ApplicationController::class)->except(['destroy']);
        Route::get('/applications/{application}/apl-02/create', [\App\Http\Controllers\Asesi\Apl02Controller::class, 'create'])->name('applications.apl02.create');
        Route::post('/applications/{application}/apl-02', [\App\Http\Controllers\Asesi\Apl02Controller::class, 'store'])->name('applications.apl02.store');
        Route::get('/applications/{application}/apl-02', [\App\Http\Controllers\Asesi\Apl02Controller::class, 'show'])->name('applications.apl02.show');
        Route::get('/applications/{application}/ak-01', [\App\Http\Controllers\Asesi\AssessmentExecutionController::class, 'showAk01'])->name('applications.ak01');
        Route::post('/applications/{application}/ak-01/sign', [\App\Http\Controllers\Asesi\AssessmentExecutionController::class, 'signAk01'])->name('applications.ak01.sign');
        Route::get('/applications/{application}/ak-02', [\App\Http\Controllers\Asesi\AssessmentExecutionController::class, 'showAk02'])->name('applications.ak02');
        Route::post('/applications/{application}/ak-02/sign', [\App\Http\Controllers\Asesi\AssessmentExecutionController::class, 'signAk02'])->name('applications.ak02.sign');
        Route::get('/applications/{application}/ak-03', [\App\Http\Controllers\Asesi\AssessmentExecutionController::class, 'showAk03'])->name('applications.ak03');
        Route::post('/applications/{application}/ak-03', [\App\Http\Controllers\Asesi\AssessmentExecutionController::class, 'storeAk03'])->name('applications.ak03.store');
    });

    /*
    |--------------------------------------------------------------------------
    | Dokumen Cetak Resmi BNSP & Generator PDF (Fase 7)
    |--------------------------------------------------------------------------
    */
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/surat-tugas/{schedule}/{scheduleAsesor}', [\App\Http\Controllers\PdfDocumentController::class, 'suratTugas'])->name('surat-tugas');
        Route::get('/berita-acara-asesmen/{schedule}', [\App\Http\Controllers\PdfDocumentController::class, 'beritaAcaraAsesmen'])->name('berita-acara-asesmen');
        Route::get('/daftar-hadir-asesmen/{schedule}', [\App\Http\Controllers\PdfDocumentController::class, 'daftarHadirAsesmen'])->name('daftar-hadir-asesmen');
        Route::get('/berita-acara-pleno/{schedule}', [\App\Http\Controllers\PdfDocumentController::class, 'beritaAcaraPleno'])->name('berita-acara-pleno');
        Route::get('/daftar-hadir-pleno/{schedule}', [\App\Http\Controllers\PdfDocumentController::class, 'daftarHadirPleno'])->name('daftar-hadir-pleno');
        Route::get('/fr-apl-01/{application}', [\App\Http\Controllers\PdfDocumentController::class, 'frApl01'])->name('fr-apl-01');
        Route::get('/fr-apl-02/{application}', [\App\Http\Controllers\PdfDocumentController::class, 'frApl02'])->name('fr-apl-02');
        Route::get('/fr-ak-01/{application}', [\App\Http\Controllers\PdfDocumentController::class, 'frAk01'])->name('fr-ak-01');
        Route::get('/fr-ak-02/{application}', [\App\Http\Controllers\PdfDocumentController::class, 'frAk02'])->name('fr-ak-02');
        Route::get('/fr-ak-03/{application}', [\App\Http\Controllers\PdfDocumentController::class, 'frAk03'])->name('fr-ak-03');
        Route::get('/fr-ak-05/{schedule}', [\App\Http\Controllers\PdfDocumentController::class, 'frAk05'])->name('fr-ak-05');
        Route::get('/sertifikat/{application}', [\App\Http\Controllers\PdfDocumentController::class, 'sertifikat'])->name('sertifikat');
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
