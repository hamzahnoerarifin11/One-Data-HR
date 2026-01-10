<?php

namespace Database\Factories;

use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Factories\Factory;

class KaryawanFactory extends Factory
{
    protected $model = Karyawan::class;

    public function definition()
    {
        return [
            'Nama_Lengkap_Sesuai_Ijazah' => $this->faker->name(),
            'nik'                        => $this->faker->unique()->numerify('#########'),
            'email'                      => $this->faker->unique()->safeEmail(),
            // 'tanggal_masuk'              => $this->faker->date(),
            
            // MASUKKAN KEMBALI DI SINI (Karena milik tabel karyawan)
            'status'            => '1', 

            // HAPUS jabatan & divisi dari sini
        ];
    }
}