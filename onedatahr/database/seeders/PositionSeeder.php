<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = Unit::with('department.division.company')->get();

        if ($units->isEmpty()) {
            return; // Skip if no units exist
        }

        $positions = [
            [
                'name' => 'Frontend Developer Senior',
                'unit_id' => $units->first()->id,
                'department_id' => $units->first()->department_id,
                'division_id' => $units->first()->division_id,
                'company_id' => $units->first()->company_id
            ],
            [
                'name' => 'Frontend Developer Junior',
                'unit_id' => $units->first()->id,
                'department_id' => $units->first()->department_id,
                'division_id' => $units->first()->division_id,
                'company_id' => $units->first()->company_id
            ],
            [
                'name' => 'Backend Developer Senior',
                'unit_id' => $units->skip(1)->first()?->id ?? $units->first()->id,
                'department_id' => $units->skip(1)->first()?->department_id ?? $units->first()->department_id,
                'division_id' => $units->skip(1)->first()?->division_id ?? $units->first()->division_id,
                'company_id' => $units->skip(1)->first()?->company_id ?? $units->first()->company_id
            ],
            [
                'name' => 'Backend Developer Junior',
                'unit_id' => $units->skip(1)->first()?->id ?? $units->first()->id,
                'department_id' => $units->skip(1)->first()?->department_id ?? $units->first()->department_id,
                'division_id' => $units->skip(1)->first()?->division_id ?? $units->first()->division_id,
                'company_id' => $units->skip(1)->first()?->company_id ?? $units->first()->company_id
            ],
            [
                'name' => 'Database Administrator',
                'unit_id' => $units->skip(2)->first()?->id ?? $units->first()->id,
                'department_id' => $units->skip(2)->first()?->department_id ?? $units->first()->department_id,
                'division_id' => $units->skip(2)->first()?->division_id ?? $units->first()->division_id,
                'company_id' => $units->skip(2)->first()?->company_id ?? $units->first()->company_id
            ],
            [
                'name' => 'System Analyst',
                'unit_id' => $units->skip(3)->first()?->id ?? $units->first()->id,
                'department_id' => $units->skip(3)->first()?->department_id ?? $units->first()->department_id,
                'division_id' => $units->skip(3)->first()?->division_id ?? $units->first()->division_id,
                'company_id' => $units->skip(3)->first()?->company_id ?? $units->first()->company_id
            ],
            [
                'name' => 'Payroll Specialist',
                'unit_id' => $units->skip(4)->first()?->id ?? $units->first()->id,
                'department_id' => $units->skip(4)->first()?->department_id ?? $units->first()->department_id,
                'division_id' => $units->skip(4)->first()?->division_id ?? $units->first()->division_id,
                'company_id' => $units->skip(4)->first()?->company_id ?? $units->first()->company_id
            ],
        ];

        foreach ($positions as $position) {
            Position::firstOrCreate($position);
        }
    }
}
