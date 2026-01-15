<?php

namespace App\Http\Controllers;

use App\Models\TempaPeserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TempaPesertaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('viewTempaPeserta');

        $pesertas = TempaPeserta::with(['kelompok', 'mentor'])->get();
        return view('pages.tempa.peserta.index', compact('pesertas'));
    }

    public function create()
    {
        $this->authorize('createTempaPeserta');

        // Ambil data kelompok dan mentor dari model yang ada
        $kelompoks = \App\Models\TempaKelompok::all();
        $mentors = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'ketua_tempa');
        })->get();

        return view('pages.tempa.peserta.create', compact('kelompoks', 'mentors'));
    }

    public function store(\App\Http\Requests\TempaPesertaRequest $request)
    {
        TempaPeserta::create($request->validated());

        return redirect()->route('tempa.peserta.index')->with('success', 'Peserta berhasil ditambahkan');
    }

    public function edit($id)
    {
        $this->authorize('editTempaPeserta');

        $peserta = TempaPeserta::findOrFail($id);
        $kelompoks = \App\Models\TempaKelompok::all();
        $mentors = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'ketua_tempa');
        })->get();

        return view('pages.tempa.peserta.edit', compact('peserta', 'kelompoks', 'mentors'));
    }

    public function update(\App\Http\Requests\TempaPesertaRequest $request, $id)
    {
        $peserta = TempaPeserta::findOrFail($id);
        $peserta->update($request->validated());

        return redirect()->route('tempa.peserta.index')->with('success', 'Peserta berhasil diperbarui');
    }

    public function destroy($id)
    {
        $this->authorize('deleteTempaPeserta');

        $peserta = TempaPeserta::findOrFail($id);
        $peserta->delete();

        return redirect()->route('tempa.peserta.index')->with('success', 'Peserta berhasil dihapus');
    }
}
