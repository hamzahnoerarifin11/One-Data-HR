<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Models
use App\Models\Karyawan;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Kontrak;
use App\Models\Perusahaan;
use App\Models\KpiAssessment;
use App\Models\KbiAssessment;
use App\Models\Division;
use App\Models\Company;
use App\Models\Holding;

class DashboardController extends Controller
{
    /**
     * MAIN FUNCTION: TRAFFIC CONTROLLER
     * Mengarahkan user berdasarkan Role di database.
     */
    /**
     * MAIN FUNCTION: TRAFFIC CONTROLLER & DATA LOADER
     * Mengarahkan user berdasarkan Role di database.
     */
    public function index(Request $request)
    {
        $user  = Auth::user();
        $tahun = $request->input('tahun', date('Y'));

        // 1. LOGIKA UNTUK ADMIN & SUPERADMIN (Melihat Global Data)
        // Admin tetap pakai dashboard terpisah (opsional, tapi request user fokus merge manager/spv/staff)
        if ($user->hasRole(['superadmin', 'admin'])) {
            return $this->adminDashboard();
        }

        // --- Cek Data Karyawan (Wajib untuk Manager, Supervisor & Staff) ---
        $karyawan = Karyawan::where('nik', $user->nik)->first();

        if (!$karyawan) {
            auth()->logout();
            return redirect()->route('signin')->with('error', 'Akun Anda tidak terhubung dengan Data Karyawan. Silakan hubungi admin.');
        }

        // =====================================================================
        // A. DATA PRIBADI (SEMUA ROLE DAPAT INI)
        // =====================================================================
        // Ambil KPI Saya
        $myKpi = KpiAssessment::where('karyawan_id', $karyawan->id_karyawan)
            ->where('tahun', $tahun)
            ->first();

        // Ambil KBI Saya (Self Assessment)
        $myKbi = KbiAssessment::where('karyawan_id', $karyawan->id_karyawan)
            ->where('tahun', $tahun)
            ->where('tipe_penilai', 'DIRI_SENDIRI')
            ->first();

        // =====================================================================
        // B. DATA TIM (KHUSUS MANAGER & SUPERVISOR)
        // =====================================================================
        $teamMonitoring = null;
        $totalTim = 0;
        $butuhApprovalKPI = 0;
        $belumDinilaiKBI = 0;
        $isManagerOrSpv = false;
        $roleTitle = 'Staff'; // Default

        // Cek Role untuk akses TIM
        // Manager / GM / Senior Manager -> Lihat Satuan Divisi
        // Supervisor -> Lihat Bawahan Langsung
        
        if ($user->hasRole(['manager', 'GM', 'senior_manager', 'direktur', 'manajer', 'Supervisor', 'supervisor'])) {
            $isManagerOrSpv = true;
            $scopeIds = [];

            // 1. Tentukan Scope Karyawan
            if ($user->hasRole(['manager', 'GM', 'senior_manager', 'direktur', 'manajer'])) {
                $roleTitle = 'Manager';
                // Logic Manager: Lihat Divisi
                $pekerjaanManager = $karyawan->pekerjaan()->orderByDesc('id_pekerjaan')->first();
                if ($pekerjaanManager && $pekerjaanManager->division_id) {
                    $divisionId = $pekerjaanManager->division_id;
                    $scopeIds = Karyawan::whereHas('pekerjaan', function ($q) use ($divisionId) {
                        $q->where('division_id', $divisionId);
                    })->pluck('id_karyawan')->toArray();
                }
            } elseif ($user->hasRole(['Supervisor', 'supervisor'])) {
                $roleTitle = 'Supervisor';
                // Logic Supervisor: Lihat Bawahan Langsung (atasan_id)
                $scopeIds = Karyawan::where('atasan_id', $karyawan->id_karyawan)
                            ->pluck('id_karyawan')->toArray();
            }

            // Exclude diri sendiri dari monitoring tim (opsional, biasanya manager tidak menilai diri sendiri di tabel tim)
            // $scopeIds = array_diff($scopeIds, [$karyawan->id_karyawan]);

            $totalTim = count($scopeIds);

            // 2. Hitung Statistik Tim
            if ($totalTim > 0) {
                // KPI Approved/Submitted
                $butuhApprovalKPI = KpiAssessment::whereIn('karyawan_id', $scopeIds)
                    ->where('tahun', $tahun)
                    ->where('status', 'SUBMITTED')
                    ->count();

                // KBI Belum Dinilai
                $sudahDinilaiKBI = KbiAssessment::whereIn('karyawan_id', $scopeIds)
                    ->where('tahun', $tahun)
                    ->where('penilai_id', Auth::id())
                    ->where('tipe_penilai', 'ATASAN')
                    ->count();
                
                $belumDinilaiKBI = max($totalTim - $sudahDinilaiKBI, 0);

                // 3. Ambil Data Tim (Pagination)
                $teamMonitoring = Karyawan::whereIn('id_karyawan', $scopeIds)
                    ->with([
                        'pekerjaan.division',
                        'kpiAssessment' => function ($q) use ($tahun) {
                            $q->where('tahun', $tahun);
                        },
                        'kbiAssessment' => function ($q) use ($tahun) {
                            $q->where('tahun', $tahun)
                              ->where('penilai_id', Auth::id())
                              ->where('tipe_penilai', 'ATASAN');
                        }
                    ])
                    ->paginate(5);
            } else {
                 // Empty Paginator
                 $teamMonitoring = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 5, 1);
            }
        }

