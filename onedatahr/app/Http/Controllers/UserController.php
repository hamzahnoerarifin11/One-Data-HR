<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::with('roles')->orderBy('created_at', 'desc')->get();
        return view('pages.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('pages.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'nik'      => 'required|string|max:50|unique:users,nik',
            'jabatan'  => 'required|string|max:200',
            'roles'    => 'required|array',
            'roles.*'  => 'exists:roles,id',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'nik'      => $request->nik,
            'jabatan'  => $request->jabatan,
            'password' => Hash::make($request->password),
        ]);

        // Attach roles
        $user->roles()->sync($request->roles);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }
    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('pages.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        return view('pages.users.edit', compact('user', 'roles'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'nik'     => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'nik')->ignore($user->id),
            ],
            'jabatan' => 'required|string|max:200',

            // 🔥 GANTI role → roles[]
            'roles'   => 'required|array',
            'roles.*' => 'exists:roles,id',

            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update data user (TANPA role)
        $user->update([
            'name'    => $request->name,
            'email'   => $request->email,
            'nik'     => $request->nik,
            'jabatan' => $request->jabatan,
        ]);

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // 🔥 INI KUNCI MULTI ROLE
        $user->roles()->sync($request->roles);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Batch delete users
     */
    public function batchDelete(Request $request)
    {
        $ids = array_filter(explode(',', $request->ids));

        if (count($ids) > 0) {
            User::whereIn('id', $ids)->delete();

            return redirect()
                ->route('users.index')
                ->with('success', 'User terpilih berhasil dihapus.');
        }

        return redirect()
            ->route('users.index')
            ->with('error', 'Tidak ada user yang dipilih.');
    }
}
