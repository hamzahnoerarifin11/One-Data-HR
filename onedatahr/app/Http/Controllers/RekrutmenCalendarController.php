<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekrutmenCalendarEntry;
use App\Models\Kandidat;

class RekrutmenCalendarController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'posisi_id' => 'required|integer|exists:posisi,id_posisi',
            'date' => 'required|date',
        ]);

        $entries = RekrutmenCalendarEntry::with('kandidat')
            ->where('posisi_id', $request->posisi_id)
            ->where('date', $request->date)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($entries);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user() && auth()->user()->hasRole('admin'), 403);

        $data = $request->validate([
            'posisi_id' => 'required|integer|exists:posisi,id_posisi',
            'date' => 'required|date',
            'kandidat_id' => 'nullable|integer|exists:kandidat,id_kandidat',
            'candidate_name' => 'nullable|string|max:150',
        ]);

        // If candidate_name provided and no kandidat_id, create a Kandidat record
        if (empty($data['kandidat_id']) && !empty($data['candidate_name'])) {
            $k = Kandidat::create([
                'nama' => $data['candidate_name'],
                'posisi_id' => $data['posisi_id'],
                'tanggal_melamar' => $data['date'],
            ]);
            $data['kandidat_id'] = $k->id_kandidat;
        }

        $entry = RekrutmenCalendarEntry::create([
            'posisi_id' => $data['posisi_id'],
            'kandidat_id' => $data['kandidat_id'] ?? null,
            'candidate_name' => $data['candidate_name'] ?? null,
            'date' => $data['date'],
            'created_by' => auth()->id(),
        ]);

        return response()->json($entry->load('kandidat'), 201);
    }

    public function destroy($id)
    {
        abort_unless(auth()->user() && auth()->user()->hasRole('admin'), 403);

        $entry = RekrutmenCalendarEntry::findOrFail($id);
        $entry->delete();

        return response()->json(['success' => true]);
    }
}
