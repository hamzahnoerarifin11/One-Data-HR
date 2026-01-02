<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kandidat;
use App\Models\Posisi;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
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

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'posisi_id' => 'required|exists:posisi,id_posisi',
            'tanggal_melamar' => 'nullable|date',
            'sumber' => 'nullable|string|max:100',
            'link_cv' => 'nullable|url',
            'file_excel' => 'nullable|mimes:xlsx,xls|max:2048',
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
    // public function exportExcelToPdf($id)
    // {
    //     $kandidat = Kandidat::findOrFail($id);

    //     // 1. Cek apakah file excel ada
    //     if (!$kandidat->file_excel || !Storage::disk('public')->exists('uploads/excel/' . $kandidat->file_excel)) {
    //         return back()->with('error', 'File Excel tidak ditemukan.');
    //     }

    //     // 2. Baca isi file Excel menjadi Array
    //     // Kita asumsikan data ada di Sheet pertama
    //     $path = storage_path('app/public/uploads/excel/' . $kandidat->file_excel);
    //     $dataExcel = Excel::toArray([], $path)[0];

    //     // 3. Generate PDF menggunakan view khusus
    //     // Kita kirim $dataExcel ke dalam view
    //     $pdf = Pdf::loadView('pages.rekrutmen.kandidat.pdf_preview', [
    //         'kandidat' => $kandidat,
    //         'rows' => $dataExcel
    //     ]);

    //     // 4. Download sebagai PDF
    //     return $pdf->download('Laporan-Excel-' . $kandidat->nama . '.pdf');
    // }
    public function exportToPdf($id)
{
    $kandidat = Kandidat::findOrFail($id);
    $path = storage_path('app/public/uploads/excel/' . $kandidat->file_excel);
    $array = Excel::toArray([], $path)[0]; // Ambil Sheet pertama

    // MAPPING DATA (Sesuaikan index baris/kolom dengan file excel asli)
    // Contoh: Baris 7 di Excel adalah index 6 di array
    $dataLaporan = [
        [
            'aspek' => 'STABILITAS EMOSI',
            'desc_low' => $array[6][1], // Kolom B baris 7
            'score' => $array[6][2],    // Kolom C (Berisi 'KS' atau tanda centang)
            'desc_high' => $array[6][7] // Kolom H
        ],
        // ... teruskan untuk aspek lainnya sesuai koordinat Excel Anda
    ];

    $kesimpulan = $array[40][1] ?? 'Data tidak tersedia'; // Contoh koordinat kesimpulan

    $pdf = Pdf::loadView('pdf.laporan_psikologi', [
        'rows' => $dataLaporan,
        'kesimpulan' => $kesimpulan,
        'kandidat' => $kandidat
    ])->setPaper('a4', 'portrait');

    return $pdf->download('Laporan-Psikologi-'.$kandidat->nama.'.pdf');
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
            'file_excel' => 'nullable|mimes:xlsx,xls|max:2048',
        ]);

            if ($request->hasFile('file_excel')) {
            $file = $request->file('file_excel');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('uploads/excel', $filename, 'public');
            $data['file_excel'] = $filename;
        }

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

