<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TempaKelompokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\TempaKelompok::create([
            'id_tempa' => 1, // Assuming tempa id 1 exists
            'nama_kelompok' => 'Kelompok A',
            'nama_mentor' => 'Mentor A',
            'mentor_id' => 19, // User id 19
        ]);

        \App\Models\TempaKelompok::create([
            'id_tempa' => 1,
            'nama_kelompok' => 'Kelompok B',
            'nama_mentor' => 'Mentor B',
            'mentor_id' => 19,
        ]);
    }
}
