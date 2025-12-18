<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekrutmenDaily;
use App\Models\Posisi;

class RekrutmenDailyController extends Controller
{
    public function index(Request $request)
    {
        // If the request does not expect JSON (browser navigation), redirect to the calendar page
        if (! $request->wantsJson()) {
            return redirect()->route('rekrutmen.calendar');
        }

        $request->validate([
            'posisi_id' => 'nullable|integer|exists:posisi,id_posisi',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2000',
        ]);

        $query = RekrutmenDaily::query();
        if ($request->filled('posisi_id')) {
            $query->where('posisi_id', $request->posisi_id);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('date', [$request->from, $request->to]);
        } elseif ($request->filled('month') && $request->filled('year')) {
            $query->whereYear('date', $request->year)->whereMonth('date', $request->month);
        }

        $data = $query->orderBy('date')->get();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        // only admin may modify daily counts
        abort_unless(auth()->user() && auth()->user()->role === 'admin', 403);

        $validated = $request->validate([
            'posisi_id' => 'required|integer|exists:posisi,id_posisi',
            'date' => 'required|date',
            'count' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();

        // ensure unique per posisi + date
        $entry = RekrutmenDaily::updateOrCreate(
            ['posisi_id' => $validated['posisi_id'], 'date' => $validated['date']],
            $validated
        );

        return response()->json(['success' => true, 'entry' => $entry]);
    }

    public function update(Request $request, $id)
    {
        // only admin may modify daily counts
        abort_unless(auth()->user() && auth()->user()->role === 'admin', 403);

        $e = RekrutmenDaily::findOrFail($id);
        $validated = $request->validate([
            'count' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);
        $e->update($validated);
        return response()->json(['success' => true, 'entry' => $e]);
    }

    public function destroy($id)
    {
        // only admin may delete
        abort_unless(auth()->user() && auth()->user()->role === 'admin', 403);

        $e = RekrutmenDaily::findOrFail($id);
        $e->delete();
        return response()->json(['success' => true]);
    }
}
