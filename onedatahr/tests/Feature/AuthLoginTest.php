<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;

uses(Tests\TestCase::class, DatabaseTransactions::class);

it('allows a user to login with correct credentials', function () {
    $password = 'secret123';
    $user = User::create([
        'name' => 'Login User',
        'email' => 'login@example.test',
        'password' => Hash::make($password),
    ]);

    post(route('signin.post'), [
        'email' => $user->email,
        'password' => $password,
    ])->assertRedirect('/dashboard');
});

it('rejects invalid credentials', function () {
    $password = 'secret456';
    $user = User::create([
        'name' => 'Bad Login',
        'email' => 'badlogin@example.test',
        'password' => Hash::make($password),
    ]);

    post(route('signin.post'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');
});
