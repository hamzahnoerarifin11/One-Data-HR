<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateActivatedAtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Posisi::where('status', 'Aktif')
            ->whereNull('activated_at')
            ->update(['activated_at' => \DB::raw('created_at')]);
    }
}
