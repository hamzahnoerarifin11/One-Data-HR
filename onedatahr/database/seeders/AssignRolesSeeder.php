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
            if ($role) {
                $user->roles()->attach($role->id);
            }
        }

        // Assign ketua_tempa role to a user (you can change this)
        $ketuaTempaUser = \App\Models\User::where('email', 'manager@gmail.com')->first();
        if ($ketuaTempaUser) {
            $role = \App\Models\Role::where('name', 'ketua_tempa')->first();
            if ($role) {
                $ketuaTempaUser->roles()->attach($role->id);
            }
        }
    }
}
