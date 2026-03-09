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
    Route::get('/dashboard', [DashboardController::class , 'index'])->name('dashboard.index');

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



    // --- SCRIPT SEMENTARA (HAPUS SETELAH DIPAKAI) ---
    Route::get('/fix-grades-manual', function () {
        // 1. Ambil semua data KPI
        $allKpi = \App\Models\KpiAssessment::all();
        $count = 0;

        foreach ($allKpi as $kpi) {
            // 2. Tentukan Grade berdasarkan Skor yang sudah ada
            $skor = $kpi->total_skor_akhir;
            $grade = 'Poor'; // Default

            if ($skor >= 100) { $grade = 'Outstanding'; }
            elseif ($skor >= 90) { $grade = 'Great'; }
            elseif ($skor >= 75) { $grade = 'Good'; }
            elseif ($skor >= 60) { $grade = 'Enough'; }

            // 3. Update Database
            $kpi->update(['grade' => $grade]);
            $count++;
        }
        );

        // --- REKRUTMEN MODULE ---
        Route::prefix('rekrutmen')->name('rekrutmen.')->group(function () {

            // Dashboards & Main Pages
            Route::get('/', [RecruitmentDashboardController::class , 'index'])->name('dashboard');
            Route::get('calendar', [RecruitmentDashboardController::class , 'calendarPage'])->name('calendar');

            // WIG & Positions
            Route::get('wig', [WigRekrutmenController::class , 'index'])->name('wig.index');
            Route::put('wig/{posisiId}', [WigRekrutmenController::class , 'update'])->name('wig.update');
            Route::get('posisi/list', [PosisiController::class , 'index'])->name('posisi.list');
            Route::post('posisi', [PosisiController::class , 'store'])->name('posisi.store');
            Route::get('posisi-manage', [PosisiController::class , 'manage'])->name('posisi.index');
            Route::put('posisi/{id}', [PosisiController::class , 'update'])->name('posisi.update');
            Route::delete('posisi/{id}', [PosisiController::class , 'destroy'])->name('posisi.destroy');
            Route::get('posisi/{id}/download-fpk', [PosisiController::class , 'downloadFpk'])->name('posisi.download-fpk');


            // Pelamar & Tahapan
            // Route::resource('pelamar', PelamarHarianController::class)->only(['index', 'store']);
            // Route::get('screening-cv', [ScreeningCvController::class, 'index'])->name('screening-cv');
            // Route::get('tes-kompetensi', [TesKompetensiController::class, 'index'])->name('tes-kompetensi');
            Route::resource('interview_hr', InterviewHrController::class);
            // Route::get('interview-user', [InterviewUserController::class, 'index'])->name('interview-user');
            Route::resource('kandidat_lanjut_user', KandidatLanjutUserController::class);
            Route::resource('pemberkasan', PemberkasanController::class);

            // Kandidat CRUD & Exports
            Route::get('kandidat/list', [KandidatController::class , 'list'])->name('kandidat.list');
            Route::get('kandidat/{id}/preview-excel', [KandidatController::class , 'previewExcel'])->name('kandidat.preview-excel');
            Route::get('kandidat/{id}/laporan', [KandidatController::class , 'generateLaporan'])->name('kandidat.laporan');
            Route::get('kandidat/download-excel/{id}', [KandidatController::class , 'downloadExcel'])->name('kandidat.downloadExcel');
            Route::get('kandidat/export-pdf/{id}', [KandidatController::class , 'exportExcelToPdf'])->name('kandidat.export-pdf');
            Route::resource('kandidat', KandidatController::class);

            // Proses Rekrutmen
            Route::get('proses/{kandidat_id}/edit', [ProsesRekrutmenController::class , 'edit'])->name('proses.edit');
            Route::post('proses', [ProsesRekrutmenController::class , 'store'])->name('proses.store');

            // Daily Activity
            Route::resource('daily', RekrutmenDailyController::class);
            Route::resource('daily/entries', RekrutmenCalendarController::class)->names('daily.entries');

            // METRICS & ANALYTICS
            Route::prefix('metrics')->name('metrics.')->group(function () {
                    // Data Endpoints (JSON)
                    Route::get('candidates', [RecruitmentDashboardController::class , 'candidatesByPositionMonth'])->name('candidates');
                    Route::get('cv', [RecruitmentDashboardController::class , 'cvPassedByPositionMonth'])->name('cv');
                    Route::get('psikotes', [RecruitmentDashboardController::class , 'psikotesPassedByPosition'])->name('psikotes');
                    Route::get('kompetensi', [RecruitmentDashboardController::class , 'kompetensiPassedByPosition'])->name('kompetensi');
                    Route::get('interview-hr', [RecruitmentDashboardController::class , 'interviewHrPassedByPositionMonth'])->name('hr');
                    Route::get('interview-user', [RecruitmentDashboardController::class , 'interviewUserPassedByPositionMonth'])->name('user');
                    Route::get('progress', [RecruitmentDashboardController::class , 'recruitmentProgressByPosition'])->name('progress');
                    Route::get('pemberkasan', [RecruitmentDashboardController::class , 'pemberkasanProgress'])->name('pemberkasan');

                    // View Pages
                    Route::get('cv-page', [RecruitmentDashboardController::class , 'cvPage'])->name('cv.page');
                    Route::get('psikotes-page', [RecruitmentDashboardController::class , 'psikotesPage'])->name('psikotes.page');
                    Route::get('kompetensi-page', [RecruitmentDashboardController::class , 'kompetensiPage'])->name('kompetensi.page');
                    Route::get('interview-hr-page', [RecruitmentDashboardController::class , 'interviewHrPage'])->name('hr.page');
                    Route::get('interview-user-page', [RecruitmentDashboardController::class , 'interviewUserPage'])->name('user.page');
                    Route::get('pemberkasan-page', [RecruitmentDashboardController::class , 'pemberkasanPage'])->name('pemberkasan.page');

                    // Exports
                    Route::get('cv/export', [RecruitmentDashboardController::class , 'exportCvCsv'])->name('cv.export');
                    Route::get('psikotes/export', [RecruitmentDashboardController::class , 'exportPsikotesCsv'])->name('psikotes.export');
                    Route::get('kompetensi/export', [RecruitmentDashboardController::class , 'exportKompetensiCsv'])->name('kompetensi.export');
                    Route::get('progress/export', [RecruitmentDashboardController::class , 'exportProgressCsv'])->name('progress.export');
                    Route::get('candidates/export', [RecruitmentDashboardController::class , 'exportCandidatesCsv'])->name('candidates.export');
                }
                );
            }
            );

            // --- OTHER ADMIN MODULES ---
            Route::resource('training', TrainingController::class);
            Route::resource('onboarding', OnboardingKaryawanController::class);

            Route::prefix('turnover')->name('turnover.')->group(function () {
            Route::get('/', [TurnoverController::class , 'index'])->name('index');
            Route::get('/export-excel', [TurnoverController::class , 'exportExcel'])->name('export.excel');
            Route::get('/export-pdf', [TurnoverController::class , 'exportPdf'])->name('export.pdf');
        }
        );
    // Route::middleware(['auth', 'role:admin|superadmin|manager|senior_manager'])->group(function () {
    //     // User management resource
    //     // 7. monitoring
    //     Route::get('/kbi/monitoring', [App\Http\Controllers\KbiController::class, 'monitoring'])->name('kbi.monitoring');
    //     // --- rekap PERFORMANCE ROUTES ---
    //     Route::get('/performance/rekap', [App\Http\Controllers\PerformanceController::class, 'index'])->name('performance.rekap');
    // });
    // Route::resource('wig-rekrutmen', WigRekrutmenController::class);
    
    // Route::resource('karyawan', KaryawanController::class);   
    });

