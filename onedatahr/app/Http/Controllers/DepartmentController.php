<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Division;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with(['division.company', 'holding'])->get()->map(function ($d) {
            $divisionName = $d->division->name ?? '-';
            $companyLabel = '-';
            if ($d->based_on == 'holding' && $d->holding) {
                $companyLabel = $d->holding->name;
            } else {
                $company = $d->division?->company;
                $companyLabel = $company ? ($company->name . ($company->holding ? ' (' . $company->holding->name . ')' : '')) : '-';
            }

            return [
                'id' => $d->id,
                'name' => $d->name,
                'division_name' => $divisionName,
                'company_name' => $companyLabel,
                'parent_name' => $d->parent?->name ?? '-',
                'created_at' => $d->created_at->format('d/m/Y'),
            ];
        });

        return view('pages.organization.department.index', compact('departments'));
    }


    // public function create()
    // {
    //     $companies = Company::all();
    //     $divisions = Division::all();
    //     $holdings = \App\Models\Holding::all();
    //     return view('pages.organization.department.create', compact('companies', 'divisions', 'holdings'));
    // }
    public function create()
    {
        $companies = Company::with(['holding', 'parent'])->get();
        $holdings = \App\Models\Holding::all();

        // Map data divisi di sini agar Blade tetap bersih
        $mappedDivisions = Division::all()->map(fn($d) => [
            'id' => $d->id,
            'name' => $d->name,
            'company_id' => $d->company_id,
            'holding_id' => $d->holding_id,
            'based_on' => $d->based_on ?? 'company',
        ]);

        return view('pages.organization.department.create', compact('companies', 'holdings', 'mappedDivisions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'based_on' => 'required|in:company,holding',
            'company_id' => 'required_if:based_on,company|nullable|exists:companies,id',
            'holding_id' => 'required_if:based_on,holding|nullable|exists:holdings,id',
            'parent_id' => 'nullable|exists:departments,id',
            'autolink_by_name' => 'nullable|boolean',
            'division_id' => 'required|exists:divisions,id',
            'name' => 'required|string|max:255'
        ]);

        $data = $request->only(['company_id', 'holding_id', 'based_on', 'division_id', 'name', 'parent_id']);

        if (($data['based_on'] ?? null) === 'company') {
            $company = Company::find($data['company_id']);
            if ($company && $company->holding && $request->input('autolink_by_name')) {
                $parent = Department::where('based_on', 'holding')
                    ->where('holding_id', $company->holding->id)
                    ->where('name', $data['name'])
                    ->first();
                if ($parent) $data['parent_id'] = $parent->id;
            }
        }

        // If parent_id provided, ensure the selected parent is a holding-based department
        if (!empty($data['parent_id'])) {
            $p = Department::find($data['parent_id']);
            if (!$p || ($p->based_on ?? '') !== 'holding') {
                return back()->withInput()->with('error', 'Parent harus berupa Departemen berbasis Holding.');
            }
        }

        Department::create($data);
        return redirect()->route('organization.department.index')->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        $department->load(['units']);
        return view('pages.organization.department.show', compact('department'));
    }

    // public function edit(Department $department)
    // {
    //     $companies = Company::all();
    //     $divisions = Division::all();
    //     $holdings = \App\Models\Holding::all();
    //     return view('pages.organization.department.edit', compact('department', 'companies', 'divisions', 'holdings'));
    // }
    public function edit(Department $department)
    {
        $companies = Company::with(['holding', 'parent'])->get();
        $holdings = \App\Models\Holding::all();

        $mappedDivisions = Division::all()->map(fn($d) => [
            'id' => $d->id,
            'name' => $d->name,
            'company_id' => $d->company_id,
            'holding_id' => $d->holding_id,
            'based_on' => $d->based_on ?? 'company',
        ]);

        return view('pages.organization.department.edit', compact('department', 'companies', 'holdings', 'mappedDivisions'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'based_on' => 'required|in:company,holding',
            'company_id' => 'required_if:based_on,company|nullable|exists:companies,id',
            'holding_id' => 'required_if:based_on,holding|nullable|exists:holdings,id',
            'parent_id' => 'nullable|exists:departments,id',
            'autolink_by_name' => 'nullable|boolean',
            'division_id' => 'required|exists:divisions,id',
            'name' => 'required|string|max:255'
        ]);

        $data = $request->only(['company_id', 'holding_id', 'based_on', 'division_id', 'name', 'parent_id']);

        if (($data['based_on'] ?? null) === 'company') {
            $company = Company::find($data['company_id']);
            if ($company && $company->holding && $request->input('autolink_by_name')) {
                $parent = Department::where('based_on', 'holding')
                    ->where('holding_id', $company->holding->id)
                    ->where('name', $data['name'])
                    ->first();
                if ($parent) $data['parent_id'] = $parent->id;
            }
        }

        // If parent_id provided, ensure the selected parent is a holding-based department
        if (!empty($data['parent_id'])) {
            $p = Department::find($data['parent_id']);
            if (!$p || ($p->based_on ?? '') !== 'holding') {
                return back()->withInput()->with('error', 'Parent harus berupa Departemen berbasis Holding.');
            }
            if ($p->id == $department->id) {
                return back()->withInput()->with('error', 'Parent tidak boleh sama dengan departemen itu sendiri.');
            }
        }

        $department->update($data);
        return redirect()->route('organization.department.index')->with('success', 'Department updated successfully.');
    }

    // API: return holding-based departments for a holding
    public function parentsByHolding($holdingId)
    {
        $parents = Department::where('based_on', 'holding')->where('holding_id', $holdingId)->get(['id', 'name']);
        return response()->json($parents);
    }

    // API: return departments for a specific division
    public function listByDivision($divisionId)
    {
        $departments = Department::with('parent')
            ->where('division_id', $divisionId)
            ->get()
            ->map(function ($d) {
                // If department has a parent (from holding), append parent name
                $name = $d->name;
                if ($d->parent) {
                    $name .= ' (' . $d->parent->name . ')';
                }
                return [
                    'id' => $d->id,
                    'name' => $name,
                    'division_id' => $d->division_id,
                    'company_id' => $d->company_id,
                    'holding_id' => $d->holding_id,
                    'parent_id' => $d->parent_id
                ];
            });
        return response()->json($departments);
    }

    // API: return departments for a holding (holding-based)
    public function listByHolding($holdingId)
    {
        $departments = Department::where('based_on', 'holding')
            ->where('holding_id', $holdingId)
            ->get()
            ->map(function ($d) {
                return [
                    'id' => $d->id,
                    'name' => $d->name
                ];
            });
        return response()->json($departments);
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('organization.department.index')->with('success', 'Department deleted successfully.');
    }
}
