<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KpiAssessment;
use App\Models\Karyawan;
use App\Models\KpiItem;
use App\Models\KpiScore;

class KpiAssessmentController extends Controller
{
    // Menampilkan Form KPI untuk Karyawan tertentu
    public function show($karyawanId, $tahun)
    {
        // 1. Ambil Data Karyawan
        $karyawan = Karyawan::findOrFail($karyawanId);

        // 2. Cari apakah sudah ada Draft KPI untuk tahun ini?
        // Jika belum ada, idealnya ada fitur "Create KPI" dulu (bisa kita bahas nanti).
        // Untuk sekarang, kita anggap datanya sudah di-seed atau dibuat.
        $kpi = KpiAssessment::where('karyawan_id', $karyawanId)
                            ->where('tahun', $tahun)
                            ->with(['items.scores']) // Eager loading biar ringan
                            ->first();

        if (!$kpi) {
            return redirect()->back()->with('error', 'Data KPI belum dibuat untuk karyawan ini.');
        }

        return view('kpi.form', compact('karyawan', 'kpi'));
    }

    // Menyimpan Input Realisasi
    public function update(Request $request, $id_kpi_assessment)
    {
        // Logika simpan akan kita buat setelah View jadi
        dd($request->all()); 
    }
}