<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Karyawan;
use App\Models\Pekerjaan;
use App\Models\Unit;
use Carbon\Carbon;
use Faker\Factory as Faker;

class KaryawanHierarchicalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jumlahData = 25;
        $faker = Faker::create('id_ID');

        $this->command->info("Sedang membuat {$jumlahData} karyawan dummy dengan struktur hierarki...");

        // Ambil hanya units yang memiliki positions
        $units = Unit::whereHas('positions')->with('positions', 'department.division.company')->get();

        if ($units->isEmpty()) {
            $this->command->error("Tidak ada data unit dengan positions!");
            return;
        }

        $createdCount = 0;

        while ($createdCount < $jumlahData && $units->isNotEmpty()) {
            // Pilih unit secara random
            $unit = $units->random();
            $department = $unit->department;
            $division = $department->division;
            $company = $division->company;

            // Pilih position secara random
            $position = $unit->positions()->inRandomOrder()->first();

            if (!$position) {
                continue;
            }

            // Buat data karyawan
            $karyawan = Karyawan::create([
                'NIK' => $faker->unique()->numerify('##########'),
                'Nama_Sesuai_KTP' => $faker->name,
                'Nama_Lengkap_Sesuai_Ijazah' => $faker->name,
                'Tempat_Lahir_Karyawan' => $faker->city,
                'Tanggal_Lahir_Karyawan' => $faker->dateTimeBetween('-50 years', '-20 years'),
                'Jenis_Kelamin_Karyawan' => $faker->randomElement(['L', 'P']),
                'Status_Pernikahan' => $faker->randomElement(['Belum Menikah', 'Menikah', 'Cerai Hidup', 'Cerai Mati (Duda/Janda)']),
                'Golongan_Darah' => $faker->randomElement(['Tidak Tahu', 'A', 'B', 'O', 'AB']),
                'Nomor_Telepon_Aktif_Karyawan' => $faker->phoneNumber,
                'Email' => $faker->unique()->safeEmail,
                'Alamat_KTP' => $faker->address,
                'RT' => $faker->numberBetween(1, 20),
                'RW' => $faker->numberBetween(1, 20),
                'Kelurahan_Desa' => $faker->citySuffix,
                'Kecamatan' => $faker->city,
                'Kabupaten_Kota' => $faker->city,
                'Provinsi' => $faker->state,
                'Alamat_Domisili' => $faker->address,
                'RT_Sesuai_Domisili' => $faker->numberBetween(1, 20),
                'RW_Sesuai_Domisili' => $faker->numberBetween(1, 20),
                'Kelurahan_Desa_Domisili' => $faker->citySuffix,
                'Kecamatan_Sesuai_Domisili' => $faker->city,
                'Kabupaten_Kota_Sesuai_Domisili' => $faker->city,
                'Provinsi_Sesuai_Domisili' => $faker->state,
                'Alamat_Lengkap' => $faker->address,
                'Status' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Buat data pekerjaan
            Pekerjaan::create([
                'id_karyawan' => $karyawan->id_karyawan,
                'company_id' => $company->id,
                'division_id' => $division->id,
                'department_id' => $department->id,
                'unit_id' => $unit->id,
                'position_id' => $position->id,
                'Bagian' => $faker->randomElement(['Administrasi', 'Operasional', 'Keuangan', 'HRD', 'IT']),
                'Jenis_Kontrak' => $faker->randomElement(['PKWT', 'PKWTT']),
                'Perjanjian' => $faker->randomElement(['Harian Lepas', 'Kontrak', 'Tetap']),
                'Lokasi_Kerja' => $faker->randomElement([
                    'Central Java - Pati',
                    'Central Java - Pekalongan',
                    'West Java - Bandung',
                    'East Java - Jember',
                    'DIY - Yogyakarta',
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $createdCount++;
            $this->command->line("✓ {$karyawan->Nama_Lengkap_Sesuai_Ijazah} ({$position->name})");
        }

        $this->command->info("✓ Berhasil membuat {$createdCount} karyawan dummy!");
    }
}
