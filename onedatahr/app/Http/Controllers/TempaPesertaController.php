<?php

namespace App\Http\Controllers;

use App\Models\TempaPeserta;
use App\Models\TempaKelompok;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TempaPesertaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('viewTempaPeserta');

        $user = Auth::user();
        $isKetuaTempa = false;

        if ($user instanceof User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        }

        if ($isKetuaTempa) {
            // Ketua TEMPA hanya melihat peserta dari kelompoknya saja
            $pesertas = TempaPeserta::with(['kelompok'])
                ->whereHas('kelompok', function($q) use ($user) {
                    $q->where('ketua_tempa_id', $user->id);
                })->get();
        } else {
            // Admin/Superadmin melihat semua peserta
            $pesertas = TempaPeserta::with(['kelompok'])->get();
        }

        return view('pages.tempa.peserta.index', compact('pesertas'));
    }

    public function show($peserta)
    {
        $this->authorize('viewTempaPeserta');

        $user = Auth::user();
        $isKetuaTempa = false;

        if ($user instanceof User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        }

        $pesertaModel = TempaPeserta::with(['kelompok.ketuaTempa'])->findOrFail($peserta);

        // Cek akses ketua_tempa
        if ($isKetuaTempa && $pesertaModel->kelompok->ketua_tempa_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        return view('pages.tempa.peserta.show', compact('pesertaModel'));
    }

    public function create()
    {
        $this->authorize('createTempaPeserta');

        $user = Auth::user();
        $isKetuaTempa = false;

        if ($user instanceof User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        }

        if ($isKetuaTempa) {
            // Ketua TEMPA hanya bisa membuat peserta untuk kelompoknya
            $kelompoks = TempaKelompok::where('ketua_tempa_id', $user->id)->get();
        } else {
            // Admin/Superadmin bisa membuat peserta di mana saja
            $kelompoks = TempaKelompok::with('ketuaTempa')->get();
        }

        return view('pages.tempa.peserta.create', compact('kelompoks', 'isKetuaTempa'));
    }

    public function store(\App\Http\Requests\TempaPesertaRequest $request)
    {
        $user = Auth::user();
        $isKetuaTempa = false;

        if ($user instanceof User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        }

        $validated = $request->validated();

        if ($isKetuaTempa) {
            // Ketua TEMPA gunakan kelompok yang dipilih
            $kelompok = TempaKelompok::where('id_kelompok', $validated['kelompok_id'])
                ->where('ketua_tempa_id', $user->id)
                ->first();

            if (!$kelompok) {
                return back()->withErrors(['kelompok_id' => 'Kelompok tidak ditemukan atau tidak milik Anda'])->withInput();
            }

            $validated['id_kelompok'] = $kelompok->id_kelompok;
            $validated['id_tempa'] = $kelompok->id_tempa;
            unset($validated['kelompok_id']);
        } else {
            // Admin/Superadmin gunakan kelompok yang dipilih
            $kelompok = TempaKelompok::find($validated['kelompok_id']);
            if (!$kelompok) {
                return back()->withErrors(['kelompok_id' => 'Kelompok tidak ditemukan'])->withInput();
            }

            $validated['id_kelompok'] = $validated['kelompok_id'];
            $validated['id_tempa'] = $kelompok->id_tempa;
            unset($validated['nama_mentor'], $validated['kelompok_id']);
        }

        TempaPeserta::create($validated);

        return redirect()
            ->route('tempa.peserta.index')
            ->with('success', 'Peserta berhasil ditambahkan');
    }

    public function edit($peserta)
    {
        $this->authorize('editTempaPeserta');

        $user = Auth::user();
        $isKetuaTempa = false;

        if ($user instanceof User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        }
        $peserta = TempaPeserta::findOrFail($peserta);

        // Cek akses ketua_tempa
        if ($isKetuaTempa && $peserta->kelompok->ketua_tempa_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        if ($isKetuaTempa) {
            // Ketua TEMPA hanya bisa edit kelompoknya sendiri
            $kelompoks = TempaKelompok::where('ketua_tempa_id', $user->id)->get();
        } else {
            // Admin/Superadmin bisa edit di kelompok mana saja
            $kelompoks = TempaKelompok::with('ketuaTempa')->get();
        }

        return view('pages.tempa.peserta.edit', compact('peserta', 'kelompoks', 'isKetuaTempa'));
    }

    public function update(\App\Http\Requests\TempaPesertaRequest $request, $peserta)
    {
        $user = Auth::user();
        $isKetuaTempa = false;

        if ($user instanceof User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        }
        $pesertaModel = TempaPeserta::findOrFail($peserta);

        // Cek akses ketua_tempa
        if ($isKetuaTempa && $pesertaModel->kelompok->ketua_tempa_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validated();

        if ($isKetuaTempa) {
            // Ketua TEMPA gunakan kelompok yang dipilih
            $kelompok = TempaKelompok::where('id_kelompok', $validated['kelompok_id'])
                ->where('ketua_tempa_id', $user->id)
                ->first();

            if (!$kelompok) {
                return back()->withErrors(['kelompok_id' => 'Kelompok tidak ditemukan atau tidak milik Anda'])->withInput();
            }

            $validated['id_kelompok'] = $kelompok->id_kelompok;
            $validated['id_tempa'] = $kelompok->id_tempa;
            unset($validated['kelompok_id']);
        } else {
            // Admin/Superadmin gunakan kelompok yang dipilih
            $validated['id_kelompok'] = $validated['kelompok_id'];
            unset($validated['nama_mentor'], $validated['kelompok_id']);
        }

        $pesertaModel->update($validated);

        return redirect()
            ->route('tempa.peserta.index')
            ->with('success', 'Peserta berhasil diperbarui');
    }

    public function destroy($peserta)
    {
        $this->authorize('deleteTempaPeserta');

        $user = Auth::user();
        $isKetuaTempa = false;

        if ($user instanceof User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        }
        $pesertaModel = TempaPeserta::findOrFail($peserta);

        // Cek akses ketua_tempa
        if ($isKetuaTempa && $pesertaModel->kelompok->ketua_tempa_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        $pesertaModel->delete();

        return redirect()
            ->route('tempa.peserta.index')
            ->with('success', 'Peserta berhasil dihapus');
    }

    public function bulkDelete(Request $request)
    {
        $this->authorize('deleteTempaPeserta');

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tempa_peserta,id_peserta'
        ]);

        $user = Auth::user();
        $isKetuaTempa = false;

        if ($user instanceof User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin']);
        }

        $ids = $request->ids;
        $deletedCount = 0;
        $skippedCount = 0;

        foreach ($ids as $id) {
            $pesertaModel = TempaPeserta::find($id);
            if (!$pesertaModel) continue;

            // Cek akses ketua_tempa
            if ($isKetuaTempa && $pesertaModel->kelompok->ketua_tempa_id != $user->id) {
                $skippedCount++;
                continue;
            }

            $pesertaModel->delete();
            $deletedCount++;
        }

        if ($deletedCount > 0 && $skippedCount == 0) {
            return redirect()->route('tempa.peserta.index')
                ->with('success', "$deletedCount peserta berhasil dihapus.");
        } elseif ($deletedCount > 0 && $skippedCount > 0) {
            return redirect()->route('tempa.peserta.index')
                ->with('success', "$deletedCount peserta berhasil dihapus. $skippedCount peserta dilewati karena hak akses.");
        } else {
            return back()->with('error', "Gagal menghapus peserta. Pastikan Anda memiliki akses.");
        }
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = [
            'Nama Peserta',
            'NIK Karyawan',
            'Nama Kelompok (Harus Sesuai)',
            'Status (Aktif/Pindah/Keluar)',
            'Keterangan Pindah (Opsional)'
        ];
        $sheet->fromArray($headers, NULL, 'A1');

        // Style Check
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E0E0E0']],
        ];
        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);
        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Sample Data
        $sheet->setCellValue('A2', 'Ahmad Fulan');
        $sheet->setCellValue('B2', '12345678');
        $sheet->setCellValue('C2', 'Kelompok Alpha');
        $sheet->setCellValue('D2', 'Aktif');
        $sheet->setCellValue('E2', '');
        
        $writer = new Xlsx($spreadsheet);
        $fileName = 'template_import_peserta_tempa.xlsx';

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

            DB::beginTransaction();

            $allKelompoks = TempaKelompok::all()->keyBy(function($item) {
                return strtolower(trim($item->nama_kelompok));
            });

            foreach ($rows as $index => $row) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    $namaPeserta = $row[0] ?? null;
                    $nik = $row[1] ?? null;
                    $namaKelompok = trim($row[2] ?? '');
                    $statusStr = strtolower(trim($row[3] ?? 'aktif'));
                    $ketPindah = $row[4] ?? null;
                    $tempat = strtolower($row[5] ?? 'pusat');
                    $ketCabang = $row[6] ?? null;

                    if (!$namaPeserta || !$namaKelompok) {
                        throw new \Exception("Nama Peserta dan Nama Kelompok wajib diisi.");
                    }

                    // Mapping Status
                    $status = match($statusStr) {
                        'aktif' => 1,
                        'pindah' => 2,
                        'keluar' => 3,
                        default => 1
                    };

                    // Search Kelompok Case Insensitive
                    $kelompok = $allKelompoks->get(strtolower($namaKelompok));

                    if (!$kelompok) {
                        throw new \Exception("Kelompok '$namaKelompok' tidak ditemukan dalam sistem.");
                    }

                    TempaPeserta::create([
                        'nama_peserta' => $namaPeserta,
                        'nik_karyawan' => $nik,
                        'id_kelompok' => $kelompok->id_kelompok,
                        'status_peserta' => $status,
                        'keterangan_pindah' => $ketPindah
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

            $msg = "$successCount peserta berhasil diimport.";
            if ($failCount > 0) {
                $msg .= " $failCount gagal. Error: " . implode(', ', $errors);
            }

            return redirect()->route('tempa.peserta.index')->with('success', $msg);

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat membaca file: ' . $e->getMessage());
        }
    }
}
