<?php

namespace App\Http\Controllers;

use App\Models\Kandidat;
use App\Models\KandidatLanjutUser;
use App\Models\InterviewHr;
use Illuminate\Http\Request;

class KandidatLanjutUserController extends Controller
{
    public function index()
    {
        $data = KandidatLanjutUser::with('kandidat.posisi')
            ->orderByDesc('created_at')
            ->get();

        return view('pages.rekrutmen.kandidat_lanjut_user.index', compact('data'));
    }

    public function create()
    {
        /**
         * Kandidat yang boleh lanjut:
         * - Sudah interview HR
         * - Keputusan = DITERIMA
         * - Belum ada di kandidat_lanjut_user
         */
        $kandidat = InterviewHr::with('kandidat.posisi')
            ->where('keputusan', 'DITERIMA')
            ->whereDoesntHave('kandidat.kandidatLanjutUser')
            ->get();

        return view('pages.rekrutmen.kandidat_lanjut_user.create', compact('kandidat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kandidat_id' => 'required|exists:kandidat,id_kandidat',
            'user_terkait' => 'required|string|max:100',

            'tanggal_penyerahan' => 'required|date',
            'tanggal_interview_user_ass' => 'nullable|date',
            'hasil_ass' => 'nullable|in:Lolos,Tidak Lolos',

            'tanggal_interview_user_asm' => 'nullable|date',
            'hasil_asm' => 'nullable|in:Lolos,Tidak Lolos',

            'catatan' => 'nullable|string',
        ]);

        /**
         * Ambil data interview HR kandidat
         */
        $interviewHr = InterviewHr::where('kandidat_id', $request->kandidat_id)
            ->where('keputusan', 'DITERIMA')
            ->firstOrFail();

        KandidatLanjutUser::create([
            'kandidat_id' => $request->kandidat_id,
            'user_terkait' => $request->user_terkait,

            'tanggal_interview_hr' => $interviewHr->hari_tanggal,
            'tanggal_penyerahan' => $request->tanggal_penyerahan,

            'tanggal_interview_user_ass' => $request->tanggal_interview_user_ass,
            'hasil_ass' => $request->hasil_ass,

            'tanggal_interview_user_asm' => $request->tanggal_interview_user_asm,
            'hasil_asm' => $request->hasil_asm,

            'catatan' => $request->catatan,
        ]);

        return redirect()
            ->route('rekrutmen.kandidat_lanjut_user.index')
            ->with('success', 'Kandidat berhasil dilanjutkan ke tahap User');
    }

    public function show($id)
    {
        $data = KandidatLanjutUser::with('kandidat.posisi')
            ->where('id_kandidat_lanjut_user', $id)
            ->firstOrFail();

        return view('pages.rekrutmen.kandidat_lanjut_user.show', compact('data'));
    }

    public function edit($id)
    {
        $data = KandidatLanjutUser::where('id_kandidat_lanjut_user', $id)
            ->firstOrFail();

        $kandidat = InterviewHr::with('kandidat.posisi')
            ->where('keputusan', 'DITERIMA')
            ->get();

        return view('pages.rekrutmen.kandidat_lanjut_user.edit', compact('data', 'kandidat'));
    }

    public function update(Request $request, $id)
    {
        $data = KandidatLanjutUser::where('id_kandidat_lanjut_user', $id)
            ->firstOrFail();

        $request->validate([
            'kandidat_id' => 'required|exists:kandidat,id_kandidat',
            'user_terkait' => 'required|string|max:100',

            'tanggal_penyerahan' => 'required|date',
            'tanggal_interview_user_ass' => 'nullable|date',
            'hasil_ass' => 'nullable|in:Lolos,Tidak Lolos',

            'tanggal_interview_user_asm' => 'nullable|date',
            'hasil_asm' => 'nullable|in:Lolos,Tidak Lolos',

            'catatan' => 'nullable|string',
        ]);

        $data->update($request->only([
            'kandidat_id',
            'user_terkait',
            'tanggal_penyerahan',
            'tanggal_interview_user_ass',
            'hasil_ass',
            'tanggal_interview_user_asm',
            'hasil_asm',
            'catatan',
        ]));

        return redirect()
            ->route('rekrutmen.kandidat_lanjut_user.index')
            ->with('success', 'Data kandidat berhasil diperbarui');
    }

    public function destroy($id)
    {
        KandidatLanjutUser::where('id_kandidat_lanjut_user', $id)->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }
}
