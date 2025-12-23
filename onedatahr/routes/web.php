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
use App\Http\Controllers\WigRekrutmenController;
use App\Http\Controllers\PosisiController;
use App\Http\Controllers\RekrutmenDailyController;
use App\Http\Controllers\RekrutmenCalendarController;
// Import Controller yang sebelumnya tertinggal agar tidak error class not found
use App\Http\Controllers\PelamarHarianController;
use App\Http\Controllers\ScreeningCvController;
use App\Http\Controllers\TesKompetensiController;
use App\Http\Controllers\InterviewHrController;
use App\Http\Controllers\InterviewUserController;
use App\Http\Controllers\SummaryController;

// Minimal routes for One Data HR
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard.index') : redirect()->route('signin');
});

// Logika sinkronisasi manual
// $kandidatGroups = Kandidat::select('posisi_id', 'status_akhir', \DB::raw('DATE(updated_at) as date'), \DB::raw('count(*) as total'))
//     ->groupBy('posisi_id', 'status_akhir', 'date')
//     ->get();

// foreach ($kandidatGroups as $group) {
//     // Masukkan ke tabel rekrutmen_daily
// }

//Delete Batch Karyawan
Route::delete('/karyawan/batch-delete', [KaryawanController::class, 'batchDelete'])->name('karyawan.batchDelete');
// Authentication pages
Route::get('/signin', function () {
    return view('pages.auth.signin', ['title' => 'Sign In']);
})->name('signin');

Route::post('/signin', [AuthController::class, 'login'])->name('signin.post');
Route::post('/signout', [AuthController::class, 'logout'])->name('signout');

