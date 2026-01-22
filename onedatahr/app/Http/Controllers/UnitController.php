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
        $units = Unit::with(['company', 'division', 'department'])->get();
        return view('pages.unit.index', compact('units'));
    }

    public function create()
    {
        $companies = Company::all();
        $divisions = Division::all();
        $departments = Department::all();
        return view('pages.unit.create', compact('companies', 'divisions', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'division_id' => 'required|exists:divisions,id',
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255'
        ]);
        Unit::create($request->only(['company_id', 'division_id', 'department_id', 'name']));
        return redirect()->route('unit.index')->with('success', 'Unit created successfully.');
    }

    public function show(Unit $unit)
    {
        return view('pages.unit.show', compact('unit'));
    }

    public function edit(Unit $unit)
    {
        $companies = Company::all();
        $divisions = Division::all();
        $departments = Department::all();
        return view('pages.unit.edit', compact('unit', 'companies', 'divisions', 'departments'));
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'division_id' => 'required|exists:divisions,id',
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255'
        ]);
        $unit->update($request->only(['company_id', 'division_id', 'department_id', 'name']));
        return redirect()->route('unit.index')->with('success', 'Unit updated successfully.');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('unit.index')->with('success', 'Unit deleted successfully.');
    }
}
