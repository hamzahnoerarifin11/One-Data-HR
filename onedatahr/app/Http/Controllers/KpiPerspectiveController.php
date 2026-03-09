<?php

namespace App\Http\Controllers;

use App\Models\KpiItem;
use App\Models\KpiPerspective;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class KpiPerspectiveController extends Controller
{
    public function index()
    {
        $perspectives = KpiPerspective::orderBy('name')->get();

        $usageCounts = KpiItem::select('perspektif', DB::raw('count(*) as total'))
            ->whereNotNull('perspektif')
            ->groupBy('perspektif')
            ->pluck('total', 'perspektif');

        return view('pages.kpi.perspectives.index', compact('perspectives', 'usageCounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:kpi_perspectives,name'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        KpiPerspective::create([
            'name' => $data['name'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->back()->with('success', 'Perspektif KPI berhasil ditambahkan.');
    }

    public function update(Request $request, KpiPerspective $perspective)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('kpi_perspectives', 'name')->ignore($perspective->id)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $perspective->update([
            'name' => $data['name'],
            'is_active' => $request->boolean('is_active', $perspective->is_active),
        ]);

        return redirect()->back()->with('success', 'Perspektif KPI berhasil diperbarui.');
    }

    public function toggleStatus(KpiPerspective $perspective)
    {
        $perspective->update(['is_active' => !$perspective->is_active]);

        return redirect()->back()->with('success', 'Status perspektif berhasil diperbarui.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:kpi_perspectives,id'
        ]);

        $ids = $request->ids;
        $perspectives = KpiPerspective::whereIn('id', $ids)->get();
        $deleted = 0;
        $skipped = 0;

        foreach ($perspectives as $p) {
            // Check usage by NAME since KpiItem stores the string name
            $isUsed = KpiItem::where('perspektif', $p->name)->exists();
            
            if ($isUsed) {
                $skipped++;
                continue;
            }
            
            $p->delete();
            $deleted++;
        }

        $message = "Berhasil menghapus {$deleted} perspektif.";
        if ($skipped > 0) {
            $message .= " {$skipped} data dilewati karena sedang digunakan oleh KPI.";
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
}
