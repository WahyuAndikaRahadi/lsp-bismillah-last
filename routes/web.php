<?php

use App\Http\Controllers\Admin\AppealController;
use App\Http\Controllers\Admin\AsesiSubmoduleController;
use App\Http\Controllers\Admin\AsesorManagementController;
use App\Http\Controllers\Admin\AssessmentScheduleController;
use App\Http\Controllers\Admin\BnspExportController;
use App\Http\Controllers\Admin\CertificationSchemeController;
use App\Http\Controllers\Admin\CompetencyUnitController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DokumenMukController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\LetterController;
use App\Http\Controllers\Admin\LspProfileController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\PlenoController;
use App\Http\Controllers\Admin\ProxyController;
use App\Http\Controllers\Admin\QualityController;
use App\Http\Controllers\Admin\QuestionBankController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SchemeController;
use App\Http\Controllers\Admin\StandardController;
use App\Http\Controllers\Admin\TukController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VerificationController;
use App\Http\Controllers\Api\WilayahController;
use App\Http\Controllers\Asesi\Apl02Controller;
use App\Http\Controllers\Asesi\AplController;
use App\Http\Controllers\Asesi\ApplicationController;
use App\Http\Controllers\Asesi\RuangUjiController;
use App\Http\Controllers\Asesor\Ak07Controller;
use App\Http\Controllers\Asesor\Apl02ReviewController;
use App\Http\Controllers\Asesor\AplReviewController;
use App\Http\Controllers\Asesor\AssessmentExecutionController;
use App\Http\Controllers\Asesor\FinalizationController;
use App\Http\Controllers\Asesor\InstrumentController;
use App\Http\Controllers\Asesor\InstrumentReviewController;
use App\Http\Controllers\Asesor\MkvaController;
use App\Http\Controllers\Asesor\NotificationController;
use App\Http\Controllers\Asesor\ScheduleController;
use App\Http\Controllers\Asesor\TukVerificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\PdfDocumentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SecureFileController;
use App\Http\Controllers\SurveillanceController;
use App\Models\Assessment;
use App\Models\AssessmentApplication;
use App\Models\AssessmentSchedule;
use App\Models\Schedule;
use Illuminate\Http\Request;
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
Route::get('/pengaduan', [ComplaintController::class, 'create'])->name('pengaduan.create');
Route::post('/pengaduan', [ComplaintController::class, 'store'])->name('pengaduan.store');
Route::get('/pengaduan/tracking', [ComplaintController::class, 'tracking'])->name('pengaduan.tracking');

