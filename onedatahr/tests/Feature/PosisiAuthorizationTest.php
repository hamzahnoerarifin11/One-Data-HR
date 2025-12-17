<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class PosisiAuthorizationTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\RecruitmentSeeder']);
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\PosisiModalTestSeeder']);
    }

    public function test_non_admin_cannot_delete_posisi_json()
    {
        $user = \App\Models\User::first();
        $pos = \App\Models\Posisi::first();
        $resp = $this->actingAs($user)->deleteJson('/rekrutmen/posisi/'.$pos->id_posisi);
        $resp->assertStatus(403);
    }

    public function test_admin_can_delete_posisi_json()
    {
        $admin = \App\Models\User::where('role','admin')->first();
        $pos = \App\Models\Posisi::create(['nama_posisi' => 'TempForDelete']);
        $resp = $this->actingAs($admin)->deleteJson('/rekrutmen/posisi/'.$pos->id_posisi);
        $resp->assertStatus(200)->assertJson(['success' => true]);
    }
}
