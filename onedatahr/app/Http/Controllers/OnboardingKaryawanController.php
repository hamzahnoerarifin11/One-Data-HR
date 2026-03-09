<?php

namespace App\Http\Controllers;

use App\Models\OnboardingKaryawan;
use App\Models\Kandidat;
use App\Models\Posisi;
use Illuminate\Http\Request;

class OnboardingKaryawanController extends Controller
{
    public function index()
    {
        $onboardings = OnboardingKaryawan::with(['kandidat', 'posisi'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pages.onboarding.index', compact('onboardings'));
    }

    public function create()
    {
        $kandidat = Kandidat::orderBy('nama')->get();
        $posisi = Posisi::orderBy('nama_posisi')->get();

        return view('pages.onboarding.create', compact('kandidat', 'posisi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kandidat_id' => 'required',
            'posisi_id' => 'required',
        ]);

        OnboardingKaryawan::create($request->all());

        return redirect()
            ->route('onboarding.index')
            ->with('success', 'Data onboarding berhasil dibuat');
    }

    public function show($id)
    {
        $onboarding = OnboardingKaryawan::with(['kandidat', 'posisi'])->findOrFail($id);

        return view('pages.onboarding.show', compact('onboarding'));
    }

    public function edit($id)
    {
        $onboarding = OnboardingKaryawan::findOrFail($id);
        $kandidat = Kandidat::orderBy('nama')->get();
        $posisi = Posisi::orderBy('nama_posisi')->get();

        return view('pages.onboarding.edit', compact('onboarding', 'kandidat', 'posisi'));
    }

    public function update(Request $request, $id)
    {
        $onboarding = OnboardingKaryawan::findOrFail($id);
        $onboarding->update($request->all());

        return redirect()
            ->route('onboarding.index')
            ->with('success', 'Data onboarding berhasil diperbarui');
    }
}
