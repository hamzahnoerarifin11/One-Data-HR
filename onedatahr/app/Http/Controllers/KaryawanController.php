<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Kontrak;
use App\Models\DataKeluarga;
use App\Models\Bpjs;
use App\Models\Perusahaan;
use App\Models\StatusKaryawan;
use App\Models\Level;
use App\Models\Role;
use App\Models\User;
use App\Models\Company;
use App\Models\Division;
use App\Models\Department;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class KaryawanController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    //     $this->middleware('role:admin|superadmin')->only(['create', 'store', 'edit', 'update', 'destroy']);
    //     // $this->middleware('role:admin|superadmin');
    // }

    public function index(Request $request)
    {
        $query = Karyawan::with(['pekerjaan.company', 'pekerjaan.holding', 'pekerjaan.division', 'pekerjaan.department', 'pekerjaan.unit', 'pekerjaan.level', 'pendidikan', 'kontrak', 'keluarga', 'bpjs', 'perusahaan', 'status']);

        // Apply organization scope filter
        $user = Auth::user();
        if ($user && !$user->hasUnrestrictedAccess()) {
            $scope = $user->org_scope ?? 'all';
            
            switch ($scope) {
                case 'holding':
                    // Filter by companies under this holding
                    $companyIds = Company::where('holding_id', $user->holding_id)->pluck('id')->toArray();
                    $query->whereHas('pekerjaan', function($q) use ($companyIds) {
                        $q->whereIn('company_id', $companyIds);
                    });
                    break;
                    
                case 'company':
                    $query->whereHas('pekerjaan', function($q) use ($user) {
                        $q->where('company_id', $user->company_id);
                    });
                    break;
                    
                case 'division':
                    $query->whereHas('pekerjaan', function($q) use ($user) {
                        $q->where('division_id', $user->division_id);
                    });
                    break;
                    
                case 'department':
                    $query->whereHas('pekerjaan', function($q) use ($user) {
                        $q->where('department_id', $user->department_id);
                    });
                    break;
                    
                case 'unit':
                    $query->whereHas('pekerjaan', function($q) use ($user) {
                        $q->where('unit_id', $user->unit_id);
                    });
                    break;
            }
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('Nama_Sesuai_KTP', 'like', '%' . $search . '%')
                  ->orWhere('NIK', 'like', '%' . $search . '%')
                  ->orWhere('Nomor_Telepon_Aktif_Karyawan', 'like', '%' . $search . '%')
                  ->orWhereHas('pekerjaan.level', function($subQ) use ($search) {
                      $subQ->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('pekerjaan', function($subQ) use ($search) {
                      $subQ->where('Lokasi_Kerja', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('pekerjaan.division', function($subQ) use ($search) {
                      $subQ->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('pekerjaan.company', function($subQ) use ($search) {
                      $subQ->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('pekerjaan.holding', function($subQ) use ($search) {
                      $subQ->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $karyawans = $query->orderBy('id_karyawan', 'desc')->paginate(10)->appends($request->query());

        return view('pages.karyawan.index', compact('karyawans'));
    }

    private function parseDate($date)
    {
        if (!$date) {
            return null;
        }

        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Lighten a hex color by a given factor (0.0 = original, 1.0 = white).
     */
    private function lightenColor(string $hex, float $factor): string
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = (int) ($r + (255 - $r) * $factor);
        $g = (int) ($g + (255 - $g) * $factor);
        $b = (int) ($b + (255 - $b) * $factor);

        return strtoupper(sprintf('%02x%02x%02x', min($r, 255), min($g, 255), min($b, 255)));
    }

    public function batchDelete(Request $request)
    {
        $ids = $request->selected_karyawan;

        if (!$ids || !is_array($ids) || count($ids) === 0) {
            return back()->with('error', 'Tidak ada data yang dipilih untuk dihapus');
        }

        DB::beginTransaction();
        try {
            $count = 0;
            foreach ($ids as $id) {
                $karyawan = Karyawan::findOrFail($id);

                // Hapus User Account jika ada
                $user = User::where('nik', $karyawan->NIK)->first();
                if ($user) {
                    $user->delete();
                }

                // Hapus Data Relasi
                $karyawan->pekerjaan()->delete();
                $karyawan->pendidikan()->delete();
                $karyawan->kontrak()->delete();
                $karyawan->keluarga()->delete();
                $karyawan->bpjs()->delete();
                $karyawan->perusahaan()->delete();
                $karyawan->status()->delete();
                
                // Hapus Karyawan
                $karyawan->delete();
                $count++;
            }

            DB::commit();
            return back()->with('success', $count . ' karyawan berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Batch delete error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $type = $request->query('type', 'csv');
        $karyawans = Karyawan::with(['pekerjaan.company', 'pekerjaan.holding', 'pekerjaan.division', 'pekerjaan.department', 'pekerjaan.unit', 'pekerjaan.level'])->get();

        if ($type === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pages.karyawan.pdf', compact('karyawans'));
            return $pdf->download('data_karyawan.pdf');
        }

        if ($type === 'excel') {
            return $this->exportExcel($karyawans);
        }

        // Default CSV
        return $this->exportCsv($karyawans);
    }

    private function exportCsv($karyawans)
    {
        $fileName = 'data_karyawan.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = array('Nama', 'NIK', 'Email', 'No Telepon', 'Jabatan', 'Divisi', 'Perusahaan');

        $callback = function() use($karyawans, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($karyawans as $karyawan) {
                $row['Nama']  = $karyawan->Nama_Sesuai_KTP;
                $row['NIK']    = $karyawan->NIK;
                $row['Email']    = $karyawan->Email;
                $row['No Telepon']  = $karyawan->Nomor_Telepon_Aktif_Karyawan;
                $row['Jabatan']  = $karyawan->pekerjaan->first()->level->name ?? '-';
                $row['Divisi']  = $karyawan->pekerjaan->first()->division->name ?? '-';
                $row['Perusahaan']  = $karyawan->pekerjaan->first()?->company->name ?? $karyawan->pekerjaan->first()?->holding->name ?? '-';

                fputcsv($file, array($row['Nama'], $row['NIK'], $row['Email'], $row['No Telepon'], $row['Jabatan'], $row['Divisi'], $row['Perusahaan']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportExcel($karyawans)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Headers
        $sheet->setCellValue('A1', 'Nama');
        $sheet->setCellValue('B1', 'NIK');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'No Telepon');
        $sheet->setCellValue('E1', 'Jabatan');
        $sheet->setCellValue('F1', 'Divisi');
        $sheet->setCellValue('G1', 'Perusahaan');

        $row = 2;
        foreach ($karyawans as $karyawan) {
            $sheet->setCellValue('A' . $row, $karyawan->Nama_Sesuai_KTP);
            $sheet->setCellValue('B' . $row, $karyawan->NIK);
            $sheet->setCellValue('C' . $row, $karyawan->Email);
            $sheet->setCellValue('D' . $row, $karyawan->Nomor_Telepon_Aktif_Karyawan);
            $sheet->setCellValue('E' . $row, $karyawan->pekerjaan->first()->level->name ?? '-');
            $sheet->setCellValue('F' . $row, $karyawan->pekerjaan->first()->division->name ?? '-');
            $sheet->setCellValue('G' . $row, $karyawan->pekerjaan->first()?->company->name ?? $karyawan->pekerjaan->first()?->holding->name ?? '-');
            $row++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $fileName = 'data_karyawan.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
        exit;
    }


    public function create()
    {
        // Get all entities for company selection with grouping
        $holdings = \App\Models\Holding::all()->map(function($h) {
            return [
                'id' => 'holding_' . $h->id, 
                'name' => $h->name, 
                'type' => 'holding',
                'detail_html' => '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">Holding</span>'
            ];
        });

        $allCompanies = \App\Models\Company::with('holding')->orderBy('name')->get();
        
        $parentCompanies = $allCompanies->whereNull('parent_id')->map(function($c) {
            return [
                'id' => $c->id, 
                'name' => $c->name, 
                'type' => 'company', 
                'holding_id' => $c->holding_id,
                'detail_html' => '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">Perusahaan</span>'
            ];
        });
        
        $subsidiaries = $allCompanies->whereNotNull('parent_id')->map(function($c) {
            $parentName = $c->parent ? $c->parent->name : '';
            return [
                'id' => $c->id, 
                'name' => $c->name, 
                'type' => 'subsidiary', 
                'holding_id' => $c->holding_id, 
                'parent_id' => $c->parent_id,
                'detail_html' => '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Anak Perusahaan</span>' . ($parentName ? ' <span class="text-gray-400 text-xs">dari ' . $parentName . '</span>' : '')
            ];
        });
        
        // Build grouped entities for dropdown
        $companies = collect();
        
        // Add Holdings
        if ($holdings->count() > 0) {
            $companies = $companies->merge($holdings);
        }
        
        // Add Parent Companies
        if ($parentCompanies->count() > 0) {
            $companies = $companies->merge($parentCompanies);
        }
        
        // Add Subsidiaries
        if ($subsidiaries->count() > 0) {
            $companies = $companies->merge($subsidiaries);
        }

        $levels = Level::orderBy('level_order')->get();
        return view('pages.karyawan.create', [
            'companies' => $companies->values(),
            'levels' => $levels,
            'lokasikerjaOptions' => getlokasikerja('pekerjaan', 'Lokasi_Kerja'),
            'perusahaanOptions' => getperusahaan('perusahaan', 'Perusahaan'),
            'pendidikanOptions' => getpendidikan('pendidikan', 'Pendidikan_Terakhir'),
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'Nama_Sesuai_KTP' => 'required|string|max:255',
            'Email' => 'nullable|email|max:255',
            'Tanggal_Non_Aktif' => 'nullable|date',
            'Alasan_Non_Aktif' => 'nullable|string|max:255',
            'Ijazah_Dikembalikan' => 'nullable|in:Ya,Tidak',
            'Bulan' => 'nullable|integer|min:0',
            'Status_BPJS_KT' => 'nullable|in:Aktif,Tidak Aktif',
            'Status_BPJS_KS' => 'nullable|in:Aktif,Tidak Aktif',
            'level_id' => 'required|exists:levels,id',
        ]);

        DB::beginTransaction();
        try {
            $karyawanData = $request->only([
                'NIK',
                'Status',
                'Kode',
                'Nama_Sesuai_KTP',
                'NIK_KTP',
                'Nama_Lengkap_Sesuai_Ijazah',
                'Tempat_Lahir_Karyawan',
                'Tanggal_Lahir_Karyawan',
                'Umur_Karyawan',
                'Jenis_Kelamin_Karyawan',
                'Status_Pernikahan',
                'Golongan_Darah',
                'Nomor_Telepon_Aktif_Karyawan',
                'Email',
                'Alamat_KTP',
                'RT',
                'RW',
                'Kelurahan_Desa',
                'Kecamatan',
                'Kabupaten_Kota',
                'Provinsi',
                'Alamat_Domisili',
                'RT_Sesuai_Domisili',
                'RW_Sesuai_Domisili',
                'Kelurahan_Desa_Domisili',
                'Kecamatan_Sesuai_Domisili',
                'Kabupaten_Kota_Sesuai_Domisili',
                'Provinsi_Sesuai_Domisili',
                'Alamat_Lengkap'
            ]);

            $karyawan = Karyawan::create($karyawanData);

            // Data Keluarga
            $keluargaData = $request->only([
                'Nama_Ayah_Kandung',
                'Nama_Ibu_Kandung',
                'Nama_Lengkap_Suami_Istri',
                'NIK_KTP_Suami_Istri',
                'Tempat_Lahir_Suami_Istri',
                'Tanggal_Lahir_Suami_Istri',
                'Nomor_Telepon_Suami_Istri',
                'Pendidikan_Terakhir_Suami_Istri'
            ]);
            $keluargaData['anak'] = $request->input('anak', []);
            $keluargaData['id_karyawan'] = $karyawan->id_karyawan;
            DataKeluarga::create($keluargaData);

            // Pekerjaan
            $pekerjaanData = $request->only(['Jabatan', 'department_id', 'division_id', 'unit_id', 'company_id', 'holding_id', 'level_id', 'Jenis_Kontrak', 'Perjanjian', 'Lokasi_Kerja']);
            $pekerjaanData['id_karyawan'] = $karyawan->id_karyawan;
            Pekerjaan::create($pekerjaanData);

            // Perusahaan
            $perusahaanName = $request->input('Perusahaan');
            if (!$perusahaanName) {
                if ($request->filled('company_id')) {
                    $cModel = \App\Models\Company::find($request->company_id);
                    $perusahaanName = $cModel ? $cModel->name : null;
                } elseif ($request->filled('holding_id')) {
                    $hModel = \App\Models\Holding::find($request->holding_id);
                    $perusahaanName = $hModel ? $hModel->name : null;
                }
            }
            Perusahaan::create(['id_karyawan' => $karyawan->id_karyawan, 'Perusahaan' => $perusahaanName]);

            // Pendidikan
            Pendidikan::create([
                'id_karyawan' => $karyawan->id_karyawan,
                'Pendidikan_Terakhir' => $request->Pendidikan_Terakhir,
                'Nama_Lengkap_Tempat_Pendidikan_Terakhir' => $request->Nama_Lengkap_Tempat_Pendidikan_Terakhir,
                'Jurusan' => $request->Jurusan,
            ]);

            // Kontrak & Hitung Masa Kerja
            $masaKerja = '';
            if ($request->filled('Tanggal_Mulai_Tugas')) {
                try {
                    $start = new \DateTime($request->Tanggal_Mulai_Tugas);
                    $now = new \DateTime();
                    if ($start <= $now) {
                        $diff = $start->diff($now);
                        $masaKerja = "{$diff->y} Tahun {$diff->m} Bulan {$diff->d} Hari";
                    }
                } catch (\Exception $e) {
                }
            }

            Kontrak::create([
                'id_karyawan' => $karyawan->id_karyawan,
                'Tanggal_Mulai_Tugas' => $request->Tanggal_Mulai_Tugas,
                'PKWT_Berakhir' => $request->PKWT_Berakhir,
                'Tanggal_Diangkat_Menjadi_Karyawan_Tetap' => $request->Tanggal_Diangkat_Menjadi_Karyawan_Tetap,
                'Riwayat_Penempatan' => $request->Riwayat_Penempatan,
                'Tanggal_Riwayat_Penempatan' => $request->Tanggal_Riwayat_Penempatan,
                'Mutasi_Promosi_Demosi' => $request->Mutasi_Promosi_Demosi,
                'Tanggal_Mutasi_Promosi_Demosi' => $request->Tanggal_Mutasi_Promosi_Demosi,
                'Masa_Kerja' => $masaKerja,
                'NO_PKWT_PERTAMA' => $request->NO_PKWT_PERTAMA,
                'NO_SK_PERTAMA' => $request->NO_SK_PERTAMA,
            ]);

            // Status Karyawan
            StatusKaryawan::create([
                'id_karyawan' => $karyawan->id_karyawan,
                'Tanggal_Non_Aktif' => $request->Tanggal_Non_Aktif,
                'Alasan_Non_Aktif' => $request->Alasan_Non_Aktif,
                'Ijazah_Dikembalikan' => $request->Ijazah_Dikembalikan,
                'Bulan' => $request->Bulan,
            ]);

            // BPJS
            Bpjs::create([
                'id_karyawan' => $karyawan->id_karyawan,
                'Status_BPJS_KT' => $request->Status_BPJS_KT,
                'Status_BPJS_KS' => $request->Status_BPJS_KS,
            ]);

            // Create User Account Otomatis
            $level = Level::find($request->level_id);
            $userResult = \App\Helpers\UserHelper::createUserForKaryawan($karyawan, $level);

            DB::commit();

            // Jika user berhasil dibuat, tampilkan credentials
            if ($userResult['success']) {
                return redirect()->route('karyawan.index')
                    ->with('success', 'Karyawan berhasil dibuat')
                    ->with('user_created', true)
                    ->with('user_credentials', [
                        'name' => $karyawan->Nama_Sesuai_KTP,
                        'email' => $userResult['email'],
                        'password' => $userResult['password'],
                        'roles' => $userResult['roles'],
                    ]);
            }

            return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil dibuat');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $karyawan = Karyawan::with(['pekerjaan.company', 'pekerjaan.division', 'pekerjaan.department', 'pekerjaan.unit', 'pekerjaan.level', 'pendidikan', 'kontrak', 'keluarga', 'bpjs', 'perusahaan', 'status'])->findOrFail($id);
        return view('pages.karyawan.show', compact('karyawan'));
    }

    public function edit($id)
    {
        $karyawan = Karyawan::with(['pekerjaan.company', 'pekerjaan.division', 'pekerjaan.department', 'pekerjaan.unit', 'pekerjaan.level', 'pendidikan', 'kontrak', 'keluarga', 'bpjs', 'perusahaan', 'status'])->findOrFail($id);
        
        // Get all entities for company selection with grouping
        $holdings = \App\Models\Holding::all()->map(function($h) {
            return [
                'id' => 'holding_' . $h->id, 
                'name' => $h->name, 
                'type' => 'holding',
                'detail_html' => '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">Holding</span>'
            ];
        });

        $allCompanies = \App\Models\Company::with(['holding', 'parent'])->orderBy('name')->get();
        
        $parentCompanies = $allCompanies->whereNull('parent_id')->map(function($c) {
            return [
                'id' => $c->id, 
                'name' => $c->name, 
                'type' => 'company', 
                'holding_id' => $c->holding_id,
                'detail_html' => '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">Perusahaan</span>'
            ];
        });
        
        $subsidiaries = $allCompanies->whereNotNull('parent_id')->map(function($c) {
            $parentName = $c->parent ? $c->parent->name : '';
            return [
                'id' => $c->id, 
                'name' => $c->name, 
                'type' => 'subsidiary', 
                'holding_id' => $c->holding_id, 
                'parent_id' => $c->parent_id,
                'detail_html' => '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Anak Perusahaan</span>' . ($parentName ? ' <span class="text-gray-400 text-xs">dari ' . $parentName . '</span>' : '')
            ];
        });
        
        // Build grouped entities for dropdown
        $companies = collect();
        
        // Add Holdings
        if ($holdings->count() > 0) {
            $companies = $companies->merge($holdings);
        }
        
        // Add Parent Companies
        if ($parentCompanies->count() > 0) {
            $companies = $companies->merge($parentCompanies);
        }
        
        // Add Subsidiaries
        if ($subsidiaries->count() > 0) {
            $companies = $companies->merge($subsidiaries);
        }

        $levels = Level::ordered()->get();
        $departments = \App\Models\Department::all();
        $divisions = \App\Models\Division::all();
        $units = \App\Models\Unit::all();
        return view('pages.karyawan.edit', array_merge(compact('karyawan', 'levels', 'departments', 'divisions', 'units'), [
            'companies' => $companies->values(),
            'lokasikerjaOptions' => getlokasikerja('pekerjaan', 'Lokasi_Kerja'),
            'perusahaanOptions' => getperusahaan('perusahaan', 'Perusahaan'),
            'pendidikanOptions' => getpendidikan('pendidikan', 'Pendidikan_Terakhir'),
            'departementOptions' => getdepartement('pekerjaan', 'Departement'),
            'divisiOptions' => getdivisi('pekerjaan', 'Divisi'),
            'unitOptions' => getunit('pekerjaan', 'Unit'),
        ]));
    }


    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $request->validate([
            'Nama_Sesuai_KTP' => 'required|string|max:255',
            'Email' => 'nullable|email|max:255',
            'Tanggal_Non_Aktif' => 'nullable|date',
            'Alasan_Non_Aktif' => 'nullable|string|max:255',
            'Ijazah_Dikembalikan' => 'nullable|in:Ya,Tidak',
            'Bulan' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // 1. Update Tabel Utama Karyawan
            $karyawan->update($request->only([
                'NIK',
                'Status',
                'Kode',
                'Nama_Sesuai_KTP',
                'NIK_KTP',
                'Nama_Lengkap_Sesuai_Ijazah',
                'Tempat_Lahir_Karyawan',
                'Tanggal_Lahir_Karyawan',
                'Umur_Karyawan',
                'Jenis_Kelamin_Karyawan',
                'Status_Pernikahan',
                'Golongan_Darah',
                'Nomor_Telepon_Aktif_Karyawan',
                'Email',
                'Alamat_KTP',
                'RT',
                'RW',
                'Kelurahan_Desa',
                'Kecamatan',
                'Kabupaten_Kota',
                'Provinsi',
                'Alamat_Domisili',
                'RT_Sesuai_Domisili',
                'RW_Sesuai_Domisili',
                'Kelurahan_Desa_Domisili',
                'Kecamatan_Sesuai_Domisili',
                'Kabupaten_Kota_Sesuai_Domisili',
                'Provinsi_Sesuai_Domisili',
                'Alamat_Lengkap'
            ]));

            // 2. Update Keluarga
            $dataKel = $request->only([
                'Nama_Ayah_Kandung',
                'Nama_Ibu_Kandung',
                'Nama_Lengkap_Suami_Istri',
                'NIK_KTP_Suami_Istri',
                'Tempat_Lahir_Suami_Istri',
                'Tanggal_Lahir_Suami_Istri',
                'Nomor_Telepon_Suami_Istri',
                'Pendidikan_Terakhir_Suami_Istri'
            ]);
            $dataKel['anak'] = $request->input('anak', []);
            $karyawan->keluarga ? $karyawan->keluarga->update($dataKel) : DataKeluarga::create(array_merge(['id_karyawan' => $id], $dataKel));

            // 3. Update BPJS
            $dataBpjs = $request->only(['Status_BPJS_KT', 'Status_BPJS_KS']);
            $karyawan->bpjs ? $karyawan->bpjs->update($dataBpjs) : Bpjs::create(array_merge(['id_karyawan' => $id], $dataBpjs));

            // 4. Update Status Karyawan (Jabatan yang Anda tanyakan)
            $dataStatus = $request->only(['Tanggal_Non_Aktif', 'Alasan_Non_Aktif', 'Ijazah_Dikembalikan', 'Bulan']);
            $karyawan->status ? $karyawan->status->update($dataStatus) : StatusKaryawan::create(array_merge(['id_karyawan' => $id], $dataStatus));

            // 5. Update Perusahaan
            $perusahaanName = $request->input('Perusahaan');
            if (!$perusahaanName) {
                if ($request->filled('company_id')) {
                    $cModel = \App\Models\Company::find($request->company_id);
                    $perusahaanName = $cModel ? $cModel->name : null;
                } elseif ($request->filled('holding_id')) {
                    $hModel = \App\Models\Holding::find($request->holding_id);
                    $perusahaanName = $hModel ? $hModel->name : null;
                }
            }
            $dataPerush = ['Perusahaan' => $perusahaanName];
            $karyawan->perusahaan ? $karyawan->perusahaan->update($dataPerush) : Perusahaan::create(array_merge(['id_karyawan' => $id], $dataPerush));

            // 6. Update Pendidikan
            $dataPend = $request->only(['Pendidikan_Terakhir', 'Nama_Lengkap_Tempat_Pendidikan_Terakhir', 'Jurusan']);
            $karyawan->pendidikan()->exists() ? $karyawan->pendidikan()->first()->update($dataPend) : Pendidikan::create(array_merge(['id_karyawan' => $id], $dataPend));

            // 7. Update Kontrak & Recalculate Masa Kerja
            $masaKerja = '';
            if ($request->filled('Tanggal_Mulai_Tugas')) {
                try {
                    $start = new \DateTime($request->Tanggal_Mulai_Tugas);
                    $now = new \DateTime();
                    if ($start <= $now) {
                        $diff = $start->diff($now);
                        $masaKerja = "{$diff->y} Tahun {$diff->m} Bulan {$diff->d} Hari";
                    }
                } catch (\Exception $e) {
                }
            }
            $dataKontrak = array_merge($request->only([
                'Tanggal_Mulai_Tugas',
                'PKWT_Berakhir',
                'Tanggal_Diangkat_Menjadi_Karyawan_Tetap',
                'Riwayat_Penempatan',
                'Tanggal_Riwayat_Penempatan',
                'Mutasi_Promosi_Demosi',
                'Tanggal_Mutasi_Promosi_Demosi',
                'NO_PKWT_PERTAMA',
                'NO_SK_PERTAMA'
            ]), ['Masa_Kerja' => $masaKerja]);

            $karyawan->kontrak ? $karyawan->kontrak->update($dataKontrak) : Kontrak::create(array_merge(['id_karyawan' => $id], $dataKontrak));

            // 8. Update Pekerjaan
            $dataKerja = $request->only(['Jabatan', 'department_id', 'division_id', 'unit_id', 'company_id', 'holding_id', 'level_id', 'Jenis_Kontrak', 'Perjanjian', 'Lokasi_Kerja']);
            $karyawan->pekerjaan()->exists() ? $karyawan->pekerjaan()->first()->update($dataKerja) : Pekerjaan::create(array_merge(['id_karyawan' => $id], $dataKerja));

            // 9. Update User Role jika level_id berubah
            if ($request->filled('level_id')) {
                $level = Level::find($request->level_id);
                $user = User::where('nik', $karyawan->NIK)->first();

                if ($user && $level) {
                    // Update role berdasarkan level jabatan yang baru
                    $roleNames = \App\Helpers\UserHelper::mapLevelToRole($level);
                    $roles = Role::whereIn('name', $roleNames)->pluck('id')->toArray();

                    // Detach old roles dan attach new roles
                    if (!empty($roles)) {
                        $user->roles()->sync($roles);
                    }
                }
            }

            DB::commit();
            return redirect()->route('karyawan.show', $id)->with('success', 'Data karyawan berhasil diperbarui');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal Update: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        DB::transaction(function () use ($karyawan) {
            // Delete linked user if exists
            if ($karyawan->user_id) {
                User::destroy($karyawan->user_id);
            }

            $karyawan->pekerjaan()->delete();
            $karyawan->pendidikan()->delete();
            $karyawan->kontrak()->delete();
            $karyawan->keluarga()->delete();
            $karyawan->bpjs()->delete();
            $karyawan->perusahaan()->delete();
            $karyawan->status()->delete();
            $karyawan->delete();
        });
        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil dihapus');
    }

    public function getDivisions($companyId)
    {
        $divisions = \App\Models\Division::where('company_id', $companyId)->get();
        return response()->json($divisions);
    }

    public function getDepartments($divisionId)
    {
        $departments = \App\Models\Department::where('division_id', $divisionId)->get();
        return response()->json($departments);
    }

    public function getUnits($departmentId)
    {
        $units = \App\Models\Unit::where('department_id', $departmentId)->get();
        return response()->json($units);
    }

    public function getPositions($unitId)
    {
        $positions = \App\Models\Position::where('unit_id', $unitId)->get();
        return response()->json($positions);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        $file = $request->file('file');
        
        try {
            $spreadsheet = IOFactory::load($file->getPathname());
            
            // Try to load from 'DATA KARYAWAN' sheet, fallback to active sheet
            try {
                $sheet = $spreadsheet->getSheetByName('DATA KARYAWAN') ?? $spreadsheet->getActiveSheet();
            } catch (\Exception $e) {
                $sheet = $spreadsheet->getActiveSheet();
            }
            $rows = $sheet->toArray();
            
            // Remove header rows: Row 1 = section labels, Row 2 = column headers
            array_shift($rows); // Remove section label row
            array_shift($rows); // Remove column header row
            
            // Remove sample data row if it looks like sample (first cell = '220022')
            if (!empty($rows) && isset($rows[0][0]) && $rows[0][0] === '220022') {
                array_shift($rows);
            }

            $successCount = 0;
            $failCount = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($rows as $index => $row) {
                // Skip empty rows
                if (empty(array_filter($row))) continue;

                // Map columns
                // Data Karyawan (Step 0)
                $nik = $row[0] ?? null;
                $status = $row[1] ?? '1';
                $kode = $row[2] ?? 'Aktif';
                $nama = $row[3] ?? null;
                $nikKtp = $row[4] ?? null;
                $namaIjazah = $row[5] ?? null;
                $tempatLahir = $row[6] ?? null;
                $tglLahir = $this->parseDate($row[7] ?? null);
                $gender = strtoupper($row[8] ?? ''); // L/P
                $statusPernikahan = $row[9] ?? null;
                $golDarah = $row[10] ?? null;
                $noTelp = $row[11] ?? null;
                $email = $row[12] ?? null;

                $alamatKtp = $row[13] ?? null;
                $rtKtp = $row[14] ?? null;
                $rwKtp = $row[15] ?? null;
                $provKtp = $row[16] ?? null;
                $kabKtp = $row[17] ?? null;
                $kecKtp = $row[18] ?? null;
                $kelKtp = $row[19] ?? null;

                $alamatDom = $row[20] ?? null;
                $rtDom = $row[21] ?? null;
                $rwDom = $row[22] ?? null;
                $provDom = $row[23] ?? null;
                $kabDom = $row[24] ?? null;
                $kecDom = $row[25] ?? null;
                $kelDom = $row[26] ?? null;

                $alamatLengkap = $row[27] ?? null;

                // Data Keluarga (Step 1)
                $namaAyah = $row[28] ?? null;
                $namaIbu = $row[29] ?? null;
                $namaPasangan = $row[30] ?? null;
                $nikPasangan = $row[31] ?? null;
                $tempatLahirPasangan = $row[32] ?? null;
                $tglLahirPasangan = $this->parseDate($row[33] ?? null);
                $telpPasangan = $row[34] ?? null;
                $pendidikanPasangan = $row[35] ?? null;

                // Parse Children (up to 3)
                $anakData = [];
                if (!empty($row[36])) {
                    $anakData[] = ['nama' => $row[36], 'tempat_lahir' => $row[37] ?? null, 'tanggal_lahir' => $this->parseDate($row[38] ?? null), 'jenis_kelamin' => $row[39] ?? null, 'pendidikan' => $row[40] ?? null];
                }
                if (!empty($row[41])) {
                    $anakData[] = ['nama' => $row[41], 'tempat_lahir' => $row[42] ?? null, 'tanggal_lahir' => $this->parseDate($row[43] ?? null), 'jenis_kelamin' => $row[44] ?? null, 'pendidikan' => $row[45] ?? null];
                }
                if (!empty($row[46])) {
                    $anakData[] = ['nama' => $row[46], 'tempat_lahir' => $row[47] ?? null, 'tanggal_lahir' => $this->parseDate($row[48] ?? null), 'jenis_kelamin' => $row[49] ?? null, 'pendidikan' => $row[50] ?? null];
                }

                // Data Pekerjaan (Step 2)
                $companyIdentifier = $row[51] ?? null;
                $divisionName = $row[52] ?? null;
                $deptName = $row[53] ?? null;
                $unitName = $row[54] ?? null;
                $levelName = $row[55] ?? null;
                $jabatan = $row[56] ?? null;
                $jenisKontrak = $row[57] ?? null;
                $perjanjian = $row[58] ?? null;
                $lokasiKerja = $row[59] ?? null;

                // Data Pendidikan (Step 3)
                $pendidikanTerakhir = $row[60] ?? null;
                $namaInstitusi = $row[61] ?? null;
                $jurusan = $row[62] ?? null;

                // Data Kontrak (Step 4)
                $tglMulaiTugas = $this->parseDate($row[63] ?? null);
                $pkwtBerakhir = $this->parseDate($row[64] ?? null);
                $tglDiangkat = $this->parseDate($row[65] ?? null);
                $riwayatPenempatan = $row[66] ?? null;
                $tglRiwayat = $this->parseDate($row[67] ?? null);
                $mutasi = $row[68] ?? null;
                $tglMutasi = $this->parseDate($row[69] ?? null);
                $noPkwt = $row[70] ?? null;
                $noSk = $row[71] ?? null;

                // Status & BPJS (Step 5 & 6)
                $tglNonAktif = $this->parseDate($row[72] ?? null);
                $alasanNonAktif = $row[73] ?? null;
                $ijazahKembali = $row[74] ?? null;
                $statusBpjsKt = $row[75] ?? null;
                $statusBpjsKs = $row[76] ?? null;

                // 1. Basic Validation
                if (!$nik || !$nama || !$email) {
                    $failCount++;
                    $errors[] = "Row " . ($index + 2) . ": NIK, Nama, dan Email wajib diisi.";
                    continue;
                }

                // Check duplicate NIK or Email
                if (Karyawan::where('NIK', $nik)->exists() || User::where('email', $email)->exists()) {
                     $failCount++;
                     $errors[] = "Row " . ($index + 2) . ": NIK atau Email sudah terdaftar ($nik / $email).";
                     continue;
                }

                // Resolve Organization (Same as before)
                $companyId = null; 
                $holdingId = null;
                if ($companyIdentifier) {
                    $comp = \App\Models\Company::where('name', $companyIdentifier)->first();
                    if ($comp) $companyId = $comp->id;
                    else {
                        $hold = \App\Models\Holding::where('name', $companyIdentifier)->first();
                        if ($hold) $holdingId = $hold->id;
                    }
                }

                $divisionId = $divisionName ? \App\Models\Division::where('name', $divisionName)->value('id') : null;
                $departmentId = $deptName ? \App\Models\Department::where('name', $deptName)->value('id') : null;
                $unitId = $unitName ? \App\Models\Unit::where('name', $unitName)->value('id') : null;
                $levelId = $levelName ? \App\Models\Level::where('name', $levelName)->value('id') : null;

                // 3. User Creation moved to after Karyawan & Pekerjaan creation to use UserHelper


                // 4. Create Karyawan
                $karyawan = Karyawan::create([
                    // 'user_id' will be assigned by UserHelper

                    'Nama_Sesuai_KTP' => $nama,
                    'NIK' => $nik,
                    'Email' => $email,
                    'Nomor_Telepon_Aktif_Karyawan' => $noTelp,
                    'NIK_KTP' => $nikKtp,
                    'Nama_Lengkap_Sesuai_Ijazah' => $namaIjazah,
                    'Tempat_Lahir_Karyawan' => $tempatLahir,
                    'Tanggal_Lahir_Karyawan' => $tglLahir,
                    'Jenis_Kelamin_Karyawan' => $gender,
                    'Status_Pernikahan' => $statusPernikahan,
                    'Golongan_Darah' => $golDarah,
                    
                    'Alamat_KTP' => $alamatKtp,
                    'RT' => $rtKtp,
                    'RW' => $rwKtp,
                    'Provinsi' => $provKtp,
                    'Kabupaten_Kota' => $kabKtp,
                    'Kecamatan' => $kecKtp,
                    'Kelurahan_Desa' => $kelKtp,
                    'Alamat_Domisili' => $alamatDom,
                    'RT_Sesuai_Domisili' => $rtDom,
                    'RW_Sesuai_Domisili' => $rwDom,
                    'Provinsi_Sesuai_Domisili' => $provDom,
                    'Kabupaten_Kota_Sesuai_Domisili' => $kabDom,
                    'Kecamatan_Sesuai_Domisili' => $kecDom,
                    'Kelurahan_Desa_Domisili' => $kelDom,
                    'Alamat_Lengkap' => $alamatLengkap,
                    'Status' => $status,
                    'Kode' => $kode,
                    'Umur_Karyawan' => $tglLahir ? \Carbon\Carbon::parse($tglLahir)->age : null
                ]);

                // 5. Create Pekerjaan
                Pekerjaan::create([
                    'id_karyawan' => $karyawan->id_karyawan,
                    'company_id' => $companyId,
                    'holding_id' => $holdingId,
                    'division_id' => $divisionId,
                    'department_id' => $departmentId,
                    'unit_id' => $unitId,
                    'level_id' => $levelId,
                    'Jabatan' => $jabatan,
                    'Lokasi_Kerja' => substr($lokasiKerja, 0, 50),
                    'Jenis_Kontrak' => $jenisKontrak,
                    'Perjanjian' => $perjanjian,
                    // 'Status' => $kode,
                ]);

                // 6. Create Related Data
                $keluarga = DataKeluarga::create([
                    'id_karyawan' => $karyawan->id_karyawan,
                    'Nama_Ayah_Kandung' => $namaAyah,
                    'Nama_Ibu_Kandung' => $namaIbu,
                    'Nama_Lengkap_Suami_Istri' => $namaPasangan,
                    'NIK_KTP_Suami_Istri' => $nikPasangan,
                    'Tempat_Lahir_Suami_Istri' => $tempatLahirPasangan,
                    'Tanggal_Lahir_Suami_Istri' => $tglLahirPasangan,
                    'Nomor_Telepon_Suami_Istri' => $telpPasangan,
                    'Pendidikan_Terakhir_Suami_Istri' => $pendidikanPasangan
                ]);

                // Save children data if any
                if (!empty($anakData)) {
                    $keluarga->anak = $anakData;
                    $keluarga->save();
                }

                Kontrak::create([
                    'id_karyawan' => $karyawan->id_karyawan,
                    'Tanggal_Mulai_Tugas' => $tglMulaiTugas,
                    'PKWT_Berakhir' => $pkwtBerakhir,
                    'Tanggal_Diangkat_Menjadi_Karyawan_Tetap' => $tglDiangkat,
                    'Riwayat_Penempatan' => $riwayatPenempatan,
                    'Tanggal_Riwayat_Penempatan' => $tglRiwayat,
                    'Mutasi_Promosi_Demosi' => $mutasi,
                    'Tanggal_Mutasi_Promosi_Demosi' => $tglMutasi,
                    'NO_PKWT_PERTAMA' => $noPkwt,
                    'NO_SK_PERTAMA' => $noSk
                ]);

                Bpjs::create([
                    'id_karyawan' => $karyawan->id_karyawan,
                    'Status_BPJS_KT' => $statusBpjsKt,
                    'Status_BPJS_KS' => $statusBpjsKs
                ]);

                Perusahaan::create(['id_karyawan' => $karyawan->id_karyawan, 'Perusahaan' => $companyIdentifier]);

                StatusKaryawan::create([
                    'id_karyawan' => $karyawan->id_karyawan,
                    // 'Status_Karyawan' => $kode,
                    'Tanggal_Non_Aktif' => $tglNonAktif,
                    'Alasan_Non_Aktif' => $alasanNonAktif,
                    'Ijazah_Dikembalikan' => $ijazahKembali
                ]);

                Pendidikan::create([
                    'id_karyawan' => $karyawan->id_karyawan,
                    'Pendidikan_Terakhir' => $pendidikanTerakhir,
                    'Nama_Lengkap_Tempat_Pendidikan_Terakhir' => $namaInstitusi,
                    'Jurusan' => $jurusan,
                ]);

                // Create User Account using Helper
                $level = $levelId ? \App\Models\Level::find($levelId) : null;
                \App\Helpers\UserHelper::createUserForKaryawan($karyawan, $level);

                
                $successCount++;
            }

            DB::commit();

            $msg = "Import selesai. Sukses: $successCount, Gagal: $failCount.";
            if (count($errors) > 0) {
                 $msg .= " Errors: " . implode(" | ", array_slice($errors, 0, 5));
                 if (count($errors) > 5) $msg .= "...";
                 return redirect()->route('karyawan.index')->with('error', $msg);
            }

            return redirect()->route('karyawan.index')->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import error: ' . $e->getMessage());
            return redirect()->route('karyawan.index')->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();

        // ============================================================
        // SHEET 1: PANDUAN PENGISIAN (Guide)
        // ============================================================
        $guide = $spreadsheet->getActiveSheet();
        $guide->setTitle('PANDUAN PENGISIAN');

        // Styling helpers
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E79']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ];
        $sectionStyle = [
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E75B6']],
            'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ];
        $subHeaderStyle = [
            'font' => ['bold' => true, 'size' => 10],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D6E4F0']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];
        $cellBorder = [
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP, 'wrapText' => true],
        ];
        $warnStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'CC0000']],
        ];
        $tipStyle = [
            'font' => ['italic' => true, 'color' => ['rgb' => '2E75B6']],
        ];

        // Column widths
        $guide->getColumnDimension('A')->setWidth(6);
        $guide->getColumnDimension('B')->setWidth(40);
        $guide->getColumnDimension('C')->setWidth(55);
        $guide->getColumnDimension('D')->setWidth(25);
        $guide->getColumnDimension('E')->setWidth(35);

        $r = 1;

        // ---- Title ----
        $guide->mergeCells("A{$r}:E{$r}");
        $guide->setCellValue("A{$r}", '📋 PANDUAN PENGISIAN TEMPLATE IMPORT KARYAWAN');
        $guide->getStyle("A{$r}:E{$r}")->applyFromArray($titleStyle);
        $guide->getRowDimension($r)->setRowHeight(40);
        $r++;

        // ---- General Instructions ----
        $guide->mergeCells("A{$r}:E{$r}");
        $guide->setCellValue("A{$r}", '⚠️ PETUNJUK UMUM');
        $guide->getStyle("A{$r}:E{$r}")->applyFromArray($sectionStyle);
        $guide->getRowDimension($r)->setRowHeight(28);
        $r++;

        $generalInstructions = [
            ['1.', 'Isi data pada sheet "DATA KARYAWAN", JANGAN mengubah baris header (baris 1-2).'],
            ['2.', 'Mulai mengisi data karyawan dari BARIS ke-3 pada sheet "DATA KARYAWAN".'],
            ['3.', 'Kolom bertanda (*) bersifat WAJIB diisi. Import akan gagal jika kolom wajib kosong.'],
            ['4.', 'Format tanggal HARUS menggunakan format: YYYY-MM-DD (contoh: 1990-05-15).'],
            ['5.', 'Untuk kolom pilihan (Gender, Status, dll), gunakan nilai yang sudah ditentukan.'],
            ['6.', 'Nama Perusahaan/Holding, Divisi, Departemen, Unit, dan Level HARUS sesuai data yang sudah ada di sistem.'],
            ['7.', 'NIK dan Email harus unik — tidak boleh duplikat dengan data yang sudah ada di sistem.'],
            ['8.', 'Kolom yang tidak wajib boleh dikosongkan.'],
            ['9.', 'Maksimal data anak yang dapat diisi: 3 anak per karyawan.'],
            ['10.', 'File yang diunggah harus berformat .xlsx, .xls, atau .csv, maksimal 2MB.'],
        ];

        foreach ($generalInstructions as $instr) {
            $guide->setCellValue("A{$r}", $instr[0]);
            $guide->mergeCells("B{$r}:E{$r}");
            $guide->setCellValue("B{$r}", $instr[1]);
            $guide->getStyle("A{$r}:E{$r}")->applyFromArray($cellBorder);
            if (in_array($instr[0], ['3.', '4.', '7.'])) {
                $guide->getStyle("B{$r}")->applyFromArray($warnStyle);
            }
            $r++;
        }
        $r++;

        // ---- Detail Column Guide ----
        // Section colors mapping
        $sectionColors = [
            'personal' => 'FF4472C4',
            'keluarga' => 'FF548235',
            'pekerjaan' => 'FFBF8F00',
            'pendidikan' => 'FF7030A0',
            'kontrak' => 'FFC55A11',
            'status' => 'FFFF0000',
        ];

        // Column detail data: [No, NamaKolom, Keterangan, Wajib, Contoh]
        $columnGuide = [
            // Section Header: Data Pribadi
            ['section' => 'DATA PRIBADI KARYAWAN (Kolom A - AB)', 'color' => $sectionColors['personal']],
            ['1', 'NIK', 'Nomor Induk Karyawan unik perusahaan', 'YA *', '220022'],
            ['2', 'Status (1/0)', '1 = Aktif, 0 = Non-Aktif', 'Tidak', '1'],
            ['3', 'Kode (Aktif/Non Aktif)', 'Tuliskan "Aktif" atau "Non Aktif"', 'Tidak', 'Aktif'],
            ['4', 'Nama Lengkap (Sesuai KTP)', 'Nama lengkap sesuai KTP', 'YA *', 'Budi Santoso'],
            ['5', 'NIK KTP', 'Nomor Induk Kependudukan (16 digit)', 'Tidak', '3201234567890001'],
            ['6', 'Nama Lengkap (Sesuai Ijazah)', 'Nama sesuai ijazah terakhir', 'Tidak', 'Budi Santoso, S.Kom'],
            ['7', 'Tempat Lahir', 'Kota/kabupaten tempat lahir', 'Tidak', 'Jakarta'],
            ['8', 'Tanggal Lahir', 'Format: YYYY-MM-DD', 'Tidak', '1990-05-15'],
            ['9', 'Jenis Kelamin', 'L = Laki-laki, P = Perempuan', 'Tidak', 'L'],
            ['10', 'Status Pernikahan', 'Belum Menikah / Menikah / Cerai', 'Tidak', 'Menikah'],
            ['11', 'Golongan Darah', 'A / B / AB / O', 'Tidak', 'O'],
            ['12', 'Nomor Telepon', 'Nomor HP aktif', 'Tidak', '081234567890'],
            ['13', 'Email', 'Email aktif (harus unik di sistem)', 'YA *', 'budi@email.com'],
            ['14', 'Alamat KTP', 'Alamat lengkap sesuai KTP', 'Tidak', 'Jl. Merdeka No. 10'],
            ['15', 'RT KTP', 'RT sesuai KTP', 'Tidak', '005'],
            ['16', 'RW KTP', 'RW sesuai KTP', 'Tidak', '003'],
            ['17', 'Provinsi KTP', 'Provinsi sesuai KTP', 'Tidak', 'DKI Jakarta'],
            ['18', 'Kabupaten/Kota KTP', 'Kabupaten/Kota sesuai KTP', 'Tidak', 'Jakarta Selatan'],
            ['19', 'Kecamatan KTP', 'Kecamatan sesuai KTP', 'Tidak', 'Kebayoran Baru'],
            ['20', 'Kelurahan/Desa KTP', 'Kelurahan/Desa sesuai KTP', 'Tidak', 'Senayan'],
            ['21', 'Alamat Domisili', 'Alamat tempat tinggal saat ini', 'Tidak', 'Jl. Sudirman No. 5'],
            ['22', 'RT Domisili', 'RT domisili', 'Tidak', '002'],
            ['23', 'RW Domisili', 'RW domisili', 'Tidak', '001'],
            ['24', 'Provinsi Domisili', 'Provinsi domisili', 'Tidak', 'DKI Jakarta'],
            ['25', 'Kabupaten/Kota Domisili', 'Kabupaten/Kota domisili', 'Tidak', 'Jakarta Pusat'],
            ['26', 'Kecamatan Domisili', 'Kecamatan domisili', 'Tidak', 'Tanah Abang'],
            ['27', 'Kelurahan/Desa Domisili', 'Kelurahan/Desa domisili', 'Tidak', 'Kebon Melati'],
            ['28', 'Alamat Lengkap', 'Alamat lengkap gabungan (opsional)', 'Tidak', 'Jl. Sudirman No.5, RT02/01, Tanah Abang, Jakarta Pusat'],

            // Section Header: Data Keluarga
            ['section' => 'DATA KELUARGA (Kolom AC - AY)', 'color' => $sectionColors['keluarga']],
            ['29', 'Nama Ayah Kandung', 'Nama ayah kandung', 'Tidak', 'Ahmad Santoso'],
            ['30', 'Nama Ibu Kandung', 'Nama ibu kandung', 'Tidak', 'Siti Aminah'],
            ['31', 'Nama Suami/Istri', 'Nama lengkap pasangan', 'Tidak', 'Dewi Lestari'],
            ['32', 'NIK Suami/Istri', 'NIK KTP pasangan (16 digit)', 'Tidak', '3201234567890002'],
            ['33', 'Tempat Lahir Suami/Istri', 'Tempat lahir pasangan', 'Tidak', 'Bandung'],
            ['34', 'Tanggal Lahir Suami/Istri', 'Format: YYYY-MM-DD', 'Tidak', '1992-08-20'],
            ['35', 'Nomor Telepon Suami/Istri', 'Nomor HP pasangan', 'Tidak', '082112345678'],
            ['36', 'Pendidikan Suami/Istri', 'Pendidikan terakhir pasangan', 'Tidak', 'S1'],
            ['37', 'Anak 1 Nama', 'Nama lengkap anak pertama', 'Tidak', 'Adi Santoso'],
            ['38', 'Anak 1 Tempat Lahir', 'Tempat lahir anak pertama', 'Tidak', 'Jakarta'],
            ['39', 'Anak 1 Tanggal Lahir', 'Format: YYYY-MM-DD', 'Tidak', '2018-03-10'],
            ['40', 'Anak 1 Jenis Kelamin', 'L = Laki-laki, P = Perempuan', 'Tidak', 'L'],
            ['41', 'Anak 1 Pendidikan', 'Jenjang pendidikan anak', 'Tidak', 'TK'],
            ['42', 'Anak 2 Nama', 'Nama lengkap anak kedua', 'Tidak', 'Ani Santoso'],
            ['43', 'Anak 2 Tempat Lahir', 'Tempat lahir anak kedua', 'Tidak', 'Jakarta'],
            ['44', 'Anak 2 Tanggal Lahir', 'Format: YYYY-MM-DD', 'Tidak', '2020-07-25'],
            ['45', 'Anak 2 Jenis Kelamin', 'L / P', 'Tidak', 'P'],
            ['46', 'Anak 2 Pendidikan', 'Jenjang pendidikan anak', 'Tidak', 'PAUD'],
            ['47', 'Anak 3 Nama', 'Nama lengkap anak ketiga', 'Tidak', ''],
            ['48', 'Anak 3 Tempat Lahir', 'Tempat lahir anak ketiga', 'Tidak', ''],
            ['49', 'Anak 3 Tanggal Lahir', 'Format: YYYY-MM-DD', 'Tidak', ''],
            ['50', 'Anak 3 Jenis Kelamin', 'L / P', 'Tidak', ''],
            ['51', 'Anak 3 Pendidikan', 'Jenjang pendidikan anak', 'Tidak', ''],

            // Section Header: Data Pekerjaan
            ['section' => 'DATA PEKERJAAN (Kolom AZ - BH)', 'color' => $sectionColors['pekerjaan']],
            ['52', 'Perusahaan / Holding', 'Nama perusahaan atau holding (harus cocok dengan data di sistem)', 'Tidak', 'PT Maju Bersama'],
            ['53', 'Divisi', 'Nama divisi (harus cocok dengan data di sistem)', 'Tidak', 'Teknologi Informasi'],
            ['54', 'Departemen', 'Nama departemen (harus cocok)', 'Tidak', 'Development'],
            ['55', 'Unit', 'Nama unit kerja (harus cocok)', 'Tidak', 'Backend'],
            ['56', 'Level Jabatan', 'Nama level jabatan (harus cocok)', 'Tidak', 'Staff'],
            ['57', 'Jabatan', 'Nama jabatan/posisi', 'Tidak', 'Software Engineer'],
            ['58', 'Jenis Kontrak', 'PKWT / PKWTT', 'Tidak', 'PKWT'],
            ['59', 'Perjanjian', 'Nomor atau jenis perjanjian kerja', 'Tidak', 'Kontrak'],
            ['60', 'Lokasi Kerja', 'Lokasi penempatan kerja (maks 50 karakter)', 'Tidak', 'Central Java - Pati'],

            // Section Header: Data Pendidikan
            ['section' => 'DATA PENDIDIKAN (Kolom BI - BK)', 'color' => $sectionColors['pendidikan']],
            ['61', 'Pendidikan Terakhir', 'SD/SLTP/SLTA/DIPLOMA I/DIPLOMA II/DIPLOMA III/DIPLOMA IV/S1/S2', 'Tidak', 'S1'],
            ['62', 'Nama Institusi', 'Nama sekolah/universitas', 'Tidak', 'Universitas Indonesia'],
            ['63', 'Jurusan', 'Program studi/jurusan', 'Tidak', 'Teknik Informatika'],

            // Section Header: Data Kontrak
            ['section' => 'DATA KONTRAK & RIWAYAT (Kolom BL - BT)', 'color' => $sectionColors['kontrak']],
            ['64', 'Tanggal Mulai Tugas', 'Format: YYYY-MM-DD', 'Tidak', '2024-01-15'],
            ['65', 'PKWT Berakhir', 'Tanggal berakhir kontrak. Format: YYYY-MM-DD', 'Tidak', '2025-01-14'],
            ['66', 'Tanggal Diangkat Tetap', 'Tanggal diangkat karyawan tetap. YYYY-MM-DD', 'Tidak', ''],
            ['67', 'Riwayat Penempatan', 'Catatan riwayat penempatan kerja', 'Tidak', 'Head Office → Cabang Surabaya'],
            ['68', 'Tanggal Riwayat Penempatan', 'Format: YYYY-MM-DD', 'Tidak', '2024-06-01'],
            ['69', 'Mutasi / Promosi / Demosi', 'Keterangan mutasi/promosi/demosi', 'Tidak', 'Promosi ke Senior'],
            ['70', 'Tanggal Mutasi', 'Format: YYYY-MM-DD', 'Tidak', '2024-06-01'],
            ['71', 'Nomor PKWT Pertama', 'Nomor surat PKWT pertama', 'Tidak', 'PKWT/2024/001'],
            ['72', 'Nomor SK Pertama', 'Nomor SK pengangkatan pertama', 'Tidak', 'SK/2024/001'],

            // Section Header: Status & BPJS
            ['section' => 'STATUS KARYAWAN & BPJS (Kolom BU - BY)', 'color' => $sectionColors['status']],
            ['73', 'Tanggal Non Aktif', 'Format: YYYY-MM-DD (kosongkan jika masih aktif)', 'Tidak', ''],
            ['74', 'Alasan Non Aktif', 'Alasan berhenti/non aktif', 'Tidak', ''],
            ['75', 'Ijazah Dikembalikan', 'Ya / Tidak', 'Tidak', ''],
            ['76', 'Status BPJS Ketenagakerjaan', 'Aktif / Tidak Aktif', 'Tidak', 'Aktif'],
            ['77', 'Status BPJS Kesehatan', 'Aktif / Tidak Aktif', 'Tidak', 'Aktif'],
        ];

        // Table header for guide
        $guide->mergeCells("A{$r}:E{$r}");
        $guide->setCellValue("A{$r}", '📖 DETAIL KOLOM PENGISIAN');
        $guide->getStyle("A{$r}:E{$r}")->applyFromArray($sectionStyle);
        $guide->getRowDimension($r)->setRowHeight(28);
        $r++;

        $guide->setCellValue("A{$r}", 'No');
        $guide->setCellValue("B{$r}", 'Nama Kolom');
        $guide->setCellValue("C{$r}", 'Keterangan');
        $guide->setCellValue("D{$r}", 'Wajib?');
        $guide->setCellValue("E{$r}", 'Contoh Pengisian');
        $guide->getStyle("A{$r}:E{$r}")->applyFromArray($subHeaderStyle);
        $guide->getRowDimension($r)->setRowHeight(22);
        $r++;

        foreach ($columnGuide as $item) {
            if (isset($item['section'])) {
                // Section header row
                $guide->mergeCells("A{$r}:E{$r}");
                $guide->setCellValue("A{$r}", $item['section']);
                $guide->getStyle("A{$r}:E{$r}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => ltrim($item['color'], 'F')]],
                    'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                ]);
                $guide->getRowDimension($r)->setRowHeight(25);
            } else {
                $guide->setCellValue("A{$r}", $item[0]);
                $guide->setCellValue("B{$r}", $item[1]);
                $guide->setCellValue("C{$r}", $item[2]);
                $guide->setCellValue("D{$r}", $item[3]);
                $guide->setCellValue("E{$r}", $item[4]);
                $guide->getStyle("A{$r}:E{$r}")->applyFromArray($cellBorder);
                // Highlight mandatory fields
                if (str_contains($item[3], 'YA')) {
                    $guide->getStyle("D{$r}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'CC0000']],
                        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFE0E0']],
                    ]);
                }
            }
            $r++;
        }

        $r++;
        // Footer note
        $guide->mergeCells("A{$r}:E{$r}");
        $guide->setCellValue("A{$r}", '💡 TIP: Kolom yang bertanda "harus cocok dengan data di sistem" artinya nilai harus PERSIS sama dengan nama yang terdaftar di menu Organisasi (Holding/Perusahaan/Divisi/Departemen/Unit/Level).');
        $guide->getStyle("A{$r}")->applyFromArray($tipStyle);
        $r++;
        $guide->mergeCells("A{$r}:E{$r}");
        $guide->setCellValue("A{$r}", '💡 TIP: Password default untuk user yang dibuat melalui import adalah "password123". Segera minta karyawan mengganti password setelah login pertama.');
        $guide->getStyle("A{$r}")->applyFromArray($tipStyle);

        // Freeze top rows
        $guide->freezePane('A3');

        // ============================================================
        // SHEET 2: DATA KARYAWAN (Actual data entry)
        // ============================================================
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('DATA KARYAWAN');

        $headers = [
            // Data Karyawan (Step 0) — 28 cols
            'NIK *', 'Status (1/0)', 'Kode (Aktif/Non Aktif)', 'Nama Lengkap (Sesuai KTP) *', 
            'NIK KTP', 'Nama Lengkap (Sesuai Ijazah)', 'Tempat Lahir', 
            'Tanggal Lahir (YYYY-MM-DD)', 'Jenis Kelamin (L/P)',
            'Status Pernikahan', 'Golongan Darah',
            'Nomor Telepon', 'Email *',
            'Alamat KTP', 'RT KTP', 'RW KTP', 'Provinsi KTP', 'Kabupaten/Kota KTP', 'Kecamatan KTP', 'Kelurahan/Desa KTP',
            'Alamat Domisili', 'RT Domisili', 'RW Domisili', 'Provinsi Domisili', 'Kabupaten/Kota Domisili', 'Kecamatan Domisili', 'Kelurahan/Desa Domisili',
            'Alamat Lengkap',

            // Data Keluarga (Step 1) — 23 cols
            'Nama Ayah Kandung', 'Nama Ibu Kandung',
            'Nama Suami/Istri', 'NIK Suami/Istri', 'Tempat Lahir Suami/Istri', 'Tanggal Lahir Suami/Istri (YYYY-MM-DD)', 'Nomor Telepon Suami/Istri', 'Pendidikan Suami/Istri',
            'Anak 1 Nama', 'Anak 1 Tempat Lahir', 'Anak 1 Tanggal Lahir (YYYY-MM-DD)', 'Anak 1 Jenis Kelamin (L/P)', 'Anak 1 Pendidikan',
            'Anak 2 Nama', 'Anak 2 Tempat Lahir', 'Anak 2 Tanggal Lahir (YYYY-MM-DD)', 'Anak 2 Jenis Kelamin (L/P)', 'Anak 2 Pendidikan',
            'Anak 3 Nama', 'Anak 3 Tempat Lahir', 'Anak 3 Tanggal Lahir (YYYY-MM-DD)', 'Anak 3 Jenis Kelamin (L/P)', 'Anak 3 Pendidikan',

            // Data Pekerjaan (Step 2) — 9 cols
            'Perusahaan / Holding', 'Divisi', 'Departemen', 'Unit', 'Level Jabatan', 'Jabatan',
            'Jenis Kontrak', 'Perjanjian', 'Lokasi Kerja',
            
            // Data Pendidikan (Step 3) — 3 cols
            'Pendidikan Terakhir', 'Nama Institusi', 'Jurusan',

            // Data Kontrak (Step 4) — 9 cols
            'Tanggal Mulai Tugas (YYYY-MM-DD)', 'PKWT Berakhir (YYYY-MM-DD)', 'Tanggal Diangkat Tetap (YYYY-MM-DD)',
            'Riwayat Penempatan', 'Tanggal Riwayat Penempatan (YYYY-MM-DD)', 
            'Mutasi / Promosi / Demosi', 'Tanggal Mutasi (YYYY-MM-DD)',
            'Nomor PKWT Pertama', 'Nomor SK Pertama',
            
            // Status & BPJS (Step 5 & 6) — 5 cols
            'Tanggal Non Aktif (YYYY-MM-DD)', 'Alasan Non Aktif', 'Ijazah Dikembalikan (Ya/Tidak)',
            'Status BPJS Ketenagakerjaan', 'Status BPJS Kesehatan'
        ];

        // Section color ranges: [startCol(0-based), endCol(0-based), colorRGB, sectionLabel]
        $sections = [
            [0, 27, '4472C4', 'DATA PRIBADI KARYAWAN'],
            [28, 50, '548235', 'DATA KELUARGA'],
            [51, 59, 'BF8F00', 'DATA PEKERJAAN'],
            [60, 62, '7030A0', 'DATA PENDIDIKAN'],
            [63, 71, 'C55A11', 'DATA KONTRAK & RIWAYAT'],
            [72, 76, 'FF0000', 'STATUS & BPJS'],
        ];

        // Row 1: Section label row (merged per section)
        foreach ($sections as $sec) {
            $startLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($sec[0] + 1);
            $endLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($sec[1] + 1);
            $sheet->mergeCells("{$startLetter}1:{$endLetter}1");
            $sheet->setCellValue("{$startLetter}1", $sec[3]);
            $sheet->getStyle("{$startLetter}1:{$endLetter}1")->applyFromArray([
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $sec[2]]],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ]);
        }
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Row 2: Individual column headers
        foreach ($headers as $index => $header) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($columnLetter . '2', $header);
            $sheet->getColumnDimension($columnLetter)->setWidth(22);

            // Find section color for this column
            $colColor = 'D6E4F0';
            foreach ($sections as $sec) {
                if ($index >= $sec[0] && $index <= $sec[1]) {
                    // Lighter version of section color for header
                    $colColor = $this->lightenColor($sec[2], 0.7);
                    break;
                }
            }

            $isMandatory = str_contains($header, '*');
            $sheet->getStyle($columnLetter . '2')->applyFromArray([
                'font' => ['bold' => true, 'size' => 9, 'color' => $isMandatory ? ['rgb' => 'CC0000'] : ['rgb' => '000000']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $colColor]],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ]);
        }
        $sheet->getRowDimension(2)->setRowHeight(40);

        // Row 3: Sample data row (light yellow background)
        $sampleData = [
            '220022', '1', 'Aktif', 'Budi Santoso', '3201234567890001', 'Budi Santoso, S.Kom', 'Jakarta',
            '1990-05-15', 'L', 'Menikah', 'O', '081234567890', 'budi@perusahaan.com',
            'Jl. Merdeka No. 10', '005', '003', 'DKI Jakarta', 'Jakarta Selatan', 'Kebayoran Baru', 'Senayan',
            'Jl. Sudirman No. 5', '002', '001', 'DKI Jakarta', 'Jakarta Pusat', 'Tanah Abang', 'Kebon Melati',
            'Jl. Sudirman No. 5, Jakarta Pusat',
            // Keluarga
            'Ahmad Santoso', 'Siti Aminah',
            'Dewi Lestari', '3201234567890002', 'Bandung', '1992-08-20', '082112345678', 'S1',
            'Adi Santoso', 'Jakarta', '2018-03-10', 'L', 'SD',
            'Ani Santoso', 'Jakarta', '2020-07-25', 'P', 'TK',
            '', '', '', '', '',
            // Pekerjaan
            'PT Maju Bersama', 'Teknologi Informasi', 'Development', 'Backend', 'Staff', 'Software Engineer',
            'PKWT', 'Kontrak', 'Central Java - Pati',
            // Pendidikan
            'S1', 'Universitas Indonesia', 'Teknik Informatika',
            // Kontrak
            '2024-01-15', '2025-01-14', '', 'Head Office', '2024-01-15', '', '', 'PKWT/2024/001', '',
            // Status & BPJS
            '', '', '', 'Aktif', 'Aktif'
        ];

        foreach ($sampleData as $index => $val) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($columnLetter . '3', $val);
            $sheet->getStyle($columnLetter . '3')->applyFromArray([
                'font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '666666']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFDE7']],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
            ]);
        }

        // Add "CONTOH →" label
        // We use a comment approach: Insert a note that row 3 is sample
        $sheet->getComment('A3')->getText()->createTextRun('Ini adalah baris contoh. Hapus atau timpa baris ini sebelum import.');

        // Data validation: Gender (L/P)
        $genderValidation = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
        $genderValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $genderValidation->setFormula1('"L,P"');
        $genderValidation->setAllowBlank(true);
        $genderValidation->setShowDropDown(true);
        $genderValidation->setPromptTitle('Jenis Kelamin');
        $genderValidation->setPrompt('Pilih L (Laki-laki) atau P (Perempuan)');

        // Col I (index 8) = Gender
        $sheet->getCell('I4')->setDataValidation(clone $genderValidation);
        $sheet->setDataValidation('I4:I1000', clone $genderValidation);
        // Anak genders: col AN (index 39), AS (44), AX (49)
        $anakGenderCols = [
            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(40),
            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(45),
            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(50),
        ];
        foreach ($anakGenderCols as $col) {
            $sheet->setDataValidation("{$col}4:{$col}1000", clone $genderValidation);
        }

        // Data validation: Status (1/0)
        $statusValidation = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
        $statusValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $statusValidation->setFormula1('"1,0"');
        $statusValidation->setAllowBlank(true);
        $statusValidation->setShowDropDown(true);
        $statusValidation->setPromptTitle('Status');
        $statusValidation->setPrompt('1 = Aktif, 0 = Non-Aktif');
        $sheet->setDataValidation('B4:B1000', $statusValidation);

        // Data validation: Kode (Aktif/Non Aktif)
        $kodeValidation = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
        $kodeValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $kodeValidation->setFormula1('"Aktif,Non Aktif"');
        $kodeValidation->setAllowBlank(true);
        $kodeValidation->setShowDropDown(true);
        $sheet->setDataValidation('C4:C1000', $kodeValidation);

        // Data validation: Status Pernikahan
        $nikahValidation = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
        $nikahValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $nikahValidation->setFormula1('"Belum Menikah,Menikah,Cerai Hidup,Cerai Mati"');
        $nikahValidation->setAllowBlank(true);
        $nikahValidation->setShowDropDown(true);
        $sheet->setDataValidation('J4:J1000', $nikahValidation);

        // Data validation: Golongan Darah
        $darahValidation = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
        $darahValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $darahValidation->setFormula1('"A,B,AB,O"');
        $darahValidation->setAllowBlank(true);
        $darahValidation->setShowDropDown(true);
        $sheet->setDataValidation('K4:K1000', $darahValidation);

        // Data validation: Ijazah Dikembalikan (Ya/Tidak)
        $ijazahCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(75); // col 75 = BW
        $ijazahValidation = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
        $ijazahValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $ijazahValidation->setFormula1('"Ya,Tidak"');
        $ijazahValidation->setAllowBlank(true);
        $ijazahValidation->setShowDropDown(true);
        $sheet->setDataValidation("{$ijazahCol}4:{$ijazahCol}1000", $ijazahValidation);

        // Data validation: BPJS KT & KS (Aktif/Tidak Aktif)
        $bpjsValidation = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
        $bpjsValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $bpjsValidation->setFormula1('"Aktif,Tidak Aktif"');
        $bpjsValidation->setAllowBlank(true);
        $bpjsValidation->setShowDropDown(true);
        $bpjsKtCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(76);
        $bpjsKsCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(77);
        $sheet->setDataValidation("{$bpjsKtCol}4:{$bpjsKtCol}1000", clone $bpjsValidation);
        $sheet->setDataValidation("{$bpjsKsCol}4:{$bpjsKsCol}1000", clone $bpjsValidation);

        // Data validation: Jenis Kontrak
        $kontrakCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(58);
        $kontrakValidation = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
        $kontrakValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $kontrakValidation->setFormula1('"PKWT,PKWTT"');
        $kontrakValidation->setAllowBlank(true);
        $kontrakValidation->setShowDropDown(true);
        $sheet->setDataValidation("{$kontrakCol}4:{$kontrakCol}1000", $kontrakValidation);

        // Data validation: Pendidikan Terakhir
        $pendCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(61);
        $pendValidation = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
        $pendValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $pendValidation->setFormula1('"SD,SLTP,SLTA,DIPLOMA I,DIPLOMA II,DIPLOMA III,DIPLOMA IV,S1,S2"');
        $pendValidation->setAllowBlank(true);
        $pendValidation->setShowDropDown(true);
        $sheet->setDataValidation("{$pendCol}4:{$pendCol}1000", $pendValidation);

        // Freeze header rows (row 1 = section, row 2 = column name)
        $sheet->freezePane('A3');

        // Set the data sheet as the active/first-visible sheet
        $spreadsheet->setActiveSheetIndex(1);

        $writer = new Xlsx($spreadsheet);
        $fileName = 'template_import_karyawan.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
        exit;
    }
}
