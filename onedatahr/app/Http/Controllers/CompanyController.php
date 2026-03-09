<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Holding;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::whereNull('parent_id')->with(['holding', 'parent'])->get()->map(function ($c) {
            $label = $c->holding ? $c->holding->name : '-';
            // Parent logic unnecessary here if parent_id is null, but kept for safety/consistency if logic changes
            if ($c->parent) {
                 $label .= ' -> ' . $c->parent->name;
            }

            return [
                'id' => $c->id,
                'name' => $c->name,
                'holding_label' => $label,
                'created_at' => $c->created_at->format('d/m/Y'),
            ];
        });

        $title = 'Data Perusahaan';
        return view('pages.organization.company.index', compact('companies', 'title'));
    }


    public function create(Request $request)
    {
        $holdings = Holding::all();
        // Get potential parent companies (those that don't have a parent themselves, i.e., top-level companies under a holding)
        // Or should we allow unlimited nesting? User said "cucu perusahaan" specifically, implying max 3 levels (Holding -> Company -> Subsidiary).
        // If "Perusahaan" is level 1 (under Holding), then "Anak Perusahaan" is level 2.
        // So potential parents are companies that have NO parent.
        $companies = Company::whereNull('parent_id')->get(); 
        
        $selectedHoldingId = $request->query('holding_id');
        return view('pages.organization.company.create', compact('holdings', 'selectedHoldingId', 'companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'holding_id' => 'required|exists:holdings,id',
            'parent_id' => 'nullable|exists:companies,id'
        ]);
        Company::create($request->only('name', 'holding_id', 'parent_id'));
        return redirect()->route('organization.company.index')->with('success', 'Company created successfully.');
    }

    public function show(Company $company)
    {
        $company->load(['divisions', 'departments', 'units']);
        return view('pages.organization.company.show', compact('company'));
    }

    public function edit(Company $company)
    {
        $holdings = Holding::all();
        // Potential parents: No parent, and not self.
        $companies = Company::whereNull('parent_id')->where('id', '!=', $company->id)->get();
        return view('pages.organization.company.edit', compact('company', 'holdings', 'companies'));
    }

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'holding_id' => 'required|exists:holdings,id',
            'parent_id' => 'nullable|exists:companies,id'
        ]);
        $company->update($request->only('name', 'holding_id', 'parent_id'));
        return redirect()->route('organization.company.index')->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('organization.company.index')->with('success', 'Company deleted successfully.');
    }
}
