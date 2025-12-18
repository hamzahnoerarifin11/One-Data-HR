<?php

namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WigRekrutmen;
use App\Models\Posisi;

class WigRekrutmenController extends Controller
{
    /**
     * ==================================================
     * HALAMAN MONITORING WIG / FPK PER POSISI
     * ==================================================
     * Menampilkan:
     * - Status FPK
     * - Tanggal publikasi
     * - Total pelamar
     * - Progress tiap tahap
     */
    public function index()
    {
        $data = Posisi::with('wigRekrutmen')
            ->orderBy('nama_posisi', 'asc')
            ->get();

        return view('pages.rekrutmen.wig.index', compact('data'));
    }

    /**
     * ==================================================
     * UPDATE DATA WIG (MONITORING PROGRES)
     * ==================================================
     */
    public function update(Request $request, $posisiId)
    {
        $request->validate([
            'status_fpk' => 'required|string'
        ]);

        $wig = WigRekrutmen::where('posisi_id', $posisiId)->firstOrFail();

        $wig->update([
            'status_fpk'                 => $request->status_fpk,
            'total_pelamar'              => $request->total_pelamar ?? $wig->total_pelamar,
            'total_lolos_cv'             => $request->total_lolos_cv ?? $wig->total_lolos_cv,
            'total_lolos_psikotes'       => $request->total_lolos_psikotes ?? $wig->total_lolos_psikotes,
            'total_lolos_tes_kompetensi' => $request->total_lolos_tes_kompetensi ?? $wig->total_lolos_tes_kompetensi,
            'total_lolos_interview_hr'   => $request->total_lolos_interview_hr ?? $wig->total_lolos_interview_hr,
            'total_lolos_interview_user' => $request->total_lolos_interview_user ?? $wig->total_lolos_interview_user,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Data WIG Rekrutmen berhasil diperbarui');
    }
}
