<?php

namespace Tests\Feature;

use Tests\TestCase;

class TailadminRecruitmentUiTest extends TestCase
{
    public function test_recruitment_pages_render_for_admin()
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('rekrutmen.dashboard'))
            ->assertStatus(200)
            ->assertSee('Recruitment Dashboard');

        $this->actingAs($admin)
            ->get(route('rekrutmen.calendar'))
            ->assertStatus(200)
            ->assertSee('Kalender Rekrutmen');

        $this->actingAs($admin)
            ->get(route('rekrutmen.kandidat.index'))
            ->assertStatus(200)
            ->assertSee('Kandidat');

        $this->actingAs($admin)
            ->get(route('rekrutmen.posisi.index'))
            ->assertStatus(200)
            ->assertSee('Daftar Posisi');
    }
}
