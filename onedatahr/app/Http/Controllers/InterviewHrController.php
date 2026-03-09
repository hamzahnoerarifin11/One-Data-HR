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
    //     $kandidat = Kandidat::where('status_akhir','CV Lolos')->get();
    //     return view('pages.rekrutmen.interview_hr.create', compact('kandidat','posisi'));

    // }
    // public function create()
    // {
    //     // 1. Tambahkan with('posisi')
    //     // 2. Pastikan filter status_akhir sesuai dengan data yang ada di database Anda
    //     $kandidat = Kandidat::with('posisi')
    //         ->where('status_akhir', 'CV Lolos') // Sesuaikan status ini (misal: 'Proses' atau 'Interview HR')
    //         ->whereDoesntHave('interviewHr')
    //         ->get();

    //     return view('pages.rekrutmen.interview_hr.create', compact('kandidat'));
    // }
    public function create()
    {
        // 1. Definisikan status-status yang diperbolehkan untuk masuk tahap Interview HR
        $statusDiperbolehkan = [
            'CV Lolos',
            'Psikotes Lolos',
            'Tes Kompetensi Lolos'
        ];

        // 2. Gunakan whereIn untuk memfilter berdasarkan array di atas
        $kandidat = Kandidat::with('posisi')
            ->whereIn('status_akhir', $statusDiperbolehkan)
            ->whereDoesntHave('interviewHr') // Memastikan belum ada data interview HR-nya
            ->get();

        return view('pages.rekrutmen.interview_hr.create', compact('kandidat'));
    }

    public function store(Request $request)
    {
        $kandidat = Kandidat::findOrFail($request->kandidat_id);
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
            'posisi_id' => $kandidat->posisi_id,
            'total' => $total
        ]);

        // update status kandidat
        // $status = $request->keputusan == 'DITERIMA'
        //     ? 'Interview HR Lolos'
        //     : 'Tidak Lolos';

        // Kandidat::where('id_kandidat',$request->kandidat_id)
        //     ->update(['status_akhir'=>$status]);
        $status = $request->keputusan == 'DITERIMA' ? 'Interview HR Lolos' : 'Tidak Lolos';
        $kandidat->status_akhir = $status;
        $kandidat->save();

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

    // public function update(Request $request, $id)
    // {
    //     $interview = InterviewHr::findOrFail($id);
    //     $kandidat = Kandidat::findOrFail($request->kandidat_id);
    //     $total =
    //         $request->skor_profesional +
    //         $request->skor_spiritual +
    //         $request->skor_learning +
    //         $request->skor_initiative +
    //         $request->skor_komunikasi +
    //         $request->skor_problem_solving +
    //         $request->skor_teamwork;

    //     $interview->update([
    //         ...$request->all(),
    //         'posisi_id' => $kandidat->posisi_id,
    //         'total'=>$total
    //     ]);
    //     // 3. Update status kandidat (jika keputusan berubah saat edit)
    //     // $status = $request->keputusan == 'DITERIMA'
    //     //     ? 'Interview HR Lolos'
    //     //     : 'Tidak Lolos';

    //     // $kandidat->update(['status_akhir' => $status]);
    //     $status = $request->keputusan == 'DITERIMA' ? 'Interview HR Lolos' : 'Tidak Lolos';
    //     $kandidat->status_akhir = $status;
    //     $kandidat->save();

    //     return redirect()->route('rekrutmen.interview_hr.index')
    //         ->with('success','Data interview diperbarui');
    // }
    public function update(Request $request, $id)
{
    // 1. Cari data interview
    $interview = InterviewHr::findOrFail($id);

    // 2. Ambil kandidat_id dari request (pastikan sudah ada input hidden di blade)
    $kandidat = Kandidat::findOrFail($request->kandidat_id);

    // 3. Hitung total skor secara manual
    $total = (int)$request->skor_profesional +
             (int)$request->skor_spiritual +
             (int)$request->skor_learning +
             (int)$request->skor_initiative +
             (int)$request->skor_komunikasi +
             (int)$request->skor_problem_solving +
             (int)$request->skor_teamwork;

    // 4. Update data interview
    $interview->update([
        'hari_tanggal'     => $request->hari_tanggal,
        'nama_interviewer' => $request->nama_interviewer,
        'model_wawancara'  => $request->model_wawancara,
        'skor_profesional' => $request->skor_profesional,
        'catatan_profesional' => $request->catatan_profesional,
        'skor_spiritual'   => $request->skor_spiritual,
        'catatan_spiritual' => $request->catatan_spiritual,
        'skor_learning'    => $request->skor_learning,
        'catatan_learning' => $request->catatan_learning,
        'skor_initiative'  => $request->skor_initiative,
        'catatan_initiative' => $request->catatan_initiative,
        'skor_komunikasi'  => $request->skor_komunikasi,
        'catatan_komunikasi' => $request->catatan_komunikasi,
        'skor_problem_solving' => $request->skor_problem_solving,
        'catatan_problem_solving' => $request->catatan_problem_solving,
        'skor_teamwork'    => $request->skor_teamwork,
        'catatan_teamwork' => $request->catatan_teamwork,
        'catatan_tambahan' => $request->catatan_tambahan,
        'keputusan'        => $request->keputusan,
        'hasil_akhir'      => $request->hasil_akhir,
        'total'            => $total,
        'posisi_id'        => $kandidat->posisi_id, // Tetap sinkronkan posisi_id
    ]);

    // 5. Update status kandidat sesuai keputusan terbaru
    $status = ($request->keputusan == 'DITERIMA') ? 'Interview HR Lolos' : 'Tidak Lolos';
    $kandidat->status_akhir = $status;
    $kandidat->save();

    return redirect()->route('rekrutmen.interview_hr.index')
        ->with('success', 'Data interview berhasil diperbarui');
}

    // public function destroy($id)
    // {
    //     // InterviewHr::destroy($id);
    //     $interview = InterviewHr::where('id_interview_hr', $id)->first();
    //     if ($interview) {
    //         // Kembalikan status kandidat ke HR Lolos sebelum data dihapus
    //         Kandidat::where('id_kandidat', $interview->kandidat_id)->update(['status_akhir' => 'CV Lolos']);
    //         $interview->delete();
    //     }
    //     return back()->with('success','Data berhasil dihapus');
    // }
//     public function destroy($id)
// {
//     $interview = InterviewHr::where('id_interview_hr', $id)->first();
//     if ($interview) {
//         // ✅ AMBIL MODEL KANDIDATNYA DULU
//         $kandidat = Kandidat::find($interview->kandidat_id);

//         if ($kandidat) {
//             $kandidat->status_akhir = 'CV Lolos';
//             $kandidat->save(); // Sekarang Observer akan terpanggil dan progress rekrutmen akan turun otomatis
//         }

//         $interview->delete();
//     }
//     return back()->with('success','Data berhasil dihapus');
//     }
        public function destroy($id)
        {
            $interview = InterviewHr::findOrFail($id);
            $kandidat = Kandidat::find($interview->kandidat_id);

            if ($kandidat) {
                // Turunkan status kandidat
                $kandidat->status_akhir = 'CV Lolos';
                // Saat save() dipanggil, KandidatObserver akan otomatis
                // menghitung ulang progress rekrutmen di tabel Posisi
                $kandidat->save();
            }

            $interview->delete();
            return back()->with('success', 'Data berhasil dihapus');
        }
}
