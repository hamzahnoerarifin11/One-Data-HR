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
        $kandidat = Kandidat::where('status_akhir', 'Interview User Lolos')
        ->whereDoesntHave('training')
        ->get();
        $posisi = Posisi::all();
        return view('pages.training.create', compact('kandidat', 'posisi'));
    }

    public function store(Request $request)
    {
    $validated = $request->validate([
        'kandidat_id'         => 'required|exists:kandidat,id_kandidat',
        'posisi_id'           => 'required|exists:posisi,id_posisi',
        'tanggal_mulai'       => 'nullable|date',
        'tanggal_selesai'     => 'nullable|date',
        'jadwal_ttd_kontrak'  => 'nullable|date',
        'hasil_evaluasi'      => 'required|string',
        'keterangan_tambahan' => 'nullable|string',
    ]);

    Training::create($validated); // Gunakan $validated lebih aman daripada $request->all()

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
        $kandidat = Kandidat::where(function($query) use ($training) {
            $query->where('status_akhir', 'Interview User Lolos')
                  ->whereDoesntHave('training');
        })
        ->orWhere('id_kandidat', $training->kandidat_id)
        ->get();
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
