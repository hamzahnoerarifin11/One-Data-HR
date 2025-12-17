<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProsesRekrutmen;
use App\Models\Kandidat;

class ProsesRekrutmenController extends Controller
{
    public function edit($id)
    {
        $proses = ProsesRekrutmen::where('kandidat_id',$id)->first();
        $kandidat = Kandidat::findOrFail($id);
        // only admin can edit process
        abort_unless(auth()->user() && auth()->user()->role === 'admin', 403);
        return view('rekrutmen.proses.edit', compact('proses','kandidat'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kandidat_id' => 'required|exists:kandidat,id_kandidat',
            'cv_lolos' => 'nullable',
            'tanggal_cv' => 'nullable|date',
            'psikotes_lolos' => 'nullable',
            'tanggal_psikotes' => 'nullable|date',
            'tes_kompetensi_lolos' => 'nullable',
            'tanggal_tes_kompetensi' => 'nullable|date',
            'interview_hr_lolos' => 'nullable',
            'tanggal_interview_hr' => 'nullable|date',
            'interview_user_lolos' => 'nullable',
            'tanggal_interview_user' => 'nullable|date',
            'tahap_terakhir' => 'nullable|string',
        ]);

        // Normalize checkbox values (checkboxes won't be present when unchecked)
        $validated['cv_lolos'] = $request->has('cv_lolos') ? 1 : 0;
        $validated['psikotes_lolos'] = $request->has('psikotes_lolos') ? 1 : 0;
        $validated['tes_kompetensi_lolos'] = $request->has('tes_kompetensi_lolos') ? 1 : 0;
        $validated['interview_hr_lolos'] = $request->has('interview_hr_lolos') ? 1 : 0;
        $validated['interview_user_lolos'] = $request->has('interview_user_lolos') ? 1 : 0;

        // restrict updates to admins only
        abort_unless(auth()->user() && auth()->user()->role === 'admin', 403);
        ProsesRekrutmen::updateOrCreate(['kandidat_id'=>$validated['kandidat_id']],$validated);
        return back()->with('success','Proses updated');
    }
}
