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

        $data = $query->orderBy('date')->get()->map(function($item){
            // support legacy 'count' field by exposing total_pelamar
            $item->total_pelamar = isset($item->total_pelamar) && $item->total_pelamar !== null ? (int)$item->total_pelamar : (isset($item->count) ? (int)$item->count : 0);
            $item->lolos_cv = isset($item->lolos_cv) ? (int)$item->lolos_cv : 0;
            $item->lolos_psikotes = isset($item->lolos_psikotes) ? (int)$item->lolos_psikotes : 0;
            $item->lolos_kompetensi = isset($item->lolos_kompetensi) ? (int)$item->lolos_kompetensi : 0;
            $item->lolos_hr = isset($item->lolos_hr) ? (int)$item->lolos_hr : 0;
            $item->lolos_user = isset($item->lolos_user) ? (int)$item->lolos_user : 0;
            return $item;
        });
        return response()->json($data);
    }

    public function store(Request $request)
    {
        // only admin may modify daily counts
        abort_unless(auth()->user() && auth()->user()->role === 'admin', 403);

        $validated = $request->validate([
            'posisi_id' => 'required|integer|exists:posisi,id_posisi',
            'date' => 'required|date',
            'total_pelamar' => 'nullable|integer|min:0',
            'lolos_cv' => 'nullable|integer|min:0',
            'lolos_psikotes' => 'nullable|integer|min:0',
            'lolos_kompetensi' => 'nullable|integer|min:0',
            'lolos_hr' => 'nullable|integer|min:0',
            'lolos_user' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        // backwards compat: accept legacy 'count' field and map to total_pelamar
        if ($request->filled('count') && ! $request->filled('total_pelamar')) {
            $validated['total_pelamar'] = (int) $request->input('count');
        }

        $validated['total_pelamar'] = isset($validated['total_pelamar']) ? (int)$validated['total_pelamar'] : 0;
        $validated['lolos_cv'] = isset($validated['lolos_cv']) ? (int)$validated['lolos_cv'] : 0;
        $validated['lolos_psikotes'] = isset($validated['lolos_psikotes']) ? (int)$validated['lolos_psikotes'] : 0;
        $validated['lolos_kompetensi'] = isset($validated['lolos_kompetensi']) ? (int)$validated['lolos_kompetensi'] : 0;
        $validated['lolos_hr'] = isset($validated['lolos_hr']) ? (int)$validated['lolos_hr'] : 0;
        $validated['lolos_user'] = isset($validated['lolos_user']) ? (int)$validated['lolos_user'] : 0;

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
            'total_pelamar' => 'nullable|integer|min:0',
            'lolos_cv' => 'nullable|integer|min:0',
            'lolos_psikotes' => 'nullable|integer|min:0',
            'lolos_kompetensi' => 'nullable|integer|min:0',
            'lolos_hr' => 'nullable|integer|min:0',
            'lolos_user' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        // map legacy count if provided
        if ($request->filled('count') && ! $request->filled('total_pelamar')) {
            $validated['total_pelamar'] = (int) $request->input('count');
        }

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
