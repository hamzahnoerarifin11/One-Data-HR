<?php

namespace Database\Seeders;

<<<<<<< HEAD
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'staff',
            'manager',
            'admin',
=======
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $roles = [
            'superadmin',
            'admin',
            'manager',
            'staff',
>>>>>>> 1b20d4b208d46b25e6fca7ddbad5856c859f0422
            'ketua_tempa',
        ];

        foreach ($roles as $role) {
<<<<<<< HEAD
            DB::table('roles')->updateOrInsert(
                ['name' => $role],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
=======
            \App\Models\Role::firstOrCreate(['name' => $role]);
        }
    }

>>>>>>> 1b20d4b208d46b25e6fca7ddbad5856c859f0422
}