Route::middleware(['auth', 'role:admin|superadmin'])->prefix('kpi')->name('kpi.')->group(function () {
    Route::get('perspectives', [KpiPerspectiveController::class , 'index'])->name('perspectives.index');
    Route::post('perspectives', [KpiPerspectiveController::class , 'store'])->name('perspectives.store');
    Route::put('perspectives/{perspective}', [KpiPerspectiveController::class , 'update'])->name('perspectives.update');
    Route::post('perspectives/{perspective}/toggle', [KpiPerspectiveController::class , 'toggleStatus'])->name('perspectives.toggle');
    Route::post('perspectives/bulk-delete', [KpiPerspectiveController::class , 'bulkDelete'])->name('perspectives.bulk-delete');
});

Route::middleware(['auth', 'role:superadmin'])->group(function () {
    // User management resource
    Route::resource('users', UserController::class);
    Route::delete('/users/batch-delete', [UserController::class , 'batchDelete'])->name('users.batchDelete');
});
Route::middleware(['auth', 'role:admin|superadmin|manager|GM|senior_manager|supervisor|direktur'])->group(function () {
    // User management resource
    // 7. monitoring
    Route::get('/kbi/monitoring', [App\Http\Controllers\KbiController::class , 'monitoring'])->name('kbi.monitoring');
    // --- rekap PERFORMANCE ROUTES ---
    Route::get('/performance/rekap', [App\Http\Controllers\PerformanceController::class , 'index'])->name('performance.rekap');
    Route::get('/performance/rekap/export/excel', [App\Http\Controllers\PerformanceController::class , 'exportExcel'])->name('performance.rekap.export.excel');
    Route::get('/performance/rekap/export/pdf', [App\Http\Controllers\PerformanceController::class , 'exportPDF'])->name('performance.rekap.export.pdf');
    Route::post('/performance/rekap/lock', [App\Http\Controllers\PerformanceController::class , 'lockPeriod'])->name('performance.rekap.lock');
    Route::post('/performance/rekap/unlock', [App\Http\Controllers\PerformanceController::class , 'unlockPeriod'])->name('performance.rekap.unlock');
    Route::get('/performance/rekap/lock-history', [App\Http\Controllers\PerformanceController::class , 'getLockHistory'])->name('performance.rekap.lock-history');
});
// Route::resource('wig-rekrutmen', WigRekrutmenController::class);

