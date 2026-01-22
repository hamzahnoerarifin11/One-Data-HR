<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::all();
        return view('pages.company.index', compact('companies'));
    }

    public function create()
    {
        return view('pages.company.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Company::create($request->only('name'));
        return redirect()->route('company.index')->with('success', 'Company created successfully.');
    }

    public function show(Company $company)
    {
        return view('pages.company.show', compact('company'));
    }

    public function edit(Company $company)
    {
        return view('pages.company.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $company->update($request->only('name'));
        return redirect()->route('company.index')->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('company.index')->with('success', 'Company deleted successfully.');
    }
}
