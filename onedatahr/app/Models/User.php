<?php

namespace App\Models;

use App\Models\Role;
use App\Traits\OrganizationScope;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, OrganizationScope;

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
        'photo',
        'org_scope',
        'holding_id',
        'company_id',
        'division_id',
        'department_id',
        'unit_id',
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

    // =========================================================
    // ORGANIZATION RELATIONSHIPS
    // =========================================================

    public function holding()
    {
        return $this->belongsTo(Holding::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function scopeAuditLogs()
    {
        return $this->hasMany(UserScopeAuditLog::class);
    }

    // =========================================================
    // ORGANIZATION SCOPE HELPERS
    // =========================================================

    /**
     * Get the organization scope label for display
     */
    public function getOrganizationScopeLabel(): string
    {
        $labels = [
            'all' => 'Semua Data',
            'holding' => 'Holding',
            'company' => 'Perusahaan',
            'division' => 'Divisi',
            'department' => 'Departemen',
            'unit' => 'Unit',
        ];

        return $labels[$this->org_scope ?? 'all'] ?? 'Unknown';
    }

    /**
     * Get the organization entity name based on scope
     */
    public function getOrganizationEntityName(): string
    {
        if ($this->org_scope === 'all') return 'Semua';
        
        switch ($this->org_scope) {
            case 'holding':
                return $this->holding?->name ?? '-';
            case 'company':
                return $this->company?->name ?? '-';
            case 'division':
                return $this->division?->name ?? '-';
            case 'department':
                return $this->department?->name ?? '-';
            case 'unit':
                return $this->unit?->name ?? '-';
            default:
                return '-';
        }
    }

    // =========================================================
    // ROLE HELPERS
    // =========================================================

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

    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    public function karyawan()
    {
        return $this->hasOne(Karyawan::class, 'user_id', 'id');
    }
}

