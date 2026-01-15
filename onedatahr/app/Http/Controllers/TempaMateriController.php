<?php

namespace App\Http\Controllers;

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
        $this->authorize('createTempaMateri'); // Admin/Superadmin only

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
        if (!Gate::allows('create-tempa-materi')) {
            abort(403);
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'file_materi' => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
        ]);

        $path = $request->file('file_materi')->store('tempa/materi', 'public');

        TempaMateri::create([
            'judul' => $request->judul,
            'file_path' => $path,
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->route('tempa.materi.index')->with('success', 'Materi berhasil diupload');
    }

    public function download($id)
    {
        $materi = TempaMateri::findOrFail($id);
        return Storage::disk('public')->download($materi->file_path);
    }
}
