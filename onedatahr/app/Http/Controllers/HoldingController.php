<?php

namespace App\Http\Controllers;

use App\Models\Holding;
use Illuminate\Http\Request;

class HoldingController extends Controller
{
    public function index()
    {
        $holdings = Holding::all()->map(function ($h) {
            return [
                'id' => $h->id,
                'name' => $h->name,
                'created_at' => $h->created_at->format('d/m/Y'),
            ];
        });
        return view('pages.organization.holding.index', compact('holdings'));
    }

    public function create()
    {
        return view('pages.organization.holding.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        Holding::create($request->only('name'));
        return redirect()->route('organization.holding.index')->with('success', 'Holding created successfully.');
    }

    public function show(Holding $holding)
    {
        // Load relationships for the strict hierarchy based on 'based_on' = 'holding'
        // Note: The Holding model should have these relationships defined with where('based_on', 'holding')
        $holding->load(['company', 'divisions', 'departments', 'units']);
        
        return view('pages.organization.holding.show', compact('holding'));
    }

    public function edit(Holding $holding)
    {
        return view('pages.organization.holding.edit', compact('holding'));
    }

    public function update(Request $request, Holding $holding)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $holding->update($request->only('name'));
        return redirect()->route('organization.holding.index')->with('success', 'Holding updated successfully.');
    }

    public function destroy(Holding $holding)
    {
        $holding->delete();
        return redirect()->route('organization.holding.index')->with('success', 'Holding deleted successfully.');
    }
}
