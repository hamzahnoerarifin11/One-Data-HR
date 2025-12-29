<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Training;
use App\Models\Kandidat;
use App\Models\Posisi;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    public function index()
    {
        $training = Training::with(['kandidat', 'posisi'])->get();
        return view('pages.training.index', compact('training'));
    }

    public function create()
    {
        $kandidat = Kandidat::all();
        $posisi = Posisi::all();
        return view('pages.training.create', compact('kandidat', 'posisi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kandidat_id' => 'required',
            'posisi_id' => 'required',
            'hasil_evaluasi' => 'required'
        ]);

        Training::create($request->all());
        return redirect()->route('training.index')->with('success', 'Data training berhasil ditambahkan');
    }

    public function show($id)
    {
        $training = Training::with(['kandidat', 'posisi'])->findOrFail($id);
        return view('pages.training.show', compact('training'));
    }

    public function edit($id)
    {
        $training = Training::findOrFail($id);
        $kandidat = Kandidat::all();
        $posisi = Posisi::all();
        return view('pages.training.edit', compact('training', 'kandidat', 'posisi'));
    }

    public function update(Request $request, $id)
    {
        $training = Training::findOrFail($id);
        $training->update($request->all());
        return redirect()->route('training.index')->with('success', 'Data training berhasil diperbarui');
    }

    public function destroy($id)
    {
        Training::findOrFail($id)->delete();
        return redirect()->route('training.index')->with('success', 'Data training berhasil dihapus');
    }
}
