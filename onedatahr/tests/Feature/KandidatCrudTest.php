<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class KandidatCrudTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\RecruitmentSeeder']);
    }

    public function test_create_update_delete_kandidat()
    {
        $user = \App\Models\User::first();
        $pos = \App\Models\Posisi::first();

        // create
        $resp = $this->actingAs($user)->post('/rekrutmen/kandidat', [
            'nama' => 'Test Candidate',
            'posisi_id' => $pos->id_posisi,
            'tanggal_melamar' => now()->format('Y-m-d'),
        ]);
        $resp->assertRedirect();
        $this->assertDatabaseHas('kandidat', ['nama' => 'Test Candidate']);
        $k = \App\Models\Kandidat::where('nama','Test Candidate')->first();

        // update
        $resp2 = $this->actingAs($user)->put('/rekrutmen/kandidat/'.$k->id_kandidat, [
            'nama' => 'Test Candidate Updated',
            'posisi_id' => $pos->id_posisi,
        ]);
        $resp2->assertRedirect();
        $this->assertDatabaseHas('kandidat', ['nama' => 'Test Candidate Updated']);

        // delete
        $resp3 = $this->actingAs($user)->delete('/rekrutmen/kandidat/'.$k->id_kandidat);
        $resp3->assertRedirect();
        $this->assertDatabaseMissing('kandidat', ['nama' => 'Test Candidate Updated']);
    }
}
