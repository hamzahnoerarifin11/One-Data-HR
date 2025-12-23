<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RekrutmenDaily;
use App\Models\Posisi;
use Carbon\Carbon;

class RekrutmenDailySeeder extends Seeder
{
    public function run(): void
    {
        $pos = Posisi::pluck('id_posisi')->toArray();
        if (empty($pos)) return;

        $start = Carbon::now()->subDays(20);
        for ($d = 0; $d < 21; $d++) {
            $date = $start->copy()->addDays($d)->toDateString();
            foreach ($pos as $p) {
                RekrutmenDaily::updateOrCreate(
                    ['posisi_id' => $p, 'date' => $date],
                    ['count' => rand(0,5), 'notes' => null, 'created_by' => 1]
                );
            }
        }
    }
}