// Recruitment / Kandidat resources and metrics

// --- KPI ROUTES (DIPINDAHKAN KE SINI AGAR AMAN) ---

// Dashboard Monitoring (All Karyawan)
Route::get('/kpi/dashboard', [KpiAssessmentController::class , 'index'])->name('kpi.index');

// Hapus KPI
Route::delete('/kpi/delete/{id}', [KpiAssessmentController::class , 'destroy'])->name('kpi.destroy');

// Generate KPI Baru
Route::post('/kpi/store', [KpiAssessmentController::class , 'store'])->name('kpi.store');

// Bulk create KPI for manager scope (simple header creation)
Route::post('/kpi/bulk-create', [KpiAssessmentController::class , 'bulkCreateForManager'])->name('kpi.bulk-create');
// Form-based bulk create (Manager fills template then submit)
Route::get('/kpi/bulk-create/form', [KpiAssessmentController::class , 'bulkCreateForm'])->name('kpi.bulk-create.form');
Route::post('/kpi/bulk-store', [KpiAssessmentController::class , 'bulkStoreWithItems'])->name('kpi.bulk-store');
Route::post('/kpi/bulk-delete-assessments', [KpiAssessmentController::class , 'bulkDelete'])->name('kpi.bulk-delete-assessments');

// KPI Import
Route::get('/kpi/import/template', [KpiAssessmentController::class , 'downloadTemplate'])->name('kpi.import.template');
Route::post('/kpi/import', [KpiAssessmentController::class , 'importExcel'])->name('kpi.import');

