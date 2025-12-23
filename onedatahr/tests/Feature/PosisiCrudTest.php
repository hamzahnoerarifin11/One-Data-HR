<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class PosisiCrudTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\RecruitmentSeeder']);
    }

    public function test_create_update_delete_posisi()
    {
        $user = \App\Models\User::first();

        // ensure we have an admin available for JSON delete
        $admin = \App\Models\User::where('role','admin')->first() ?? $user;

        // create via API (JSON)
        $resp = $this->actingAs($user)->postJson('/rekrutmen/posisi', ['nama_posisi' => 'Test Position']);
        $resp->assertStatus(200);
        $this->assertDatabaseHas('posisi', ['nama_posisi' => 'Test Position']);

        $pos = \App\Models\Posisi::where('nama_posisi', 'Test Position')->first();
        $this->assertNotNull($pos);

        // update
        $resp2 = $this->actingAs($user)->putJson('/rekrutmen/posisi/'.$pos->id_posisi, ['nama_posisi' => 'Test Position Updated']);
        $resp2->assertStatus(200);
        $this->assertDatabaseHas('posisi', ['nama_posisi' => 'Test Position Updated']);

        // delete via standard form (redirect)
        $resp3 = $this->actingAs($user)->delete('/rekrutmen/posisi/'.$k->id_posisi);
        $resp3->assertRedirect();
        $this->assertDatabaseMissing('posisi', ['nama_posisi' => 'Test Position Updated']);

        // test JSON delete via API (AJAX style)
        $resp4 = $this->actingAs($admin)->deleteJson('/rekrutmen/posisi/'.$k->id_posisi);
        $resp4->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseMissing('posisi', ['nama_posisi' => 'Test Position Updated']);
    }
}
