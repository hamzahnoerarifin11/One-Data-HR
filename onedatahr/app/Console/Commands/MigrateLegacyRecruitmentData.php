<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kandidat;
use App\Models\RekrutmenDaily;
use Illuminate\Support\Facades\DB;

class MigrateLegacyRecruitmentData extends Command
{
    // Nama command yang akan dipanggil
    protected $signature = 'rekrutmen:migrate-legacy';
    protected $description = 'Mengisi kolom tanggal tahapan untuk data lama dan sinkronisasi ke kalender';

    public function handle()
    {
        $this->info('1. Mengisi kolom tanggal tahapan di tabel kandidat...');

        // Mapping status ke kolom tanggal
        $mapping = [
            'CV Lolos'               => 'tgl_lolos_cv',
            'Psikotes Lolos'         => 'tgl_lolos_psikotes',
            'Tes Kompetensi Lolos'   => 'tgl_lolos_kompetensi',
            'Interview HR Lolos'     => 'tgl_lolos_hr',
            'Interview User Lolos'   => 'tgl_lolos_user',
        ];

        foreach ($mapping as $status => $kolomTgl) {
            // Update data kandidat yang kolom tanggalnya masih NULL
            // Kita asumsikan data lama terjadi pada tanggal_melamar
            $affected = Kandidat::where('status_akhir', $status)
                ->whereNull($kolomTgl)
                ->update([$kolomTgl => DB::raw('tanggal_melamar')]);

            $this->line("- Status [$status]: $affected data diperbarui.");
        }

        $this->info('2. Sinkronisasi ke tabel Rekrutmen Daily (Kalender)...');

        // Reset data status di rekrutmen_daily agar tidak double saat dihitung ulang
        RekrutmenDaily::query()->update([
            'lolos_cv' => 0, 'lolos_psikotes' => 0, 'lolos_kompetensi' => 0,
            'lolos_hr' => 0, 'lolos_user' => 0
        ]);

        foreach ($mapping as $status => $kolomTgl) {
            $stats = Kandidat::select('posisi_id', $kolomTgl)
                ->whereNotNull($kolomTgl)
                ->groupBy('posisi_id', $kolomTgl)
                ->selectRaw("count(*) as total, $kolomTgl as tgl")
                ->get();

            foreach ($stats as $row) {
                RekrutmenDaily::updateOrCreate(
                    ['posisi_id' => $row->posisi_id, 'date' => $row->tgl],
                    [$this->mapToDailyColumn($status) => $row->total]
                );
            }
        }

        $this->info('Migrasi Selesai! Sekarang kalender Anda sudah akurat.');
    }

    // Helper untuk menentukan kolom di tabel daily
    private function mapToDailyColumn($status)
    {
        $map = [
            'CV Lolos'               => 'lolos_cv',
            'Psikotes Lolos'         => 'lolos_psikotes',
            'Tes Kompetensi Lolos'   => 'lolos_kompetensi',
            'Interview HR Lolos'     => 'lolos_hr',
            'Interview User Lolos'   => 'lolos_user',
        ];
        return $map[$status];
    }
}
