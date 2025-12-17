<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Kontrak;
use App\Models\Perusahaan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
public function index()
{
    /** 1. Total Karyawan */
    $totalKaryawan = Karyawan::count();
    // KARYAWAN AKTIF (sesuai DB Anda: Kode = 'Aktif')
    $karyawanAktif = Karyawan::where('Kode', 'Aktif')->count();

    $totalKontrak = Kontrak::count();

    $totalDepartemen = Pekerjaan::distinct('Departement')
        ->count('Departement');

    /** 2. Gender */
    $genderData = Karyawan::select(
            DB::raw("CASE
                WHEN Jenis_Kelamin_Karyawan = 'L' THEN 'Laki-laki'
                WHEN Jenis_Kelamin_Karyawan = 'P' THEN 'Perempuan'
                ELSE 'Tidak Diketahui' END as gender"),
            DB::raw('count(*) as total')
        )
        ->groupBy('gender')
        ->pluck('total','gender')
        ->toArray();

    /** 3. Jabatan */
    $jabatanData = Pekerjaan::whereNotNull('Jabatan')
        ->groupBy('Jabatan')
        ->select('Jabatan', DB::raw('count(*) as total'))
        ->pluck('total','Jabatan')
        ->toArray();

    /** 4. Divisi */
    $divisiData = Pekerjaan::whereNotNull('Divisi')
        ->groupBy('Divisi')
        ->select('Divisi', DB::raw('count(*) as total'))
        ->pluck('total','Divisi')
        ->toArray();

    /** 5. Pendidikan */
    $pendidikanOrder = ['SD','SLTP','SLTA','DIPLOMA I','DIPLOMA II','DIPLOMA III','DIPLOMA IV','S1','S2'];
    $pendidikanData = Pendidikan::whereNotNull('Pendidikan_Terakhir')
        ->groupBy('Pendidikan_Terakhir')
        ->select('Pendidikan_Terakhir', DB::raw('count(*) as total'))
        ->pluck('total','Pendidikan_Terakhir')
        ->toArray();

    /** 6. Masa Kerja */
    $tenureCounts = [
        '< 1 Tahun' => 0,
        '1 - 3 Tahun' => 0,
        '4 - 8 Tahun' => 0,
        '> 8 Tahun' => 0,
    ];

    foreach (Kontrak::whereNotNull('Tanggal_Mulai_Tugas')->get() as $k) {
        $years = Carbon::parse($k->Tanggal_Mulai_Tugas)->diffInYears(now());

        if ($years < 1) $tenureCounts['< 1 Tahun']++;
        elseif ($years <= 3) $tenureCounts['1 - 3 Tahun']++;
        elseif ($years <= 8) $tenureCounts['4 - 8 Tahun']++;
        else $tenureCounts['> 8 Tahun']++;
    }

    /** 7. Umur */
    $ageCounts = [
        '< 25' => 0,
        '25 - 27' => 0,
        '28 - 30' => 0,
        '30 - 40' => 0,
        '40 - 50' => 0,
        '> 50' => 0,
    ];

    foreach (Karyawan::whereNotNull('Tanggal_Lahir_Karyawan')->get() as $k) {
        $age = Carbon::parse($k->Tanggal_Lahir_Karyawan)->age;

        if ($age < 25) $ageCounts['< 25']++;
        elseif ($age <= 27) $ageCounts['25 - 27']++;
        elseif ($age <= 30) $ageCounts['28 - 30']++;
        elseif ($age <= 40) $ageCounts['30 - 40']++;
        elseif ($age <= 50) $ageCounts['40 - 50']++;
        else $ageCounts['> 50']++;
    }

    /** 8. Perusahaan */
    $perusahaanData = Perusahaan::whereNotNull('Perusahaan')
        ->groupBy('Perusahaan')
        ->select('Perusahaan', DB::raw('count(*) as total'))
        ->pluck('total','Perusahaan')
        ->toArray();

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
}
