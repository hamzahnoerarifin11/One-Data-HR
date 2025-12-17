<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class MetricsPagesTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\RecruitmentSeeder']);
    }

    public function test_metrics_pages_load()
    {
        $user = \App\Models\User::first();
        $pages = [
            '/rekrutmen/metrics/cv-page',
            '/rekrutmen/metrics/psikotes-page',
            '/rekrutmen/metrics/kompetensi-page',
            '/rekrutmen/metrics/interview-hr-page',
            '/rekrutmen/metrics/interview-user-page',
        ];

        foreach($pages as $p){
            $resp = $this->actingAs($user)->get($p);
            $resp->assertStatus(200);
        }
    }
}
