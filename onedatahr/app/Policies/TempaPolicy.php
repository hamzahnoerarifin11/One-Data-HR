<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TempaPolicy
{
    use HandlesAuthorization;

    /**
     * Superadmin selalu lolos
     */
    public function before(User $user)
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }
    }

    public function viewTempaPeserta(User $user)
    {
        return $user->hasRole(['ketua_tempa', 'admin']);
    }

    public function createTempaPeserta(User $user)
    {
        return $user->hasRole(['ketua_tempa', 'admin']);
    }

    public function editTempaPeserta(User $user)
    {
        return $user->hasRole(['ketua_tempa', 'admin']);
    }

    public function deleteTempaPeserta(User $user)
    {
        return $user->hasRole(['ketua_tempa', 'admin']);
    }

    public function viewTempaAbsensi(User $user)
    {
        return $user->hasRole(['ketua_tempa', 'admin']);
    }

    public function createTempaAbsensi(User $user)
    {
        return $user->hasRole('ketua_tempa'); // Hanya ketua tempa yang bisa create
    }

    public function updateTempaAbsensi(User $user)
    {
        return $user->hasRole('ketua_tempa'); // Hanya ketua tempa yang bisa update
    }

    public function deleteTempaAbsensi(User $user)
    {
        return $user->hasRole('ketua_tempa'); // Hanya ketua tempa yang bisa delete
    }

    public function viewTempaMonitoring(User $user)
    {
        return $user->hasRole(['admin']);
    }

    public function createTempaMateri(User $user)
    {
        return $user->hasRole(['admin']);
    }
}
