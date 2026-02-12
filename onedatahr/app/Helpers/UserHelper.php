<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Role;
use App\Models\Level;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserHelper
{
    /**
     * Map level jabatan to role
     * Customize mapping sesuai kebutuhan bisnis
     */
    public static function mapLevelToRole(Level $level): array
    {
        $levelName = strtolower($level->name);

        /**
         * PENTING:
         * Urutan keyword dari yang PALING SPESIFIK ke PALING UMUM
         */
        $mapping = [
            // ===== LEVEL PALING SPESIFIK =====
            'senior manager' => ['senior_manager'],
            'section head'   => ['section_head'],

            // ===== LEVEL STRUKTURAL =====
            'director'       => ['direktur'],
            'direktur'       => ['direktur'],
            'manager'        => ['manager'],
            'supervisor'     => ['supervisor'],

            // ===== LEVEL STAFF =====
            'staff'          => ['staff'],

            // ===== DEFAULT =====
            'default'        => ['staff'],
        ];

        foreach ($mapping as $keyword => $roles) {
            if ($keyword !== 'default' && str_contains($levelName, $keyword)) {
                return $roles;
            }
        }

        return $mapping['default'];
    }


    /**
     * Generate random password
     */
    public static function generatePassword($length = 12): string
    {
        return Str::password($length, symbols: true);
    }

    /**
     * Create user account untuk karyawan
     */
    public static function createUserForKaryawan($karyawan, Level $level = null): array
    {
        try {
            // Generate unique email jika belum ada
            $email = $karyawan->Email;
            if (!$email) {
                // Generate dari NIK atau nama
                $baseEmail = strtolower(str_replace(' ', '.', $karyawan->Nama_Sesuai_KTP));
                $email = $baseEmail . '@company.local';

                // Check if email already exists
                $counter = 1;
                while (User::where('email', $email)->exists()) {
                    $email = $baseEmail . $counter . '@company.local';
                    $counter++;
                }
            }

            // Check if user with same NIK already exists
            if ($karyawan->NIK && User::where('nik', $karyawan->NIK)->exists()) {
                $existingUser = User::where('nik', $karyawan->NIK)->first();
                // Link existing user to karyawan
                $karyawan->user_id = $existingUser->id;
                $karyawan->save();

                return [
                    'success' => true,
                    'user' => $existingUser,
                    'email' => $existingUser->email,
                    'password' => '(sudah ada)',
                    'roles' => $existingUser->roles->pluck('name')->toArray(),
                    'message' => 'User sudah ada, otomatis terhubung',
                    'existing' => true,
                ];
            }

            // Determine org scope from pekerjaan data
            $pekerjaan = $karyawan->pekerjaan()->first();
            $orgScope = 'all';
            $holdingId = null;
            $companyId = null;
            $divisionId = null;
            $departmentId = null;
            $unitId = null;

            if ($pekerjaan) {
                if ($pekerjaan->unit_id) {
                    $orgScope = 'unit';
                    $unitId = $pekerjaan->unit_id;
                    $departmentId = $pekerjaan->department_id;
                    $divisionId = $pekerjaan->division_id;
                    $companyId = $pekerjaan->company_id;
                    $holdingId = $pekerjaan->holding_id;
                } elseif ($pekerjaan->department_id) {
                    $orgScope = 'department';
                    $departmentId = $pekerjaan->department_id;
                    $divisionId = $pekerjaan->division_id;
                    $companyId = $pekerjaan->company_id;
                    $holdingId = $pekerjaan->holding_id;
                } elseif ($pekerjaan->division_id) {
                    $orgScope = 'division';
                    $divisionId = $pekerjaan->division_id;
                    $companyId = $pekerjaan->company_id;
                    $holdingId = $pekerjaan->holding_id;
                } elseif ($pekerjaan->company_id) {
                    $orgScope = 'company';
                    $companyId = $pekerjaan->company_id;
                    $holdingId = $pekerjaan->holding_id;
                } elseif ($pekerjaan->holding_id) {
                    $orgScope = 'holding';
                    $holdingId = $pekerjaan->holding_id;
                }
            }

            // Generate password
            $plainPassword = self::generatePassword();

            // Create user
            $user = User::create([
                'name' => $karyawan->Nama_Sesuai_KTP,
                'email' => $email,
                'nik' => $karyawan->NIK,
                'jabatan' => optional($pekerjaan)->Jabatan ?? 'Staff',
                'password' => \Illuminate\Support\Facades\Hash::make($plainPassword),
                'org_scope' => $orgScope,
                'holding_id' => $holdingId,
                'company_id' => $companyId,
                'division_id' => $divisionId,
                'department_id' => $departmentId,
                'unit_id' => $unitId,
            ]);

            // Link user back to karyawan
            $karyawan->user_id = $user->id;
            $karyawan->save();

            // Assign role based on level
            $roles = [];
            $roleNames = ['staff']; // default
            if ($level) {
                $roleNames = self::mapLevelToRole($level);
                $roles = Role::whereIn('name', $roleNames)->pluck('id')->toArray();
            }

            // If no roles assigned, assign default staff role
            if (empty($roles)) {
                $defaultRole = Role::where('name', 'staff')->first();
                if ($defaultRole) {
                    $roles = [$defaultRole->id];
                }
            }

            if (!empty($roles)) {
                $user->roles()->attach($roles);
            }

            return [
                'success' => true,
                'user' => $user,
                'email' => $email,
                'password' => $plainPassword,
                'roles' => $roleNames,
                'message' => 'User berhasil dibuat'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error membuat user: ' . $e->getMessage(),
                'error' => $e
            ];
        }
    }

    /**
     * Send credentials to user via email
     */
    public static function sendCredentialsEmail($user, $plainPassword, $roles)
    {
        try {
            // Implement email sending here
            // Bisa menggunakan Laravel Mail facade

            return [
                'success' => true,
                'message' => 'Email berhasil dikirim'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error mengirim email: ' . $e->getMessage()
            ];
        }
    }
}
