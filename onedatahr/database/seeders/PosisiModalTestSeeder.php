<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Posisi;

class PosisiModalTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            'Software Engineer',
            'Quality Assurance',
            'Product Manager',
            'UI/UX Designer',
            'DevOps Engineer',
        ];

        foreach ($positions as $p) {
            Posisi::firstOrCreate(['nama_posisi' => $p]);
        }
    }
}
