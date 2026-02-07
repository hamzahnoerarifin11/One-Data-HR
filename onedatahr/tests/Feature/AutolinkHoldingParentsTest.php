<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Holding;
use App\Models\Company;
use App\Models\Division;
use App\Models\Department;
use App\Models\Unit;

class AutolinkHoldingParentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dry_run_does_not_modify_records()
    {
        $holding = Holding::create(['name' => 'Group A', 'type' => 'group']);
        $company = Company::create(['name' => 'Company A', 'holding_id' => $holding->id]);

        $parentDiv = Division::create(['name' => 'HR', 'based_on' => 'holding', 'holding_id' => $holding->id]);
        $childDiv = Division::create(['name' => 'HR', 'based_on' => 'company', 'company_id' => $company->id]);

        $this->artisan('org:autolink-parents', ['--type' => 'division'])->assertExitCode(0);

        $this->assertNull($childDiv->fresh()->parent_id);
    }

    public function test_commit_links_parents()
    {
        $holding = Holding::create(['name' => 'Group B', 'type' => 'group']);
        $company = Company::create(['name' => 'Company B', 'holding_id' => $holding->id]);

        $parentDept = Department::create(['name' => 'Finance', 'based_on' => 'holding', 'holding_id' => $holding->id]);
        $childDept = Department::create(['name' => 'Finance', 'based_on' => 'company', 'company_id' => $company->id, 'division_id' => null]);

        $parentUnit = Unit::create(['name' => 'Ops', 'based_on' => 'holding', 'holding_id' => $holding->id]);
        $childUnit = Unit::create(['name' => 'Ops', 'based_on' => 'company', 'company_id' => $company->id, 'division_id' => null, 'department_id' => null]);

        // run commit for departments only
        $this->artisan('org:autolink-parents', ['--type' => 'department', '--commit' => true])->assertExitCode(0);
        $this->assertEquals($parentDept->id, $childDept->fresh()->parent_id);

        // run commit for units only
        $this->artisan('org:autolink-parents', ['--type' => 'unit', '--commit' => true])->assertExitCode(0);
        $this->assertEquals($parentUnit->id, $childUnit->fresh()->parent_id);
    }
}
