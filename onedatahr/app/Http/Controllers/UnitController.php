<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Division;
use App\Models\Department;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::with(['department.division.company.holding', 'holding'])->get()->map(function ($u) {
            $companyLabel = '-';
            if ($u->based_on == 'holding' && $u->holding) {
                $companyLabel = $u->holding->name;
            } else {
                $company = $u->department?->division?->company;
                $companyLabel = $company ? ($company->name . ($company->holding ? ' (' . $company->holding->name . ')' : '')) : '-';
            }

            return [
                'id' => $u->id,
                'name' => $u->name,
                'department_name' => $u->department->name ?? '-',
                'division_name' => $u->department?->division?->name ?? '-',
                'company_name' => $companyLabel,
                'parent_name' => $u->parent?->name ?? '-',
                'created_at' => $u->created_at->format('d/m/Y'),
            ];
        });

        return view('pages.organization.unit.index', compact('units'));
    }


    public function create()
    {
        $companies = Company::with(['holding', 'parent'])->get();
        $divisions = Division::all();
        $departments = Department::all();
        $holdings = \App\Models\Holding::all();
        return view('pages.organization.unit.create', compact('companies', 'divisions', 'departments', 'holdings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'based_on' => 'required|in:company,holding',
            'company_id' => 'required_if:based_on,company|nullable|exists:companies,id',
            'holding_id' => 'required_if:based_on,holding|nullable|exists:holdings,id',
            'parent_id' => 'nullable|exists:units,id',
            'autolink_by_name' => 'nullable|boolean',
            'division_id' => 'required|exists:divisions,id',
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255'
        ]);

        $data = $request->only(['company_id', 'holding_id', 'based_on', 'division_id', 'department_id', 'name', 'parent_id']);

        if (($data['based_on'] ?? null) === 'company') {
            $company = Company::find($data['company_id']);
            if ($company && $company->holding && $request->input('autolink_by_name')) {
                $parent = Unit::where('based_on', 'holding')
                    ->where('holding_id', $company->holding->id)
                    ->where('name', $data['name'])
                    ->first();
                if ($parent) $data['parent_id'] = $parent->id;
            }
        }

        // If parent_id provided, ensure the selected parent is a holding-based unit
        if (!empty($data['parent_id'])) {
            $p = Unit::find($data['parent_id']);
            if (!$p || ($p->based_on ?? '') !== 'holding') {
                return back()->withInput()->with('error', 'Parent harus berupa Unit berbasis Holding.');
            }
        }

        Unit::create($data);
        return redirect()->route('organization.unit.index')->with('success', 'Unit created successfully.');
    }

    public function show(Unit $unit)
    {
        $unit->load(['positions']);
        return view('pages.organization.unit.show', compact('unit'));
    }

    public function edit(Unit $unit)
    {
        $companies = Company::with(['holding', 'parent'])->get();
        $divisions = Division::all();
        $departments = Department::all();
        $holdings = \App\Models\Holding::all();
        return view('pages.organization.unit.edit', compact('unit', 'companies', 'divisions', 'departments', 'holdings'));
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'based_on' => 'required|in:company,holding',
            'company_id' => 'required_if:based_on,company|nullable|exists:companies,id',
            'holding_id' => 'required_if:based_on,holding|nullable|exists:holdings,id',
            'parent_id' => 'nullable|exists:units,id',
            'autolink_by_name' => 'nullable|boolean',
            'division_id' => 'required|exists:divisions,id',
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255'
        ]);

        $data = $request->only(['company_id', 'holding_id', 'based_on', 'division_id', 'department_id', 'name', 'parent_id']);

        if (($data['based_on'] ?? null) === 'company') {
            $company = Company::find($data['company_id']);
            if ($company && $company->holding && $request->input('autolink_by_name')) {
                $parent = Unit::where('based_on', 'holding')
                    ->where('holding_id', $company->holding->id)
                    ->where('name', $data['name'])
                    ->first();
                if ($parent) $data['parent_id'] = $parent->id;
            }
        }

        // If parent_id provided, ensure the selected parent is a holding-based unit
        if (!empty($data['parent_id'])) {
            $p = Unit::find($data['parent_id']);
            if (!$p || ($p->based_on ?? '') !== 'holding') {
                return back()->withInput()->with('error', 'Parent harus berupa Unit berbasis Holding.');
            }
            if ($p->id == $unit->id) {
                return back()->withInput()->with('error', 'Parent tidak boleh sama dengan unit itu sendiri.');
            }
        }

        $unit->update($data);
        return redirect()->route('organization.unit.index')->with('success', 'Unit updated successfully.');
    }

    // API: return holding-based units for a holding
    public function parentsByHolding($holdingId)
    {
        $parents = Unit::where('based_on', 'holding')->where('holding_id', $holdingId)->get(['id', 'name']);
        return response()->json($parents);
    }

    // API: return units for a specific department
    public function listByDepartment($departmentId)
    {
        $units = Unit::with('parent')
            ->where('department_id', $departmentId)
            ->get()
            ->map(function ($u) {
                $name = $u->name;
                if ($u->parent) {
                    $name .= ' (' . $u->parent->name . ')';
                }
                return [
                    'id' => $u->id,
                    'name' => $name,
                    'department_id' => $u->department_id,
                    'parent_id' => $u->parent_id
                ];
            });
        return response()->json($units);
    }



    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('organization.unit.index')->with('success', 'Unit deleted successfully.');
    }
}
