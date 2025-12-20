<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\RecruitmentDashboardController;
use App\Http\Controllers\KandidatController;
use App\Http\Controllers\ProsesRekrutmenController;
use App\Http\Controllers\PemberkasanController;
use App\Http\Controllers\KpiAssessmentController;

// Minimal routes for One Data HR
Route::get('/', function () {
    // UBAH: redirect()->route('signin') jadi 'login'
    return auth()->check() ? redirect()->route('dashboard.index') : redirect()->route('login');
});

// authentication pages
Route::get('/signin', function () {
    return view('pages.auth.signin', ['title' => 'Sign In']);
})->name('login'); // <--- PERBAIKAN UTAMA: Ubah 'signin' jadi 'login'

// auth post route
Route::post('/signin', [AuthController::class, 'login'])->name('signin.post');
Route::post('/signout', [AuthController::class, 'logout'])->name('signout');

// dashboard home (require auth)
// Semua route di dalam grup ini TERLINDUNGI (Wajib Login)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // karyawan resource
    Route::resource('karyawan', KaryawanController::class);

    // recruitment / kandidat resources and metrics
    Route::prefix('rekrutmen')->name('rekrutmen.')->group(function(){
        Route::get('/', [RecruitmentDashboardController::class, 'index'])->name('dashboard');

        // metrics endpoints (JSON)
        Route::get('/metrics/candidates', [RecruitmentDashboardController::class,'candidatesByPositionMonth'])->name('metrics.candidates');
        Route::get('/metrics/cv', [RecruitmentDashboardController::class,'cvPassedByPositionMonth'])->name('metrics.cv');
        Route::get('/metrics/cv/export', [RecruitmentDashboardController::class,'exportCvCsv'])->name('metrics.cv.export');
        Route::get('/metrics/psikotes', [RecruitmentDashboardController::class,'psikotesPassedByPosition'])->name('metrics.psikotes');
        Route::get('/metrics/psikotes/export', [RecruitmentDashboardController::class,'exportPsikotesCsv'])->name('metrics.psikotes.export');
        Route::get('/metrics/kompetensi', [RecruitmentDashboardController::class,'kompetensiPassedByPosition'])->name('metrics.kompetensi');
        Route::get('/metrics/interview-hr', [RecruitmentDashboardController::class,'interviewHrPassedByPositionMonth'])->name('metrics.hr');
        Route::get('/metrics/interview-user', [RecruitmentDashboardController::class,'interviewUserPassedByPositionMonth'])->name('metrics.user');
        Route::get('/metrics/progress', [RecruitmentDashboardController::class,'recruitmentProgressByPosition'])->name('metrics.progress');
        Route::get('/metrics/progress/export', [RecruitmentDashboardController::class,'exportProgressCsv'])->name('metrics.progress.export');
        Route::get('/metrics/pemberkasan', [RecruitmentDashboardController::class,'pemberkasanProgress'])->name('metrics.pemberkasan');
        // CSV export
        Route::get('/metrics/candidates/export', [RecruitmentDashboardController::class,'exportCandidatesCsv'])->name('metrics.candidates.export');

        // CRUD
        Route::resource('kandidat', KandidatController::class);
        Route::get('proses/{kandidat_id}/edit', [ProsesRekrutmenController::class,'edit'])->name('proses.edit');
        Route::post('proses', [ProsesRekrutmenController::class,'store'])->name('proses.store');
        Route::resource('pemberkasan', PemberkasanController::class)->only(['index','create','store','edit','update']);
    });

    // --- KPI ROUTES (DIPINDAHKAN KE SINI AGAR AMAN) ---
    
    // Dashboard Monitoring (All Karyawan)
    Route::get('/kpi/dashboard', [KpiAssessmentController::class, 'index'])->name('kpi.index');
    
    // Hapus KPI
    Route::delete('/kpi/delete/{id}', [KpiAssessmentController::class, 'destroy'])->name('kpi.destroy');

    // Generate KPI Baru
    Route::post('/kpi/store', [KpiAssessmentController::class, 'store'])->name('kpi.store');
    
    // KPI Assessment Routes
    // Contoh URL: /kpi/penilaian/5/2025 (Karyawan ID 5, Tahun 2025)
    Route::get('/kpi/penilaian/{karyawan_id}/{tahun}', [KpiAssessmentController::class, 'show'])->name('kpi.show');
    Route::post('/kpi/update/{id}', [KpiAssessmentController::class, 'update'])->name('kpi.update');
    
    Route::post('/kpi/{id}/finalize', [App\Http\Controllers\KpiAssessmentController::class, 'finalize'])->name('kpi.finalize');
});