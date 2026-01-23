<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Division;
use App\Models\Department;
use App\Models\Unit;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        // Ambil positions dengan relasi lengkap melalui unit
        $positions = Position::with(['unit', 'unit.department', 'unit.department.division', 'unit.department.division.company'])->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'unit_name' => $p->unit ? $p->unit->name : '-',
                'department_name' => $p->unit && $p->unit->department ? $p->unit->department->name : '-',
                'division_name' => $p->unit && $p->unit->department && $p->unit->department->division ? $p->unit->department->division->name : '-',
                'company_name' => $p->unit && $p->unit->department && $p->unit->department->division && $p->unit->department->division->company ? $p->unit->department->division->company->name : '-',
                'created_at' => $p->created_at ? $p->created_at->format('d/m/Y') : '-',
            ];
        });

        return view('pages.position.index', compact('positions'));
    }

    public function create()
    {
        $companies = Company::all();
        $divisions = Division::all();
        $departments = Department::all();
        $units = Unit::all();
        return view('pages.position.create', compact('companies', 'divisions', 'departments', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'division_id' => 'required|exists:divisions,id',
            'department_id' => 'required|exists:departments,id',
            'unit_id' => 'required|exists:units,id',
            'name' => 'required|string|max:255'
        ]);
        Position::create($request->only(['company_id', 'division_id', 'department_id', 'unit_id', 'name']));
        return redirect()->route('position.index')->with('success', 'Position created successfully.');
    }

    public function show(Position $position)
    {
        return view('pages.position.show', compact('position'));
    }

    public function edit(Position $position)
    {
        $companies = Company::all();
        $divisions = Division::all();
        $departments = Department::all();
        $units = Unit::all();
        return view('pages.position.edit', compact('position', 'companies', 'divisions', 'departments', 'units'));
    }

    public function update(Request $request, Position $position)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'division_id' => 'required|exists:divisions,id',
            'department_id' => 'required|exists:departments,id',
            'unit_id' => 'required|exists:units,id',
            'name' => 'required|string|max:255'
        ]);
        $position->update($request->only(['company_id', 'division_id', 'department_id', 'unit_id', 'name']));
        return redirect()->route('position.index')->with('success', 'Position updated successfully.');
    }

    public function destroy(Position $position)
    {
        $position->delete();
        return redirect()->route('position.index')->with('success', 'Position deleted successfully.');
    }
}
