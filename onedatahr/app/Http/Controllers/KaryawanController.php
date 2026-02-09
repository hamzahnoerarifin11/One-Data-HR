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
        $query = Karyawan::with(['pekerjaan.company', 'pekerjaan.division', 'pekerjaan.department', 'pekerjaan.unit', 'pekerjaan.level', 'pendidikan', 'kontrak', 'keluarga', 'bpjs', 'perusahaan', 'status']);

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
                  });
            });
        }

        $karyawans = $query->orderBy('id_karyawan', 'desc')->paginate(10)->appends($request->query());

        return view('pages.karyawan.index', compact('karyawans'));
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
        $karyawans = Karyawan::with(['pekerjaan.company', 'pekerjaan.division', 'pekerjaan.department', 'pekerjaan.unit', 'pekerjaan.level'])->get();

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
                $row['Perusahaan']  = $karyawan->pekerjaan->first()->company->name ?? '-';

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
            $sheet->setCellValue('G' . $row, $karyawan->pekerjaan->first()->company->name ?? '-');
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
}
