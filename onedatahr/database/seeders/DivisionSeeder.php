<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Company;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        if ($companies->isEmpty()) {
            return; // Skip if no companies exist
        }

        $divisionNames = [
            'Divisi Teknologi Informasi',
            'Divisi Keuangan',
            'Divisi Sumber Daya Manusia',
            'Divisi Pemasaran',
            'Divisi Operasional',
            'Divisi Produksi',
            'Divisi Logistik',
        ];

        foreach ($companies as $company) {
            // Create 2-3 divisions per company
            $numDivisions = rand(2, 3);
            $selectedNames = array_slice($divisionNames, 0, $numDivisions);

            foreach ($selectedNames as $name) {
                Division::firstOrCreate([
                    'company_id' => $company->id,
                    'name' => $name,
                ]);
            }
        }
    }
}
