<?php

namespace Tests\Feature;

use Tests\TestCase;

class RecruitmentMetricsTest extends TestCase
{
    public function test_candidates_metrics_endpoint_returns_ok()
    {
        $response = $this->actingAs(\App\Models\User::first() ?? null)->get('/rekrutmen/metrics/candidates');
        $response->assertStatus(200);
        $response->assertJsonIsArray();
    }

    public function test_cv_metrics_endpoint_returns_ok()
    {
        $response = $this->actingAs(\App\Models\User::first() ?? null)->get('/rekrutmen/metrics/cv');
        $response->assertStatus(200);
        $response->assertJsonIsArray();
    }

    public function test_progress_metrics_endpoint_returns_ok()
    {
        $response = $this->actingAs(\App\Models\User::first() ?? null)->get('/rekrutmen/metrics/progress');
        $response->assertStatus(200);
        $response->assertJsonIsArray();
    }
}
