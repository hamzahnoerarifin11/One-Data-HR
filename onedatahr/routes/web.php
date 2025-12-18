<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\RecruitmentDashboardController;
use App\Http\Controllers\KandidatController;
use App\Http\Controllers\ProsesRekrutmenController;
// use App\Http\Controllers\PemberkasanController;
use App\Http\Controllers\Rekrutmen\PosisiController;
use App\Http\Controllers\Rekrutmen\PelamarHarianController;
use App\Http\Controllers\Rekrutmen\ScreeningCvController;
use App\Http\Controllers\Rekrutmen\TesKompetensiController;
use App\Http\Controllers\Rekrutmen\InterviewHrController;
use App\Http\Controllers\Rekrutmen\InterviewUserController;
use App\Http\Controllers\Rekrutmen\SummaryController;
use App\Http\Controllers\Rekrutmen\PemberkasanController;
use App\Http\Controllers\Rekrutmen\WigRekrutmenController;


// Minimal routes for One Data HR
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard.index') : redirect()->route('signin');
});

// authentication pages
Route::get('/signin', function () {
    return view('pages.auth.signin', ['title' => 'Sign In']);
})->name('signin');

// auth post route
Route::post('/signin', [AuthController::class, 'login'])->name('signin.post');
Route::post('/signout', [AuthController::class, 'logout'])->name('signout');

// dashboard home (require auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // karyawan resource
    Route::resource('karyawan', KaryawanController::class);

    // recruitment / kandidat resources and metrics

    Route::prefix('rekrutmen')->name('rekrutmen.')->group(function(){
        Route::get('/', [RecruitmentDashboardController::class, 'index'])->name('dashboard');

        Route::get('wig', [WigRekrutmenController::class, 'index'])
        ->name('rekrutmen.wig');

        Route::put('wig/{posisiId}', [WigRekrutmenController::class, 'update']);
        Route::get('pelamar', [PelamarHarianController::class,'index'])
        ->name('rekrutmen.pelamar');
        Route::post('pelamar', [PelamarHarianController::class,'store']);


        Route::get('screening-cv', [ScreeningCvController::class,'index']);
        Route::get('tes-kompetensi', [TesKompetensiController::class,'index']);
        Route::get('interview-hr', [InterviewHrController::class,'index']);
        Route::get('interview-user', [InterviewUserController::class,'index']);


        Route::get('summary', [SummaryController::class,'index']);
        Route::get('pemberkasan', [PemberkasanController::class,'index']);
        // metrics endpoints (JSON)
        Route::get('/metrics/candidates', [RecruitmentDashboardController::class,'candidatesByPositionMonth'])->name('metrics.candidates');
        Route::get('/metrics/cv', [RecruitmentDashboardController::class,'cvPassedByPositionMonth'])->name('metrics.cv');
        Route::get('/metrics/cv/export', [RecruitmentDashboardController::class,'exportCvCsv'])->name('metrics.cv.export');
        Route::get('/metrics/psikotes', [RecruitmentDashboardController::class,'psikotesPassedByPosition'])->name('metrics.psikotes');
        Route::get('/metrics/psikotes/export', [RecruitmentDashboardController::class,'exportPsikotesCsv'])->name('metrics.psikotes.export');
        Route::get('/metrics/kompetensi', [RecruitmentDashboardController::class,'kompetensiPassedByPosition'])->name('metrics.kompetensi');
        Route::get('/metrics/interview-hr', [RecruitmentDashboardController::class,'interviewHrPassedByPositionMonth'])->name('metrics.hr');
        Route::get('/metrics/interview-user', [RecruitmentDashboardController::class,'interviewUserPassedByPositionMonth'])->name('metrics.user');

        // pages for per-stage metrics
        Route::get('/metrics/cv-page', [RecruitmentDashboardController::class,'cvPage'])->name('metrics.cv.page');
        Route::get('/metrics/psikotes-page', [RecruitmentDashboardController::class,'psikotesPage'])->name('metrics.psikotes.page');
        Route::get('/metrics/kompetensi-page', [RecruitmentDashboardController::class,'kompetensiPage'])->name('metrics.kompetensi.page');
        Route::get('/metrics/interview-hr-page', [RecruitmentDashboardController::class,'interviewHrPage'])->name('metrics.hr.page');
        Route::get('/metrics/interview-user-page', [RecruitmentDashboardController::class,'interviewUserPage'])->name('metrics.user.page');
        Route::get('/metrics/progress', [RecruitmentDashboardController::class,'recruitmentProgressByPosition'])->name('metrics.progress');
        Route::get('/metrics/progress/export', [RecruitmentDashboardController::class,'exportProgressCsv'])->name('metrics.progress.export');
        Route::get('/metrics/pemberkasan', [RecruitmentDashboardController::class,'pemberkasanProgress'])->name('metrics.pemberkasan');
        Route::get('/metrics/pemberkasan-page', [RecruitmentDashboardController::class,'pemberkasanPage'])->name('metrics.pemberkasan.page');
        // CSV export
        Route::get('/metrics/candidates/export', [RecruitmentDashboardController::class,'exportCandidatesCsv'])->name('metrics.candidates.export');

        // CRUD
        Route::resource('kandidat', KandidatController::class);
        Route::get('proses/{kandidat_id}/edit', [ProsesRekrutmenController::class,'edit'])->name('proses.edit');
        Route::post('proses', [ProsesRekrutmenController::class,'store'])->name('proses.store');
        Route::resource('pemberkasan', PemberkasanController::class)->only(['index','create','store','edit','update']);

        // posisi - small API for listing and creating positions (used by dashboard filters)
        Route::get('posisi/list', [\App\Http\Controllers\PosisiController::class, 'index'])->name('posisi.list');
        Route::post('posisi', [\App\Http\Controllers\PosisiController::class, 'store'])->name('posisi.store');
        // posisi management (page + update/delete)
        Route::get('posisi', [\App\Http\Controllers\PosisiController::class, 'manage'])->name('posisi.index');
        Route::put('posisi/{id}', [\App\Http\Controllers\PosisiController::class, 'update'])->name('posisi.update');
        Route::delete('posisi/{id}', [\App\Http\Controllers\PosisiController::class, 'destroy'])->name('posisi.destroy');

        // daily recruitment metrics (calendar data, per-posisi daily counts)
        Route::get('daily', [\App\Http\Controllers\RekrutmenDailyController::class, 'index'])->name('daily.index');
        Route::get('calendar', [\App\Http\Controllers\RecruitmentDashboardController::class, 'calendarPage'])->name('calendar');
        Route::post('daily', [\App\Http\Controllers\RekrutmenDailyController::class, 'store'])->name('daily.store');
        Route::put('daily/{id}', [\App\Http\Controllers\RekrutmenDailyController::class, 'update'])->name('daily.update');
        Route::delete('daily/{id}', [\App\Http\Controllers\RekrutmenDailyController::class, 'destroy'])->name('daily.destroy');
    });
});






















