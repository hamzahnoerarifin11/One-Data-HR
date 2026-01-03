<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kandidat;
use App\Models\Posisi;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Concerns\ToArray;

// use App\Models\RekrutmenDaily; // Pastikan model ini ada
// use Carbon\Carbon;

class KandidatController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua data tanpa pagination dulu untuk Alpine.js filter
        // Atau gunakan ->get() jika datanya belum ribuan
        $query = Kandidat::with('posisi')->orderBy('created_at','desc');

        if ($request->filled('posisi_id')) {
            $query->where('posisi_id', $request->posisi_id);
        }

        // Tips: Untuk Alpine.js search side-client, kita butuh koleksi data
        $kandidats = $query->get();
        // $posisis = Posisi::all();
        $posisis = Posisi::where('status', 'Aktif')->get();
        // ⬆️ JIKA KOLOM BERBEDA, GANTI BARIS INI SAJA:
        // $posisis = Posisi::where('is_active', 1)->get()


        return view('pages.rekrutmen.kandidat.index', compact('kandidats','posisis'));
    }
    public function downloadExcel($id)
    {
        $kandidat = Kandidat::findOrFail($id);
        $path = 'uploads/excel/' . $kandidat->file_excel;

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->download($path);
        }

        return back()->with('error', 'File tidak ditemukan.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'posisi_id' => 'required|exists:posisi,id_posisi',
            'tanggal_melamar' => 'nullable|date',
            'sumber' => 'nullable|string|max:100',
            'link_cv' => 'nullable|url',
            'file_excel' => 'nullable|mimes:xlsx,xls|max:10000',
        ]);

            if ($request->hasFile('file_excel')) {
            $file = $request->file('file_excel');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('uploads/excel', $filename, 'public');
            $data['file_excel'] = $filename;
        }

        $kandidat = Kandidat::create($data);

        // Jika request datang dari AJAX (Fetch)
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Kandidat berhasil ditambahkan', 'data' => $kandidat]);
        }

        return redirect()->route('rekrutmen.kandidat.index')->with('success','Kandidat created');
    }


 public function generateLaporan($id)
{
    $kandidat = Kandidat::with('posisi')->findOrFail($id);

    if (!$kandidat->file_excel) {
        abort(404, 'File excel belum tersedia');
    }

    $path = Storage::disk('public')->path('uploads/excel/' . $kandidat->file_excel);

    if (!file_exists($path)) {
        abort(404, 'File excel tidak ditemukan');
    }

    // ✅ BACA EXCEL LANGSUNG (PALING STABIL)
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
    $sheet = $spreadsheet->getActiveSheet()->toArray();

    $payload = [
        'kandidat'    => $kandidat,
        'tanggal_tes' => $sheet[2][1] ?? date('d/m/Y'),
        'psikogram'   => [
            [
                'aspek' => 'STABILITAS EMOSI',
                'score' => $sheet[4][12] ?? '-',
                'desc'  => $sheet[4][13] ?? '-',
            ],
            [
                'aspek' => 'RELASI SOSIAL',
                'score' => $sheet[5][12] ?? '-',
                'desc'  => $sheet[5][13] ?? '-',
            ],
        ],
        'kesimpulan'  => $sheet[44][1] ?? '-',
        'saran'       => $sheet[40][1] ?? '-',
    ];

    $pdf = Pdf::loadView(
        'pages.rekrutmen.kandidat.pdf_preview',
        $payload
    )->setPaper('A4', 'portrait');

    return response()->streamDownload(
        fn () => print($pdf->output()),
        'Laporan_' . str_replace(' ', '_', $kandidat->nama) . '.pdf',
        ['Content-Type' => 'application/pdf']
    );
}



  // Gunakan $id alih-alih Type-hint Kandidat jika binding bermasalah
    public function update(Request $request, $id)
    {
        $kandidat = Kandidat::findOrFail($id);

        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'posisi_id' => 'required|exists:posisi,id_posisi',
            'tanggal_melamar' => 'nullable|date',
            'sumber' => 'nullable|string|max:100',
            'status_akhir' => 'required|string',
            'link_cv' => 'nullable|url',
            'file_excel' => 'nullable|mimes:xlsx,xls|max:10000',
        ]);
        if ($request->hasFile('file_excel')) {
        // Jika ada file baru, hapus yang lama dan simpan yang baru
        if ($kandidat->file_excel) {
            Storage::disk('public')->delete('uploads/excel/' . $kandidat->file_excel);
        }

        $file = $request->file('file_excel');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('uploads/excel', $filename, 'public');
        $data['file_excel'] = $filename;
    } else {
        // JIKA TIDAK ADA FILE BARU:
        // Jangan timpa kolom file_excel (hapus dari array $data)
        unset($data['file_excel']);
    }

    //       if ($request->hasFile('file_excel')) {
    //     // Hapus file lama jika ada
    //     if ($kandidat->file_excel) {
    //         Storage::disk('public')->delete('uploads/excel/' . $kandidat->file_excel);
    //     }

    //     $file = $request->file('file_excel');
    //     $filename = time() . '_' . $file->getClientOriginalName();
    //     // Pastikan folder uploads/excel sudah ada di public storage
    //     $file->storeAs('uploads/excel', $filename, 'public');
    //     $data['file_excel'] = $filename;
    // }

        $kandidat->update($data);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Data berhasil diperbarui']);
        }

        return redirect()->route('rekrutmen.kandidat.index')->with('success','Kandidat updated');
    }

    public function destroy($id)
    {
        $kandidat = Kandidat::findOrFail($id);
        $kandidat->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Kandidat berhasil dihapus']);
        }

        return redirect()->route('rekrutmen.kandidat.index')->with('success','Kandidat deleted');
    }

    // Method lainnya tetap sama...

    /**
     * Return a JSON list of candidates for use in ajax selects (filtered by posisi or q)
     */
    public function list(Request $request)
    {
        $query = Kandidat::orderBy('created_at','desc');
        if ($request->filled('posisi_id')) {
            $query->where('posisi_id', $request->posisi_id);
        }
        if ($request->filled('q')) {
            $query->where('nama', 'like', '%'.$request->q.'%');
        }
        $c = $query->limit(50)->get(['id_kandidat','nama']);
        return response()->json($c);
    }
}

