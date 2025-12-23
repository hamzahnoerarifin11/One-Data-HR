<?php

namespace App\Http\Controllers;

use App\Models\WigRekrutmen;
use App\Models\Posisi;
use Illuminate\Http\Request;

class WigRekrutmenController extends Controller
{
    public function index()
    {
        $data = WigRekrutmen::with('posisi')->latest()->get();
        return view('pages.rekrutmen.wig.index', compact('data'));
    }

    public function create()
    {
        $posisi = Posisi::orderBy('nama_posisi')->get();
        return view('pages.rekrutmen.wig.create', compact('posisi'));
    }

    public function store(Request $request)
    {
        WigRekrutmen::create($request->all());
        return redirect()->route('wig-rekrutmen.index')->with('success','Data berhasil disimpan');
    }

    public function show($id)
    {
        $data = WigRekrutmen::with('posisi')->findOrFail($id);
        return view('pages.rekrutmen.wig.show', compact('data'));
    }

    public function edit($id)
    {
        $data = WigRekrutmen::findOrFail($id);
        $posisi = Posisi::orderBy('nama_posisi')->get();
        return view('pages.rekrutmen.wig.edit', compact('data','posisi'));
    }

    public function update(Request $request, $id)
    {
        WigRekrutmen::findOrFail($id)->update($request->all());
        return redirect()->route('wig-rekrutmen.index')->with('success','Data diperbarui');
    }

    public function destroy($id)
    {
        WigRekrutmen::findOrFail($id)->delete();
        return back()->with('success','Data dihapus');
    }
}