// Layanan Surveilans Pemegang Sertifikat (Fase 8)
Route::get('/surveillance', [SurveillanceController::class, 'create'])->name('surveillance.create');
Route::post('/surveillance', [SurveillanceController::class, 'store'])->name('surveillance.store');

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
    Route::post('/admin/proxy/switch/{user}', [ProxyController::class, 'impersonate'])
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
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ProIndo Jadwal Management
        Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
        Route::get('/jadwal/create', [JadwalController::class, 'create'])->name('jadwal.create');
        Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
        Route::get('/jadwal/{jadwal}', [JadwalController::class, 'show'])->name('jadwal.show');
        Route::get('/jadwal/{jadwal}/edit', [JadwalController::class, 'edit'])->name('jadwal.edit');
        Route::match(['put', 'patch'], '/jadwal/{jadwal}', [JadwalController::class, 'update'])->name('jadwal.update');
        Route::delete('/jadwal/{jadwal}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');

        Route::post('/assessment/{assessment}/review-registration', [JadwalController::class, 'reviewRegistration'])->name('assessment.review-registration');
        Route::post('/jadwal/{jadwal}/batch-review-registration', [JadwalController::class, 'batchReviewRegistration'])->name('jadwal.batch-review-registration');
        Route::post('/assessment/{assessment}/mark-absent', [JadwalController::class, 'markAbsent'])->name('assessment.mark-absent');

        // ProIndo Dokumen MUK Center & Exports
        Route::get('/dokumen-muk', [DokumenMukController::class, 'index'])->name('dokumen-muk.index');
        Route::get('/dokumen-muk/{schedule}/export/{type}', [DokumenMukController::class, 'exportSchedule'])->name('dokumen-muk.export');
        Route::get('/dokumen-muk/asesi/{assessment}/export/{type}', [DokumenMukController::class, 'exportAsesi'])->name('dokumen-muk.export-asesi');
        Route::get('/dokumen-muk/{schedule}/export-sertifikat', [DokumenMukController::class, 'exportSertifikat'])->name('dokumen-muk.export-sertifikat');
        Route::get('/dokumen-muk/{schedule}/export-ba', [DokumenMukController::class, 'exportBa'])->name('dokumen-muk.export-ba');
        Route::get('/dokumen-muk/{schedule}/export-hadir-asesor', [DokumenMukController::class, 'exportHadirAsesor'])->name('dokumen-muk.export-hadir-asesor');
        Route::get('/dokumen-muk/{schedule}/export-hadir-asesi', [DokumenMukController::class, 'exportHadirAsesi'])->name('dokumen-muk.export-hadir-asesi');
        Route::get('/dokumen-muk/{schedule}/export-excel-biodata', [DokumenMukController::class, 'exportExcelBiodata'])->name('dokumen-muk.export-excel-biodata');
        Route::get('/dokumen-muk/{schedule}/export-excel-hadir-asesi', [DokumenMukController::class, 'exportExcelHadirAsesi'])->name('dokumen-muk.export-excel-hadir-asesi');
        Route::get('/dokumen-muk/{schedule}/export-excel-hadir-asesor', [DokumenMukController::class, 'exportExcelHadirAsesor'])->name('dokumen-muk.export-excel-hadir-asesor');
        Route::get('/dokumen-muk/individual/{assessment}', [DokumenMukController::class, 'exportIndividual'])->name('dokumen-muk.export-individual');

        // Proxy Impersonation
        Route::get('/proxy', [ProxyController::class, 'index'])->name('proxy.index');
        Route::post('/proxy/{user}', [ProxyController::class, 'impersonate'])->name('proxy.impersonate');

        // Master Data ProIndo
        Route::resource('users', UserController::class);
        Route::resource('schemes', SchemeController::class);
        Route::resource('question-banks', QuestionBankController::class);

        // Master Data Engine (Fase 2)
        Route::get('/lsp-profile', [LspProfileController::class, 'edit'])->name('lsp-profile.edit');
        Route::put('/lsp-profile', [LspProfileController::class, 'update'])->name('lsp-profile.update');

        Route::resource('/standards', StandardController::class)->except(['create', 'show', 'edit']);
        Route::resource('/legacy-schemes', CertificationSchemeController::class)->names('legacy_schemes');

        Route::post('/schemes/{scheme}/requirements', [CertificationSchemeController::class, 'storeRequirement'])->name('schemes.requirements.store');
        Route::delete('/requirements/{requirement}', [CertificationSchemeController::class, 'destroyRequirement'])->name('schemes.requirements.destroy');

        Route::post('/schemes/{scheme}/units', [CompetencyUnitController::class, 'store'])->name('schemes.units.store');
        Route::put('/units/{unit}', [CompetencyUnitController::class, 'update'])->name('schemes.units.update');
        Route::delete('/units/{unit}', [CompetencyUnitController::class, 'destroy'])->name('schemes.units.destroy');

        Route::post('/units/{unit}/elements', [CompetencyUnitController::class, 'storeElement'])->name('schemes.elements.store');
        Route::put('/elements/{element}', [CompetencyUnitController::class, 'updateElement'])->name('schemes.elements.update');
        Route::delete('/elements/{element}', [CompetencyUnitController::class, 'destroyElement'])->name('schemes.elements.destroy');

        Route::post('/elements/{element}/criterias', [CompetencyUnitController::class, 'storeCriteria'])->name('schemes.criterias.store');
        Route::put('/criterias/{criteria}', [CompetencyUnitController::class, 'updateCriteria'])->name('schemes.criterias.update');
        Route::delete('/criterias/{criteria}', [CompetencyUnitController::class, 'destroyCriteria'])->name('schemes.criterias.destroy');

        Route::resource('/tuks', TukController::class)->except(['create', 'show', 'edit']);
        Route::patch('/tuks/{tuk}/toggle-status', [TukController::class, 'toggleStatus'])->name('tuks.toggle-status');

        Route::resource('/asesors', AsesorManagementController::class)->except(['create', 'show', 'edit']);

        // Event & Manajemen Jadwal (Fase 3 & 5)
        Route::resource('/events', EventController::class);
        Route::resource('/schedules', AssessmentScheduleController::class);
        Route::patch('/schedules/{schedule}/status', [AssessmentScheduleController::class, 'updateStatus'])->name('schedules.update-status');
        Route::post('/schedules/{schedule}/asesors', [AssessmentScheduleController::class, 'assignAsesor'])->name('schedules.asesors.assign');
        Route::delete('/schedules/{schedule}/asesors/{scheduleAsesor}', [AssessmentScheduleController::class, 'removeAsesor'])->name('schedules.asesors.remove');
        Route::post('/schedules/{schedule}/plot-asesor', [AssessmentScheduleController::class, 'plotAsesiToAsesor'])->name('schedules.plot-asesor');

        // Verifikasi Permohonan & Berkas APL-01 (Fase 4)
        Route::get('/verifications', [VerificationController::class, 'index'])->name('verifications.index');
        Route::get('/verifications/{application}', [VerificationController::class, 'show'])->name('verifications.show');
        Route::post('/verifications/{application}/status', [VerificationController::class, 'updateStatus'])->name('verifications.update-status');
        Route::post('/verifications/{application}/plot', [VerificationController::class, 'plotSchedule'])->name('verifications.plot-schedule');
        Route::post('/verifications/documents/{document}', [VerificationController::class, 'verifyDocument'])->name('verifications.verify-document');

        // Penetapan Rapat Pleno & Kelulusan (Fase 7)
        Route::get('/pleno', [PlenoController::class, 'index'])->name('pleno.index');
        Route::get('/pleno/{schedule}', [PlenoController::class, 'show'])->name('pleno.show');
        Route::post('/pleno/{schedule}', [PlenoController::class, 'store'])->name('pleno.store');

        // Ekspor Format BNSP, Pengaduan, & Surveilans (Fase 8)
        Route::get('/exports/bnsp', [BnspExportController::class, 'index'])->name('exports.bnsp.index');
        Route::get('/exports/bnsp/download', [BnspExportController::class, 'export'])->name('exports.bnsp.download');
        Route::get('/exports/bnsp/schedule/{schedule}', [BnspExportController::class, 'exportSchedule'])->name('exports.bnsp.schedule');

        Route::get('/complaints', [App\Http\Controllers\Admin\ComplaintController::class, 'index'])->name('complaints.index');
        Route::get('/complaints/{complaint}', [App\Http\Controllers\Admin\ComplaintController::class, 'show'])->name('complaints.show');
        Route::patch('/complaints/{complaint}', [App\Http\Controllers\Admin\ComplaintController::class, 'updateStatus'])->name('complaints.update-status');

        Route::get('/surveillance', [App\Http\Controllers\Admin\SurveillanceController::class, 'index'])->name('surveillance.index');
        Route::patch('/surveillance/{surveillance}/verify', [App\Http\Controllers\Admin\SurveillanceController::class, 'verify'])->name('surveillance.verify');

        // Layanan Banding Asesmen
        Route::get('/appeals', [AppealController::class, 'index'])->name('appeals.index');

        // Laporan & Ekspor Lengkap Parity Demo
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/general', [ReportController::class, 'general'])->name('general');
            Route::get('/monthly', [ReportController::class, 'monthly'])->name('monthly');
            Route::get('/annual', [ReportController::class, 'annual'])->name('annual');
            Route::get('/asesor-performance', [ReportController::class, 'asesorPerformance'])->name('asesor-performance');
            Route::get('/tuk-performance', [ReportController::class, 'tukPerformance'])->name('tuk-performance');
            Route::get('/certificates', [ReportController::class, 'certificates'])->name('certificates');
        });

        // Penjaminan Mutu & Audit Internal
        Route::prefix('quality')->name('quality.')->group(function () {
            Route::get('/documents', [QualityController::class, 'documents'])->name('documents.index');
            Route::post('/documents', [QualityController::class, 'storeDocument'])->name('documents.store');
            Route::get('/audits', [QualityController::class, 'audits'])->name('audits.index');
            Route::post('/audits', [QualityController::class, 'storeAudit'])->name('audits.store');
        });

        // Keuangan, Biaya & Rekening Bank
        Route::prefix('finance')->name('finance.')->group(function () {
            Route::get('/bank-accounts', [FinanceController::class, 'bankAccounts'])->name('bank-accounts.index');
            Route::post('/bank-accounts', [FinanceController::class, 'storeBankAccount'])->name('bank-accounts.store');
            Route::get('/pricing', [FinanceController::class, 'pricing'])->name('pricing');
            Route::get('/transactions', [FinanceController::class, 'transactions'])->name('transactions.index');
            Route::post('/transactions', [FinanceController::class, 'storeTransaction'])->name('transactions.store');
            Route::get('/balance-sheet', [FinanceController::class, 'balanceSheet'])->name('balance-sheet');
            Route::get('/income-statement', [FinanceController::class, 'incomeStatement'])->name('income-statement');
        });

        // Persuratan Resmi LSP
        Route::get('/letters', [LetterController::class, 'index'])->name('letters.index');
        Route::post('/letters', [LetterController::class, 'store'])->name('letters.store');

        // Log SMS & Notifikasi Masuk/Keluar
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/sent', [MessageController::class, 'sent'])->name('sent');
            Route::get('/inbox', [MessageController::class, 'inbox'])->name('inbox');
        });

        // Asesi Submodules
        Route::prefix('asesi')->name('asesi.')->group(function () {
            Route::get('/unscheduled', [AsesiSubmoduleController::class, 'unscheduled'])->name('unscheduled');
            Route::get('/scheduled', [AsesiSubmoduleController::class, 'scheduled'])->name('scheduled');
            Route::get('/competent', [AsesiSubmoduleController::class, 'competent'])->name('competent');
            Route::get('/incompetent', [AsesiSubmoduleController::class, 'incompetent'])->name('incompetent');
        });
    });

    // 2. Asesor Kompetensi Area
    Route::middleware('role:asesor,ASESOR')->prefix('asesor')->name('asesor.')->group(function () {
        Route::get('/dashboard', function () {
            return app(App\Http\Controllers\Asesor\JadwalController::class)->index();
        })->name('dashboard');

        Route::get('/profile', [App\Http\Controllers\Asesor\ProfileController::class, 'edit'])->name('profile.edit');
        Route::get('/profile/show', [App\Http\Controllers\Asesor\ProfileController::class, 'edit'])->name('profile.show');
        Route::match(['post', 'put'], '/profile', [App\Http\Controllers\Asesor\ProfileController::class, 'update'])->name('profile.update');

        Route::get('/jadwal', [App\Http\Controllers\Asesor\JadwalController::class, 'index'])->name('jadwal.index');
        Route::get('/jadwal/{schedule}/asesi', [App\Http\Controllers\Asesor\JadwalController::class, 'asesiList'])->name('jadwal.asesi');

        // Workspace Terpadu Asesor (Single Unified Workspace per Asesi)
        Route::get('/assessment/{assessment}/workspace', [App\Http\Controllers\Asesor\WorkspaceController::class, 'show'])->name('assessment.workspace');

        // Review APL (both aliases supported)
        Route::get('/assessment/{assessment}/review-apl', [AplReviewController::class, 'show'])->name('apl.review');
        Route::post('/assessment/{assessment}/review-apl', [AplReviewController::class, 'decide'])->name('apl.decide');
        Route::get('/review-apl/{assessment}', [AplReviewController::class, 'show'])->name('review-apl');
        Route::post('/review-apl/{assessment}', [AplReviewController::class, 'decide'])->name('review-apl.update');

        // AK01
        Route::get('/schedule/{schedule}/ak01', [InstrumentController::class, 'ak01Schedule'])->name('schedule.ak01');
        Route::post('/ak-01-sign/{id}', function (Request $request, $id) {
            if ($assessment = Assessment::find($id)) {
                return app(InstrumentController::class)->ak01Sign($assessment, $request);
            }
            if ($app = AssessmentApplication::find($id)) {
                return app(AssessmentExecutionController::class)->signAk01($request, $app);
            }
            abort(404);
        })->name('ak01.sign');
        Route::post('/assessment/{assessment}/ak01/sign', [InstrumentController::class, 'ak01Sign']);
        Route::post('/applications/{application}/ak-01/sign', [AssessmentExecutionController::class, 'signAk01']);

        // AK07 Penyesuaian Wajar (Single & Batch)
        Route::get('/schedule/{schedule}/ak07', [Ak07Controller::class, 'index'])->name('schedule.ak07');
        Route::post('/schedule/{schedule}/ak07', [Ak07Controller::class, 'store'])->name('schedule.ak07.store');
        Route::get('/schedule/{schedule}/ak07/create', [Ak07Controller::class, 'index'])->name('ak07.create');
        Route::post('/schedule/{schedule}/ak07/create', [Ak07Controller::class, 'store'])->name('ak07.store');

        // Ruang Uji Jadwal (Schedule-level: Displays Selected Instruments)
        Route::get('/schedule/{schedule}/ruang-uji', [InstrumentController::class, 'scheduleRuangUji'])->name('schedule.ruang-uji');

        // Instrument Asesi List (Displays Asesis for a Selected Instrument)
        Route::get('/schedule/{schedule}/instrument/{code}', [InstrumentController::class, 'scheduleInstrument'])->name('schedule.instrument');

        // Ruang Uji Asesor per Assessment
        Route::get('/assessment/{assessment}/ruang-uji', [InstrumentController::class, 'hub'])->name('assessment.ruang-uji');

        // Generic Instrument routes for AK01, IA01, IA01B, IA02, IA03, IA05
        Route::get('/assessment/{assessment}/instrument/{code}', [InstrumentController::class, 'instrumentShow'])
            ->whereIn('code', ['ak01', 'ia01', 'ia01b', 'ia02', 'ia03', 'ia05', 'AK01', 'IA01', 'IA01B', 'IA02', 'IA03', 'IA05'])
            ->name('assessment.instrument');
        Route::post('/assessment/{assessment}/instrument/{code}', [InstrumentController::class, 'instrumentStore'])
            ->whereIn('code', ['ak01', 'ia01', 'ia01b', 'ia02', 'ia03', 'ia05', 'AK01', 'IA01', 'IA01B', 'IA02', 'IA03', 'IA05'])
            ->name('assessment.instrument.store');

        // AK02 Rekaman Asesmen
        Route::get('/ak-02-form/{id}', function ($id) {
            if ($assessment = Assessment::find($id)) {
                return app(FinalizationController::class)->showAk02($assessment);
            }
            if ($app = AssessmentApplication::find($id)) {
                return app(AssessmentExecutionController::class)->showAk02($app);
            }
            abort(404);
        })->name('ak02.form');
        Route::post('/ak-02-store/{id}', function (Request $request, $id) {
            if ($assessment = Assessment::find($id)) {
                return app(FinalizationController::class)->updateAk02($assessment, $request);
            }
            if ($app = AssessmentApplication::find($id)) {
                return app(AssessmentExecutionController::class)->storeAk02($request, $app);
            }
            abort(404);
        })->name('ak02.store');
        Route::get('/assessment/{assessment}/ak02', [FinalizationController::class, 'showAk02'])->name('assessment.ak02');
        Route::post('/assessment/{assessment}/ak02', [FinalizationController::class, 'updateAk02'])->name('assessment.ak02.update');
        Route::get('/applications/{application}/ak-02', [AssessmentExecutionController::class, 'showAk02']);
        Route::post('/applications/{application}/ak-02', [AssessmentExecutionController::class, 'storeAk02']);

        // AK05 & AK06 Reports
        Route::get('/ak-05-report/{id}', function ($id) {
            if ($schedule = Schedule::find($id)) {
                return app(FinalizationController::class)->showReports($schedule);
            }
            if ($sched = AssessmentSchedule::find($id)) {
                return app(AssessmentExecutionController::class)->showAk05($sched);
            }
            abort(404);
        })->name('ak05.report');
        Route::post('/ak-05-store/{id}', function (Request $request, $id) {
            if ($schedule = Schedule::find($id)) {
                return app(FinalizationController::class)->submitReports($schedule, $request);
            }
            if ($sched = AssessmentSchedule::find($id)) {
                return app(AssessmentExecutionController::class)->storeAk05($request, $sched);
            }
            abort(404);
        })->name('ak05.store');
        Route::get('/schedule/{schedule}/reports', [FinalizationController::class, 'showReports'])->name('schedule.reports');
        Route::post('/schedule/{schedule}/reports', [FinalizationController::class, 'submitReports'])->name('schedule.reports.submit');
        Route::get('/schedules/{schedule}/ak-05', [AssessmentExecutionController::class, 'showAk05']);
        Route::post('/schedules/{schedule}/ak-05', [AssessmentExecutionController::class, 'storeAk05']);

        // Finalize
        Route::post('/schedule/{schedule}/finalize', [FinalizationController::class, 'finalize'])->name('schedule.finalize');
        Route::post('/assessment/{assessment}/complete', [FinalizationController::class, 'completeAssessment'])->name('assessment.complete');
        Route::post('/assessment/{assessment}/mark-absent', [FinalizationController::class, 'markAbsent'])->name('assessment.mark-absent');

        // Extended parity submodules
        Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/schedules/{schedule}', [ScheduleController::class, 'show'])->name('schedules.show');
        Route::get('/tuks', [TukVerificationController::class, 'index'])->name('tuks.index');
        Route::post('/tuks', [TukVerificationController::class, 'store'])->name('tuks.store');
        Route::get('/instruments', [InstrumentReviewController::class, 'index'])->name('instruments.index');
        Route::post('/instruments', [InstrumentReviewController::class, 'store'])->name('instruments.store');
        Route::get('/mkva', [MkvaController::class, 'index'])->name('mkva.index');
        Route::post('/mkva', [MkvaController::class, 'store'])->name('mkva.store');
        Route::get('/messages', [NotificationController::class, 'index'])->name('messages.index');
        Route::get('/apl-02', [Apl02ReviewController::class, 'index'])->name('apl02.index');
        Route::get('/apl-02/{apl02}/review', [Apl02ReviewController::class, 'review'])->name('apl02.review');
        Route::put('/apl-02/{apl02}', [Apl02ReviewController::class, 'update'])->name('apl02.update');
    });

    // Secure Document & Evidence Access
    Route::get('/secure-file/proof/{proof}', [SecureFileController::class, 'showProof'])->name('secure-file.proof');
    Route::get('/secure-file/evidence/{response}', [SecureFileController::class, 'showEvidence'])->name('secure-file.evidence');

    // 3. Asesi Mandiri Area (Fase 4, 5, 6)
    Route::middleware('role:asesi,ASESI')->prefix('asesi')->name('asesi.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Asesi\JadwalController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [App\Http\Controllers\Asesi\ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile', [App\Http\Controllers\Asesi\ProfileController::class, 'update'])->name('profile.update');

        // Jadwal
        Route::get('/jadwal', [App\Http\Controllers\Asesi\JadwalController::class, 'index'])->name('jadwal.index');
        Route::post('/jadwal/join', [App\Http\Controllers\Asesi\JadwalController::class, 'join'])->middleware('throttle:10,1')->name('jadwal.join');

        // Asesmen & APL
        Route::get('/assessment/{assessment}/apl', [AplController::class, 'show'])->name('assessment.apl');
        Route::post('/assessment/{assessment}/apl', [AplController::class, 'store'])->name('assessment.apl.store');

        // Ruang Uji (Protected by APL Approval Gate)
        Route::middleware(['apl.approved'])->prefix('assessment/{assessment}')->name('assessment.')->group(function () {
            Route::get('/ak01', [RuangUjiController::class, 'ak01'])->name('ak01');
            Route::post('/ak01', [RuangUjiController::class, 'signAk01'])->name('ak01.sign');
            Route::post('/consent-esign', [RuangUjiController::class, 'consentEsign'])->name('consent-esign');

            Route::get('/ruang-uji', [RuangUjiController::class, 'hub'])->name('ruang-uji');

            // FR.IA Instruments
            Route::get('/instrument/IA05', [RuangUjiController::class, 'ia05'])->name('instrument.ia05');
            Route::post('/instrument/IA05', [RuangUjiController::class, 'submitIa05'])->name('instrument.ia05.submit');
            Route::post('/ia05/save-draft', [RuangUjiController::class, 'saveIa05Draft'])->name('ia05.save-draft');

            Route::post('/instrument/{code}/upload-evidence', [RuangUjiController::class, 'uploadEvidence'])->name('instrument.upload-evidence');

            Route::get('/ak03', [RuangUjiController::class, 'showAk03'])->name('ak03');
            Route::post('/ak03', [RuangUjiController::class, 'submitAk03'])->name('ak03.submit');
            Route::get('/appeal', [RuangUjiController::class, 'showAppeal'])->name('appeal');
            Route::post('/appeal', [RuangUjiController::class, 'submitAppeal'])->name('appeal.submit');
        });

        // Backward compatibility routes for legacy application links
        Route::resource('/applications', ApplicationController::class)->except(['destroy']);
        Route::get('/applications/{application}/apl-02/create', [Apl02Controller::class, 'create'])->name('applications.apl02.create');
        Route::post('/applications/{application}/apl-02', [Apl02Controller::class, 'store'])->name('applications.apl02.store');
        Route::get('/applications/{application}/apl-02', [Apl02Controller::class, 'show'])->name('applications.apl02.show');
        Route::get('/applications/{application}/ak-01', [App\Http\Controllers\Asesi\AssessmentExecutionController::class, 'showAk01'])->name('applications.ak01');
        Route::post('/applications/{application}/ak-01/sign', [App\Http\Controllers\Asesi\AssessmentExecutionController::class, 'signAk01'])->name('applications.ak01.sign');
        Route::get('/applications/{application}/ak-02', [App\Http\Controllers\Asesi\AssessmentExecutionController::class, 'showAk02'])->name('applications.ak02');
        Route::post('/applications/{application}/ak-02/sign', [App\Http\Controllers\Asesi\AssessmentExecutionController::class, 'signAk02'])->name('applications.ak02.sign');
        Route::get('/applications/{application}/ak-03', [App\Http\Controllers\Asesi\AssessmentExecutionController::class, 'showAk03'])->name('applications.ak03');
        Route::post('/applications/{application}/ak-03', [App\Http\Controllers\Asesi\AssessmentExecutionController::class, 'storeAk03'])->name('applications.ak03.store');
    });

    /*
    |--------------------------------------------------------------------------
    | Dokumen Cetak Resmi BNSP & Generator PDF (Fase 7)
    |--------------------------------------------------------------------------
    */
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/surat-tugas/{schedule}/{scheduleAsesor}', [PdfDocumentController::class, 'suratTugas'])->name('surat-tugas');
        Route::get('/berita-acara-asesmen/{schedule}', [PdfDocumentController::class, 'beritaAcaraAsesmen'])->name('berita-acara-asesmen');
        Route::get('/daftar-hadir-asesmen/{schedule}', [PdfDocumentController::class, 'daftarHadirAsesmen'])->name('daftar-hadir-asesmen');
        Route::get('/berita-acara-pleno/{schedule}', [PdfDocumentController::class, 'beritaAcaraPleno'])->name('berita-acara-pleno');
        Route::get('/daftar-hadir-pleno/{schedule}', [PdfDocumentController::class, 'daftarHadirPleno'])->name('daftar-hadir-pleno');
        Route::get('/fr-apl-01/{application}', [PdfDocumentController::class, 'frApl01'])->name('fr-apl-01');
        Route::get('/fr-apl-02/{application}', [PdfDocumentController::class, 'frApl02'])->name('fr-apl-02');
        Route::get('/fr-ak-01/{application}', [PdfDocumentController::class, 'frAk01'])->name('fr-ak-01');
        Route::get('/fr-ak-02/{application}', [PdfDocumentController::class, 'frAk02'])->name('fr-ak-02');
        Route::get('/fr-ak-03/{application}', [PdfDocumentController::class, 'frAk03'])->name('fr-ak-03');
        Route::get('/fr-ak-05/{schedule}', [PdfDocumentController::class, 'frAk05'])->name('fr-ak-05');
        Route::get('/sertifikat/{application}', [PdfDocumentController::class, 'sertifikat'])->name('sertifikat');
    });
});

/*
|--------------------------------------------------------------------------
| Wilayah Indonesia API (Cascading Dropdown)
|--------------------------------------------------------------------------
*/
Route::prefix('api/wilayah')->name('api.wilayah.')->group(function () {
    Route::get('/provinces', [WilayahController::class, 'provinces'])->name('provinces');
    Route::get('/regencies', [WilayahController::class, 'regencies'])->name('regencies');
    Route::get('/districts', [WilayahController::class, 'districts'])->name('districts');
    Route::get('/villages', [WilayahController::class, 'villages'])->name('villages');
});
