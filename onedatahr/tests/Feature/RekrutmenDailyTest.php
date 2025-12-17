<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class RekrutmenDailyTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\RecruitmentSeeder']);
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\PosisiModalTestSeeder']);
    }

    public function test_create_and_list_daily()
    {
        $user = \App\Models\User::first();
        $pos = \App\Models\Posisi::first();
        $this->assertNotNull($pos);

        $resp = $this->actingAs($user)->postJson('/rekrutmen/daily', [
            'posisi_id' => $pos->id_posisi,
            'date' => now()->format('Y-m-d'),
            'count' => 3,
        ]);
        $resp->assertStatus(200)->assertJson(['success' => true]);

        $list = $this->actingAs($user)->get('/rekrutmen/daily?month='.now()->format('n').'&year='.now()->format('Y'));
        $list->assertStatus(200);
        $this->assertStringContainsString(now()->format('Y-m-d'), $list->getContent());
    }
}