// Dashboard home (require auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Karyawan resource
    Route::resource('karyawan', KaryawanController::class);

    Route::resource('wig-rekrutmen', WigRekrutmenController::class);

    // Route::resource('interview_hr', InterviewHrController::class);



    // Recruitment / Kandidat resources and metrics
    Route::prefix('rekrutmen')->name('rekrutmen.')->group(function(){
        Route::get('/', [RecruitmentDashboardController::class, 'index'])->name('dashboard');

        // PERBAIKAN DI SINI: Nama route diubah agar sesuai dengan yang dipanggil di View [rekrutmen.wig.index]
        Route::get('wig', [WigRekrutmenController::class, 'index'])->name('wig.index');
        Route::put('wig/{posisiId}', [WigRekrutmenController::class, 'update'])->name('wig.update');

        Route::get('pelamar', [PelamarHarianController::class,'index'])->name('pelamar');
        Route::post('pelamar', [PelamarHarianController::class,'store'])->name('pelamar.store');

        Route::get('screening-cv', [ScreeningCvController::class,'index'])->name('screening-cv');
        Route::get('tes-kompetensi', [TesKompetensiController::class,'index'])->name('tes-kompetensi');
        Route::resource(
                        'interview_hr',
                        InterviewHrController::class
                    )->names('interview_hr');

        Route::get('interview-user', [InterviewUserController::class,'index'])->name('interview-user');

        Route::get('summary', [SummaryController::class,'index'])->name('summary');

        // Metrics endpoints (JSON)
        Route::get('/metrics/candidates', [RecruitmentDashboardController::class,'candidatesByPositionMonth'])->name('metrics.candidates');
        Route::get('/metrics/cv', [RecruitmentDashboardController::class,'cvPassedByPositionMonth'])->name('metrics.cv');
        Route::get('/metrics/cv/export', [RecruitmentDashboardController::class,'exportCvCsv'])->name('metrics.cv.export');
        Route::get('/metrics/psikotes', [RecruitmentDashboardController::class,'psikotesPassedByPosition'])->name('metrics.psikotes');
        Route::get('/metrics/psikotes/export', [RecruitmentDashboardController::class,'exportPsikotesCsv'])->name('metrics.psikotes.export');
        Route::get('/metrics/kompetensi', [RecruitmentDashboardController::class,'kompetensiPassedByPosition'])->name('metrics.kompetensi');
        Route::get('/metrics/interview_hr', [RecruitmentDashboardController::class,'interviewHrPassedByPositionMonth'])->name('metrics.hr');
        Route::get('/metrics/interview-user', [RecruitmentDashboardController::class,'interviewUserPassedByPositionMonth'])->name('metrics.user');

        // Pages for per-stage metrics
        Route::get('/metrics/cv-page', [RecruitmentDashboardController::class,'cvPage'])->name('metrics.cv.page');
        Route::get('/metrics/psikotes-page', [RecruitmentDashboardController::class,'psikotesPage'])->name('metrics.psikotes.page');
        Route::get('/metrics/kompetensi-page', [RecruitmentDashboardController::class,'kompetensiPage'])->name('metrics.kompetensi.page');
        Route::get('/metrics/interview_hr-page', [RecruitmentDashboardController::class,'interviewHrPage'])->name('metrics.hr.page');
        Route::get('/metrics/interview-user-page', [RecruitmentDashboardController::class,'interviewUserPage'])->name('metrics.user.page');
        Route::get('/metrics/progress', [RecruitmentDashboardController::class,'recruitmentProgressByPosition'])->name('metrics.progress');
        Route::get('/metrics/progress/export', [RecruitmentDashboardController::class,'exportProgressCsv'])->name('metrics.progress.export');
        Route::get('/metrics/pemberkasan', [RecruitmentDashboardController::class,'pemberkasanProgress'])->name('metrics.pemberkasan');
        Route::get('/metrics/pemberkasan-page', [RecruitmentDashboardController::class,'pemberkasanPage'])->name('metrics.pemberkasan.page');
        Route::get('/metrics/candidates/export', [RecruitmentDashboardController::class,'exportCandidatesCsv'])->name('metrics.candidates.export');

        // CRUD
        Route::resource('kandidat', KandidatController::class);
        Route::get('proses/{kandidat_id}/edit', [ProsesRekrutmenController::class,'edit'])->name('proses.edit');
        Route::post('proses', [ProsesRekrutmenController::class,'store'])->name('proses.store');

        // Perbaikan: Hindari double naming untuk resource
        Route::resource('pemberkasan', PemberkasanController::class)->only(['index','create','store','edit','update']);

        // Posisi management
        Route::get('posisi/list', [PosisiController::class, 'index'])->name('posisi.list');
        Route::post('posisi', [PosisiController::class, 'store'])->name('posisi.store');
        Route::get('kandidat/list', [KandidatController::class, 'list'])->name('kandidat.list');
        Route::get('posisi-manage', [PosisiController::class, 'manage'])->name('posisi.index');
        Route::put('posisi/{id}', [PosisiController::class, 'update'])->name('posisi.update');
        Route::delete('posisi/{id}', [PosisiController::class, 'destroy'])->name('posisi.destroy');

        // Daily recruitment metrics
        Route::get('daily', [RekrutmenDailyController::class, 'index'])->name('daily.index');
        Route::get('calendar', [RecruitmentDashboardController::class, 'calendarPage'])->name('calendar');
        Route::post('daily', [RekrutmenDailyController::class, 'store'])->name('daily.store');
        Route::put('daily/{id}', [RekrutmenDailyController::class, 'update'])->name('daily.update');
        Route::delete('daily/{id}', [RekrutmenDailyController::class, 'destroy'])->name('daily.destroy');

        // Daily entries
        Route::get('daily/entries', [RekrutmenCalendarController::class, 'index'])->name('daily.entries.index');
        Route::post('daily/entries', [RekrutmenCalendarController::class, 'store'])->name('daily.entries.store');
        Route::delete('daily/entries/{id}', [RekrutmenCalendarController::class, 'destroy'])->name('daily.entries.destroy');
    });

    // KPI Assessment Routes (Pindahkan ke dalam Middleware Auth agar aman)
    Route::prefix('kpi')->name('kpi.')->group(function() {
        Route::get('/dashboard', [KpiAssessmentController::class, 'index'])->name('index');
        Route::delete('/delete/{id}', [KpiAssessmentController::class, 'destroy'])->name('destroy');
        Route::post('/store', [KpiAssessmentController::class, 'store'])->name('store');
        Route::get('/penilaian/{karyawan_id}/{tahun}', [KpiAssessmentController::class, 'show'])->name('show');
        Route::post('/update/{id}', [KpiAssessmentController::class, 'update'])->name('update');
    });
});