// KPI Assessment Routes
// Contoh URL: /kpi/penilaian/5/2025 (Karyawan ID 5, Tahun 2025)
Route::get('/kpi/penilaian/{karyawan_id}/{tahun}', [KpiAssessmentController::class , 'show'])->name('kpi.show');
Route::post('/kpi/update/{id}', [KpiAssessmentController::class , 'update'])->name('kpi.update');

Route::post('/kpi/{id}/finalize', [App\Http\Controllers\KpiAssessmentController::class , 'finalize'])->name('kpi.finalize');

// --- KOMPETENSI LMS ROUTES ---
Route::get('/kompetensi/monitoring', [App\Http\Controllers\KompetensiController::class, 'index'])->name('kompetensi.monitoring');

// --- KBI ROUTES ---
// 1. Dashboard KBI (Menu Utama untuk memilih siapa yang dinilai)
Route::get('/kbi/dashboard', [KbiController::class , 'index'])->name('kbi.index');

// 2. Form Penilaian (Form yang sudah Anda buat)
// Parameter: id karyawan yg dinilai, dan tipe penilai (DIRI_SENDIRI/ATASAN/BAWAHAN)
Route::get('/kbi/nilai/{karyawan_id}/{tipe}', [KbiController::class , 'create'])->name('kbi.create');

// 3. Simpan Data
Route::post('/kbi/store', [KbiController::class , 'store'])->name('kbi.store');

// 4. Lihat Hasil Detail (Report)
Route::get('/kbi/hasil/{id_assessment}', [KbiController::class , 'show'])->name('kbi.show');
// 5. Update Atasan yang dinilai oleh Karyawan
Route::post('/kbi/update-atasan', [App\Http\Controllers\KbiController::class , 'updateAtasan'])->name('kbi.update-atasan');
// 6. Reset Atasan yang dinilai oleh Karyawan
Route::post('/kbi/reset-atasan', [App\Http\Controllers\KbiController::class , 'resetAtasan'])->name('kbi.reset-atasan');


// Export routes (accessible to all authenticated users)
Route::get('/kpi/export/excel', [KpiAssessmentController::class , 'exportExcel'])->name('performance.export.excel');
Route::get('/kpi/export/pdf', [KpiAssessmentController::class , 'exportPdf'])->name('performance.export.pdf');

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
Route::post('/kpi/items/store', [KpiAssessmentController::class , 'storeItem'])->name('kpi.store-item');
// Route untuk Hapus Item KPI
Route::delete('/kpi/items/{id}', [KpiAssessmentController::class , 'destroyItem'])->name('kpi.delete-item');
// Route untuk Update Item KPI
Route::put('/kpi/items/{id}', [KpiAssessmentController::class , 'updateItem'])->name('kpi.update-item');
Route::post('/kpi/items/bulk-delete', [KpiAssessmentController::class , 'bulkDestroyItems'])->name('kpi.items.bulk-delete');


// Routes untuk TEMPA
Route::middleware(['auth', 'role:admin|superadmin|ketua_tempa'])->prefix('tempa')->name('tempa.')->group(function () {
    // Kelompok TEMPA
    Route::post('kelompok/bulk-delete', [\App\Http\Controllers\TempaKelompokController::class, 'bulkDelete'])->name('kelompok.bulk-delete');
    Route::get('kelompok/import-template', [\App\Http\Controllers\TempaKelompokController::class, 'downloadTemplate'])->name('kelompok.import-template');
    Route::post('kelompok/import', [\App\Http\Controllers\TempaKelompokController::class, 'import'])->name('kelompok.import');
    Route::resource('kelompok', \App\Http\Controllers\TempaKelompokController::class)->parameters([
        'kelompok' => 'kelompok'
    ]);

    // Peserta TEMPA
    Route::post('peserta/bulk-delete', [\App\Http\Controllers\TempaPesertaController::class, 'bulkDelete'])->name('peserta.bulk-delete');
    Route::get('peserta/import-template', [\App\Http\Controllers\TempaPesertaController::class, 'downloadTemplate'])->name('peserta.import-template');
    Route::post('peserta/import', [\App\Http\Controllers\TempaPesertaController::class, 'import'])->name('peserta.import');
    Route::resource('peserta', \App\Http\Controllers\TempaPesertaController::class)->parameters([
        'peserta' => 'peserta' // This forces the parameter to be {peserta} instead of {pesertum}
    ]);

    // Absensi TEMPA
    Route::post('absensi/bulk-delete', [\App\Http\Controllers\TempaAbsensiController::class, 'bulkDelete'])->name('absensi.bulk-delete');
    Route::resource('absensi', \App\Http\Controllers\TempaAbsensiController::class);

    // Monitoring TEMPA
    Route::get('monitoring', [\App\Http\Controllers\TempaMonitoringController::class , 'index'])->name('monitoring.index');

    // Materi TEMPA
    Route::post('materi/bulk-delete', [\App\Http\Controllers\TempaMateriController::class, 'bulkDelete'])->name('materi.bulk-delete');
    Route::resource('materi', \App\Http\Controllers\TempaMateriController::class)->except(['show']);
    Route::get('materi/download/{id}', [\App\Http\Controllers\TempaMateriController::class , 'download'])->name('materi.download');
});

