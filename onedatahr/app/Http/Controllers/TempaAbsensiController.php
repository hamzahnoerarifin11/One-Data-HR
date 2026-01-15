<?php

namespace App\Http\Controllers;

use App\Models\TempaAbsensi;
use App\Models\TempaPeserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Gunakan Facade yang benar

class TempaAbsensiController extends Controller
{
    public function index()
    {
        $pesertas = TempaPeserta::with(['kelompok', 'absensi'])
            ->when(Auth::user()->role === 'ketua_tempa', function($q) {
                return $q->where('mentor_id', Auth::id());
            })
            ->get();

        return view('pages.tempa.absensi.index', compact('pesertas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_peserta' => 'required|exists:tempa_peserta,id', // Validasi id harus ada di tabel
            'bulan' => 'required|numeric|min:1|max:12',
            'pertemuan_ke' => 'required|numeric|min:1|max:5',
            'status_hadir' => 'required|boolean',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $peserta = TempaPeserta::findOrFail($request->id_peserta);

        // Security Check
        if (Auth::user()->role === 'ketua_tempa' && $peserta->mentor_id !== Auth::id()) {
            return abort(403, 'Anda tidak berhak mengabsen peserta ini.');
        }

        // Simpan File ke folder public
        $path = $request->file('foto')->store('absensi_tempa', 'public');

        TempaAbsensi::updateOrCreate(
            [
                'id_peserta' => $request->id_peserta,
                'bulan' => $request->bulan,
                'pertemuan_ke' => $request->pertemuan_ke
            ],
            [
                'status_hadir' => $request->status_hadir,
                'foto_bukti' => $path
            ]
        );

        return back()->with('success', 'Absensi berhasil disimpan.');
    }
}
