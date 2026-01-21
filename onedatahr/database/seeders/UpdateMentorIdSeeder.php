<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TempaPeserta;
use App\Models\TempaKelompok;

class UpdateMentorIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pesertas = TempaPeserta::with('kelompok')->get();

        foreach ($pesertas as $peserta) {
            if ($peserta->kelompok && $peserta->kelompok->ketua_tempa_id) {
                $peserta->update(['mentor_id' => $peserta->kelompok->ketua_tempa_id]);
            }
        }

        $this->command->info('Updated mentor_id for ' . $pesertas->count() . ' peserta');
    }
}