// Routes untuk Struktur Pekerjaan
Route::middleware(['auth', 'role:admin|superadmin'])->prefix('organization')->name('organization.')->group(function () {
    Route::resource('subsidiary', \App\Http\Controllers\SubsidiaryController::class)->parameters([
        'subsidiary' => 'subsidiary'
    ]);
    Route::resource('company', \App\Http\Controllers\CompanyController::class)->parameters([
        'company' => 'company'
    ]);
    Route::resource('holding', \App\Http\Controllers\HoldingController::class)->parameters([
        'holding' => 'holding'
    ]);
    Route::resource('division', \App\Http\Controllers\DivisionController::class)->parameters([
        'division' => 'division'
    ]);
    Route::get('division/parents/{holdingId}', [\App\Http\Controllers\DivisionController::class , 'parentsByHolding'])->name('division.parentsByHolding');
    Route::get('division/by-company/{companyId}', [\App\Http\Controllers\DivisionController::class , 'listByCompany'])->name('division.byCompany');
    Route::get('division/by-holding/{holdingId}', [\App\Http\Controllers\DivisionController::class , 'listByHolding'])->name('division.byHolding');

    Route::resource('department', \App\Http\Controllers\DepartmentController::class)->parameters([
        'department' => 'department'
    ]);
    Route::get('department/parents/{holdingId}', [\App\Http\Controllers\DepartmentController::class , 'parentsByHolding'])->name('department.parentsByHolding');
    Route::get('department/by-division/{divisionId}', [\App\Http\Controllers\DepartmentController::class , 'listByDivision'])->name('department.byDivision');
    Route::get('department/by-holding/{holdingId}', [\App\Http\Controllers\DepartmentController::class , 'listByHolding'])->name('department.byHolding');

    Route::resource('unit', \App\Http\Controllers\UnitController::class)->parameters([
        'unit' => 'unit'
    ]);
    Route::get('unit/parents/{holdingId}', [\App\Http\Controllers\UnitController::class , 'parentsByHolding'])->name('unit.parentsByHolding');
    Route::get('unit/by-department/{departmentId}', [\App\Http\Controllers\UnitController::class , 'listByDepartment'])->name('unit.byDepartment');
    Route::resource('position', \App\Http\Controllers\PositionController::class)->parameters([
        'position' => 'position'
    ]);
    Route::resource('level', \App\Http\Controllers\LevelController::class)->parameters([
        'level' => 'level'
    ]);
    // API endpoint untuk get levels
    Route::get('level/api/get-levels', [\App\Http\Controllers\LevelController::class , 'getLevels'])->name('level.api');
});



Route::middleware(['role:superadmin|admin|manajer'])
    ->group(function () {
        Route::get('/materi', [MateriController::class , 'index'])
            ->name('materi.index');
    });