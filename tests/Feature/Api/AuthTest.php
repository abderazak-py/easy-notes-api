<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    //
});

it('can register a new user', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertNoContent();

    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
    ]);
});

it('cannot register with existing email', function () {
    User::factory()->create(['email' => 'test@example.com']);

    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertUnprocessable();
});

it('validates registration fields', function () {
    $response = $this->postJson('/api/register', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

it('can login with correct credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'user' => ['id', 'name', 'email'],
            'token',
        ]);
});

it('cannot login with wrong credentials', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertUnprocessable();
});

it('validates login fields', function () {
    $response = $this->postJson('/api/login', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'password']);
});

it('can logout when authenticated', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/logout');

    $response->assertNoContent();
});

it('cannot logout when not authenticated', function () {
    $response = $this->postJson('/api/logout');

    $response->assertUnauthorized();
});
