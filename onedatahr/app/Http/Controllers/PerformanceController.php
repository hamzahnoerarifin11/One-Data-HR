<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\KbiAssessment;
use App\Models\KpiAssessment;
use App\Models\Company;
use App\Models\Division;
use App\Models\Department;
use App\Exports\PerformanceRekapExport;
use App\Exports\PerformanceRekapPDF;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PerformanceLock;

class PerformanceController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    //     $this->middleware('role:admin|superadmin|manager|senior_manager');
    // }

    public function index(Request $request)
    {
        $user = Auth::user();
        $tahun = request()->get('tahun', date('Y'));
        $mode = request()->get('mode', 'manager'); // 'manager' atau 'superadmin'

        if($user->roles->contains('Supervisor')){
            return redirect()->back()->with('error', 'Akses Ditolak: Anda tidak diizinkan melihat Rekapitulasi Kinerja.');
        }

        // ======================================================
        // 0. VALIDASI MODE & PENGAMAN DATA DIRI (PENTING!)
        // ======================================================
        $me = Karyawan::where('nik', $user->nik)->orWhere('NIK', $user->nik)->first();

        if (!$me && !$user->hasRole(['superadmin', 'admin'])) {
            return redirect()->back()->with('error', 'Data profil karyawan Anda belum terhubung. Silakan hubungi HRD.');
        }

        // Jika user memiliki role ganda, pastikan mereka punya role superadmin untuk mode superadmin
        if ($mode === 'superadmin' && !$user->hasRole(['superadmin', 'admin'])) {
            $mode = 'manager'; // Fallback ke manager mode
        }

        // Jika user hanya manager/gm (tanpa superadmin), force ke manager mode
        if (!$user->hasRole(['superadmin', 'admin']) && $user->hasRole(['manager', 'gm'])) {
            $mode = 'manager';
        }

        // ======================================================
        // 1. QUERY UTAMA DENGAN EAGER LOAD
        // ======================================================
        $query = Karyawan::with('pekerjaan');

        // A. Filter Search (Nama/NIK)
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('Nama_Lengkap_Sesuai_Ijazah', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('NIK', 'LIKE', '%' . $keyword . '%');
            });
        }

        // B. Filter Role (Manager/senior_manager hanya lihat bawahan)
        if ($user->hasRole(['manager', 'senior_manager', 'GM', 'manajer'])) {
            // MODIFIKASI: Manager melihat SEMUA karyawan di DIVISI-nya (bukan hanya direct subordinate)
            $latestJob = $me->pekerjaan()->latest('id_pekerjaan')->first();
            $divisionId = $latestJob ? $latestJob->division_id : null;

            if ($divisionId) {
                $query->whereHas('pekerjaan', function($q) use ($divisionId) {
                    $q->where('division_id', $divisionId);
                });
            } else {
                // Fallback jika tidak punya divisi (misal data kotor), tetap pakai logic lama atau kosongkan
                $query->where('atasan_id', $me->id_karyawan);
            }
        } elseif ($mode === 'manager' && $user->hasRole('staff')) {
            $query->where('id_karyawan', $me->id_karyawan);
        }
        // Jika mode superadmin, tampilkan semua (tidak ada filter role)

        // Eksekusi Query
        $karyawans = $query->get();

        // ======================================================
        // 2. HITUNG NILAI & MAPPING
        // ======================================================
        $rekapCollection = $karyawans->map(function ($k) use ($tahun) {
            // --- A. Hitung KBI ---
            $nilaiKbi = KbiAssessment::where('karyawan_id', $k->id_karyawan)
                ->where('tahun', $tahun)
                ->avg('rata_rata_akhir');

            $skorKbiAsli = $nilaiKbi ? $nilaiKbi : 0;

            // --- B. Hitung KPI ---
            $kpiRecord = KpiAssessment::where('karyawan_id', $k->id_karyawan)
                ->where('tahun', $tahun)
                ->latest('created_at')
                ->first();

            $skorKpi = $kpiRecord ? $kpiRecord->total_skor_akhir : 0;

            // --- C. Hitung Final Score (Bobot 70:30) ---
            $finalScore = ($skorKpi * 0.7) + ($skorKbiAsli * 0.3);

            // --- D. Tentukan Grade ---
            if ($finalScore >= 89) $grade = 'A';
            elseif ($finalScore >= 79) $grade = 'B';
            elseif ($finalScore >= 69) $grade = 'C';
            else $grade = 'D';

            // Ambil pekerjaan terbaru untuk divisi, departemen, perusahaan
            $pekerjaanTerbaru = $k->pekerjaan()->latest('id_pekerjaan')->first();

            // Return Object Lengkap
            return (object) [
                'id_karyawan' => $k->id_karyawan,
                'nik'         => $k->NIK,
                'nama'        => $k->Nama_Lengkap_Sesuai_Ijazah,
                'jabatan'     => $pekerjaanTerbaru?->position?->name ?? '-',
                'divisi'      => $pekerjaanTerbaru?->division?->name ?? '-',
                'departemen'  => $pekerjaanTerbaru?->department?->name ?? '-',
                'perusahaan'  => $pekerjaanTerbaru?->company?->name ?? '-',
                'division_id' => $pekerjaanTerbaru?->division_id,
                'skor_kbi_asli' => $skorKbiAsli,
                'skor_kbi'    => number_format($skorKbiAsli, 2),
                'skor_kpi'    => number_format($skorKpi, 2),
                'final_score' => $finalScore,
                'final_score_formatted' => number_format($finalScore, 2),
                'grade'       => $grade,
                'is_below_standard' => $finalScore < 69 ? 1 : 0
            ];
        });

        // ======================================================
        // 3. FILTER GRADE
        // ======================================================
        if ($request->has('grade') && $request->grade != '') {
            $rekapCollection = $rekapCollection->where('grade', $request->grade);
        }

        // ======================================================
        // 4. HITUNG EXECUTIVE SUMMARY
        // ======================================================
        $totalKaryawan = count($rekapCollection);
        $avgFinalScore = $totalKaryawan > 0 ? $rekapCollection->avg('final_score') : 0;
        $gradeDistribution = [
            'A' => $rekapCollection->where('grade', 'A')->count(),
            'B' => $rekapCollection->where('grade', 'B')->count(),
            'C' => $rekapCollection->where('grade', 'C')->count(),
            'D' => $rekapCollection->where('grade', 'D')->count(),
        ];
        $belowStandard = $rekapCollection->sum('is_below_standard');
        $pctBelowStandard = $totalKaryawan > 0 ? round(($belowStandard / $totalKaryawan) * 100, 2) : 0;

        // ======================================================
        // 5. HIGHLIGHT ANOMALI
        // ======================================================
        // Divisi dengan Grade D terbanyak
        $divisiGradeD = $rekapCollection
            ->where('grade', 'D')
            ->groupBy('divisi')
            ->map(function ($items) {
                return [
                    'divisi' => $items->first()->divisi,
                    'count' => count($items),
                    'percentage' => round((count($items) / count($items->groupBy('nama'))) * 100, 2)
                ];
            })
            ->sortByDesc('count')
            ->first();

        // Divisi dengan lonjakan performa (dibanding tahun sebelumnya)
        $lonjakan = $this->hitungLonjakan($rekapCollection, $tahun);

        // ======================================================
        // 6. GROUPING BY DIVISI + SUMMARY
        // ======================================================
        $groupedByDivisi = $rekapCollection->groupBy('divisi')->map(function ($items) {
            $count = count($items);
            return [
                'divisi' => $items->first()->divisi,
                'total' => $count,
                'avg_score' => round($items->avg('final_score'), 2),
                'grade_a' => $items->where('grade', 'A')->count(),
                'grade_b' => $items->where('grade', 'B')->count(),
                'grade_c' => $items->where('grade', 'C')->count(),
                'grade_d' => $items->where('grade', 'D')->count(),
                'items' => $items
            ];
        })->sortBy('divisi');

        // ======================================================
        // 7. GET FILTER OPTIONS
        // ======================================================
        $companies = Company::all();
        $divisions = Division::all();
        $departments = Department::all();

        // ======================================================
        // 8. PAGINASI MANUAL (Karena Data dari Collection)
        // ======================================================
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;

        $currentItems = $rekapCollection->slice(($page - 1) * $perPage, $perPage)->all();

        $rekap = new LengthAwarePaginator(
            $currentItems,
            count($rekapCollection),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        $rekap->appends($request->all());

        // ======================================================
        // 9. RETURN VIEW
        // ======================================================
        return view('pages.performance.rekap', compact(
            'rekap',
            'tahun',
            'mode',
            'totalKaryawan',
            'avgFinalScore',
            'gradeDistribution',
            'belowStandard',
            'pctBelowStandard',
            'divisiGradeD',
            'lonjakan',
            'groupedByDivisi',
            'companies',
            'divisions',
            'departments'
        ));
    }

    /**
     * Hitung lonjakan performa dibanding tahun lalu
     */
    private function hitungLonjakan($rekapCollection, $tahun)
    {
        $tahunLalu = $tahun - 1;

        // Untuk demo, kita ambil divisi dengan avg score tertinggi
        $lonjakanByDivisi = $rekapCollection
            ->groupBy('divisi')
            ->map(function ($items) {
                return [
                    'divisi' => $items->first()->divisi,
                    'avg_score' => round($items->avg('final_score'), 2)
                ];
            })
            ->sortByDesc('avg_score')
            ->first();

        return $lonjakanByDivisi ?? null;
    }

    /**
     * Export data ke Excel
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        $tahun = $request->get('tahun', date('Y'));
        $mode = $request->get('mode', 'manager');

        // Validasi akses
        if (!$user->hasRole(['superadmin', 'admin', 'manager', 'gm'])) {
            return back()->with('error', 'Anda tidak memiliki akses untuk export data');
        }

        // Get data (same logic as index)
        $me = Karyawan::where('nik', $user->nik)->orWhere('NIK', $user->nik)->first();
        $query = Karyawan::with('pekerjaan');

        // Apply filters
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('Nama_Lengkap_Sesuai_Ijazah', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('NIK', 'LIKE', '%' . $keyword . '%');
            });
        }

        if ($request->has('perusahaan') && $request->perusahaan != '') {
            $query->whereHas('pekerjaan', function ($q) use ($request) {
                $q->where('company_id', $request->perusahaan);
            });
        }

        if ($request->has('divisi') && $request->divisi != '') {
            $query->whereHas('pekerjaan', function ($q) use ($request) {
                $q->where('division_id', $request->divisi);
            });
        }

        if ($request->has('departemen') && $request->departemen != '') {
            $query->whereHas('pekerjaan', function ($q) use ($request) {
                $q->where('department_id', $request->departemen);
            });
        }

        // Role-based filtering
        if ($mode === 'manager' && $user->hasRole(['manager', 'gm', 'manajer', 'senior_manager'])) {
             // MOD: Manager see all in division
            $latestJob = $me->pekerjaan()->latest('id_pekerjaan')->first();
            $divisionId = $latestJob ? $latestJob->division_id : null;

            if ($divisionId) {
                $query->whereHas('pekerjaan', function($q) use ($divisionId) {
                    $q->where('division_id', $divisionId);
                });
            } else {
                $query->where('atasan_id', $me->id_karyawan);
            }
        } elseif ($mode === 'manager' && $user->hasRole('staff')) {
            $query->where('id_karyawan', $me->id_karyawan);
        }

        $karyawans = $query->get();

        // Map data dengan perhitungan
        $rekapCollection = $karyawans->map(function ($k) use ($tahun) {
            $nilaiKbi = KbiAssessment::where('karyawan_id', $k->id_karyawan)
                ->where('tahun', $tahun)
                ->avg('rata_rata_akhir');

            $skorKbiAsli = $nilaiKbi ? $nilaiKbi : 0;

            $kpiRecord = KpiAssessment::where('karyawan_id', $k->id_karyawan)
                ->where('tahun', $tahun)
                ->latest('created_at')
                ->first();

            $skorKpi = $kpiRecord ? $kpiRecord->total_skor_akhir : 0;
            $finalScore = ($skorKpi * 0.7) + ($skorKbiAsli * 0.3);

            if ($finalScore >= 89) $grade = 'A';
            elseif ($finalScore >= 79) $grade = 'B';
            elseif ($finalScore >= 69) $grade = 'C';
            else $grade = 'D';

            $pekerjaanTerbaru = $k->pekerjaan()->latest('id_pekerjaan')->first();

            return (object) [
                'id_karyawan' => $k->id_karyawan,
                'nik' => $k->NIK,
                'nama' => $k->Nama_Lengkap_Sesuai_Ijazah,
                'jabatan' => $pekerjaanTerbaru?->position?->name ?? '-',
                'divisi' => $pekerjaanTerbaru?->division?->name ?? '-',
                'departemen' => $pekerjaanTerbaru?->department?->name ?? '-',
                'perusahaan' => $pekerjaanTerbaru?->company?->name ?? '-',
                'skor_kbi' => number_format($skorKbiAsli, 2),
                'skor_kpi' => number_format($skorKpi, 2),
                'final_score_formatted' => number_format($finalScore, 2),
                'grade' => $grade,
            ];
        });

        // Filter by grade if specified
        if ($request->has('grade') && $request->grade != '') {
            $rekapCollection = $rekapCollection->where('grade', $request->grade);
        }

        // Summary
        $summary = [
            'totalKaryawan' => count($rekapCollection),
            'avgFinalScore' => $rekapCollection->avg('final_score_formatted') ?? 0,
            'gradeDistribution' => [
                'A' => $rekapCollection->where('grade', 'A')->count(),
                'B' => $rekapCollection->where('grade', 'B')->count(),
                'C' => $rekapCollection->where('grade', 'C')->count(),
                'D' => $rekapCollection->where('grade', 'D')->count(),
            ],
            'pctBelowStandard' => count($rekapCollection) > 0
                ? round(($rekapCollection->where('grade', 'D')->count() / count($rekapCollection)) * 100, 2)
                : 0
        ];

        // Create filename
        $filename = 'Rekap_Kinerja_' . $tahun . '_' . date('d-m-Y_His') . '.xlsx';

        // Create export instance and generate spreadsheet
        $export = new PerformanceRekapExport($rekapCollection, $summary, $tahun);
        $spreadsheet = $export->generate();

        // Save to file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        // Output to browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export data ke PDF
     */
    public function exportPDF(Request $request)
    {
        $user = Auth::user();
        $tahun = $request->get('tahun', date('Y'));
        $mode = $request->get('mode', 'manager','manajer');

        // Validasi akses
        if (!$user->hasRole(['superadmin', 'admin', 'manager', 'gm','manajer'])) {
            return back()->with('error', 'Anda tidak memiliki akses untuk export data');
        }

        // Get data (same logic as index)
        $me = Karyawan::where('nik', $user->nik)->orWhere('NIK', $user->nik)->first();
        $query = Karyawan::with('pekerjaan');

        // Apply filters (same as Excel)
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('Nama_Lengkap_Sesuai_Ijazah', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('NIK', 'LIKE', '%' . $keyword . '%');
            });
        }

        if ($request->has('perusahaan') && $request->perusahaan != '') {
            $query->whereHas('pekerjaan', function ($q) use ($request) {
                $q->where('company_id', $request->perusahaan);
            });
        }

        if ($request->has('divisi') && $request->divisi != '') {
            $query->whereHas('pekerjaan', function ($q) use ($request) {
                $q->where('division_id', $request->divisi);
            });
        }

        if ($request->has('departemen') && $request->departemen != '') {
            $query->whereHas('pekerjaan', function ($q) use ($request) {
                $q->where('department_id', $request->departemen);
            });
        }

        if ($mode === 'manager' && $user->hasRole(['manager', 'gm', 'manajer', 'senior_manager'])) {
             // MOD: Manager see all in division
            $latestJob = $me->pekerjaan()->latest('id_pekerjaan')->first();
            $divisionId = $latestJob ? $latestJob->division_id : null;

            if ($divisionId) {
                $query->whereHas('pekerjaan', function($q) use ($divisionId) {
                    $q->where('division_id', $divisionId);
                });
            } else {
                $query->where('atasan_id', $me->id_karyawan);
            }
        } elseif ($mode === 'manager' && $user->hasRole('staff')) {
            $query->where('id_karyawan', $me->id_karyawan);
        }

        $karyawans = $query->get();

        // Map data
        $rekapCollection = $karyawans->map(function ($k) use ($tahun) {
            $nilaiKbi = KbiAssessment::where('karyawan_id', $k->id_karyawan)
                ->where('tahun', $tahun)
                ->avg('rata_rata_akhir');

            $skorKbiAsli = $nilaiKbi ? $nilaiKbi : 0;

            $kpiRecord = KpiAssessment::where('karyawan_id', $k->id_karyawan)
                ->where('tahun', $tahun)
                ->latest('created_at')
                ->first();

            $skorKpi = $kpiRecord ? $kpiRecord->total_skor_akhir : 0;
            $finalScore = ($skorKpi * 0.7) + ($skorKbiAsli * 0.3);

            if ($finalScore >= 89) $grade = 'A';
            elseif ($finalScore >= 79) $grade = 'B';
            elseif ($finalScore >= 69) $grade = 'C';
            else $grade = 'D';

            $pekerjaanTerbaru = $k->pekerjaan()->latest('id_pekerjaan')->first();

            return (object) [
                'nama' => $k->Nama_Lengkap_Sesuai_Ijazah,
                'nik' => $k->NIK,
                'divisi' => $pekerjaanTerbaru?->division?->name ?? '-',
                'departemen' => $pekerjaanTerbaru?->department?->name ?? '-',
                'skor_kbi' => number_format($skorKbiAsli, 2),
                'skor_kpi' => number_format($skorKpi, 2),
                'final_score_formatted' => number_format($finalScore, 2),
                'grade' => $grade,
            ];
        });

        if ($request->has('grade') && $request->grade != '') {
            $rekapCollection = $rekapCollection->where('grade', $request->grade);
        }

        // Group by divisi
        $groupedByDivisi = $rekapCollection->groupBy('divisi')->map(function ($items) {
            return [
                'divisi' => $items->first()->divisi,
                'total' => count($items),
                'avg_score' => round($items->avg('final_score_formatted'), 2),
                'items' => $items
            ];
        })->sortBy('divisi');

        // Summary
        $summary = [
            'totalKaryawan' => count($rekapCollection),
            'avgFinalScore' => $rekapCollection->avg('final_score_formatted') ?? 0,
            'gradeDistribution' => [
                'A' => $rekapCollection->where('grade', 'A')->count(),
                'B' => $rekapCollection->where('grade', 'B')->count(),
                'C' => $rekapCollection->where('grade', 'C')->count(),
                'D' => $rekapCollection->where('grade', 'D')->count(),
            ]
        ];

        // Generate HTML
        $pdfExport = new PerformanceRekapPDF($rekapCollection, $summary, $tahun, $groupedByDivisi);
        $html = $pdfExport->generateHTML();

        // Create PDF
        $pdf = Pdf::loadHTML($html);
        $filename = 'Rekap_Kinerja_' . $tahun . '_' . date('d-m-Y_His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Lock performance period (tahun)
     */
    public function lockPeriod(Request $request)
    {
        // Only superadmin can lock
        if (!Auth::user()->hasRole(['superadmin', 'admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengunci periode.',
            ], 403);
        }

        // Validate request
        $validated = $request->validate([
            'tahun' => 'required|integer|min:2000|max:2099',
            'reason' => 'nullable|string|max:1000',
        ]);

        try {
            // Lock tahun
            $lock = PerformanceLock::lock(
                $validated['tahun'],
                Auth::id(),
                $validated['reason'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Periode performance tahun ' . $validated['tahun'] . ' berhasil dikunci.',
                'data' => $lock,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunci periode: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Unlock performance period (superadmin only)
     */
    public function unlockPeriod(Request $request)
    {
        // Only superadmin can unlock
        if (!Auth::user()->hasRole(['superadmin', 'admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk membuka kunci periode.',
            ], 403);
        }

        // Validate request
        $validated = $request->validate([
            'tahun' => 'required|integer|min:2000|max:2099',
            'reason' => 'nullable|string|max:1000',
        ]);

        try {
            // Unlock tahun
            $unlocked = PerformanceLock::unlock(
                $validated['tahun'],
                Auth::id(),
                $validated['reason'] ?? null
            );

            if (!$unlocked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periode tidak dalam status terkunci.',
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Periode performance tahun ' . $validated['tahun'] . ' berhasil dibuka kunci.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuka kunci periode: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get lock history for tahun
     */
    public function getLockHistory(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));

        try {
            $history = PerformanceLock::getHistory((int) $tahun);

            return response()->json([
                'success' => true,
                'data' => $history,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil history lock: ' . $e->getMessage(),
            ], 500);
        }
    }
}
