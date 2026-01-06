<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kandidat;
use App\Models\Posisi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;


class KandidatController extends Controller
{
    public function index(Request $request)
    {
        $query = Kandidat::with('posisi')
            ->orderBy('created_at', 'desc');

        if ($request->filled('posisi_id')) {
            $query->where('posisi_id', $request->posisi_id);
        }

        $kandidats = $query->get();
        $posisis   = Posisi::where('status', 'Aktif')->get();

        return view(
            'pages.rekrutmen.kandidat.index',
            compact('kandidats', 'posisis')
        );
    }

    // public function downloadExcel($id)
    // {
    //     $kandidat = Kandidat::findOrFail($id);
    //     $path = 'uploads/excel/' . $kandidat->file_excel;

    //     if (Storage::disk('public')->exists($path)) {
    //         return Storage::disk('public')->download($path);
    //     }

    //     return back()->with('error', 'File tidak ditemukan.');
    // }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'             => 'required|string|max:150',
            'posisi_id'        => 'required|exists:posisi,id_posisi',
            'tanggal_melamar'  => 'nullable|date',
            'sumber'           => 'nullable|string|max:100',
            'link_cv'          => 'nullable|url',
            'file_excel'       => 'nullable|mimes:xlsx,xls|max:10000',
        ]);

        if ($request->hasFile('file_excel')) {
            $file = $request->file('file_excel');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('uploads/excel', $filename, 'public');
            $data['file_excel'] = $filename;
        }

        $kandidat = Kandidat::create($data);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Kandidat berhasil ditambahkan',
                'data'    => $kandidat
            ]);
        }

        return redirect()
            ->route('rekrutmen.kandidat.index')
            ->with('success', 'Kandidat berhasil ditambahkan');
    }
    public function downloadExcel($id)
    {
        $kandidat = Kandidat::findOrFail($id);

        if (!$kandidat->excel_path || !Storage::disk('public')->exists($kandidat->excel_path)) {
            return back()->with('error', 'File Excel tidak ditemukan');
        }

        return Storage::disk('public')->download(
            $kandidat->excel_path,
            $kandidat->file_excel
        );
    }
    private function generateCsvPreview($excelPath)
    {
        $csvPath = str_replace('.xlsx', '.csv', $excelPath);

        $reader = new Xlsx();
        $reader->setReadDataOnly(true);

        $spreadsheet = $reader->load($excelPath);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
        $writer->setDelimiter(';');
        $writer->setEnclosure('');
        $writer->setSheetIndex(0);
        $writer->save($csvPath);

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return $csvPath;
    }

    public function previewExcel($id)
    {
        set_time_limit(120);
        ini_set('memory_limit', '512M');

        $kandidat = Kandidat::findOrFail($id);

        if (!$kandidat->excel_path) {
            abort(404, 'File Excel belum tersedia');
        }

        $path = Storage::disk('public')->path($kandidat->excel_path);

        if (!file_exists($path)) {
            abort(404, 'File Excel tidak ditemukan');
        }

        // ✅ GUNAKAN HTML READER (PALING CEPAT)
        $reader = IOFactory::createReader('Html');
        $reader->setReadDataOnly(true);

        $spreadsheet = IOFactory::load($path);

        ob_start();
        $writer = IOFactory::createWriter($spreadsheet, 'Html');
        $writer->save('php://output');
        $html = ob_get_clean();

        return view('pages.rekrutmen.kandidat.preview-excel-html', [
            'kandidat' => $kandidat,
            'html'     => $html
        ]);
    }



    public function generateLaporan($id)
    {
        set_time_limit(120);
        ini_set('memory_limit', '512M');

        $kandidat = Kandidat::with('posisi')->findOrFail($id);

        if (!$kandidat->excel_path) {
            abort(404, 'File Excel belum diupload');
        }

        $path = Storage::disk('public')->path($kandidat->excel_path);

        if (!file_exists($path)) {
            abort(404, 'File Excel tidak ditemukan');
        }

        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getSheet(0);

        $cell = fn ($c) => trim((string) $sheet->getCell($c)->getValue());

        $data = [
            'kandidat' => $kandidat,
            'tanggal_tes' => now()->format('d/m/Y'),
            'psikogram' => [
                ['aspek' => 'Stabilitas Emosi', 'score' => $cell('M5') ?: '-', 'desc' => $cell('N5') ?: '-'],
                ['aspek' => 'Relasi Sosial', 'score' => $cell('M6') ?: '-', 'desc' => $cell('N6') ?: '-'],
            ],
            'kesimpulan' => $cell('B45') ?: '-',
            'saran'      => $cell('B41') ?: '-',
        ];

        return Pdf::loadView('pages.rekrutmen.kandidat.pdf_preview', $data)
            ->setPaper('A4', 'portrait')
            ->download('Laporan_' . str_replace(' ', '_', $kandidat->nama) . '.pdf');
    }



    public function update(Request $request, $id)
    {
        $kandidat = Kandidat::findOrFail($id);

        $data = $request->validate([
            'nama'             => 'required|string|max:150',
            'posisi_id'        => 'required|exists:posisi,id_posisi',
            'tanggal_melamar'  => 'nullable|date',
            'sumber'           => 'nullable|string|max:100',
            'status_akhir'     => 'required|string',
            'link_cv'          => 'nullable|url',
            'file_excel'       => 'nullable|mimes:xlsx,xls|max:10000',
        ]);

        if ($request->hasFile('file_excel')) {
            if ($kandidat->file_excel) {
                Storage::disk('public')->delete(
                    'uploads/excel/' . $kandidat->file_excel
                );
            }

            $file = $request->file('file_excel');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('uploads/excel', $filename, 'public');
            $data['file_excel'] = $filename;
        } else {
            unset($data['file_excel']);
        }

        $kandidat->update($data);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Data berhasil diperbarui'
            ]);
        }

        return redirect()
            ->route('rekrutmen.kandidat.index')
            ->with('success', 'Kandidat berhasil diperbarui');
    }

    public function destroy($id)
    {
        $kandidat = Kandidat::findOrFail($id);

        if ($kandidat->file_excel) {
            Storage::disk('public')->delete(
                'uploads/excel/' . $kandidat->file_excel
            );
        }

        $kandidat->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Kandidat berhasil dihapus'
            ]);
        }

        return redirect()
            ->route('rekrutmen.kandidat.index')
            ->with('success', 'Kandidat berhasil dihapus');
    }

    public function list(Request $request)
    {
        $query = Kandidat::orderBy('created_at', 'desc');

        if ($request->filled('posisi_id')) {
            $query->where('posisi_id', $request->posisi_id);
        }

        if ($request->filled('q')) {
            $query->where('nama', 'like', '%' . $request->q . '%');
        }

        return response()->json(
            $query->limit(50)->get(['id_kandidat', 'nama'])
        );
    }
}
