<?php

namespace App\Http\Controllers;

use App\Models\Tempa;
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

        $tempas = \App\Models\Tempa::all();
        return view('pages.tempa.materi.create', compact('tempas'));
    }

    public function store(Request $request)
    {
        $this->authorize('createTempaMateri');

        $request->validate([
            'id_tempa' => 'required|exists:tempa,id_tempa',
            'judul' => 'required|string|max:255',
            'file_materi' => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
        ]);

        $path = $request->file('file_materi')->store('tempa/materi', 'public');

        TempaMateri::create([
            'id_tempa' => $request->id_tempa,
            'judul_materi' => $request->judul,
            'file_materi' => $path,
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->route('tempa.materi.index')->with('success', 'Materi berhasil diupload');
    }

    public function download($id)
    {
        $this->authorize('viewTempaMateri'); // Admin/Superadmin/Ketua TEMPA can download

        $materi = TempaMateri::findOrFail($id);
        return Storage::disk('public')->download($materi->file_materi);
    }
}
