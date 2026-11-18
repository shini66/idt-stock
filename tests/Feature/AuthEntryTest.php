<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('redirects visitors from the root route to login', function () {
    $this->get('/')
        ->assertRedirect(route('login'));
});

test('shows a registration link on the login form when registration is available', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('href="'.route('register').'"', false)
        ->assertSee('Register');
});

test('redirects guests from the dashboard to login', function () {
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
});

test('allows unverified users to reach the dashboard redirect', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect('/stock');
});
