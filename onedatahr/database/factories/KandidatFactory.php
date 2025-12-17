<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Kandidat;
use App\Models\Posisi;

class KandidatFactory extends Factory
{
    protected $model = Kandidat::class;

    public function definition()
    {
        $posisi = Posisi::inRandomOrder()->first()?->id_posisi ?? 1;
        return [
            'nama' => $this->faker->name,
            'posisi_id' => $posisi,
            'tanggal_melamar' => $this->faker->dateTimeBetween('-3 months','now')->format('Y-m-d'),
            'sumber' => $this->faker->randomElement(['Jobsite','Referral','LinkedIn']),
            'status_akhir' => 'Masuk',
        ];
    }
}
