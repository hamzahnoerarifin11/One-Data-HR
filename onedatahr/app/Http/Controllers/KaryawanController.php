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
        $this->middleware('role:admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $karyawans = Karyawan::with(['pekerjaan', 'pendidikan', 'kontrak', 'keluarga', 'bpjs', 'perusahaan', 'status'])
            ->orderBy('id_karyawan', 'desc')
            ->get();

        return view('pages.karyawan.index', compact('karyawans'));
    }

    public function batchDelete(Request $request)
    {
        $ids = explode(',', $request->ids);
        Karyawan::whereIn('id_karyawan', $ids)->delete();
        return back()->with('success', count($ids) . ' karyawan berhasil dihapus.');
    }

    public function create()
    {
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
        $request->validate([
            'Nama_Sesuai_KTP' => 'required|string|max:255',
            'Email' => 'nullable|email|max:255',
            'Tanggal_Non_Aktif' => 'nullable|date',
            'Alasan_Non_Aktif' => 'nullable|string|max:255',
            'Ijazah_Dikembalikan' => 'nullable|in:Ya,Tidak',
            'Bulan' => 'nullable|integer|min:0',
            'Status_BPJS_KT' => 'nullable|in:Aktif,Tidak Aktif',
            'Status_BPJS_KS' => 'nullable|in:Aktif,Tidak Aktif',
        ]);

        DB::beginTransaction();
        try {
            $karyawanData = $request->only([
                'NIK', 'Status', 'Kode', 'Nama_Sesuai_KTP', 'NIK_KTP', 'Nama_Lengkap_Sesuai_Ijazah',
                'Tempat_Lahir_Karyawan', 'Tanggal_Lahir_Karyawan', 'Umur_Karyawan', 'Jenis_Kelamin_Karyawan',
                'Status_Pernikahan', 'Golongan_Darah', 'Nomor_Telepon_Aktif_Karyawan', 'Email', 'Alamat_KTP',
                'RT', 'RW', 'Kelurahan_Desa', 'Kecamatan', 'Kabupaten_Kota', 'Provinsi', 'Alamat_Domisili',
                'RT_Sesuai_Domisili', 'RW_Sesuai_Domisili', 'Kelurahan_Desa_Domisili', 'Kecamatan_Sesuai_Domisili',
                'Kabupaten_Kota_Sesuai_Domisili', 'Provinsi_Sesuai_Domisili', 'Alamat_Lengkap'
            ]);

            $karyawan = Karyawan::create($karyawanData);

            // Data Keluarga
            $keluargaData = $request->only([
                'Nama_Ayah_Kandung', 'Nama_Ibu_Kandung', 'Nama_Lengkap_Suami_Istri', 'NIK_KTP_Suami_Istri', 'Tempat_Lahir_Suami_Istri',
                'Tanggal_Lahir_Suami_Istri', 'Nomor_Telepon_Suami_Istri', 'Pendidikan_Terakhir_Suami_Istri'
            ]);
            $keluargaData['anak'] = $request->input('anak', []);
            $keluargaData['id_karyawan'] = $karyawan->id_karyawan;
            DataKeluarga::create($keluargaData);

            // Pekerjaan
            $pekerjaanData = $request->only(['Jabatan', 'Bagian', 'Departement', 'Divisi', 'Unit', 'Jenis_Kontrak', 'Perjanjian', 'Lokasi_Kerja']);
            $pekerjaanData['id_karyawan'] = $karyawan->id_karyawan;
            Pekerjaan::create($pekerjaanData);

            // Perusahaan
            $perusahaanName = $request->input('Perusahaan');
            if (!$perusahaanName && $request->filled('id_perusahaan')) {
                $pModel = Perusahaan::find($request->input('id_perusahaan'));
                $perusahaanName = $pModel ? $pModel->Perusahaan : null;
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
                } catch (\Exception $e) {}
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

            DB::commit();
            return redirect()->route('pages.karyawan.index')->with('success', 'Karyawan berhasil dibuat');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $karyawan = Karyawan::with(['pekerjaan', 'pendidikan', 'kontrak', 'keluarga', 'bpjs', 'perusahaan', 'status'])->findOrFail($id);
        return view('pages.karyawan.show', compact('karyawan'));
    }

    public function edit($id)
    {
        $karyawan = Karyawan::with(['pekerjaan', 'pendidikan', 'kontrak', 'keluarga', 'bpjs', 'perusahaan', 'status'])->findOrFail($id);
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
                'NIK', 'Status', 'Kode', 'Nama_Sesuai_KTP', 'NIK_KTP', 'Nama_Lengkap_Sesuai_Ijazah',
                'Tempat_Lahir_Karyawan', 'Tanggal_Lahir_Karyawan', 'Umur_Karyawan', 'Jenis_Kelamin_Karyawan',
                'Status_Pernikahan', 'Golongan_Darah', 'Nomor_Telepon_Aktif_Karyawan', 'Email', 'Alamat_KTP',
                'RT', 'RW', 'Kelurahan_Desa', 'Kecamatan', 'Kabupaten_Kota', 'Provinsi', 'Alamat_Domisili',
                'RT_Sesuai_Domisili', 'RW_Sesuai_Domisili', 'Kelurahan_Desa_Domisili', 'Kecamatan_Sesuai_Domisili',
                'Kabupaten_Kota_Sesuai_Domisili', 'Provinsi_Sesuai_Domisili', 'Alamat_Lengkap'
            ]));

            // 2. Update Keluarga
            $dataKel = $request->only([
                'Nama_Ayah_Kandung', 'Nama_Ibu_Kandung', 'Nama_Lengkap_Suami_Istri', 'NIK_KTP_Suami_Istri',
                'Tempat_Lahir_Suami_Istri', 'Tanggal_Lahir_Suami_Istri', 'Nomor_Telepon_Suami_Istri', 'Pendidikan_Terakhir_Suami_Istri'
            ]);
            $dataKel['anak'] = $request->input('anak', []);
            $karyawan->keluarga ? $karyawan->keluarga->update($dataKel) : DataKeluarga::create(array_merge(['id_karyawan' => $id], $dataKel));

            // 3. Update BPJS
            $dataBpjs = $request->only(['Status_BPJS_KT', 'Status_BPJS_KS']);
            $karyawan->bpjs ? $karyawan->bpjs->update($dataBpjs) : Bpjs::create(array_merge(['id_karyawan' => $id], $dataBpjs));

            // 4. Update Status Karyawan (Bagian yang Anda tanyakan)
            $dataStatus = $request->only(['Tanggal_Non_Aktif', 'Alasan_Non_Aktif', 'Ijazah_Dikembalikan', 'Bulan']);
            $karyawan->status ? $karyawan->status->update($dataStatus) : StatusKaryawan::create(array_merge(['id_karyawan' => $id], $dataStatus));

            // 5. Update Perusahaan
            $dataPerush = $request->only(['Perusahaan']);
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
                } catch (\Exception $e) {}
            }
            $dataKontrak = array_merge($request->only([
                'Tanggal_Mulai_Tugas', 'PKWT_Berakhir', 'Tanggal_Diangkat_Menjadi_Karyawan_Tetap',
                'Riwayat_Penempatan', 'Tanggal_Riwayat_Penempatan', 'Mutasi_Promosi_Demosi',
                'Tanggal_Mutasi_Promosi_Demosi', 'NO_PKWT_PERTAMA', 'NO_SK_PERTAMA'
            ]), ['Masa_Kerja' => $masaKerja]);

            $karyawan->kontrak ? $karyawan->kontrak->update($dataKontrak) : Kontrak::create(array_merge(['id_karyawan' => $id], $dataKontrak));

            // 8. Update Pekerjaan
            $dataKerja = $request->only(['Jabatan', 'Bagian', 'Departement', 'Divisi', 'Unit', 'Jenis_Kontrak', 'Perjanjian', 'Lokasi_Kerja']);
            $karyawan->pekerjaan()->exists() ? $karyawan->pekerjaan()->first()->update($dataKerja) : Pekerjaan::create(array_merge(['id_karyawan' => $id], $dataKerja));

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
}
