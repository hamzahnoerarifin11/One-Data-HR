<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OnboardingKaryawanSeeder extends Seeder
{
    public function run()
    {
        $kandidatList = DB::table('kandidat')
            ->select('id_kandidat', 'posisi_id')
            ->get();

        if ($kandidatList->isEmpty()) {
            return;
        }

        foreach ($kandidatList as $kandidat) {

            // =========================
            // TANGGAL DASAR
            // =========================
            $tanggalMasuk = Carbon::now()->subDays(rand(60, 360));

            // 30% turnover
            $isTurnover = rand(1, 100) <= 30;

            $data = [
                // RELASI
                'kandidat_id' => $kandidat->id_kandidat,
                'posisi_id'   => $kandidat->posisi_id,

                // DATA PRIBADI
                'pendidikan_terakhir' => collect(['SMA', 'D3', 'S1', 'S2'])->random(),
                'nama_sekolah' => collect([
                    'Universitas Indonesia',
                    'Universitas Gadjah Mada',
                    'Institut Teknologi Bandung',
                    'Politeknik Negeri Jakarta',
                    'SMA Negeri 1 Jakarta'
                ])->random(),

                'alamat_domisili' => 'Jl. Contoh No. ' . rand(10, 250),
                'nomor_wa'        => '08' . rand(1000000000, 9999999999),

                // KONTRAK
                'jadwal_ttd_kontrak' => $tanggalMasuk->copy()->addDays(7)->toDateString(),

                // DEFAULT ENUM
                'status_turnover'   => 'belum',
                'status_onboarding' => 'draft',

                // TIMESTAMP
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // =========================
            // STATUS TURNOVER / AKTIF
            // =========================
            if ($isTurnover) {

                $tanggalResign = $tanggalMasuk->copy()->addDays(rand(30, 300));

                $data['status_turnover'] = 'turnover';
                $data['tanggal_resign']  = $tanggalResign->toDateString();
                $data['masa_kerja_hari'] = $tanggalMasuk->diffInDays($tanggalResign);
                $data['alasan_resign']   = collect([
                    'Mendapatkan pekerjaan lain',
                    'Tidak sesuai ekspektasi',
                    'Masalah keluarga',
                    'Lingkungan kerja'
                ])->random();

                $data['status_onboarding'] = 'selesai';

            } else {

                $data['tanggal_lolos_probation'] = $tanggalMasuk->copy()->addDays(90)->toDateString();
                $data['status_turnover'] = 'lolos';
                $data['status_onboarding'] = collect(['progress', 'selesai'])->random();
            }

            // =========================
            // ID CARD
            // =========================
            $data['id_card_status'] = collect(['proses', 'jadi', 'diambil'])->random();

            if ($data['id_card_status'] !== 'proses') {
                $data['id_card_proses'] = Carbon::now()->subDays(rand(10, 30))->toDateString();
            }

            if (in_array($data['id_card_status'], ['jadi', 'diambil'])) {
                $data['id_card_jadi'] = Carbon::now()->subDays(rand(5, 15))->toDateString();
            }

            if ($data['id_card_status'] === 'diambil') {
                $data['id_card_diambil'] = Carbon::now()->subDays(rand(1, 7))->toDateString();
            }

            // =========================
            // FINGERPRINT
            // =========================
            $data['fingerprint_status'] = collect(['belum', 'sudah'])->random();

            if ($data['fingerprint_status'] === 'sudah') {
                $data['fingerprint_sudah'] = Carbon::now()->subDays(rand(1, 14))->toDateString();
            }

            // =========================
            // DOKUMEN (DATE, BUKAN URL)
            // =========================
            if (rand(0, 1)) {
                $data['link_data_dikirim_hr'] = Carbon::now()->subDays(rand(20, 60))->toDateString();
            }

            if (rand(0, 1)) {
                $data['link_data_dilengkapi_karyawan'] = Carbon::now()->subDays(rand(5, 30))->toDateString();
            }

            if (rand(0, 1)) {
                $data['ijazah_diterima_hr'] = Carbon::now()->subDays(rand(10, 40))->toDateString();
            }

            if (rand(0, 1)) {
                $data['kontrak_ttd_pusat'] = Carbon::now()->subDays(rand(5, 20))->toDateString();
            }

            // =========================
            // MATERI INDUCTION
            // =========================
            $data['visi_misi']          = rand(0, 1);
            $data['wadja_philosophy']   = rand(0, 1);
            $data['sejarah_perusahaan'] = rand(0, 1);
            $data['kondisi_perizinan']  = rand(0, 1);
            $data['tata_tertib']        = rand(0, 1);
            $data['bpjs']               = rand(0, 1);
            $data['k3']                 = rand(0, 1);

            if (rand(0, 1)) {
                $data['tanggal_induction'] = Carbon::now()->subDays(rand(10, 60))->toDateString();
            }

            // =========================
            // EVALUASI
            // =========================
            if (rand(0, 1)) {
                $data['evaluasi'] = 'Onboarding berjalan sesuai SOP dan kebutuhan unit kerja.';
            }

            DB::table('onboarding_karyawan')->insert($data);
        }
    }
}
