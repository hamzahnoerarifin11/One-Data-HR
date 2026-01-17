<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssignRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Assign superadmin role to user with email admin@example.com
        $user = \App\Models\User::where('email', 'admin@example.com')->first();
        if ($user) {
            $role = \App\Models\Role::where('name', 'superadmin')->first();
            if ($role && !$user->roles()->where('name', 'superadmin')->exists()) {
                $user->roles()->attach($role->id);
            }
        }

        // Assign ketua_tempa role to a user (you can change this)
        $ketuaTempaUser = \App\Models\User::find(19); // Assuming user id 19 is ketua_tempa
        if ($ketuaTempaUser) {
            $role = \App\Models\Role::where('name', 'ketua_tempa')->first();
            if ($role && !$ketuaTempaUser->roles()->where('name', 'ketua_tempa')->exists()) {
                $ketuaTempaUser->roles()->attach($role->id);
            }
            // Also assign superadmin for testing
            $superRole = \App\Models\Role::where('name', 'superadmin')->first();
            if ($superRole && !$ketuaTempaUser->roles()->where('name', 'superadmin')->exists()) {
                $ketuaTempaUser->roles()->attach($superRole->id);
            }
            // Also assign admin
            $adminRole = \App\Models\Role::where('name', 'admin')->first();
            if ($adminRole && !$ketuaTempaUser->roles()->where('name', 'admin')->exists()) {
                $ketuaTempaUser->roles()->attach($adminRole->id);
            }
        }
    }
}
