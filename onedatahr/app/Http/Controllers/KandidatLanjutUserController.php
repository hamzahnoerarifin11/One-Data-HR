<?php

namespace App\Http\Controllers;

use App\Models\Kandidat;
use App\Models\KandidatLanjutUser;
use App\Models\InterviewHr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'tanggal_penyerahan' => 'required|date',
            'catatan' => 'nullable|string',
            // Validasi untuk array dinamis
            'detail_interview' => 'nullable|array',
            'detail_interview.*.nama_user' => 'required|string',
            'detail_interview.*.tanggal' => 'required|date',
            'detail_interview.*.hasil' => 'required|in:Lolos,Tidak Lolos,Pending',
        ]);

        $interviewHr = InterviewHr::where('kandidat_id', $request->kandidat_id)
            ->where('keputusan', 'DITERIMA')
            ->firstOrFail();

        DB::transaction(function () use ($request, $interviewHr) {
            $detail = $request->input('detail_interview', []);

            // Simpan data Lanjut User
            KandidatLanjutUser::create([
                'kandidat_id'          => $request->kandidat_id,
                'tanggal_interview_hr' => $interviewHr->hari_tanggal,
                'tanggal_penyerahan'   => $request->tanggal_penyerahan,
                'catatan'              => $request->catatan,
                'detail_interview'     => $detail,
            ]);

            // Update Status Akhir Kandidat
            $this->updateStatusKandidat($request->kandidat_id, $detail);
        });

        // KandidatLanjutUser::create([
        //     'kandidat_id'          => $request->kandidat_id,
        //     'tanggal_interview_hr' => $interviewHr->hari_tanggal,
        //     'tanggal_penyerahan'   => $request->tanggal_penyerahan,
        //     'catatan'              => $request->catatan,
        //     // Simpan array dinamis ke kolom detail_interview (JSON)
        //     'detail_interview'     => $request->input('detail_interview', []),
        // ]);

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
            'tanggal_penyerahan' => 'required|date',
            'catatan' => 'nullable|string',
            'detail_interview' => 'nullable|array',
            'detail_interview.*.hasil' => 'required|in:Lolos,Tidak Lolos,Pending',
        ]);
        DB::transaction(function () use ($request, $data) {
            $detail = $request->input('detail_interview', []);
            $data->update([
                'kandidat_id'        => $request->kandidat_id,
                'tanggal_penyerahan' => $request->tanggal_penyerahan,
                'catatan'            => $request->catatan,
                'detail_interview'   => $detail,
            ]);

            // Update Status Akhir Kandidat
            $this->updateStatusKandidat($request->kandidat_id, $detail);
        });
        return redirect()
            ->route('rekrutmen.kandidat_lanjut_user.index')
            ->with('success', 'Data kandidat berhasil diperbarui');
    }
    // private function updateStatusKandidat($kandidat_id, $detail_interview)
    // {
    //     $status_akhir = 'Interview HR Lolos'; // Default status awal

    //     if (!empty($detail_interview)) {
    //         $daftar_hasil = collect($detail_interview)->pluck('hasil')->toArray();

    //         if (in_array('Tidak Lolos', $daftar_hasil)) {
    //             $status_akhir = 'Tidak Lolos';
    //         } elseif (!in_array('Pending', $daftar_hasil) && !in_array('', $daftar_hasil)) {
    //             // Jika semua 'Lolos'
    //             $status_akhir = 'Interview User Lolos';
    //         }
    //     }

    //     Kandidat::where('id_kandidat', $kandidat_id)->update([
    //         'status_akhir' => $status_akhir
    //     ]);
    // }
    private function updateStatusKandidat($kandidat_id, $detail_interview)
    {
        // 1. Ambil instance model Kandidat
        $kandidat = Kandidat::findOrFail($kandidat_id);

        $status_akhir = 'Interview HR Lolos'; // Default status awal

        if (!empty($detail_interview)) {
            $daftar_hasil = collect($detail_interview)->pluck('hasil')->toArray();

            if (in_array('Tidak Lolos', $daftar_hasil)) {
                $status_akhir = 'Tidak Lolos';
            } elseif (!in_array('Pending', $daftar_hasil) && in_array('Lolos', $daftar_hasil)) {
                // Jika tidak ada pending dan ada yang lolos, anggap lolos user
                $status_akhir = 'Interview User Lolos';
            }
        }

        // 2. Update status_akhir melalui model instance
        $kandidat->status_akhir = $status_akhir;

        // 3. Simpan. Ini AKAN memicu KandidatObserver@saved dan mengupdate Progress Posisi otomatis
        $kandidat->save();
    }

    // public function destroy($id)
    // {
    //     $data = KandidatLanjutUser::where('id_kandidat_lanjut_user', $id)->first();
    //     if ($data) {
    //         // Kembalikan status kandidat ke HR Lolos sebelum data dihapus
    //         Kandidat::where('id_kandidat', $data->kandidat_id)->update(['status_akhir' => 'Interview HR Lolos']);
    //         $data->delete();
    //     }
    //     return back()->with('success', 'Data berhasil dihapus');
    // }
    public function destroy($id)
    {
        $data = KandidatLanjutUser::where('id_kandidat_lanjut_user', $id)->first();

        if ($data) {
            // 1. Cari model kandidatnya
            $kandidat = Kandidat::find($data->kandidat_id);

            if ($kandidat) {
                // 2. Kembalikan statusnya
                $kandidat->status_akhir = 'Interview HR Lolos';

                // 3. Simpan agar Observer menghitung ulang progress posisi menjadi 'Interview User'
                $kandidat->save();
            }

            // 4. Hapus data lanjut user
            $data->delete();
        }

        return back()->with('success', 'Data berhasil dihapus');
    }
}
