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
use App\Http\Controllers\TurnoverController;



// Minimal routes for One Data HR
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard.index') : redirect()->route('signin');
});


//Delete Batch Karyawan

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
    // Route::middleware(['auth','role:superadmin,admin'])->group(function () {
    //     Route::resource('karyawan', KaryawanController::class);
    //     Route::delete('/karyawan/batch-delete', [KaryawanController::class, 'batchDelete'])->name('karyawan.batchDelete');
    // });
Route::middleware(['auth', 'role:admin|superadmin'])->group(function () {
        // --- KARYAWAN MANAGEMENT ---
    Route::post('karyawan/batch-delete', [KaryawanController::class, 'batchDelete'])->name('karyawan.batchDelete');
    Route::resource('karyawan', KaryawanController::class);

    // --- REKRUTMEN MODULE ---
    Route::prefix('rekrutmen')->name('rekrutmen.')->group(function () {

        // Dashboards & Main Pages
        Route::get('/', [RecruitmentDashboardController::class, 'index'])->name('dashboard');
        Route::get('summary', [SummaryController::class, 'index'])->name('summary');
        Route::get('calendar', [RecruitmentDashboardController::class, 'calendarPage'])->name('calendar');

        // WIG & Positions
        Route::get('wig', [WigRekrutmenController::class, 'index'])->name('wig.index');
        Route::put('wig/{posisiId}', [WigRekrutmenController::class, 'update'])->name('wig.update');
        Route::get('posisi/list', [PosisiController::class, 'index'])->name('posisi.list');
        Route::post('posisi', [PosisiController::class, 'store'])->name('posisi.store');
        Route::get('posisi-manage', [PosisiController::class, 'manage'])->name('posisi.index');
        Route::put('posisi/{id}', [PosisiController::class, 'update'])->name('posisi.update');
        Route::delete('posisi/{id}', [PosisiController::class, 'destroy'])->name('posisi.destroy');


        // Pelamar & Tahapan
        Route::resource('pelamar', PelamarHarianController::class)->only(['index', 'store']);
        Route::get('screening-cv', [ScreeningCvController::class, 'index'])->name('screening-cv');
        Route::get('tes-kompetensi', [TesKompetensiController::class, 'index'])->name('tes-kompetensi');
        Route::resource('interview_hr', InterviewHrController::class);
        Route::get('interview-user', [InterviewUserController::class, 'index'])->name('interview-user');
        Route::resource('kandidat_lanjut_user', KandidatLanjutUserController::class);
        Route::resource('pemberkasan', PemberkasanController::class);

        // Kandidat CRUD & Exports
        Route::get('kandidat/list', [KandidatController::class, 'list'])->name('kandidat.list');
        Route::get('kandidat/{id}/preview-excel', [KandidatController::class, 'previewExcel'])->name('kandidat.preview-excel');
        Route::get('kandidat/{id}/laporan', [KandidatController::class, 'generateLaporan'])->name('kandidat.laporan');
        Route::get('kandidat/download-excel/{id}', [KandidatController::class, 'downloadExcel'])->name('kandidat.downloadExcel');
        Route::get('kandidat/export-pdf/{id}', [KandidatController::class, 'exportExcelToPdf'])->name('kandidat.export-pdf');
        Route::resource('kandidat', KandidatController::class);

        // Proses Rekrutmen
        Route::get('proses/{kandidat_id}/edit', [ProsesRekrutmenController::class, 'edit'])->name('proses.edit');
        Route::post('proses', [ProsesRekrutmenController::class, 'store'])->name('proses.store');

        // Daily Activity
        Route::resource('daily', RekrutmenDailyController::class);
        Route::resource('daily/entries', RekrutmenCalendarController::class)->names('daily.entries');

        // METRICS & ANALYTICS
        Route::prefix('metrics')->name('metrics.')->group(function () {
            // Data Endpoints (JSON)
            Route::get('candidates', [RecruitmentDashboardController::class, 'candidatesByPositionMonth'])->name('candidates');
            Route::get('cv', [RecruitmentDashboardController::class, 'cvPassedByPositionMonth'])->name('cv');
            Route::get('psikotes', [RecruitmentDashboardController::class, 'psikotesPassedByPosition'])->name('psikotes');
            Route::get('kompetensi', [RecruitmentDashboardController::class, 'kompetensiPassedByPosition'])->name('kompetensi');
            Route::get('interview-hr', [RecruitmentDashboardController::class, 'interviewHrPassedByPositionMonth'])->name('hr');
            Route::get('interview-user', [RecruitmentDashboardController::class, 'interviewUserPassedByPositionMonth'])->name('user');
            Route::get('progress', [RecruitmentDashboardController::class, 'recruitmentProgressByPosition'])->name('progress');
            Route::get('pemberkasan', [RecruitmentDashboardController::class, 'pemberkasanProgress'])->name('pemberkasan');

            // View Pages
            Route::get('cv-page', [RecruitmentDashboardController::class, 'cvPage'])->name('cv.page');
            Route::get('psikotes-page', [RecruitmentDashboardController::class, 'psikotesPage'])->name('psikotes.page');
            Route::get('kompetensi-page', [RecruitmentDashboardController::class, 'kompetensiPage'])->name('kompetensi.page');
            Route::get('interview-hr-page', [RecruitmentDashboardController::class, 'interviewHrPage'])->name('hr.page');
            Route::get('interview-user-page', [RecruitmentDashboardController::class, 'interviewUserPage'])->name('user.page');
            Route::get('pemberkasan-page', [RecruitmentDashboardController::class, 'pemberkasanPage'])->name('pemberkasan.page');

            // Exports
            Route::get('cv/export', [RecruitmentDashboardController::class, 'exportCvCsv'])->name('cv.export');
            Route::get('psikotes/export', [RecruitmentDashboardController::class, 'exportPsikotesCsv'])->name('psikotes.export');
            Route::get('kompetensi/export', [RecruitmentDashboardController::class, 'exportKompetensiCsv'])->name('kompetensi.export');
            Route::get('progress/export', [RecruitmentDashboardController::class, 'exportProgressCsv'])->name('progress.export');
            Route::get('candidates/export', [RecruitmentDashboardController::class, 'exportCandidatesCsv'])->name('candidates.export');
        });
    });

    // --- OTHER ADMIN MODULES ---
    Route::resource('training', TrainingController::class);
    Route::resource('onboarding', OnboardingKaryawanController::class);

    Route::prefix('turnover')->name('turnover.')->group(function () {
        Route::get('/', [TurnoverController::class, 'index'])->name('index');
        Route::get('/export-excel', [TurnoverController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export-pdf', [TurnoverController::class, 'exportPdf'])->name('export.pdf');
    });
});

    // Route::resource('karyawan', KaryawanController::class);

Route::middleware(['auth', 'role:superadmin'])->group(function () {
    // User management resource
    Route::resource('users', UserController::class);
    Route::delete('/users/batch-delete', [UserController::class, 'batchDelete'])->name('users.batchDelete');
    });
Route::middleware(['auth', 'role:admin|superadmin|manager'])->group(function () {
    // User management resource
    // 7. monitoring
    Route::get('/kbi/monitoring', [App\Http\Controllers\KbiController::class, 'monitoring'])->name('kbi.monitoring');
  // --- rekap PERFORMANCE ROUTES ---
    Route::get('/performance/rekap', [App\Http\Controllers\PerformanceController::class, 'index'])->name('performance.rekap');

    });
    // Route::resource('wig-rekrutmen', WigRekrutmenController::class);

    // Recruitment / Kandidat resources and metrics

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


    // Export routes (accessible to all authenticated users)
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
