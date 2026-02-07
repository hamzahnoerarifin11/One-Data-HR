<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Division;
use App\Models\Department;
use App\Models\Unit;
use App\Models\Company;
use Illuminate\Support\Str;

class AutolinkHoldingParents extends Command
{
    protected $signature = 'org:autolink-parents {--type=all : division|department|unit|all} {--commit : actually write changes (dry-run by default)}';
    protected $description = 'Autolink company-based Division/Department/Unit to holding-based parents when names match';

    public function handle()
    {
        $type = $this->option('type');
        $commit = $this->option('commit');

        $this->info('Starting autolink process (type=' . $type . ', commit=' . ($commit ? 'yes' : 'no') . ')');

        $summary = [];

        if ($type === 'all' || $type === 'division') $summary['divisions'] = $this->processDivisions($commit);
        if ($type === 'all' || $type === 'department') $summary['departments'] = $this->processDepartments($commit);
        if ($type === 'all' || $type === 'unit') $summary['units'] = $this->processUnits($commit);

        $this->line('--- Summary ---');
        foreach ($summary as $k => $s) {
            $this->line(ucfirst($k) . ": checked={$s['checked']}, linked={$s['linked']}");
        }

        $this->info('Autolink finished.');
        return 0;
    }

    protected function processDivisions($commit)
    {
        $checked = 0; $linked = 0;

        $items = Division::where('based_on', 'company')->whereNull('parent_id')->whereNotNull('company_id')->get();
        foreach ($items as $item) {
            $checked++;
            $company = Company::find($item->company_id);
            if (!$company || !$company->holding_id) continue;
            $parent = Division::where('based_on', 'holding')
                ->where('holding_id', $company->holding_id)
                ->whereRaw('LOWER(TRIM(name)) = ?', [Str::lower(trim($item->name))])
                ->first();
            if ($parent) {
                $this->line("[Division] Found parent for '{$item->name}' => parent_id={$parent->id} (holding={$company->holding_id})");
                if ($commit) {
                    $item->parent_id = $parent->id;
                    $item->save();
                }
                $linked++;
            }
        }

        return ['checked' => $checked, 'linked' => $linked];
    }

    protected function processDepartments($commit)
    {
        $checked = 0; $linked = 0;

        $items = Department::where('based_on', 'company')->whereNull('parent_id')->whereNotNull('company_id')->get();
        foreach ($items as $item) {
            $checked++;
            $company = Company::find($item->company_id);
            if (!$company || !$company->holding_id) continue;
            $parent = Department::where('based_on', 'holding')
                ->where('holding_id', $company->holding_id)
                ->whereRaw('LOWER(TRIM(name)) = ?', [Str::lower(trim($item->name))])
                ->first();
            if ($parent) {
                $this->line("[Department] Found parent for '{$item->name}' => parent_id={$parent->id} (holding={$company->holding_id})");
                if ($commit) {
                    $item->parent_id = $parent->id;
                    $item->save();
                }
                $linked++;
            }
        }

        return ['checked' => $checked, 'linked' => $linked];
    }

    protected function processUnits($commit)
    {
        $checked = 0; $linked = 0;

        $items = Unit::where('based_on', 'company')->whereNull('parent_id')->whereNotNull('company_id')->get();
        foreach ($items as $item) {
            $checked++;
            $company = Company::find($item->company_id);
            if (!$company || !$company->holding_id) continue;
            $parent = Unit::where('based_on', 'holding')
                ->where('holding_id', $company->holding_id)
                ->whereRaw('LOWER(TRIM(name)) = ?', [Str::lower(trim($item->name))])
                ->first();
            if ($parent) {
                $this->line("[Unit] Found parent for '{$item->name}' => parent_id={$parent->id} (holding={$company->holding_id})");
                if ($commit) {
                    $item->parent_id = $parent->id;
                    $item->save();
                }
                $linked++;
            }
        }

        return ['checked' => $checked, 'linked' => $linked];
    }
}
