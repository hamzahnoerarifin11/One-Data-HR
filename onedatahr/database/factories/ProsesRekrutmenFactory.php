<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ProsesRekrutmen;
use App\Models\Kandidat;

class ProsesRekrutmenFactory extends Factory
{
    protected $model = ProsesRekrutmen::class;

    public function definition()
    {
        $k = Kandidat::inRandomOrder()->first()?->id_kandidat ?? null;
        $cv = $this->faker->boolean(80);
        $ps = $cv ? $this->faker->boolean(60) : false;
        $komp = $ps ? $this->faker->boolean(40) : false;
        return [
            'kandidat_id' => $k,
            'cv_lolos' => $cv,
            'tanggal_cv' => $cv ? $this->faker->dateTimeBetween('-60 days','-30 days')->format('Y-m-d') : null,
            'psikotes_lolos' => $ps,
            'tanggal_psikotes' => $ps ? $this->faker->dateTimeBetween('-29 days','-20 days')->format('Y-m-d') : null,
            'tes_kompetensi_lolos' => $komp,
            'tanggal_tes_kompetensi' => $komp ? $this->faker->dateTimeBetween('-19 days','-10 days')->format('Y-m-d') : null,
        ];
    }
}
