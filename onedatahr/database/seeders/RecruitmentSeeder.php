<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecruitmentSeeder extends Seeder
{
    public function run()
    {
        // positions
        DB::table('posisi')->insertOrIgnore([
            ['nama_posisi' => 'Software Engineer', 'created_at' => now(), 'updated_at' => now()],
            ['nama_posisi' => 'HR Staff', 'created_at' => now(), 'updated_at' => now()],
            ['nama_posisi' => 'Sales', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $pos = DB::table('posisi')->pluck('id_posisi')->toArray();

        // make sample candidates and processes
        for($i=1;$i<=20;$i++){
            $posisi = $pos[array_rand($pos)];
            $tanggal = Carbon::now()->subDays(rand(0,90))->toDateString();
            $kId = DB::table('kandidat')->insertGetId([
                'nama' => 'Candidate '.$i,
                'posisi_id' => $posisi,
                'tanggal_melamar' => $tanggal,
                'sumber' => 'Jobsite',
                'status_akhir' => 'Masuk',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $cv = rand(0,1);
            $ps = $cv ? rand(0,1) : 0;
            $komp = $ps ? rand(0,1) : 0;
            $hr = $komp ? rand(0,1) : 0;
            $user = $hr ? rand(0,1) : 0;

            DB::table('proses_rekrutmen')->insert([
                'kandidat_id' => $kId,
                'cv_lolos' => $cv,
                'tanggal_cv' => $cv ? Carbon::parse($tanggal)->addDays(rand(1,5))->toDateString() : null,
                'psikotes_lolos' => $ps,
                'tanggal_psikotes' => $ps ? Carbon::parse($tanggal)->addDays(rand(6,10))->toDateString() : null,
                'tes_kompetensi_lolos' => $komp,
                'tanggal_tes_kompetensi' => $komp ? Carbon::parse($tanggal)->addDays(rand(11,15))->toDateString() : null,
                'interview_hr_lolos' => $hr,
                'tanggal_interview_hr' => $hr ? Carbon::parse($tanggal)->addDays(rand(16,20))->toDateString() : null,
                'interview_user_lolos' => $user,
                'tanggal_interview_user' => $user ? Carbon::parse($tanggal)->addDays(rand(21,25))->toDateString() : null,
                'tahap_terakhir' => $user ? 'Diterima' : ($hr ? 'Interview User' : ($komp ? 'Interview HR' : ($ps ? 'Tes Kompetensi' : ($cv ? 'Psikotes' : 'Masuk')))),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // some pemberkasan entries
        $sample = DB::table('kandidat')->limit(6)->pluck('id_kandidat');
        foreach($sample as $k){
            DB::table('pemberkasan')->insert([
                'kandidat_id' => $k,
                'follow_up' => 'Initial docs',
                'kandidat_kirim_berkas' => Carbon::now()->subDays(rand(1,10))->toDateString(),
                'selesai_recruitment' => rand(0,1) ? Carbon::now()->subDays(rand(0,5))->toDateString() : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
