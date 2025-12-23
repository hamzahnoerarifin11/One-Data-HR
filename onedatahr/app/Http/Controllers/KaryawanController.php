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
use Illuminate\Support\Facades\DB;

class KaryawanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // hanya admin bisa create/edit/destroy
        $this->middleware('role:admin')->only(['create','store','edit','update','destroy']);
    }
    public function index()
    {
        // Gunakan get() agar 800+ data dikirim semua ke view
        $karyawans = Karyawan::with(['pekerjaan','pendidikan','kontrak','keluarga','bpjs','perusahaan','status'])
            ->orderBy('id_karyawan','desc')
            ->get(); 
            
        return view('pages.karyawan.index', compact('karyawans'));
    }
    public function batchDelete(Request $request) {
    $ids = explode(',', $request->ids);
    \App\Models\Karyawan::whereIn('id_karyawan', $ids)->delete();
    return back()->with('success', count($ids) . ' karyawan berhasil dihapus.');
    }

    public function create()
    {
        // form multi-step
        return view('pages.karyawan.create', [
        'jabatanOptions' => getjabatan('pekerjaan', 'Jabatan'),
        'departementOptions' => getdepartement('pekerjaan', 'Departement'),
        'divisiOptions' => getdivisi('pekerjaan', 'Divisi'),
        'unitOptions' => getunit('pekerjaan', 'Unit'),
        'lokasikerjaOptions' => getlokasikerja('pekerjaan', 'Lokasi_Kerja'),
        'perusahaanOptions' => getperusahaan('perusahaan', 'Perusahaan'),
        'pendidikanOptions' => getpendidikan('pendidikan', 'Pendidikan_Terakhir'),
         ]);

    }

    public function store(Request $request)
    {
        // validasi dasar bagian data karyawan — lebih rinci di client
        $request->validate([
            'Nama_Sesuai_KTP' => 'required|string|max:255',
            'Email' => 'nullable|email|max:255',
            'Tanggal_Non_Aktif' => 'nullable|date',
            'Alasan_Non_Aktif' => 'nullable|string|max:255',
            'Ijazah_Dikembalikan' => 'nullable|in:Ya,Tidak',
            'Bulan' => 'nullable|integer|min:0',
            'Status_BPJS_KT' => 'nullable|in:Aktif,Tidak Aktif',
            'Status_BPJS_KS' => 'nullable|in:Aktif,Tidak Aktif',
            'Masa_Kerja' => 'nullable|string|max:100',

            // Anda bisa menambah validasi field lain sesuai yang diinginkan
        ]);

        DB::beginTransaction();
        try {
            // simpan data karyawan (semua kolom dari karyawan table)
            $karyawanData = $request->only([
                'NIK','Status','Kode','Nama_Sesuai_KTP','NIK_KTP','Nama_Lengkap_Sesuai_Ijazah',
                'Tempat_Lahir_Karyawan','Tanggal_Lahir_Karyawan','Umur_Karyawan','Jenis_Kelamin_Karyawan',
                'Status_Pernikahan','Golongan_Darah','Nomor_Telepon_Aktif_Karyawan','Email','Alamat_KTP',
                'RT','RW','Kelurahan_Desa','Kecamatan','Kabupaten_Kota','Provinsi','Alamat_Domisili',
                'RT_Sesuai_Domisili','RW_Sesuai_Domisili','Kelurahan_Desa_Domisili','Kecamatan_Sesuai_Domisili',
                'Kabupaten_Kota_Sesuai_Domisili','Provinsi_Sesuai_Domisili','Alamat_Lengkap'
            ]);

            $karyawan = Karyawan::create($karyawanData);

            // Data keluarga (one)
            if ($request->filled('Nama_Ayah_Kandung') || $request->filled('Nama_Ibu_Kandung') || $request->filled('anak')) {
                $keluargaData = $request->only([
                    'Nama_Ayah_Kandung','Nama_Ibu_Kandung','Nama_Lengkap_Suami_Istri','NIK_KTP_Suami_Istri','Tempat_Lahir_Suami_Istri',
                    'Tanggal_Lahir_Suami_Istri','Nomor_Telepon_Suami_Istri','Pendidikan_Terakhir_Suami_Istri',
                    'Nama_Lengkap_Anak_Pertama','Tempat_Lahir_Anak_Pertama','Tanggal_Lahir_Anak_Pertama','Jenis_Kelamin_Anak_Pertama','Pendidikan_Terakhir_Anak_Pertama',
                    'Nama_Lengkap_Anak_Kedua','Tempat_Lahir_Anak_Kedua','Tanggal_Lahir_Anak_Kedua','Jenis_Kelamin_Anak_Kedua','Pendidikan_Terakhir_Anak_Kedua',
                    'Nama_Lengkap_Anak_Ketiga','Tempat_Lahir_Anak_Ketiga','Tanggal_Lahir_Anak_Ketiga','Jenis_Kelamin_Anak_Ketiga','Pendidikan_Terakhir_Anak_Ketiga',
                    'Nama_Lengkap_Anak_Keempat','Tempat_Lahir_Anak_Keempat','Tanggal_Lahir_Anak_Keempat','Jenis_Kelamin_Anak_Keempat','Pendidikan_Terakhir_Anak_Keempat',
                    'Nama_Lengkap_Anak_Kelima','Tempat_Lahir_Anak_Kelima','Tanggal_Lahir_Anak_Kelima','Jenis_Kelamin_Anak_Kelima','Pendidikan_Terakhir_Anak_Kelima',
                    'Nama_Lengkap_Anak_Keenam','Tempat_Lahir_Anak_Keenam','Tanggal_Lahir_Anak_Keenam','Pendidikan_Terakhir_Anak_Keenam'
                ]);
                $keluargaData['anak'] = $request->input('anak', []);
                $keluargaData['id_karyawan'] = $karyawan->id_karyawan;
                DataKeluarga::create($keluargaData);
            }

            // Pekerjaan (boleh banyak, tapi dari form kita simpan satu entri default)
            if ($request->filled('Bagian') || $request->filled('Jabatan') || $request->filled('Jenis_Kontrak') || $request->filled('Perjanjian')) {
                $pekerjaanData = $request->only(['Jabatan','Bagian','Departement','Divisi','Unit','Jenis_Kontrak','Perjanjian','Lokasi_Kerja']);
                $pekerjaanData['id_karyawan'] = $karyawan->id_karyawan;
                Pekerjaan::create($pekerjaanData);
            }

            // Perusahaan (one)
            // Perusahaan (one) — either new name or selected existing
            if ($request->filled('Perusahaan')) {
                Perusahaan::create([
                    'id_karyawan' => $karyawan->id_karyawan,
                    'Perusahaan' => $request->input('Perusahaan')
                ]);
            } elseif ($request->filled('id_perusahaan')) {
                $pModel = Perusahaan::find($request->input('id_perusahaan'));
                $pName = $pModel ? $pModel->Perusahaan : null;
                if ($pName) {
                    Perusahaan::create([
                        'id_karyawan' => $karyawan->id_karyawan,
                        'Perusahaan' => $pName
                    ]);
                }
            }

            // Pendidikan (bisa multi — form mengirim array)
            // if ($request->has('pendidikan') && is_array($request->input('pendidikan'))) {
            //     foreach ($request->input('pendidikan') as $pd) {
            //         $pd = array_filter($pd); // hapus kosong
            //         if (!empty($pd)) {
            //             Pendidikan::create(array_merge(['id_karyawan' => $karyawan->id_karyawan], $pd));
            //         }
            //     }
            // } else if ($request->filled('Pendidikan_Terakhir')) {
            //     Pendidikan::create(array_merge(['id_karyawan' => $karyawan->id_karyawan], $request->only(['Pendidikan_Terakhir','Nama_Lengkap_Tempat_Pendidikan_Terakhir','Jurusan'])));
            // }

            // Pendidikan
            // Pendidikan (ONE sesuai view)
            if (
                $request->filled('Pendidikan_Terakhir') ||
                $request->filled('Nama_Lengkap_Tempat_Pendidikan_Terakhir') ||
                $request->filled('Jurusan')
            ) {
                Pendidikan::create([
                    'id_karyawan' => $karyawan->id_karyawan,
                    'Pendidikan_Terakhir' => $request->Pendidikan_Terakhir,
                    'Nama_Lengkap_Tempat_Pendidikan_Terakhir' => $request->Nama_Lengkap_Tempat_Pendidikan_Terakhir,
                    'Jurusan' => $request->Jurusan,
                ]);
            }


            // Kontrak (bisa banyak)
            // if ($request->has('kontrak') && is_array($request->input('kontrak'))) {
            //     foreach ($request->input('kontrak') as $kn) {
            //         $kn = array_filter($kn);
            //         if (!empty($kn)) {
            //             // compute masa kerja
            //             if (!empty($kn['Tanggal_Mulai_Tugas'])) {
            //                 try {
            //                     $start = new \DateTime($kn['Tanggal_Mulai_Tugas']);
            //                     $end = new \DateTime();
            //                     if ($start <= $end) {
            //                         $diff = $start->diff($end);
            //                         $kn['Masa_Kerja'] = $diff->y . ' Tahun ' . $diff->m . ' Bulan ' . $diff->d . ' Hari';
            //                     } else {
            //                         $kn['Masa_Kerja'] = '';
            //                     }
            //                 } catch (\Exception $e) { $kn['Masa_Kerja'] = ''; }
            //             }
            //             Kontrak::create(array_merge(['id_karyawan' => $karyawan->id_karyawan], $kn));
            //         }
            //     }
            // } else if ($request->filled('Tanggal_Mulai_Tugas')) {
            //     Kontrak::create(array_merge(['id_karyawan' => $karyawan->id_karyawan], $request->only([
            //         'Tanggal_Mulai_Tugas','PKWT_berakhir','Tanggal_Diangkat_Menjadi_Karyawan_Tetap','Riwayat_Penempatan','Tanggal_Riwayat_Penempatan','Mutasi_Promosi_Demosi','Tanggal_Mutasi_Promosi_Demosi','Masa_Kerja','NO_PKWT_PERTAMA','NO_SK_PERTAMA'
            //     ])));
            // }
            // Kontrak (ONE sesuai view)

            // Kontrak (ONE)
            if ($request->filled('Tanggal_Mulai_Tugas')) {

                // Hitung masa kerja
                $masaKerja = '';
                try {
                    $start = new \DateTime($request->Tanggal_Mulai_Tugas);
                    $now = new \DateTime();
                    if ($start <= $now) {
                        $diff = $start->diff($now);
                        $masaKerja = "{$diff->y} Tahun {$diff->m} Bulan {$diff->d} Hari";
                    }
                } catch (\Exception $e) {}

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
            }


            // Status Karyawan (one)
            if ($request->filled('Tanggal_Non_Aktif') || $request->filled('Alasan_Non_Aktif')) {
                StatusKaryawan::create(array_merge(['id_karyawan' => $karyawan->id_karyawan], $request->only(['Tanggal_Non_Aktif','Alasan_Non_Aktif','Ijazah_Dikembalikan','Bulan'])));
            }

            // BPJS
            if ($request->filled('Status_BPJS_KT') || $request->filled('Status_BPJS_KS')) {
                Bpjs::create(array_merge(['id_karyawan' => $karyawan->id_karyawan], $request->only(['Status_BPJS_KT','Status_BPJS_KS'])));
            }

            DB::commit();
            return redirect()->route('pages.karyawan.index')->with('success', 'Karyawan berhasil dibuat');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $karyawan = Karyawan::with(['pekerjaan','pendidikan','kontrak','keluarga','bpjs','perusahaan','status'])->findOrFail($id);
        return view('pages.karyawan.show', compact('karyawan'));
    }

    public function edit($id)
    {
        $karyawan = Karyawan::with(['pekerjaan','pendidikan','kontrak','keluarga','bpjs','perusahaan','status'])->findOrFail($id);
        return view('pages.karyawan.edit', array_merge(compact('karyawan'), [
            'jabatanOptions' => getjabatan('pekerjaan', 'Jabatan'),
            'departementOptions' => getdepartement('pekerjaan', 'Departement'),
            'divisiOptions' => getdivisi('pekerjaan', 'Divisi'),
            'unitOptions' => getunit('pekerjaan', 'Unit'),
            'lokasikerjaOptions' => getlokasikerja('pekerjaan', 'Lokasi_Kerja'),
            'perusahaanOptions' => getperusahaan('perusahaan', 'Perusahaan'),
            'pendidikanOptions' => getpendidikan('pendidikan', 'Pendidikan_Terakhir'),
        ]));
    }

    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::with([
                                        'kontrak',
                                        'pendidikan',
                                        'keluarga',
                                        'bpjs',
                                        'pekerjaan',
                                        'status',
                                        'perusahaan'
                                    ])->findOrFail($id);
        $request->validate([
            'Nama_Sesuai_KTP' => 'required|string|max:255',
            'Email' => 'nullable|email|max:255',
            'Tanggal_Non_Aktif' => 'nullable|date',
            'Alasan_Non_Aktif' => 'nullable|string|max:255',
            'Ijazah_Dikembalikan' => 'nullable|in:Ya,Tidak',
            'Bulan' => 'nullable|integer|min:0',
            'Status_BPJS_KT' => 'nullable|in:Aktif,Tidak Aktif',
            'Status_BPJS_KS' => 'nullable|in:Aktif,Tidak Aktif',
            'Masa_Kerja' => 'nullable|string|max:100',
        ]);
        $karyawan->update($request->only([
            'NIK','Status','Kode','Nama_Sesuai_KTP','NIK_KTP','Nama_Lengkap_Sesuai_Ijazah',
            'Tempat_Lahir_Karyawan','Tanggal_Lahir_Karyawan','Umur_Karyawan','Jenis_Kelamin_Karyawan',
            'Status_Pernikahan','Golongan_Darah','Nomor_Telepon_Aktif_Karyawan','Email','Alamat_KTP',
            'RT','RW','Kelurahan_Desa','Kecamatan','Kabupaten_Kota','Provinsi','Alamat_Domisili',
            'RT_Sesuai_Domisili','RW_Sesuai_Domisili','Kelurahan_Desa_Domisili','Kecamatan_Sesuai_Domisili',
            'Kabupaten_Kota_Sesuai_Domisili','Provinsi_Sesuai_Domisili','Alamat_Lengkap'
        ]));

        // update related simple one-to-one records (contoh: keluarga, bpjs, perusahaan, status)
        if ($request->filled('Nama_Ayah_Kandung') || $request->filled('Nama_Ibu_Kandung') ||$request->filled('anak')) {
            $kel = $karyawan->keluarga;
            $data = $request->only([
                'Nama_Ayah_Kandung','Nama_Ibu_Kandung','Nama_Lengkap_Suami_Istri','NIK_KTP_Suami_Istri','Tempat_Lahir_Suami_Istri',
                'Tanggal_Lahir_Suami_Istri','Nomor_Telepon_Suami_Istri','Pendidikan_Terakhir_Suami_Istri'
            ]);
             // 🔥 UPDATE JSON ANAK
            $data['anak'] = $request->input('anak', []);

            if ($kel) { $kel->update($data); } else { DataKeluarga::create(array_merge(['id_karyawan' => $karyawan->id_karyawan], $data)); }
        }

        // update BPJS (one)
        if ($request->filled('Status_BPJS_KT') || $request->filled('Status_BPJS_KS')) {
            $bpjs = $karyawan->bpjs;
            $dataBpjs = $request->only(['Status_BPJS_KT','Status_BPJS_KS']);
            if ($bpjs) {
                $bpjs->update($dataBpjs);
            } else {
                Bpjs::create(array_merge(['id_karyawan' => $karyawan->id_karyawan], $dataBpjs));
            }
        }

        // update Status Karyawan (one)
        if ($request->filled('Tanggal_Non_Aktif') || $request->filled('Alasan_Non_Aktif') || $request->filled('Ijazah_Dikembalikan') || $request->filled('Bulan')) {
            $status = $karyawan->status;
            $dataStatus = $request->only(['Tanggal_Non_Aktif','Alasan_Non_Aktif','Ijazah_Dikembalikan','Bulan']);
            if ($status) {
                $status->update($dataStatus);
            } else {
                StatusKaryawan::create(array_merge(['id_karyawan' => $karyawan->id_karyawan], $dataStatus));
            }
        }
        //perusahaan
        if ($request->filled('Perusahaan')) {
            $perusahaan = $karyawan->perusahaan;
            $dataPerusahaan = $request->only(['Perusahaan']);
            if ($perusahaan) {
                $perusahaan->update($dataPerusahaan);
            } else {
                Perusahaan::create(array_merge(['id_karyawan' => $karyawan->id_karyawan], $dataPerusahaan));
            }
        }

        // sync pendidikan (delete & reinsert)
        // if ($request->has('pendidikan') && is_array($request->input('pendidikan'))) {
        //     $karyawan->pendidikan()->delete();
        //     foreach ($request->input('pendidikan') as $pd) {
        //         $pd = array_filter($pd);
        //         if (!empty($pd)) Pendidikan::create(array_merge(['id_karyawan' => $karyawan->id_karyawan], $pd));
        //     }
        // }

        // pendidikan (ONE sesuai view)
        // Update Pendidikan (ONE)
        if (
            $request->filled('Pendidikan_Terakhir') ||
            $request->filled('Nama_Lengkap_Tempat_Pendidikan_Terakhir') ||
            $request->filled('Jurusan')
        ) {
            $pendidikan = $karyawan->pendidikan()->first();

            $dataPendidikan = [
                'Pendidikan_Terakhir' => $request->Pendidikan_Terakhir,
                'Nama_Lengkap_Tempat_Pendidikan_Terakhir' => $request->Nama_Lengkap_Tempat_Pendidikan_Terakhir,
                'Jurusan' => $request->Jurusan,
            ];

            if ($pendidikan) {
                $pendidikan->update($dataPendidikan);
            } else {
                Pendidikan::create(array_merge(['id_karyawan' => $karyawan->id_karyawan], $dataPendidikan));
            }
        }


        // sync kontrak (delete & reinsert) with masa kerja compute
        // if ($request->has('kontrak') && is_array($request->input('kontrak'))) {
        //     $karyawan->kontrak()->delete();
        //     foreach ($request->input('kontrak') as $kn) {
        //         $kn = array_filter($kn);
        //         if (!empty($kn)) {
        //             if (!empty($kn['Tanggal_Mulai_Tugas'])) {
        //                 try {
        //                     $start = new \DateTime($kn['Tanggal_Mulai_Tugas']);
        //                     $end = new \DateTime();
        //                     if ($start <= $end) {
        //                         $diff = $start->diff($end);
        //                         $kn['Masa_Kerja'] = $diff->y . ' Tahun ' . $diff->m . ' Bulan ' . $diff->d . ' Hari';
        //                     } else { $kn['Masa_Kerja'] = ''; }
        //                 } catch (\Exception $e) { $kn['Masa_Kerja'] = ''; }
        //             }
        //             Kontrak::create(array_merge(['id_karyawan' => $karyawan->id_karyawan], $kn));
        //         }
        //     }
        // }

        // Kontrak (ONE sesuai view)
        // Update Kontrak (ONE)
        if ($request->filled('Tanggal_Mulai_Tugas')) {

            $kontrak = $karyawan->kontrak;

            $masaKerja = '';
            try {
                $start = new \DateTime($request->Tanggal_Mulai_Tugas);
                $now = new \DateTime();
                if ($start <= $now) {
                    $diff = $start->diff($now);
                    $masaKerja = "{$diff->y} Tahun {$diff->m} Bulan {$diff->d} Hari";
                }
            } catch (\Exception $e) {}

            $dataKontrak = [
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
            ];

            if ($kontrak) {
                $kontrak->update($dataKontrak);
            } else {
                Kontrak::create(array_merge(['id_karyawan' => $karyawan->id_karyawan], $dataKontrak));
            }
        }


        // update pekerjaan (satu entri utama)
        if ($request->filled('Bagian') || $request->filled('Jabatan') || $request->filled('Jenis_Kontrak') || $request->filled('Perjanjian')) {
            $pekerjaan = $karyawan->pekerjaan()->first();
            $pekerjaanData = $request->only(['Jabatan','Bagian','Departement','Divisi','Unit','Jenis_Kontrak','Perjanjian','Lokasi_Kerja']);
            if ($pekerjaan) {
                $pekerjaan->update($pekerjaanData);
            } else {
                $pekerjaanData['id_karyawan'] = $karyawan->id_karyawan;
                Pekerjaan::create($pekerjaanData);
            }
        }

        return redirect()->route('karyawan.show', $id)->with('success', 'Karyawan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $karyawan = Karyawan::with([
                                    'kontrak',
                                    'pendidikan',
                                    'keluarga',
                                    'bpjs',
                                    'pekerjaan',
                                    'status',
                                    'perusahaan'
                                ])->findOrFail($id);
        DB::transaction(function() use ($karyawan) {
            // tergantung kebijakan: hapus relasi juga
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
}
