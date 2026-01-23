<?php

namespace App\Http\Controllers;

use App\Models\TempaKelompok;
use App\Models\TempaMateri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class TempaMateriController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('viewTempaMateri'); // Admin/Superadmin/Ketua TEMPA can view

        $materis = TempaMateri::with('uploader')->get();
        return view('pages.tempa.materi.index', compact('materis'));
    }

    public function create()
    {
        $this->authorize('createTempaMateri');

        return view('pages.tempa.materi.create');
    }

    public function store(Request $request)
    {
        $this->authorize('createTempaMateri');

        $request->validate([
            'judul' => 'required|string|max:255',
            'file_materi' => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
        ]);

        $path = $request->file('file_materi')->store('tempa/materi', 'public');

        TempaMateri::create([
            'judul_materi' => $request->judul,
            'file_materi' => $path,
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->route('tempa.materi.index')->with('success', 'Materi berhasil diupload');
    }

    public function edit($id)
    {
        $this->authorize('createTempaMateri'); // Same permission as create

        $materi = TempaMateri::findOrFail($id);
        return view('pages.tempa.materi.edit', compact('materi'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('createTempaMateri');

        $request->validate([
            'judul' => 'required|string|max:255',
            'file_materi' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
        ]);

        $materi = TempaMateri::findOrFail($id);

        $data = ['judul_materi' => $request->judul];

        if ($request->hasFile('file_materi')) {
            // Delete old file
            if ($materi->file_materi && Storage::disk('public')->exists($materi->file_materi)) {
                Storage::disk('public')->delete($materi->file_materi);
            }
            $path = $request->file('file_materi')->store('tempa/materi', 'public');
            $data['file_materi'] = $path;
        }

        $materi->update($data);

        return redirect()->route('tempa.materi.index')->with('success', 'Materi berhasil diperbarui');
    }

    public function destroy($id)
    {
        $this->authorize('createTempaMateri');

        $materi = TempaMateri::findOrFail($id);

        // Delete file
        if ($materi->file_materi && Storage::disk('public')->exists($materi->file_materi)) {
            Storage::disk('public')->delete($materi->file_materi);
        }

        $materi->delete();

        return redirect()->route('tempa.materi.index')->with('success', 'Materi berhasil dihapus');
    }

    public function download($id)
    {
        $this->authorize('viewTempaMateri'); // Same permission as view

        $materi = TempaMateri::findOrFail($id);

        if (!$materi->file_materi || !Storage::disk('public')->exists($materi->file_materi)) {
            return redirect()->route('tempa.materi.index')->with('error', 'File tidak ditemukan');
        }

        return Storage::disk('public')->download($materi->file_materi);
    }
}
