<?php

namespace App\Http\Controllers;

use App\Models\InterviewHr;
use App\Models\Kandidat;
use Illuminate\Http\Request;

class InterviewHrController extends Controller
{
    public function index()
    {
        $data = InterviewHr::with('kandidat','posisi')->latest()->get();
        return view('pages.rekrutmen.interview_hr.index', compact('data'));
    }

    // public function create()
    // {
    //     $kandidat = Kandidat::where('status_akhir','Interview HR Lolos')->get();
    //     return view('pages.rekrutmen.interview_hr.create', compact('kandidat','posisi'));

    // }
    public function create()
    {
        // 1. Tambahkan with('posisi')
        // 2. Pastikan filter status_akhir sesuai dengan data yang ada di database Anda
        $kandidat = Kandidat::with('posisi')
            ->where('status_akhir', 'Interview HR Lolos') // Sesuaikan status ini (misal: 'Proses' atau 'Interview HR')
            ->whereDoesntHave('interviewHr')
            ->get();

        return view('pages.rekrutmen.interview_hr.create', compact('kandidat'));
    }

    public function store(Request $request)
    {
        $total =
            $request->skor_profesional +
            $request->skor_spiritual +
            $request->skor_learning +
            $request->skor_initiative +
            $request->skor_komunikasi +
            $request->skor_problem_solving +
            $request->skor_teamwork;

        $interview = InterviewHr::create([
            ...$request->all(),
            'total' => $total
        ]);

        // update status kandidat
        $status = $request->keputusan == 'DITERIMA'
            ? 'Interview HR Lolos'
            : 'Tidak Lolos';

        Kandidat::where('id_kandidat',$request->kandidat_id)
            ->update(['status_akhir'=>$status]);

        return redirect()->route('rekrutmen.interview_hr.index')
            ->with('success','Interview HR berhasil disimpan');
    }

    public function show($id)
    {
        $interview = InterviewHr::with('kandidat','posisi')->findOrFail($id);
        return view('pages.rekrutmen.interview_hr.show', compact('interview'));
    }

    public function edit($id)
    {
        $interview = InterviewHr::findOrFail($id);
        return view('pages.rekrutmen.interview_hr.edit', compact('interview'));
    }

    public function update(Request $request, $id)
    {
        $interview = InterviewHr::findOrFail($id);

        $total =
            $request->skor_profesional +
            $request->skor_spiritual +
            $request->skor_learning +
            $request->skor_initiative +
            $request->skor_komunikasi +
            $request->skor_problem_solving +
            $request->skor_teamwork;

        $interview->update([
            ...$request->all(),
            'total'=>$total
        ]);

        return redirect()->route('rekrutmen.interview_hr.index')
            ->with('success','Data interview diperbarui');
    }

    public function destroy($id)
    {
        InterviewHr::destroy($id);
        return back()->with('success','Data berhasil dihapus');
    }
}
