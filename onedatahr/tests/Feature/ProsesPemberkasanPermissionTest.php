<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class ProsesPemberkasanPermissionTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\RecruitmentSeeder']);
    }

    public function test_non_admin_cannot_update_proses()
    {
        $user = \App\Models\User::where('role','<>','admin')->first() ?? \App\Models\User::first();
        $k = \App\Models\Kandidat::first();
        $resp = $this->actingAs($user)->post('/rekrutmen/proses', [
            'kandidat_id' => $k->id_kandidat,
            'cv_lolos' => 1,
        ]);
        $resp->assertStatus(403);
    }

    public function test_admin_can_update_proses()
    {
        $user = \App\Models\User::where('role','admin')->first();
        $k = \App\Models\Kandidat::first();
        $resp = $this->actingAs($user)->post('/rekrutmen/proses', [
            'kandidat_id' => $k->id_kandidat,
            'cv_lolos' => 1,
        ]);
        $resp->assertStatus(302);
    }

    public function test_non_admin_cannot_edit_pemberkasan()
    {
        $user = \App\Models\User::where('role','<>','admin')->first() ?? \App\Models\User::first();
        $p = \App\Models\Pemberkasan::first();
        if(!$p) $this->markTestSkipped('No pemberkasan sample');
        $resp = $this->actingAs($user)->get('/rekrutmen/pemberkasan/'.$p->id_pemberkasan.'/edit');
        $resp->assertStatus(403);
    }
}
