<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class PemberkasanMonitorTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\RecruitmentSeeder']);
    }

    public function test_pemberkasan_monitor_page_loads()
    {
        $user = \App\Models\User::first();
        $resp = $this->actingAs($user)->get('/rekrutmen/metrics/pemberkasan-page');
        $resp->assertStatus(200);
    }
}
