<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\TempaKelompok;
use App\Models\User;

class TempaKelompokPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'superadmin', 'ketua_tempa']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TempaKelompok $tempaKelompok): bool
    {
        return $user->hasRole(['admin', 'superadmin', 'ketua_tempa']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'superadmin', 'ketua_tempa']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TempaKelompok $tempaKelompok): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        // Ketua tempa hanya bisa update kelompoknya sendiri
        if ($user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin'])) {
            return $tempaKelompok->ketua_tempa_id == $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TempaKelompok $tempaKelompok): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        // Ketua tempa hanya bisa delete kelompoknya sendiri
        if ($user->hasRole('ketua_tempa') && !$user->hasRole(['admin', 'superadmin'])) {
            return $tempaKelompok->ketua_tempa_id == $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TempaKelompok $tempaKelompok): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TempaKelompok $tempaKelompok): bool
    {
        return false;
    }
}
