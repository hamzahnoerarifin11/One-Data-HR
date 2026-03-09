<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\Department;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = Department::with('division.company')->get();

        if ($departments->isEmpty()) {
            return; // Skip if no departments exist
        }

        $units = [
            [
                'name' => 'Unit Frontend Development',
                'department_id' => $departments->first()->id,
                'division_id' => $departments->first()->division_id,
                'company_id' => $departments->first()->company_id
            ],
            [
                'name' => 'Unit Backend Development',
                'department_id' => $departments->first()->id,
                'division_id' => $departments->first()->division_id,
                'company_id' => $departments->first()->company_id
            ],
            [
                'name' => 'Unit Database Administration',
                'department_id' => $departments->skip(1)->first()?->id ?? $departments->first()->id,
                'division_id' => $departments->skip(1)->first()?->division_id ?? $departments->first()->division_id,
                'company_id' => $departments->skip(1)->first()?->company_id ?? $departments->first()->company_id
            ],
            [
                'name' => 'Unit Sistem Analisis',
                'department_id' => $departments->skip(1)->first()?->id ?? $departments->first()->id,
                'division_id' => $departments->skip(1)->first()?->division_id ?? $departments->first()->division_id,
                'company_id' => $departments->skip(1)->first()?->company_id ?? $departments->first()->company_id
            ],
            [
                'name' => 'Unit Payroll',
                'department_id' => $departments->skip(2)->first()?->id ?? $departments->first()->id,
                'division_id' => $departments->skip(2)->first()?->division_id ?? $departments->first()->division_id,
                'company_id' => $departments->skip(2)->first()?->company_id ?? $departments->first()->company_id
            ],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate($unit);
        }
    }
}
