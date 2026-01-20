<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekrutmenCalendarEntry;
use App\Models\Kandidat;
use Carbon\Carbon;

class RekrutmenCalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Hanya admin/superadmin yang bisa memodifikasi (Store, Update, Destroy)
        // User biasa mungkin hanya bisa melihat (Index)
        $this->middleware('role:admin|superadmin')->except(['index']);
    }

    /**
     * Mengambil data kalender berdasarkan Range Tanggal (Start - End)
     * Ini penting agar FullCalendar bisa me-load data sebulan sekaligus.
     */
    public function index(Request $request)
    {
        $request->validate([
            'posisi_id' => 'nullable|integer', // Boleh null jika ingin liat semua jadwal
            'start'     => 'required|date',    // FullCalendar mengirim param 'start'
            'end'       => 'required|date',    // FullCalendar mengirim param 'end'
        ]);

        $query = RekrutmenCalendarEntry::with('kandidat')
            ->whereBetween('date', [$request->start, $request->end]);

        if ($request->filled('posisi_id')) {
            $query->where('posisi_id', $request->posisi_id);
        }

        $entries = $query->orderBy('date', 'asc')->get();

        // Mapping agar sesuai format FullCalendar (opsional, tapi disarankan)
        $events = $entries->map(function ($item) {
            $candidateName = $item->kandidat ? $item->kandidat->nama_lengkap : ($item->candidate_name ?? 'Tanpa Nama');
            return [
                'id' => $item->id,
                'title' => $item->title ?? $candidateName, // Judul Event
                'start' => $item->date, // Tanggal Event
                'extendedProps' => [
                    'kandidat_id' => $item->kandidat_id,
                    'posisi_id' => $item->posisi_id,
                    'keterangan' => $item->keterangan // Jika ada kolom note/deskripsi
                ],
                // Memberi warna beda jika kandidat sudah dihapus
                'backgroundColor' => $item->kandidat ? '#3b82f6' : '#9ca3af', 
            ];
        });

        return response()->json($events);
    }

    public function store(Request $request)
    {
        // Validasi: Wajib pilih kandidat yang sudah ada demi integritas data
        $request->validate([
            'posisi_id'   => 'required|integer|exists:posisi,id_posisi',
            'kandidat_id' => 'required|integer|exists:kandidat,id_kandidat',
            'date'        => 'required|date',
            'title'       => 'nullable|string|max:100', // Misal: "Interview HR"
            'keterangan'  => 'nullable|string|max:255',
        ]);

        // Cek apakah kandidat tersebut sudah punya jadwal di hari yang sama? (Optional)
        // $exists = RekrutmenCalendarEntry::where('kandidat_id', $request->kandidat_id)
        //     ->where('date', $request->date)->exists();
        // if($exists) return response()->json(['message' => 'Kandidat sudah ada jadwal di tgl ini'], 422);

        $kandidat = Kandidat::find($request->kandidat_id);

        $entry = RekrutmenCalendarEntry::create([
            'posisi_id'      => $request->posisi_id,
            'kandidat_id'    => $request->kandidat_id,
            'candidate_name' => $kandidat->nama_lengkap, // Backup nama (opsional)
            'title'          => $request->title ?? 'Jadwal Interview',
            'date'           => $request->date,
            'keterangan'     => $request->keterangan ?? null,
            'created_by'     => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil ditambahkan',
            'data'    => $entry
        ], 201);
    }

    /**
     * Fitur Update (Penting untuk Drag & Drop di Kalender)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $entry = RekrutmenCalendarEntry::findOrFail($id);
        
        $entry->update([
            'date' => $request->date,
            // Bisa update title/keterangan juga jika dikirim
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dipindah',
        ]);
    }

    public function destroy($id)
    {
        $entry = RekrutmenCalendarEntry::findOrFail($id);
        $entry->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal dihapus'
        ]);
    }
}