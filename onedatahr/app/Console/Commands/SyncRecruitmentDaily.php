<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kandidat;
use App\Models\RekrutmenDaily;
use Illuminate\Support\Facades\DB;

class SyncRecruitmentDaily extends Command
{
    // Nama command yang akan dipanggil di terminal
    protected $signature = 'rekrutmen:sync-daily';
    protected $description = 'Sinkronisasi data rekrutmen_daily dari data kandidat yang sudah ada';

    public function handle()
    {
        $this->info('Sinkronisasi data harian berdasarkan tanggal riil tiap tahapan...');

        $tahapan = [
            'tgl_lolos_cv'         => 'lolos_cv',
            'tgl_lolos_psikotes'   => 'lolos_psikotes',
            'tgl_lolos_kompetensi' => 'lolos_kompetensi',
            'tgl_lolos_hr'         => 'lolos_hr',
            'tgl_lolos_user'       => 'lolos_user',
        ];

        foreach ($tahapan as $kolomTgl => $kolomDaily) {
            $data = Kandidat::select('posisi_id', $kolomTgl)
                ->whereNotNull($kolomTgl)
                ->groupBy('posisi_id', $kolomTgl)
                ->selectRaw("count(*) as total")
                ->get();

            foreach ($data as $row) {
                RekrutmenDaily::updateOrCreate(
                    ['posisi_id' => $row->posisi_id, 'date' => $row->$kolomTgl],
                    [$kolomDaily => $row->total]
                );
            }
        }
        $this->info('Selesai!');
    }
}
