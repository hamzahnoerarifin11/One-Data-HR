<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;

uses(Tests\TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    // register a temporary route protected by role middleware
    Route::middleware('role:admin')->get('/__test-role', function () {
        return response('ok', 200);
    });
});

it('blocks access for non-admin users', function () {
    $user = User::create([
        'name' => 'Regular User',
        'email' => 'regular@example.test',
        'password' => Hash::make('password'),
        'role' => 'user',
    ]);

    actingAs($user)
        ->get('/__test-role')
        ->assertStatus(403);
});

it('allows access for admin users', function () {
    $admin = User::create([
        'name' => 'Admin User',
        'email' => 'admin-test@example.test',
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]);

    actingAs($admin)
        ->get('/__test-role')
        ->assertStatus(200)
        ->assertSee('ok');
});
