<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Division;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisions = Division::with('company')->get();

        if ($divisions->isEmpty()) {
            return; // Skip if no divisions exist
        }

        $departments = [
            ['name' => 'Departemen Pengembangan Software', 'division_id' => $divisions->first()->id, 'company_id' => $divisions->first()->company_id],
            ['name' => 'Departemen Sistem Informasi', 'division_id' => $divisions->first()->id, 'company_id' => $divisions->first()->company_id],
            ['name' => 'Departemen Keuangan', 'division_id' => $divisions->skip(1)->first()?->id ?? $divisions->first()->id, 'company_id' => $divisions->skip(1)->first()?->company_id ?? $divisions->first()->company_id],
            ['name' => 'Departemen Akuntansi', 'division_id' => $divisions->skip(1)->first()?->id ?? $divisions->first()->id, 'company_id' => $divisions->skip(1)->first()?->company_id ?? $divisions->first()->company_id],
            ['name' => 'Departemen SDM', 'division_id' => $divisions->skip(2)->first()?->id ?? $divisions->first()->id, 'company_id' => $divisions->skip(2)->first()?->company_id ?? $divisions->first()->company_id],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate($department);
        }
    }
}
