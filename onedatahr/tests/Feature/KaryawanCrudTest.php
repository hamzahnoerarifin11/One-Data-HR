<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;

uses(Tests\TestCase::class, DatabaseTransactions::class);

it('allows admin to create a karyawan', function () {
    // create admin user
    $admin = User::create([
        'name' => 'Admin Creator',
        'email' => 'admin-create@example.test',
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]);

    $payload = [
        'Nama_Sesuai_KTP' => 'John Doe',
        'Email' => 'john.doe@example.test',
        'Bagian' => 'HR',
        'Jabatan' => 'Staff',
        'Pendidikan_Terakhir' => 'S1',
    ];

    actingAs($admin)
        ->post(route('karyawan.store'), $payload)
        ->assertRedirect(route('karyawan.index'));

    $this->assertDatabaseHas('karyawan', [
        'Nama_Sesuai_KTP' => 'John Doe',
        'Email' => 'john.doe@example.test',
    ]);
});
