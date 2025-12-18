<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kandidat;
use App\Models\Posisi;

class KandidatController extends Controller
{
    public function index(Request $request)
    {
        $query = Kandidat::with('posisi')->orderBy('created_at','desc');
        if ($request->filled('posisi_id')) {
            $query->where('posisi_id', $request->posisi_id);
        }
        $kandidats = $query->paginate(20);
        $posisis = Posisi::all();
        return view('pages.rekrutmen.kandidat.index', compact('kandidats','posisis'));
    }

    public function create()
    {
        $posisis = Posisi::all();
        return view('pages.rekrutmen.kandidat.create', compact('posisis'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'posisi_id' => 'required|exists:posisi,id_posisi',
            'tanggal_melamar' => 'nullable|date',
            'sumber' => 'nullable|string|max:100',
        ]);

        Kandidat::create($data);

        return redirect()->route('rekrutmen.kandidat.index')->with('success','Kandidat created');
    }

    public function show(Kandidat $kandidat)
    {
        $kandidat->load('posisi','proses','pemberkasan');
        return view('pages.rekrutmen.kandidat.show', compact('kandidat'));
    }

    public function edit(Kandidat $kandidat)
    {
        $posisis = Posisi::all();
        return view('pages.rekrutmen.kandidat.edit', compact('kandidat','posisis'));
    }

    public function update(Request $request, Kandidat $kandidat)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'posisi_id' => 'required|exists:posisi,id_posisi',
            'tanggal_melamar' => 'nullable|date',
            'sumber' => 'nullable|string|max:100',
            'status_akhir' => 'nullable|string|max:100',
        ]);

        $kandidat->update($data);
        return redirect()->route('rekrutmen.kandidat.index')->with('success','Kandidat updated');
    }

    public function destroy(Kandidat $kandidat)
    {
        $kandidat->delete();
        return redirect()->route('rekrutmen.kandidat.index')->with('success','Kandidat deleted');
    }

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

