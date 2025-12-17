<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class RecruitmentExportTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\RecruitmentSeeder']);
    }

    public function test_export_candidates_csv()
    {
        $user = \App\Models\User::first();
        $response = $this->actingAs($user)->get('/rekrutmen/metrics/candidates/export');
        $response->assertStatus(200);
        $response->assertHeader('content-type');
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
        $content = $response->getContent();
        $this->assertStringContainsString('id_posisi,nama_posisi,year,month,total', trim($content));
    }

    public function test_export_cv_csv()
    {
        $user = \App\Models\User::first();
        $response = $this->actingAs($user)->get('/rekrutmen/metrics/cv/export');
        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
        $content = $response->getContent();
        $this->assertStringContainsString('id_posisi,nama_posisi,year,month,total', trim($content));
    }

    public function test_export_psikotes_csv()
    {
        $user = \App\Models\User::first();
        $response = $this->actingAs($user)->get('/rekrutmen/metrics/psikotes/export');
        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
        $content = $response->getContent();
        $this->assertStringContainsString('id_posisi,nama_posisi,total', trim($content));
    }

    public function test_export_progress_csv()
    {
        $user = \App\Models\User::first();
        $response = $this->actingAs($user)->get('/rekrutmen/metrics/progress/export');
        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
        $content = $response->getContent();
        $this->assertStringContainsString('id_posisi,nama_posisi,total,cv_lolos', trim($content));
    }

    public function test_export_candidates_filtered_by_posisi()
    {
        $user = \App\Models\User::first();
        $pos = \App\Models\Posisi::first();
        $this->assertNotNull($pos, 'No posisi seeded');
        $response = $this->actingAs($user)->get('/rekrutmen/metrics/candidates/export?posisi_id='.$pos->id_posisi);
        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
        $content = $response->getContent();
        $this->assertStringContainsString($pos->nama_posisi, $content);
    }
}
