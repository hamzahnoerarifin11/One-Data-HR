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

        $divisions = [
            ['name' => 'Divisi Teknologi Informasi', 'company_id' => $companies->first()->id],
            ['name' => 'Divisi Keuangan', 'company_id' => $companies->first()->id],
            ['name' => 'Divisi Sumber Daya Manusia', 'company_id' => $companies->first()->id],
            ['name' => 'Divisi Pemasaran', 'company_id' => $companies->skip(1)->first()?->id ?? $companies->first()->id],
            ['name' => 'Divisi Operasional', 'company_id' => $companies->skip(1)->first()?->id ?? $companies->first()->id],
        ];

        foreach ($divisions as $division) {
            Division::firstOrCreate($division);
        }
    }
}
