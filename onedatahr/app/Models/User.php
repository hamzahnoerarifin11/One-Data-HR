<?php

namespace App\Models;

use App\Models\Role;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'nik',
        'jabatan',
        'password',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function isStaff()
    {
        // Cek apakah kolom role isinya 'staff' (huruf kecil sesuai database)
        return $this->roles === 'staff';
    }

    public function isAdmin()
    {
        // Menganggap superadmin dan admin sebagai Admin
        return in_array($this->roles, ['admin', 'superadmin']);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    // public function hasRole(string|array $role): bool
    // {
    //     if (is_string($role)) {
    //         $role = [$role];
    //     }
    //     return $this->roles()->where('name', $role)->exists();
    // }
    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }
        return $this->roles()->whereIn('name', $roles)->exists();
    }

}
