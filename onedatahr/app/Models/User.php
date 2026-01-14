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
    // Perbaiki method check role agar mengecek ke tabel relasi, bukan kolom string
    public function isAdmin()
    {
        return $this->roles->whereIn('name', ['admin', 'superadmin'])->count() > 0;
    }

    public function isStaff()
    {
        return $this->roles->where('name', 'staff')->count() > 0;
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function hasRole($roles)
    {
        // 1. Pastikan input selalu array
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        // 2. Hapus atau komentari bagian "fallback data lama" ini
        // karena property $this->role bisa mengembalikan string kosong/null
        // yang mengacaukan in_array
        /*
        if ($this->role && in_array($this->role, $roles)) {
            return true;
        }
        */

        // 3. Langsung cek ke relasi tabel role_user
        // Gunakan whereIn pada query builder untuk performa dan akurasi
        return $this->roles()->whereIn('name', $roles)->exists();
    }

}
