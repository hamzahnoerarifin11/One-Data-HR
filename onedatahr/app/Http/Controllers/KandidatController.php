<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kandidat;
use App\Models\Posisi;
// use App\Models\RekrutmenDaily; // Pastikan model ini ada
// use Carbon\Carbon;

class KandidatController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua data tanpa pagination dulu untuk Alpine.js filter
        // Atau gunakan ->get() jika datanya belum ribuan
        $query = Kandidat::with('posisi')->orderBy('created_at','desc');
        
        if ($request->filled('posisi_id')) {
            $query->where('posisi_id', $request->posisi_id);
        }

        // Tips: Untuk Alpine.js search side-client, kita butuh koleksi data
        $kandidats = $query->get(); 
        // $posisis = Posisi::all();
        $posisis = Posisi::where('status', 'Aktif')->get();
        // ⬆️ JIKA KOLOM BERBEDA, GANTI BARIS INI SAJA:
        // $posisis = Posisi::where('is_active', 1)->get()

        
        return view('pages.rekrutmen.kandidat.index', compact('kandidats','posisis'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'posisi_id' => 'required|exists:posisi,id_posisi',
            'tanggal_melamar' => 'nullable|date',
            'sumber' => 'nullable|string|max:100',
        ]);

        $kandidat = Kandidat::create($data);

        // Jika request datang dari AJAX (Fetch)
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Kandidat berhasil ditambahkan', 'data' => $kandidat]);
        }

        return redirect()->route('rekrutmen.kandidat.index')->with('success','Kandidat created');
    }

    // Gunakan $id alih-alih Type-hint Kandidat jika binding bermasalah
    public function update(Request $request, $id)
    {
        $kandidat = Kandidat::findOrFail($id);

        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'posisi_id' => 'required|exists:posisi,id_posisi',
            'tanggal_melamar' => 'nullable|date',
            'sumber' => 'nullable|string|max:100',
            'status_akhir' => 'required|string',
        ]);

        $kandidat->update($data);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Data berhasil diperbarui']);
        }

        return redirect()->route('rekrutmen.kandidat.index')->with('success','Kandidat updated');
    }

    public function destroy($id)
    {
        $kandidat = Kandidat::findOrFail($id);
        $kandidat->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Kandidat berhasil dihapus']);
        }

        return redirect()->route('rekrutmen.kandidat.index')->with('success','Kandidat deleted');
    }

    // Method lainnya tetap sama...

    /**
     * Return a JSON list of candidates for use in ajax selects (filtered by posisi or q)
     */
    public function list(Request $request)
    {
        $query = Kandidat::orderBy('created_at','desc');
        if ($request->filled('posisi_id')) {
            $query->where('posisi_id', $request->posisi_id);
        }
        if ($request->filled('q')) {
            $query->where('nama', 'like', '%'.$request->q.'%');
        }
        $c = $query->limit(50)->get(['id_kandidat','nama']);
        return response()->json($c);
    }
}

