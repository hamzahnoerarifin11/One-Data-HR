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
        $departments = Department::with(['division.company'])->get()->map(function ($d) {
            return [
                'id' => $d->id,
                'name' => $d->name,
                'division_name' => $d->division->name ?? '-',
                'company_name' => $d->division?->company?->name ?? '-',
                'created_at' => $d->created_at->format('d/m/Y'),
            ];
        });

        return view('pages.department.index', compact('departments'));
    }


    public function create()
    {
        $companies = Company::all();
        $divisions = Division::all();
        return view('pages.department.create', compact('companies', 'divisions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'division_id' => 'required|exists:divisions,id',
            'name' => 'required|string|max:255'
        ]);
        Department::create($request->only(['company_id', 'division_id', 'name']));
        return redirect()->route('department.index')->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        return view('pages.department.show', compact('department'));
    }

    public function edit(Department $department)
    {
        $companies = Company::all();
        $divisions = Division::all();
        return view('pages.department.edit', compact('department', 'companies', 'divisions'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'division_id' => 'required|exists:divisions,id',
            'name' => 'required|string|max:255'
        ]);
        $department->update($request->only(['company_id', 'division_id', 'name']));
        return redirect()->route('department.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('department.index')->with('success', 'Department deleted successfully.');
    }
}
