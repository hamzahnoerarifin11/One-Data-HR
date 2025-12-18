<?php

namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Posisi;
use App\Models\WigRekrutmen;

class PosisiController extends Controller
{
    /**
     * ==================================================
     * HALAMAN DAFTAR POSISI (MASTER POSISI)
     * ==================================================
     */
    public function index()
    {
        $posisi = Posisi::with('wigRekrutmen')
            ->orderBy('nama_posisi', 'asc')
            ->get();

        return view('pages.rekrutmen.posisi.index', compact('posisi'));
    }

    /**
     * ==================================================
     * SIMPAN POSISI BARU + DATA PUBLIKASI AWAL
     * ==================================================
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_posisi'        => 'required|string|max:255',
            'tanggal_publikasi'  => 'required|date',
        ]);

        // 1. Simpan master posisi
        $posisi = Posisi::create([
            'nama_posisi' => $request->nama_posisi,
            'slug'        => Str::slug($request->nama_posisi),
            'status'      => 'Aktif',
        ]);

        // 2. Simpan data WIG / FPK / publikasi
        WigRekrutmen::create([
            'posisi_id'                   => $posisi->id_posisi,
            'tanggal_publikasi_loker'     => $request->tanggal_publikasi,
            'status_fpk'                  => 'OPEN',
            'total_pelamar'               => 0,
            'total_lolos_cv'              => 0,
            'total_lolos_psikotes'        => 0,
            'total_lolos_tes_kompetensi'  => 0,
            'total_lolos_interview_hr'    => 0,
            'total_lolos_interview_user'  => 0,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Posisi berhasil ditambahkan');
    }

    /**
     * ==================================================
     * UPDATE DATA POSISI
     * ==================================================
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_posisi' => 'required|string|max:255',
            'status'      => 'required|in:Aktif,Ditutup',
        ]);

        $posisi = Posisi::findOrFail($id);

        $posisi->update([
            'nama_posisi' => $request->nama_posisi,
            'slug'        => Str::slug($request->nama_posisi),
            'status'      => $request->status,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Posisi berhasil diperbarui');
    }

    /**
     * ==================================================
     * TUTUP POSISI (SOFT CLOSE)
     * ==================================================
     */
    public function close($id)
    {
        $posisi = Posisi::findOrFail($id);

        $posisi->update([
            'status' => 'Ditutup'
        ]);

        return redirect()
            ->back()
            ->with('success', 'Posisi berhasil ditutup');
    }
}
