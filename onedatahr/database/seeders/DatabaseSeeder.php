<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create a test user and admin compatible with existing users table schema
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin One',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]
        );

        // KPI Module
    $this->call([
        KpiPeriodSeeder::class,
        KpiSeeder::class,
        KpiTargetSeeder::class,
        KpiRealizationSeeder::class,
    ]);


        // Seed recruitment demo data
        $this->call(\Database\Seeders\RecruitmentSeeder::class);

        // Seed some default positions used by the dashboard filters and tests
        $this->call(\Database\Seeders\PosisiModalTestSeeder::class);

        // Seed some daily recruitment demo metrics (calendar)
        $this->call(\Database\Seeders\RekrutmenDailySeeder::class);

        // Seed onboarding demo data
        $this->call(\Database\Seeders\OnboardingKaryawanSeeder::class);
    }
}
