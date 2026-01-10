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
use App\Http\Controllers\KbiController;
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
use App\Http\Controllers\KandidatLanjutUserController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OnboardingKaryawanController;


// Minimal routes for One Data HR
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard.index') : redirect()->route('signin');
});
Route::resource('training', TrainingController::class)->names('training');

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
    Route::delete('/users/batch-delete', [UserController::class, 'batchDelete'])->name('users.batchDelete');
    Route::resource('users', UserController::class);
    // Karyawan resource
    Route::resource('karyawan', KaryawanController::class);
    Route::resource('onboarding', OnboardingKaryawanController::class);

    // User management resource
    Route::resource('users', UserController::class);

    Route::resource('wig-rekrutmen', WigRekrutmenController::class);

    // Route::resource('interview_hr', InterviewHrController::class);
    // routes/web.php
    // Route::prefix('rekrutmen/kandidat')->name('rekrutmen.kandidat.')->group(function () {
    //     Route::get('{id}/preview-excel', [KandidatController::class, 'previewExcel'])
    //         ->name('previewExcel');

    //     Route::get('{id}/laporan', [KandidatController::class, 'generateLaporan'])
    //         ->name('laporan');
    // });
// routes/web.php
        // Route::prefix('rekrutmen/kandidat')
        //     ->name('rekrutmen.kandidat.')
        //     ->group(function () {

        //         Route::get('/', [KandidatController::class, 'index'])
        //             ->name('index');

        //         Route::post('/', [KandidatController::class, 'store'])
        //             ->name('store');

        //         Route::delete('{id}', [KandidatController::class, 'destroy'])
        //             ->name('destroy');

        //         Route::get('{id}/download-excel', [KandidatController::class, 'downloadExcel'])
        //             ->name('downloadExcel');

        //         Route::get('{id}/preview-excel', [KandidatController::class, 'previewExcel'])
        //             ->name('previewExcel');

        //         Route::get('{id}/laporan', [KandidatController::class, 'generateLaporan'])
        //             ->name('laporan');
        //     });



    // Recruitment / Kandidat resources and metrics
    Route::prefix('rekrutmen')->name('rekrutmen.')->group(function () {
        Route::get('/', [RecruitmentDashboardController::class, 'index'])->name('dashboard');

        // PERBAIKAN DI SINI: Nama route diubah agar sesuai dengan yang dipanggil di View [rekrutmen.wig.index]
        Route::get('wig', [WigRekrutmenController::class, 'index'])->name('wig.index');
        Route::put('wig/{posisiId}', [WigRekrutmenController::class, 'update'])->name('wig.update');

        Route::get('pelamar', [PelamarHarianController::class, 'index'])->name('pelamar');
        Route::post('pelamar', [PelamarHarianController::class, 'store'])->name('pelamar.store');

        Route::get('screening-cv', [ScreeningCvController::class, 'index'])->name('screening-cv');
        Route::get('tes-kompetensi', [TesKompetensiController::class, 'index'])->name('tes-kompetensi');
        Route::resource(
            'interview_hr',
            InterviewHrController::class
        )->names('interview_hr');

        //Kandidat Lanjut User
        Route::resource('kandidat_lanjut_user', KandidatLanjutUserController::class)->names('kandidat_lanjut_user');

        //Pemberkasan
        Route::resource('pemberkasan', PemberkasanController::class)->names('pemberkasan');


        Route::get('interview-user', [InterviewUserController::class, 'index'])->name('interview-user');

        Route::get('summary', [SummaryController::class, 'index'])->name('summary');

        // Metrics endpoints (JSON)
        Route::get('/metrics/candidates', [RecruitmentDashboardController::class,'candidatesByPositionMonth'])->name('metrics.candidates');
        Route::get('/metrics/cv', [RecruitmentDashboardController::class,'cvPassedByPositionMonth'])->name('metrics.cv');
        Route::get('/metrics/cv/export', [RecruitmentDashboardController::class,'exportCvCsv'])->name('metrics.cv.export');
        Route::get('/metrics/psikotes', [RecruitmentDashboardController::class,'psikotesPassedByPosition'])->name('metrics.psikotes');
        Route::get('/metrics/psikotes/export', [RecruitmentDashboardController::class,'exportPsikotesCsv'])->name('metrics.psikotes.export');
        Route::get('/metrics/kompetensi', [RecruitmentDashboardController::class,'kompetensiPassedByPosition'])->name('metrics.kompetensi');
        Route::get(
    '/metrics/kompetensi/export',
    [RecruitmentDashboardController::class, 'exportKompetensiCsv']
)->name('metrics.kompetensi.export');
        Route::get('/metrics/interview_hr', [RecruitmentDashboardController::class,'interviewHrPassedByPositionMonth'])->name('metrics.hr');
        Route::get('/metrics/interview-user', [RecruitmentDashboardController::class,'interviewUserPassedByPositionMonth'])->name('metrics.user');
        Route::get('/metrics/candidates', [RecruitmentDashboardController::class, 'candidatesByPositionMonth'])->name('metrics.candidates');
        Route::get('/metrics/cv', [RecruitmentDashboardController::class, 'cvPassedByPositionMonth'])->name('metrics.cv');
        Route::get('/metrics/cv/export', [RecruitmentDashboardController::class, 'exportCvCsv'])->name('metrics.cv.export');
        Route::get('/metrics/psikotes', [RecruitmentDashboardController::class, 'psikotesPassedByPosition'])->name('metrics.psikotes');
        Route::get('/metrics/psikotes/export', [RecruitmentDashboardController::class, 'exportPsikotesCsv'])->name('metrics.psikotes.export');
        Route::get('/metrics/kompetensi', [RecruitmentDashboardController::class, 'kompetensiPassedByPosition'])->name('metrics.kompetensi');
        Route::get('/metrics/interview_hr', [RecruitmentDashboardController::class, 'interviewHrPassedByPositionMonth'])->name('metrics.hr');
        Route::get('/metrics/interview-user', [RecruitmentDashboardController::class, 'interviewUserPassedByPositionMonth'])->name('metrics.user');

        // Pages for per-stage metrics
        Route::get('/metrics/cv-page', [RecruitmentDashboardController::class, 'cvPage'])->name('metrics.cv.page');
        Route::get('/metrics/psikotes-page', [RecruitmentDashboardController::class, 'psikotesPage'])->name('metrics.psikotes.page');
        Route::get('/metrics/kompetensi-page', [RecruitmentDashboardController::class, 'kompetensiPage'])->name('metrics.kompetensi.page');
        Route::get('/metrics/interview_hr-page', [RecruitmentDashboardController::class, 'interviewHrPage'])->name('metrics.hr.page');
        Route::get('/metrics/interview-user-page', [RecruitmentDashboardController::class, 'interviewUserPage'])->name('metrics.user.page');
        Route::get('/metrics/progress', [RecruitmentDashboardController::class, 'recruitmentProgressByPosition'])->name('metrics.progress');
        Route::get('/metrics/progress/export', [RecruitmentDashboardController::class, 'exportProgressCsv'])->name('metrics.progress.export');
        Route::get('/metrics/pemberkasan', [RecruitmentDashboardController::class, 'pemberkasanProgress'])->name('metrics.pemberkasan');
        Route::get('/metrics/pemberkasan-page', [RecruitmentDashboardController::class, 'pemberkasanPage'])->name('metrics.pemberkasan.page');
        Route::get('/metrics/candidates/export', [RecruitmentDashboardController::class, 'exportCandidatesCsv'])->name('metrics.candidates.export');

        // CRUD
       // Pastikan strukturnya seperti ini
         Route::get('/kandidat/{id}/preview-excel',
    [KandidatController::class, 'previewExcel']
)->name('kandidat.preview-excel');

        Route::get(
            'kandidat/{id}/laporan',
            [KandidatController::class, 'generateLaporan']
        )->name('kandidat.laporan');


        Route::get('kandidat/download-excel/{id}', [KandidatController::class, 'downloadExcel'])->name('kandidat.downloadExcel');
        Route::resource('kandidat', KandidatController::class);
        Route::get('proses/{kandidat_id}/edit', [ProsesRekrutmenController::class, 'edit'])->name('proses.edit');
        Route::post('proses', [ProsesRekrutmenController::class, 'store'])->name('proses.store');

        Route::get('/rekrutmen/kandidat/export-pdf/{id}', [KandidatController::class, 'exportExcelToPdf']);

        Route::get('proses/{kandidat_id}/edit', [ProsesRekrutmenController::class,'edit'])->name('proses.edit');
        Route::post('proses', [ProsesRekrutmenController::class,'store'])->name('proses.store');


        // Perbaikan: Hindari double naming untuk resource
        Route::resource('pemberkasan', PemberkasanController::class)->only(['index', 'create', 'store', 'edit', 'update']);

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

    // --- KBI ROUTES ---
    // 1. Dashboard KBI (Menu Utama untuk memilih siapa yang dinilai)
    Route::get('/kbi/dashboard', [KbiController::class, 'index'])->name('kbi.index');

    // 2. Form Penilaian (Form yang sudah Anda buat)
    // Parameter: id karyawan yg dinilai, dan tipe penilai (DIRI_SENDIRI/ATASAN/BAWAHAN)
    Route::get('/kbi/nilai/{karyawan_id}/{tipe}', [KbiController::class, 'create'])->name('kbi.create');

    // 3. Simpan Data
    Route::post('/kbi/store', [KbiController::class, 'store'])->name('kbi.store');

    // 4. Lihat Hasil Detail (Report)
    Route::get('/kbi/hasil/{id_assessment}', [KbiController::class, 'show'])->name('kbi.show');
    // 5. Update Atasan yang dinilai oleh Karyawan
    Route::post('/kbi/update-atasan', [App\Http\Controllers\KbiController::class, 'updateAtasan'])->name('kbi.update-atasan');
    // 6. Reset Atasan yang dinilai oleh Karyawan
    Route::post('/kbi/reset-atasan', [App\Http\Controllers\KbiController::class, 'resetAtasan'])->name('kbi.reset-atasan');
    // 7. monitoring
    Route::get('/kbi/monitoring', [App\Http\Controllers\KbiController::class, 'monitoring'])->name('kbi.monitoring');

    // --- rekap PERFORMANCE ROUTES ---
    Route::get('/performance/rekap', [App\Http\Controllers\PerformanceController::class, 'index'])->name('performance.rekap');

    Route::get('/kpi/export/excel', [KpiAssessmentController::class, 'exportExcel'])->name('performance.export.excel');
    Route::get('/kpi/export/pdf', [KpiAssessmentController::class, 'exportPdf'])->name('performance.export.pdf');

    // // --- SCRIPT SEMENTARA (HAPUS SETELAH DIPAKAI) ---
    // Route::get('/fix-grades-manual', function () {
    //     // 1. Ambil semua data KPI
    //     $allKpi = \App\Models\KpiAssessment::all();
    //     $count = 0;
    //         if ($skor >= 100) { $grade = 'Outstanding'; }
    //         elseif ($skor >= 90) { $grade = 'Great'; }
    //         elseif ($skor >= 75) { $grade = 'Good'; }
    //         elseif ($skor >= 60) { $grade = 'Enough'; }
    //         // 3. Update Database
    //         $kpi->update(['grade' => $grade]);
    //         $count++;
    //     }
    //     foreach ($allKpi as $kpi) {
    //         // 2. Tentukan Grade berdasarkan Skor yang sudah ada
    //         $skor = $kpi->total_skor_akhir;
    //         $grade = 'Poor'; // Default
    //         if ($skor >= 100) { $grade = 'Outstanding'; }
    //         elseif ($skor >= 90) { $grade = 'Great'; }
    //         elseif ($skor >= 75) { $grade = 'Good'; }
    //         elseif ($skor >= 60) { $grade = 'Enough'; }
    //         // 3. Update Database
    //         $kpi->update(['grade' => $grade]);
    //         $count++;
    //     }
    //     return "Sukses! Berhasil update grade untuk $count data KPI. Silakan kembali ke Dashboard.";
    // });

    // Route khusus untuk menyimpan ITEM KPI baru
    Route::post('/kpi/items/store', [KpiAssessmentController::class, 'storeItem'])->name('kpi.store-item');
    // Route untuk Hapus Item KPI
    Route::delete('/kpi/items/{id}', [KpiAssessmentController::class, 'destroyItem'])->name('kpi.delete-item');
    // Route untuk Update Item KPI
    Route::put('/kpi/items/{id}', [KpiAssessmentController::class, 'updateItem'])->name('kpi.update-item');
});
