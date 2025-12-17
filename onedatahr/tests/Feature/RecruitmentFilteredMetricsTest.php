<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class RecruitmentFilteredMetricsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // run seeder to ensure deterministic sample data
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\RecruitmentSeeder']);
    }

    public function test_candidates_filtered_by_month()
    {
        $first = \App\Models\Kandidat::orderBy('tanggal_melamar')->first();
        $month = $first->tanggal_melamar->format('Y-m');
        $from = $month.'-01';
        $to = $month.'-31';

        $response = $this->actingAs(\App\Models\User::first())->get('/rekrutmen/metrics/candidates?from='.$from.'&to='.$to);
        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }

    public function test_cv_filtered_by_month()
    {
        $any = \App\Models\ProsesRekrutmen::whereNotNull('tanggal_cv')->first();
        if(!$any) $this->markTestSkipped('No cv date found in sample data');
        $month = \Carbon\parse($any->tanggal_cv)->format('Y-m');
        $from = $month.'-01'; $to = $month.'-31';
        $response = $this->actingAs(\App\Models\User::first())->get('/rekrutmen/metrics/cv?from='.$from.'&to='.$to);
        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }

    public function test_candidates_filtered_by_posisi()
    {
        $pos = \App\Models\Posisi::first();
        $this->assertNotNull($pos, 'No posisi seeded');
        $response = $this->actingAs(\App\Models\User::first())->get('/rekrutmen/metrics/candidates?posisi_id='.$pos->id_posisi);
        $response->assertStatus(200);
        $rows = $response->json();
        foreach($rows as $r){
            $this->assertEquals($pos->id_posisi, $r['id_posisi']);
        }
    }

    public function test_psikotes_filtered_by_posisi()
    {
        $pos = \App\Models\Posisi::first();
        $response = $this->actingAs(\App\Models\User::first())->get('/rekrutmen/metrics/psikotes?posisi_id='.$pos->id_posisi);
        $response->assertStatus(200);
        $rows = $response->json();
        foreach($rows as $r){
            $this->assertEquals($pos->id_posisi, $r['id_posisi']);
        }
    }

    public function test_invalid_date_range_returns_422()
    {
        $from = '2025-12-31';
        $to = '2025-01-01';
        $response = $this->actingAs(\App\Models\User::first())->get('/rekrutmen/metrics/candidates?from='.$from.'&to='.$to);
        $response->assertStatus(422);
    }
}
