<?php

namespace App\Http\Controllers;

use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $levels = Level::orderBy('level_order')
            ->get()
            ->map(function ($level) {
                return [
                    'id' => $level->id,
                    'name' => $level->name,
                    'level_order' => $level->level_order,
                    'created_at' => $level->created_at ? $level->created_at->format('d/m/Y') : '-',
                    'pekerjaan_count' => $level->pekerjaan()->count(),
                ];
            });

        return view('pages.organization.level.index', compact('levels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.organization.level.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:levels,name',
            'level_order' => 'required|integer|min:1|unique:levels,level_order',
        ], [
            'name.required' => 'Nama level jabatan harus diisi',
            'name.unique' => 'Nama level jabatan sudah terdaftar',
            'level_order.required' => 'Urutan level harus diisi',
            'level_order.unique' => 'Urutan level sudah terdaftar',
            'level_order.min' => 'Urutan level minimal harus 1',
        ]);

        try {
            $level = Level::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Level jabatan berhasil dibuat',
                'data' => $level,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat level jabatan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Level $level)
    {
        return view('pages.organization.level.show', compact('level'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Level $level)
    {
        return view('pages.organization.level.edit', compact('level'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Level $level): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:levels,name,' . $level->id,
            'level_order' => 'required|integer|min:1|unique:levels,level_order,' . $level->id,
        ], [
            'name.required' => 'Nama level jabatan harus diisi',
            'name.unique' => 'Nama level jabatan sudah terdaftar',
            'level_order.required' => 'Urutan level harus diisi',
            'level_order.unique' => 'Urutan level sudah terdaftar',
            'level_order.min' => 'Urutan level minimal harus 1',
        ]);

        try {
            $level->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Level jabatan berhasil diperbarui',
                'data' => $level,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui level jabatan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Level $level): JsonResponse
    {
        // Check if level is being used
        if ($level->pekerjaan()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Level jabatan tidak dapat dihapus karena masih digunakan',
            ], 409);
        }

        try {
            $level->delete();

            return response()->json([
                'success' => true,
                'message' => 'Level jabatan berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus level jabatan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all levels for AJAX request (used in form selects)
     */
    public function getLevels(): JsonResponse
    {
        $levels = Level::orderBy('level_order')
            ->select(['id', 'name', 'level_order'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $levels,
        ], 200);
    }
}