        // View Unified: pages/dashboard/index.blade.php
        return view('pages.dashboard.index', compact(
            'karyawan',
            'tahun',
            'myKpi',
            'myKbi',
            'isManagerOrSpv',
            'roleTitle',
            'teamMonitoring',
            'totalTim',
            'butuhApprovalKPI',
            'belumDinilaiKBI'
        ));
    }

    // =========================================================================
    // 1. DASHBOARD ADMIN / SUPERADMIN (Global HR Stats)
    // =========================================================================
    private function adminDashboard()
    {
        $request = request();
        $filters = [
            'holding_id'    => $request->holding_id,
            'company_id'    => $request->company_id,
            'division_id'   => $request->division_id,
            'department_id' => $request->department_id,
            'unit_id'       => $request->unit_id,
            'start_date'    => $request->start_date ?? date('Y-01-01'),
            'end_date'      => $request->end_date ?? date('Y-12-31'),
        ];

        // --- Base Query Karyawan (Active & Inactive for history) ---
        $karyawanQuery = Karyawan::query();

        // Apply Entity Filters to Karyawan Query via Pekerjaan
        if (array_filter($filters)) {
            $karyawanQuery->whereHas('pekerjaan', function ($q) use ($filters) {
                if ($filters['holding_id']) $q->where('holding_id', $filters['holding_id']);
                if ($filters['company_id']) $q->where('company_id', $filters['company_id']);
                if ($filters['division_id']) $q->where('division_id', $filters['division_id']);
                if ($filters['department_id']) $q->where('department_id', $filters['department_id']);
                if ($filters['unit_id']) $q->where('unit_id', $filters['unit_id']);
            });
        }

        // --- Statistik Utama ---
        $totalKaryawan = (clone $karyawanQuery)->count();
        $karyawanAktif = (clone $karyawanQuery)->where('Kode', 'Aktif')->count();
        $karyawanNonAktif = (clone $karyawanQuery)->where('Kode', '!=', 'Aktif')->count();
        
        $totaldepartment_id = (clone $karyawanQuery)
            ->join('pekerjaan', 'karyawan.id_karyawan', '=', 'pekerjaan.id_karyawan')
            ->distinct('pekerjaan.department_id')
            ->count('pekerjaan.department_id');

        // --- Statistik Demografi (Gender) ---
        $genderData = (clone $karyawanQuery)
             ->select(DB::raw("CASE WHEN Jenis_Kelamin_Karyawan = 'L' THEN 'Laki-laki' WHEN Jenis_Kelamin_Karyawan = 'P' THEN 'Perempuan' ELSE 'Tidak Diketahui' END as gender"), DB::raw('count(*) as total'))
             ->groupBy('gender')
             ->pluck('total', 'gender')
             ->toArray();

        // --- Statistik Jabatan (Top 10) ---
        $jabatanData = (clone $karyawanQuery)
            ->join('pekerjaan', 'karyawan.id_karyawan', '=', 'pekerjaan.id_karyawan')
            ->join('positions', 'pekerjaan.position_id', '=', 'positions.id')
            ->select('positions.name', DB::raw('count(*) as total'))
            ->groupBy('positions.name')
            ->orderByDesc('total')
            ->limit(10)
            ->pluck('total', 'positions.name')
            ->toArray();

        // --- Statistik Divisi ---
        $divisiData = (clone $karyawanQuery)
             ->join('pekerjaan', 'karyawan.id_karyawan', '=', 'pekerjaan.id_karyawan')
             ->join('divisions', 'pekerjaan.division_id', '=', 'divisions.id')
             ->select('divisions.name', DB::raw('count(*) as total'))
             ->groupBy('divisions.name')
             ->orderByDesc('total')
             ->pluck('total', 'divisions.name')
             ->toArray();

         // --- Statistik Perusahaan ---
        $perusahaanData = (clone $karyawanQuery)
            ->join('pekerjaan', 'karyawan.id_karyawan', '=', 'pekerjaan.id_karyawan')
            ->join('companies', 'pekerjaan.company_id', '=', 'companies.id')
            ->select('companies.name', DB::raw('count(*) as total'))
            ->groupBy('companies.name')
            ->pluck('total', 'companies.name')
            ->toArray();

        // --- Level Jabatan ---
        $levelData = (clone $karyawanQuery)
            ->join('pekerjaan', 'karyawan.id_karyawan', '=', 'pekerjaan.id_karyawan')
            ->join('levels', 'pekerjaan.level_id', '=', 'levels.id')
            ->select('levels.name', DB::raw('count(*) as total'))
            ->groupBy('levels.name')
            ->orderByDesc('total') // Or by level_order if available
            ->pluck('total', 'levels.name')
            ->toArray();

        // --- Entity Stats (Holding, Company, Subsidiary) ---
        // Holding
        $holdingData = (clone $karyawanQuery)
            ->join('pekerjaan', 'karyawan.id_karyawan', '=', 'pekerjaan.id_karyawan')
            ->join('holdings', 'pekerjaan.holding_id', '=', 'holdings.id')
            ->select('holdings.name', DB::raw('count(*) as total'))
            ->groupBy('holdings.name')
            ->pluck('total', 'holdings.name')
            ->toArray();

        // Company (Parent Companies)
        // Optimization: Use whereHas to filter companies with no parent (if that's the definition of "Company" vs "Subsidiary" requested)
        // However, user said "Perusahaan dan Anak Perusahaan".
        // Let's get all companies and group them.
        // Actually, if we look at Company model, it has parent_id. 
        // Let's try to separate them if possible, or just list them all.
        // Better approach:
        // 1. Top Companies (No Parent)
        // 2. Subsidiaries (Has Parent)
        
        $companyStats = (clone $karyawanQuery)
            ->join('pekerjaan', 'karyawan.id_karyawan', '=', 'pekerjaan.id_karyawan')
            ->join('companies', 'pekerjaan.company_id', '=', 'companies.id')
            ->select('companies.name', 'companies.parent_id', DB::raw('count(*) as total'))
            ->groupBy('companies.id', 'companies.name', 'companies.parent_id') // Group by ID to be safe
            ->get();
            
        $parentCompanyData = $companyStats->whereNull('parent_id')->pluck('total', 'name')->toArray();
        $subsidiaryData = $companyStats->whereNotNull('parent_id')->pluck('total', 'name')->toArray();

        // --- Pendidikan ---
        $pendidikanData = (clone $karyawanQuery)
            ->join('pendidikan', 'karyawan.id_karyawan', '=', 'pendidikan.id_karyawan')
            ->select('pendidikan.Pendidikan_Terakhir', DB::raw('count(*) as total'))
            ->groupBy('pendidikan.Pendidikan_Terakhir')
            ->pluck('total', 'pendidikan.Pendidikan_Terakhir')
            ->toArray();

        // --- Masa Kerja & Umur ---
        // Optimization: Select only necessary columns
        $karyawanForStats = (clone $karyawanQuery)
            ->with(['kontrak' => function($q) { $q->select('id_karyawan', 'Tanggal_Mulai_Tugas'); }])
            ->select('id_karyawan', 'Tanggal_Lahir_Karyawan')
            ->get();

        $tenureCounts = ['< 1 Tahun' => 0, '1 - 3 Tahun' => 0, '4 - 8 Tahun' => 0, '> 8 Tahun' => 0];
        $ageCounts = ['< 25' => 0, '25 - 27' => 0, '28 - 30' => 0, '30 - 40' => 0, '40 - 50' => 0, '> 50' => 0];

        foreach ($karyawanForStats as $k) {
            // Masa Kerja
            if ($k->kontrak && $k->kontrak->Tanggal_Mulai_Tugas) {
                $years = Carbon::parse($k->kontrak->Tanggal_Mulai_Tugas)->diffInYears(now());
                if ($years < 1) $tenureCounts['< 1 Tahun']++;
                elseif ($years <= 3) $tenureCounts['1 - 3 Tahun']++;
                elseif ($years <= 8) $tenureCounts['4 - 8 Tahun']++;
                else $tenureCounts['> 8 Tahun']++;
            }

            // Umur
            if ($k->Tanggal_Lahir_Karyawan) {
                $age = Carbon::parse($k->Tanggal_Lahir_Karyawan)->age;
                if ($age < 25) $ageCounts['< 25']++;
                elseif ($age <= 27) $ageCounts['25 - 27']++;
                elseif ($age <= 30) $ageCounts['28 - 30']++;
                elseif ($age <= 40) $ageCounts['30 - 40']++;
                elseif ($age <= 50) $ageCounts['40 - 50']++;
                else $ageCounts['> 50']++;
            }
        }

        // --- TURNOVER CALCULATION (PER BULAN) ---
        $turnoverData = [];
        $start = Carbon::parse($filters['start_date']);
        $end = Carbon::parse($filters['end_date']);
        
        $period = \Carbon\CarbonPeriod::create($start, '1 month', $end);

        foreach ($period as $dt) {
            $monthName = $dt->locale('id')->isoFormat('MMM Y'); // Format Indonesia
            $month = $dt->month;
            $year = $dt->year;

            // Masuk
            $masukQuery = Kontrak::query()
                ->whereMonth('Tanggal_Mulai_Tugas', $month)
                ->whereYear('Tanggal_Mulai_Tugas', $year);

             $masukQuery->whereHas('karyawan.pekerjaan', function ($q) use ($filters) {
                if ($filters['holding_id']) $q->where('holding_id', $filters['holding_id']);
                if ($filters['company_id']) $q->where('company_id', $filters['company_id']);
                if ($filters['division_id']) $q->where('division_id', $filters['division_id']);
            });
            $masukCount = $masukQuery->count();

            // Keluar (Resign, PHK, dll)
            // Fix: Status_Karyawan column does not exist. Presence of Tanggal_Non_Aktif indicates exit.
            $keluarQuery = \App\Models\StatusKaryawan::query()
                ->whereMonth('Tanggal_Non_Aktif', $month)
                ->whereYear('Tanggal_Non_Aktif', $year);

            $keluarQuery->whereHas('karyawan.pekerjaan', function ($q) use ($filters) {
                if ($filters['holding_id']) $q->where('holding_id', $filters['holding_id']);
                if ($filters['company_id']) $q->where('company_id', $filters['company_id']);
                if ($filters['division_id']) $q->where('division_id', $filters['division_id']);
            });
            $keluarCount = $keluarQuery->count();

            // Rough logic for Rate: (Keluar / Total Saat Ini) * 100
            $rate = ($totalKaryawan > 0) ? round(($keluarCount / $totalKaryawan) * 100, 2) : 0;

            $turnoverData[] = [
                'month' => $monthName,
                'masuk' => $masukCount,
                'keluar' => $keluarCount,
                'rate' => $rate
            ];
        }

        // Dropdown Data
        $holdings = Holding::all(); // Assuming Holding model exists
        $companies = Company::all();
        // Load divisions filtered by company if selected
        $divisions = $filters['company_id'] ? Division::whereHas('company', fn($q)=>$q->where('id', $filters['company_id']))->get() : Division::all();
        
        return view('pages.dashboard', compact(
            'totalKaryawan', 'karyawanAktif', 'karyawanNonAktif', 'totaldepartment_id',
            'genderData', 'jabatanData', 'divisiData', 'perusahaanData', 'pendidikanData',
            'levelData', 'holdingData', 'parentCompanyData', 'subsidiaryData', // New Data
            'tenureCounts', 'ageCounts', 'turnoverData',
            'filters', 'holdings', 'companies', 'divisions'
        ));
    }
}
