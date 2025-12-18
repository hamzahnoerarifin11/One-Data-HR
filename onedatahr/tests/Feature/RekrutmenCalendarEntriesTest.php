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

    public function test_admin_can_list_entries()
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $pos = Posisi::create(['nama_posisi' => 'ListPos']);
        $k = Kandidat::create(['nama' => 'ListKid', 'posisi_id' => $pos->id_posisi]);

        $e = \App\Models\RekrutmenCalendarEntry::create(['posisi_id' => $pos->id_posisi, 'kandidat_id' => $k->id_kandidat, 'date' => '2025-12-30']);

        $url = route('rekrutmen.daily.entries.index') . '?posisi_id=' . $pos->id_posisi . '&date=2025-12-30';
        $this->actingAs($admin)->getJson($url)
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $e->id]);
    }

    public function test_admin_can_delete_entry()
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $pos = Posisi::create(['nama_posisi' => 'DelPos']);
        $e = \App\Models\RekrutmenCalendarEntry::create(['posisi_id' => $pos->id_posisi, 'candidate_name' => 'ToDelete', 'date' => '2025-12-29']);

        $this->actingAs($admin)->deleteJson(route('rekrutmen.daily.entries.destroy', $e->id))
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('rekrutmen_calendar_entries', ['id' => $e->id]);
    }
}
