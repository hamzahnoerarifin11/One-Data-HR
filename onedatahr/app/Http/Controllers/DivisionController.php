<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Division::with('company')->get()->map(function ($d) {
            return [
                'id' => $d->id,
                'name' => $d->name,
                'company_name' => $d->company->name ?? '-',
                'created_at' => $d->created_at->format('d/m/Y'),
            ];
        });

        return view('pages.organization.division.index', compact('divisions'));
    }


    public function create()
    {
        $companies = Company::all();
        return view('pages.organization.division.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255'
        ]);
        Division::create($request->only(['company_id', 'name']));
        return redirect()->route('organization.division.index')->with('success', 'Division created successfully.');
    }

    public function show(Division $division)
    {
        return view('pages.organization.division.show', compact('division'));
    }

    public function edit(Division $division)
    {
        $companies = Company::all();
        return view('pages.organization.division.edit', compact('division', 'companies'));
    }

    public function update(Request $request, Division $division)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255'
        ]);
        $division->update($request->only(['company_id', 'name']));
        return redirect()->route('organization.division.index')->with('success', 'Division updated successfully.');
    }

    public function destroy(Division $division)
    {
        $division->delete();
        return redirect()->route('organization.division.index')->with('success', 'Division deleted successfully.');
    }
}
