<?php

namespace App\Http\Controllers;

use App\Http\Requests\TempaKelompokRequest;
use App\Models\TempaKelompok;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\DB;

class TempaKelompokController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('viewTempaKelompok');

        $user = Auth::user();
        $isKetuaTempa = false;

        if ($user instanceof \App\Models\User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        }

        if ($isKetuaTempa) {
            // Ketua TEMPA hanya melihat kelompoknya sendiri
            $kelompok = TempaKelompok::with(['ketuaTempa'])
                ->where('ketua_tempa_id', $user->id)
                ->get();
        } else {
            // Admin/Superadmin melihat semua kelompok
            $kelompok = TempaKelompok::with(['ketuaTempa'])->get();
        }

        return view('pages.tempa.kelompok.index', compact('kelompok'));
    }

    public function create()
    {
        $this->authorize('createTempaKelompok');

        $user = Auth::user();
        $isKetuaTempa = false;

        if ($user instanceof \App\Models\User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        }

        if ($isKetuaTempa) {
            // Ketua TEMPA hanya bisa membuat kelompok untuk dirinya sendiri
            return view('pages.tempa.kelompok.create');
        } else {
            // Admin/Superadmin bisa membuat kelompok untuk siapa saja
            return view('pages.tempa.kelompok.create');
        }
    }

    public function store(TempaKelompokRequest $request)
    {
        $user = Auth::user();
        $isKetuaTempa = false;

        if ($user instanceof \App\Models\User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        }

        $validated = $request->validated();

        if ($isKetuaTempa) {
            $validated['ketua_tempa_id'] = $user->id;
        } else {
            // Untuk admin, bisa set ketua_tempa_id jika diperlukan, tapi untuk sekarang biarkan null atau set default
            $validated['ketua_tempa_id'] = $request->input('ketua_tempa_id', $user->id); // atau sesuaikan
        }

        TempaKelompok::create($validated);

        return redirect()
            ->route('tempa.kelompok.index')
            ->with('success', 'Kelompok berhasil ditambahkan');
    }

    public function edit($kelompok)
    {
        $this->authorize('editTempaKelompok');

        $user = Auth::user();
        $kelompok = TempaKelompok::findOrFail($kelompok);
        $isKetuaTempa = false;

        if ($user instanceof \App\Models\User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        }

        // Cek akses ketua_tempa
        if ($isKetuaTempa && $kelompok->ketua_tempa_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        return view('pages.tempa.kelompok.edit', compact('kelompok'));
    }

    public function show($kelompok)
    {
        $this->authorize('viewTempaKelompok');

        $user = Auth::user();
        $isKetuaTempa = false;

        if ($user instanceof \App\Models\User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        }

        $kelompok = TempaKelompok::with(['ketuaTempa', 'pesertas'])->findOrFail($kelompok);

        // Cek akses ketua_tempa
        if ($isKetuaTempa && $kelompok->ketua_tempa_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        return view('pages.tempa.kelompok.show', compact('kelompok'));
    }

    public function update(TempaKelompokRequest $request, $kelompok)
    {
        $user = Auth::user();
        $kelompokModel = TempaKelompok::findOrFail($kelompok);
        $isKetuaTempa = false;

        if ($user instanceof \App\Models\User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        }

        // Cek akses ketua_tempa
        if ($isKetuaTempa && $kelompokModel->ketua_tempa_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validated();

        $kelompokModel->update($validated);

        return redirect()
            ->route('tempa.kelompok.index')
            ->with('success', 'Kelompok berhasil diperbarui');
    }

    public function destroy($kelompok)
    {
        $this->authorize('deleteTempaKelompok');

        $user = Auth::user();
        $kelompokModel = TempaKelompok::findOrFail($kelompok);
        $isKetuaTempa = false;

        if ($user instanceof \App\Models\User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        }

        // Cek akses ketua_tempa
        if ($isKetuaTempa && $kelompokModel->ketua_tempa_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        // Cek apakah ada peserta di kelompok ini
        if ($kelompokModel->pesertas()->count() > 0) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus kelompok yang masih memiliki peserta']);
        }

        $kelompokModel->delete();

        return redirect()
            ->route('tempa.kelompok.index')
            ->with('success', 'Kelompok berhasil dihapus');
    }

    public function bulkDelete(Request $request)
    {
        $this->authorize('deleteTempaKelompok');

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tempa_kelompok,id_kelompok'
        ]);

        $user = Auth::user();
        $isKetuaTempa = false;

        if ($user instanceof \App\Models\User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        }

        $ids = $request->ids;
        $deletedCount = 0;
        $skippedCount = 0;

        foreach ($ids as $id) {
            $kelompok = TempaKelompok::find($id);
            if (!$kelompok) continue;

            // Cek akses ketua_tempa
            if ($isKetuaTempa && $kelompok->ketua_tempa_id != $user->id) {
                $skippedCount++;
                continue;
            }

            // Cek apakah ada peserta
            if ($kelompok->pesertas()->count() > 0) {
                $skippedCount++;
                continue;
            }

            $kelompok->delete();
            $deletedCount++;
        }

        if ($deletedCount > 0 && $skippedCount == 0) {
            return redirect()->route('tempa.kelompok.index')
                ->with('success', "$deletedCount kelompok berhasil dihapus.");
        } elseif ($deletedCount > 0 && $skippedCount > 0) {
            return redirect()->route('tempa.kelompok.index')
                ->with('success', "$deletedCount kelompok berhasil dihapus. $skippedCount kelompok dilewati karena hak akses atau masih memiliki peserta.");
        } else {
            return back()->with('error', "Gagal menghapus kelompok. Pastikan Anda memiliki akses dan kelompok tidak memiliki peserta.");
        }
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = ['Nama Kelompok', 'Nama Mentor', 'Tempat (pusat/cabang)', 'Keterangan Cabang (Jika Tempat=cabang)'];
        $sheet->fromArray($headers, NULL, 'A1');

        // Style Check
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E0E0E0']],
        ];
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);
        foreach (range('A', 'D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Sample Data
        $sheet->setCellValue('A2', 'Kelompok Alpha');
        $sheet->setCellValue('B2', 'Budi Santoso');
        $sheet->setCellValue('C2', 'pusat');
        $sheet->setCellValue('D2', '');

        $sheet->setCellValue('A3', 'Kelompok Beta');
        $sheet->setCellValue('B3', 'Siti Aminah');
        $sheet->setCellValue('C3', 'cabang');
        $sheet->setCellValue('D3', 'Cabang Bandung');

        $writer = new Xlsx($spreadsheet);
        $fileName = 'template_import_kelompok_tempa.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
        exit;
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        $file = $request->file('file');

        try {
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Remove header row
            array_shift($rows);

            $successCount = 0;
            $failCount = 0;
            $errors = [];
            $currentUser = Auth::user();

            DB::beginTransaction();

            foreach ($rows as $index => $row) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    $namaKelompok = $row[0] ?? null;
                    $namaMentor = $row[1] ?? null;
                    $tempat = strtolower($row[2] ?? 'pusat');
                    $ketCabang = $row[3] ?? null;

                    if (!$namaKelompok) {
                        throw new \Exception("Nama Kelompok wajib diisi.");
                    }

                    TempaKelompok::create([
                        'nama_kelompok' => $namaKelompok,
                        'nama_mentor' => $namaMentor,
                        'ketua_tempa_id' => $currentUser->id, // Default to current user importing
                        'tempat' => in_array($tempat, ['pusat', 'cabang']) ? $tempat : 'pusat',
                        'keterangan_cabang' => $ketCabang,
                        'created_by_tempa' => $currentUser->id
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    $failCount++;
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            if ($failCount > 0 && $successCount == 0) {
                 DB::rollBack();
                 return back()->with('error', 'Gagal import data. ' . implode(', ', $errors));
            }

            DB::commit();

            $msg = "$successCount kelompok berhasil diimport.";
            if ($failCount > 0) {
                $msg .= " $failCount gagal. Error: " . implode(', ', $errors);
            }

            return redirect()->route('tempa.kelompok.index')->with('success', $msg);

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat membaca file: ' . $e->getMessage());
        }
    }
}
