<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Posisi;
use App\Models\Kandidat;

class RekrutmenCalendarEntriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_entry_with_name()
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $pos = Posisi::create(['nama_posisi' => 'Test Posisi']);

        $payload = ['posisi_id' => $pos->id_posisi, 'date' => '2025-12-25', 'candidate_name' => 'John Doe'];

        $this->actingAs($admin)->postJson(route('rekrutmen.daily.entries.store'), $payload)
            ->assertStatus(201)
            ->assertJsonFragment(['candidate_name' => 'John Doe']);

        $this->assertDatabaseHas('rekrutmen_calendar_entries', ['posisi_id' => $pos->id_posisi, 'candidate_name' => 'John Doe']);
        $this->assertDatabaseHas('kandidat', ['nama' => 'John Doe', 'posisi_id' => $pos->id_posisi]);
    }

    public function test_admin_can_create_entry_with_existing_candidate()
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $pos = Posisi::create(['nama_posisi' => 'X']);
        $k = Kandidat::create(['nama' => 'Existing', 'posisi_id' => $pos->id_posisi]);

        $payload = ['posisi_id' => $pos->id_posisi, 'date' => '2025-12-26', 'kandidat_id' => $k->id_kandidat];
        $this->actingAs($admin)->postJson(route('rekrutmen.daily.entries.store'), $payload)
            ->assertStatus(201)
            ->assertJsonFragment(['kandidat_id' => $k->id_kandidat]);

        $this->assertDatabaseHas('rekrutmen_calendar_entries', ['posisi_id' => $pos->id_posisi, 'kandidat_id' => $k->id_kandidat]);
    }

    public function test_non_admin_cannot_create_entry()
    {
        $user = \App\Models\User::factory()->create(['role' => 'user']);
        $pos = Posisi::create(['nama_posisi' => 'Y']);
        $payload = ['posisi_id' => $pos->id_posisi, 'date' => '2025-12-26', 'candidate_name' => 'Nope'];

        $this->actingAs($user)->postJson(route('rekrutmen.daily.entries.store'), $payload)
            ->assertStatus(403);
    }
}
