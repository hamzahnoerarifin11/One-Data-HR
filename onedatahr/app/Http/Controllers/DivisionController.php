<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Division::with(['company.holding', 'holding'])->get()->map(function ($d) {
            $label = '-';
            if ($d->based_on == 'holding' && $d->holding) {
                $label = $d->holding->name;
            } else {
                $company = $d->company;
                $label = $company ? ($company->name . ($company->holding ? ' (' . $company->holding->name . ')' : '')) : '-';
            }

            return [
                'id' => $d->id,
                'name' => $d->name,
                'company_name' => $label,
                'parent_name' => $d->parent?->name ?? '-',
                'created_at' => $d->created_at->format('d/m/Y'),
            ];
        });

        return view('pages.organization.division.index', compact('divisions'));
    }


    public function create(Request $request)
    {
        $companies = Company::with(['holding', 'parent'])->get();
        $holdings = \App\Models\Holding::all();
        $selectedCompanyId = $request->query('company_id');
        return view('pages.organization.division.create', compact('companies', 'holdings', 'selectedCompanyId'));
    }

    public function store(Request $request)
    {
        // ... (store method remains unchanged for now, just updating create/edit data)
        \Log::debug('Division store request input', $request->all());
        $request->validate([
            'based_on' => 'required|in:company,holding',
            'company_id' => 'required_if:based_on,company|nullable|exists:companies,id',
            'holding_id' => 'required_if:based_on,holding|nullable|exists:holdings,id',
            'parent_id' => 'nullable|exists:divisions,id',
            'autolink_by_name' => 'nullable|boolean',
            'name' => 'required|string|max:255'
        ]);

        $data = $request->only(['company_id', 'holding_id', 'based_on', 'name', 'parent_id']);

        // Autolink by name when company-based and company has a holding
        if (($data['based_on'] ?? null) === 'company') {
            $company = Company::find($data['company_id']);
            if ($company && $company->holding && $request->input('autolink_by_name')) {
                $parent = Division::where('based_on', 'holding')
                    ->where('holding_id', $company->holding->id)
                    ->where('name', $data['name'])
                    ->first();
                if ($parent) $data['parent_id'] = $parent->id;
            }
        }

        // Ensure irrelevant fields are cleared depending on based_on
        if (($data['based_on'] ?? null) === 'holding') {
            $data['company_id'] = null;
            $data['parent_id'] = null; // holding-based divisions don't have parent
        } else {
            // company-based division shouldn't have holding_id
            $data['holding_id'] = null;
        }

        // If parent_id provided, ensure valid parent
        if (!empty($data['parent_id'])) {
            $p = Division::find($data['parent_id']);
            if (!$p) {
                return back()->withInput()->with('error', 'Parent division not found.');
            }

            if (($data['based_on'] ?? null) === 'company') {
                $company = Company::find($data['company_id']);
                if ($company) {
                    if ($company->parent_id) {
                        // CURRENT IS SUBSIDIARY -> Parent should be from Parent Company
                        if ($p->company_id != $company->parent_id) {
                             return back()->withInput()->with('error', 'Induk Divisi untuk Divisi Cucu Perusahaan harus berasal dari Perusahaan Induknya.');
                        }
                    } else {
                        // CURRENT IS COMPANY -> Parent should be from Holding
                         if (($p->based_on ?? '') !== 'holding') {
                            return back()->withInput()->with('error', 'Induk Divisi untuk Divisi Perusahaan harus berasal dari Holding.');
                        }
                    }
                }
            }
        }

        try {
            \Log::debug('Creating division', $data);
            $division = Division::create($data);
        } catch (\Exception $e) {
            \Log::error('Division create failed: ' . $e->getMessage(), ['data' => $data]);
            return back()->withInput()->with('error', 'Gagal menyimpan divisi: ' . $e->getMessage());
        }

        return redirect()->route('organization.division.index')->with('success', 'Division created successfully.');
    }

    public function show(Division $division)
    {
        $division->load(['departments', 'units']);
        return view('pages.organization.division.show', compact('division'));
    }

    public function edit(Division $division)
    {
        $companies = Company::with(['holding', 'parent'])->get();
        $holdings = \App\Models\Holding::all();
        return view('pages.organization.division.edit', compact('division', 'companies', 'holdings'));
    }

    public function update(Request $request, Division $division)
    {
        $request->validate([
            'based_on' => 'required|in:company,holding',
            'company_id' => 'required_if:based_on,company|nullable|exists:companies,id',
            'holding_id' => 'required_if:based_on,holding|nullable|exists:holdings,id',
            'parent_id' => 'nullable|exists:divisions,id',
            'autolink_by_name' => 'nullable|boolean',
            'name' => 'required|string|max:255'
        ]);

        $data = $request->only(['company_id', 'holding_id', 'based_on', 'name', 'parent_id']);

        if (($data['based_on'] ?? null) === 'company') {
            $company = Company::find($data['company_id']);
            if ($company && $company->holding && $request->input('autolink_by_name')) {
                $parent = Division::where('based_on', 'holding')
                    ->where('holding_id', $company->holding->id)
                    ->where('name', $data['name'])
                    ->first();
                if ($parent) $data['parent_id'] = $parent->id;
            }
        }

        // If parent_id provided, ensure valid parent
        if (!empty($data['parent_id'])) {
            $p = Division::find($data['parent_id']);
            if (!$p) {
                return back()->withInput()->with('error', 'Parent division not found.');
            }

            // Ensure parent is not the division itself
            if ($p->id == $division->id) {
                return back()->withInput()->with('error', 'Parent tidak boleh sama dengan divisi itu sendiri.');
            }

            if (($data['based_on'] ?? null) === 'company') {
                $company = Company::find($data['company_id']);
                if ($company) {
                    if ($company->parent_id) {
                        // CURRENT IS SUBSIDIARY -> Parent should be from Parent Company
                        if ($p->company_id != $company->parent_id) {
                             return back()->withInput()->with('error', 'Induk Divisi untuk Divisi Cucu Perusahaan harus berasal dari Perusahaan Induknya.');
                        }
                    } else {
                        // CURRENT IS COMPANY -> Parent should be from Holding
                         if (($p->based_on ?? '') !== 'holding') {
                            return back()->withInput()->with('error', 'Induk Divisi untuk Divisi Perusahaan harus berasal dari Holding.');
                        }
                    }
                }
            }
        }

        // Ensure irrelevant fields are cleared depending on based_on
        if (($data['based_on'] ?? null) === 'holding') {
            $data['company_id'] = null;
            $data['parent_id'] = null; // holding-based divisions don't have parent
        } else {
            // company-based division shouldn't have holding_id
            $data['holding_id'] = null;
        }

        $division->update($data);
        return redirect()->route('organization.division.index')->with('success', 'Division updated successfully.');
    }

    // API: return holding-based divisions for a holding
    public function parentsByHolding($holdingId)
    {
        $parents = Division::where('based_on', 'holding')->where('holding_id', $holdingId)->get(['id', 'name']);
        return response()->json($parents);
    }

    // API: return divisions relevant for a company (company-based)
    public function listByCompany($companyId)
    {
        // Fix: Only return divisions belonging to the company directly.
        // Include parent info in name if exists.
        $divisions = Division::with('parent')
            ->where('company_id', $companyId)
            ->get()
            ->map(function ($d) {
                $name = $d->name;
                // If it has a parent (usually from holding), append parent name
                if ($d->parent) {
                    $name .= ' (' . $d->parent->name . ')';
                }
                return [
                    'id' => $d->id,
                    'name' => $name, // formatted name
                    'company_id' => $d->company_id,
                    'holding_id' => $d->holding_id,
                    'parent_id' => $d->parent_id,
                    'based_on' => $d->based_on
                ];
            });

        return response()->json($divisions);
    }

    // API: return divisions for a holding
    public function listByHolding($holdingId)
    {
        $divisions = Division::where('based_on','holding')
            ->where('holding_id', $holdingId)
            ->get()
            ->map(function ($d) {
                return [
                    'id' => $d->id,
                    'name' => $d->name,
                    'company_id' => null,
                    'holding_id' => $d->holding_id,
                    'based_on' => 'holding'
                ];
            });
        return response()->json($divisions);
    }

    public function destroy(Division $division)
    {
        $division->delete();
        return redirect()->route('organization.division.index')->with('success', 'Division deleted successfully.');
    }
}
