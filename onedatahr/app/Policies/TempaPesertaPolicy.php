<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\TempaPeserta;
use App\Models\User;

class TempaPesertaPolicy
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
    public function view(User $user, TempaPeserta $tempaPeserta): bool
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
    public function update(User $user, TempaPeserta $tempaPeserta): bool
    {
        return $user->hasRole(['admin', 'superadmin', 'ketua_tempa']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TempaPeserta $tempaPeserta): bool
    {
        return $user->hasRole(['admin', 'superadmin', 'ketua_tempa']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TempaPeserta $tempaPeserta): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TempaPeserta $tempaPeserta): bool
    {
        return false;
    }
}
