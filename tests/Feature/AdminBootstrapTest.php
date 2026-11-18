<?php

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function registrationInput(string $email, array $overrides = []): array
{
    return array_merge([
        'name' => 'Registered User',
        'email' => $email,
        'password' => 'password',
        'password_confirmation' => 'password',
    ], $overrides);
}

test('promotes the earliest existing user and registers the new user as an operator', function () {
    $earliestUser = User::factory()->create();
    User::factory()->create();

    $registeredUser = app(CreateNewUser::class)->create(registrationInput('registered@example.com'));

    expect($earliestUser->fresh()->role)->toBe('admin')
        ->and($registeredUser->role)->toBe('operator');
});

test('assigns the first fresh registration as an admin', function () {
    $registeredUser = app(CreateNewUser::class)->create(registrationInput('first@example.com'));

    expect($registeredUser->fresh()->role)->toBe('admin');
});

test('assigns later registrations as operators', function () {
    app(CreateNewUser::class)->create(registrationInput('first@example.com'));

    $registeredUser = app(CreateNewUser::class)->create(registrationInput('later@example.com'));

    expect($registeredUser->fresh()->role)->toBe('operator');
});

test('does not accept a role from registration data', function () {
    app(CreateNewUser::class)->create(registrationInput('first@example.com'));

    $registeredUser = app(CreateNewUser::class)->create(registrationInput('attacker@example.com', [
        'role' => 'admin',
    ]));

    expect($registeredUser->fresh()->role)->toBe('operator');
});
