<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemberkasan;

class PemberkasanController extends Controller
{
    public function index()
    {
        $data = Pemberkasan::with('kandidat.posisi')->paginate(20);
        return view('pages.rekrutmen.pemberkasan.index', compact('data'));
    }

    public function create(Request $request)
    {
        $kandidat_id = $request->get('kandidat_id');
        return view('pages.rekrutmen.pemberkasan.create', compact('kandidat_id'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kandidat_id' => 'required|exists:kandidat,id_kandidat',
            'follow_up' => 'nullable|string',
            'kandidat_kirim_berkas' => 'nullable|date',
            'selesai_recruitment' => 'nullable|date',
        ]);
        Pemberkasan::create($data);
        return redirect()->route('pages.rekrutmen.pemberkasan.index')->with('success','Pemberkasan dibuat');
    }

    public function edit($id)
    {
        $item = Pemberkasan::findOrFail($id);
        // only admin
        abort_unless(auth()->user() && auth()->user()->role === 'admin', 403);
        return view('pages.rekrutmen.pemberkasan.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = Pemberkasan::findOrFail($id);
        // only admin
        abort_unless(auth()->user() && auth()->user()->role === 'admin', 403);
        $data = $request->validate([
            'follow_up' => 'nullable|string',
            'kandidat_kirim_berkas' => 'nullable|date',
            'selesai_recruitment' => 'nullable|date',
        ]);
        $item->update($data);
        return redirect()->route('pages.rekrutmen.pemberkasan.index')->with('success','Updated');
    }
}
