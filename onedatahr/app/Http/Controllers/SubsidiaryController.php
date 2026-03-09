<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class SubsidiaryController extends Controller
{
    /**
     * Display a listing of subsidiaries (companies with a parent).
     */
    public function index()
    {
        $companies = Company::whereNotNull('parent_id')
            ->with(['holding', 'parent'])
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'parent_name' => $c->parent ? $c->parent->name : '-',
                    'holding_name' => $c->holding ? $c->holding->name : '-',
                    'created_at' => $c->created_at->format('d/m/Y'),
                ];
            });

        return view('pages.organization.subsidiary.index', compact('companies'));
    }

    /**
     * Show the form for creating a new subsidiary.
     */
    public function create()
    {
        // Parent candidates: Companies that have NO parent (Top-level companies)
        // A subsidiary cannot be a parent of another subsidiary (max 3 levels: Holding -> Company -> Subsidiary)
        $parentCompanies = Company::whereNull('parent_id')->with('holding')->get();
        return view('pages.organization.subsidiary.create', compact('parentCompanies'));
    }

    /**
     * Store a newly created subsidiary.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'required|exists:companies,id',
        ]);

        $parent = Company::find($request->parent_id);
        
        // Auto-assign holding from parent company
        $holdingId = $parent->holding_id;

        Company::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'holding_id' => $holdingId,
        ]);

        return redirect()->route('organization.subsidiary.index')->with('success', 'Anak Perusahaan berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified subsidiary.
     */
    public function edit(Company $subsidiary)
    {
        // Explicitly model binding route key 'subsidiary' to Company model in route definition or just use id
        $parentCompanies = Company::whereNull('parent_id')->with('holding')->get();
        return view('pages.organization.subsidiary.edit', compact('subsidiary', 'parentCompanies'));
    }

    /**
     * Update the specified subsidiary.
     */
    public function update(Request $request, Company $subsidiary)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'required|exists:companies,id',
        ]);

        $parent = Company::find($request->parent_id);
        $holdingId = $parent->holding_id;

        $subsidiary->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'holding_id' => $holdingId,
        ]);

        return redirect()->route('organization.subsidiary.index')->with('success', 'Anak Perusahaan berhasil diperbarui.');
    }

    /**
     * Display the specified subsidiary.
     */
    public function show(Company $subsidiary)
    {
        $subsidiary->load(['holding', 'parent', 'divisions', 'departments', 'units']);
        return view('pages.organization.subsidiary.show', compact('subsidiary'));
    }

    /**
     * Remove the specified subsidiary.
     */
    public function destroy(Company $subsidiary)
    {
        $subsidiary->delete();
        return redirect()->route('organization.subsidiary.index')->with('success', 'Anak Perusahaan berhasil dihapus.');
    }
}
