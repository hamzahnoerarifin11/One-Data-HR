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
use App\Models\Role;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * MAIN FUNCTION: TRAFFIC CONTROLLER
     * Mengarahkan user berdasarkan Role di database.
     */
    public function index(Request $request)
    {
        $user  = Auth::user();
        // $roles = Role::orderBy('name')->get();
        // $role  = $user->role; // Asumsi nama kolom di tabel users adalah 'role'
        $tahun = $request->input('tahun', date('Y'));

        // 1. LOGIKA UNTUK ADMIN & SUPERADMIN (Melihat Global Data)
        if ($user->hasRole(['superadmin', 'admin'])) {
            return $this->adminDashboard();
        }

        // --- Cek Data Karyawan (Wajib untuk Manager & Staff) ---
        $karyawan = Karyawan::where('nik', $user->nik)->first();

        if (!$karyawan) {
            // Jika akun login tapi tidak connect ke data karyawan
            // Logout user dan redirect ke signin dengan pesan error
            auth()->logout();
            return redirect()->route('signin')->with('error', 'Akun Anda tidak terhubung dengan Data Karyawan. Silakan hubungi admin.');
        }

        // 2. LOGIKA UNTUK MANAGER (Melihat Tim)
        if ($user->hasRole('manager')) {
            return $this->managerDashboard($request, $karyawan, $tahun);
        }

        // 3. LOGIKA UNTUK STAFF (Melihat Diri Sendiri)
        // Default fallback jika role 'staff' atau role lain yang tidak terdefinisi
        return $this->staffDashboard($request, $karyawan, $tahun);
    }

    // =========================================================================
    // 1. DASHBOARD ADMIN / SUPERADMIN (Global HR Stats)
    // =========================================================================
    private function adminDashboard()
    {
        // --- Statistik Karyawan ---
        $totalKaryawan   = Karyawan::count();
        $karyawanAktif   = Karyawan::where('Kode', 'Aktif')->count();
        $totalKontrak    = Kontrak::count();
        $totalDepartemen = Pekerjaan::distinct('Departement')->count('Departement');

        // --- Statistik Demografi (Gender, Jabatan, Divisi, dll) ---
        // (Kode query sama persis seperti sebelumnya)
        $genderData = Karyawan::select(DB::raw("CASE WHEN Jenis_Kelamin_Karyawan = 'L' THEN 'Laki-laki' WHEN Jenis_Kelamin_Karyawan = 'P' THEN 'Perempuan' ELSE 'Tidak Diketahui' END as gender"), DB::raw('count(*) as total'))->groupBy('gender')->pluck('total', 'gender')->toArray();

        $jabatanData = Pekerjaan::whereNotNull('Jabatan')->groupBy('Jabatan')->select('Jabatan', DB::raw('count(*) as total'))->pluck('total', 'Jabatan')->toArray();

        $divisiData = Pekerjaan::whereNotNull('Divisi')->groupBy('Divisi')->select('Divisi', DB::raw('count(*) as total'))->pluck('total', 'Divisi')->toArray();

        $pendidikanData = Pendidikan::whereNotNull('Pendidikan_Terakhir')->groupBy('Pendidikan_Terakhir')->select('Pendidikan_Terakhir', DB::raw('count(*) as total'))->pluck('total', 'Pendidikan_Terakhir')->toArray();

        // --- Masa Kerja & Umur ---
        $tenureCounts = ['< 1 Tahun' => 0, '1 - 3 Tahun' => 0, '4 - 8 Tahun' => 0, '> 8 Tahun' => 0];
        foreach (Kontrak::whereNotNull('Tanggal_Mulai_Tugas')->get() as $k) {
            $years = Carbon::parse($k->Tanggal_Mulai_Tugas)->diffInYears(now());
            if ($years < 1) $tenureCounts['< 1 Tahun']++;
            elseif ($years <= 3) $tenureCounts['1 - 3 Tahun']++;
            elseif ($years <= 8) $tenureCounts['4 - 8 Tahun']++;
            else $tenureCounts['> 8 Tahun']++;
        }

        $ageCounts = ['< 25' => 0, '25 - 27' => 0, '28 - 30' => 0, '30 - 40' => 0, '40 - 50' => 0, '> 50' => 0];
        foreach (Karyawan::whereNotNull('Tanggal_Lahir_Karyawan')->get() as $k) {
            $age = Carbon::parse($k->Tanggal_Lahir_Karyawan)->age;
            if ($age < 25) $ageCounts['< 25']++;
            elseif ($age <= 27) $ageCounts['25 - 27']++;
            elseif ($age <= 30) $ageCounts['28 - 30']++;
            elseif ($age <= 40) $ageCounts['30 - 40']++;
            elseif ($age <= 50) $ageCounts['40 - 50']++;
            else $ageCounts['> 50']++;
        }

        $perusahaanData = Perusahaan::whereNotNull('Perusahaan')->groupBy('Perusahaan')->select('Perusahaan', DB::raw('count(*) as total'))->pluck('total', 'Perusahaan')->toArray();

        // View: pages/dashboard/admin.blade.php (atau dashboard.blade.php yang lama)
        return view('pages.dashboard', compact(
            'totalKaryawan',
            'karyawanAktif',
            'totalKontrak',
            'totalDepartemen',
            'genderData',
            'jabatanData',
            'divisiData',
            'pendidikanData',
            'tenureCounts',
            'ageCounts',
            'perusahaanData'
        ));
    }

    // =========================================================================
    // 2. DASHBOARD MANAGER (Monitoring Tim)
    // =========================================================================
    private function managerDashboard($request, $manager, $tahun)
    {
        // 1. Ambil ID Tim (Bawahan Langsung)
        $listBawahanIds = Karyawan::where('atasan_id', $manager->id_karyawan)->pluck('id_karyawan');
        $totalTim       = $listBawahanIds->count();

        // 2. KPI: Butuh Approval (Status: SUBMITTED)
        $butuhApprovalKPI = KpiAssessment::whereIn('karyawan_id', $listBawahanIds)
            ->where('tahun', $tahun)
            ->whereIn('status', ['SUBMITTED']) // Manager hanya peduli yang sudah submit
            ->count();

        // 3. KBI: Belum Dinilai Manager
        $sudahDinilaiKBI = KbiAssessment::whereIn('karyawan_id', $listBawahanIds)
            ->where('tahun', $tahun)
            ->where('penilai_id', Auth::id()) // Dinilai oleh User yang login
            ->where('tipe_penilai', 'ATASAN')
            ->count();

        $belumDinilaiKBI = $totalTim - $sudahDinilaiKBI;

        // 4. Tabel Monitoring (Pagination)
        $teamMonitoring = Karyawan::where('atasan_id', $manager->id_karyawan)
            ->with([
                'pekerjaan',
                'kpiAssessment' => function ($q) use ($tahun) {
                    $q->where('tahun', $tahun);
                },
                // Cek status KBI apakah sudah dinilai manager ini
                'kbiAssessment' => function ($q) use ($tahun) {
                    $q->where('tahun', $tahun)
                        ->where('penilai_id', Auth::id())
                        ->where('tipe_penilai', 'ATASAN');
                }
            ])
            ->paginate(5); // Tampilkan 5 per halaman di dashboard

        // View: pages/dashboard/manager.blade.php
        return view('pages.dashboard.manager', compact(
            'manager',
            'tahun',
            'totalTim',
            'butuhApprovalKPI',
            'belumDinilaiKBI',
            'teamMonitoring'
        ));
    }

    // =========================================================================
    // 3. DASHBOARD STAFF (Data Pribadi)
    // =========================================================================
    private function staffDashboard($request, $karyawan, $tahun)
    {
        // Ambil KPI Saya
        $myKpi = KpiAssessment::where('karyawan_id', $karyawan->id_karyawan)
            ->where('tahun', $tahun)
            ->first();

        // Ambil KBI Saya (Self Assessment)
        $myKbi = KbiAssessment::where('karyawan_id', $karyawan->id_karyawan)
            ->where('tahun', $tahun)
            ->where('tipe_penilai', 'DIRI_SENDIRI')
            ->first();

        // View: pages/dashboard/staff.blade.php
        return view('pages.dashboard.staff', compact('karyawan', 'tahun', 'myKpi', 'myKbi'));
    }
}
