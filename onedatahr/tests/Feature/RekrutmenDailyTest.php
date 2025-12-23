<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class RekrutmenDailyTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // Ensure a fresh database for this test
        Artisan::call('migrate:fresh', ['--force' => true]);

        // Some legacy tables (like posisi) may not have migrations; create minimal schema for tests
        if (!\Illuminate\Support\Facades\Schema::hasTable('posisi')) {
            \Illuminate\Support\Facades\Schema::create('posisi', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->integer('id_posisi')->primary();
                $table->string('nama_posisi');
                $table->timestamps();
            });
        }

        // Create a sample posisi and admin user directly for tests
        \App\Models\Posisi::insert([
            ['id_posisi' => 1, 'nama_posisi' => 'Software Engineer', 'created_at' => now(), 'updated_at' => now()],
            ['id_posisi' => 2, 'nama_posisi' => 'HR Staff', 'created_at' => now(), 'updated_at' => now()],
            ['id_posisi' => 3, 'nama_posisi' => 'Sales', 'created_at' => now(), 'updated_at' => now()],
        ]);

        if (!\Illuminate\Support\Facades\Schema::hasColumn('users','role')) {
            \Illuminate\Support\Facades\Schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('role')->default('user');
            });
        }

        if (!\App\Models\User::where('role', 'admin')->exists()) {
            \App\Models\User::factory()->create(['role' => 'admin', 'email' => 'admin@example.test']);
        }

        // Disable CSRF middleware for this test so POST assertions don't redirect
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_create_and_list_daily()
    {
        $user = \App\Models\User::where('role','admin')->first() ?? \App\Models\User::first();
        $pos = \App\Models\Posisi::first();
        $this->assertNotNull($pos);

        $this->actingAs($user, 'web');
        $this->assertAuthenticatedAs($user);

        $resp = $this->withHeaders([
            'X-CSRF-TOKEN' => csrf_token(),
            'X-Requested-With' => 'XMLHttpRequest',
        ])->postJson('/rekrutmen/daily', [
            'posisi_id' => $pos->id_posisi,
            'date' => now()->format('Y-m-d'),
            'total_pelamar' => 7,
            'lolos_cv' => 3,
            'lolos_psikotes' => 2,
        ]);
        // if route redirects (302), capture target for debugging
        if ($resp->getStatusCode() !== 200) {
            $location = $resp->headers->get('Location') ?? 'none';
            $content = $resp->getContent();
            // Dump debug into laravel.log for review
            \Illuminate\Support\Facades\Log::error('rekrutmen_daily_post_debug', ['status'=>$resp->getStatusCode(),'location'=>$location,'content'=>$content]);
            $this->fail("Request returned non-200 status: {$resp->getStatusCode()}\nLOCATION: {$location}\nCONTENT: {$content}");
        }

        $this->assertTrue($resp->json('success') === true);
        $this->assertEquals(7, $resp->json('entry.total_pelamar'));

        $list = $this->actingAs($user)->getJson('/rekrutmen/daily?month='.now()->format('n').'&year='.now()->format('Y'));
        $list->assertStatus(200);
        $list->assertJsonFragment(['total_pelamar' => 7]);
    }

    public function test_non_json_get_redirects_to_calendar()
    {
        $user = \App\Models\User::first();
        $resp = $this->actingAs($user)->get('/rekrutmen/daily');
        $resp->assertStatus(302)->assertRedirect(route('rekrutmen.calendar'));

    }
}
