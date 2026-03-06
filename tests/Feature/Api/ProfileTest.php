<?php

/**
 * @property \App\Models\User $user
 */

use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    /** @var \Tests\TestCase $this */
    $this->user = User::factory()->create(['username' => null]);
    Sanctum::actingAs($this->user);
});

it('can setup profile for the first time', function () {
    $response = $this->postJson('/api/profile/setup', [
        'name' => 'John Doe',
        'username' => 'johndoe',
        'bio' => 'Hello world!',
        'gender' => 'male',
    ]);

    $response->assertCreated()
        ->assertJson([
            'message' => 'Profile setup successfully.',
            'user' => [
                'name' => 'John Doe',
                'username' => 'johndoe',
                'bio' => 'Hello world!',
                'gender' => 'male',
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'username' => 'johndoe',
    ]);
});

it('cannot setup profile twice', function () {
    $this->user->update(['username' => 'existinguser']);

    $response = $this->postJson('/api/profile/setup', [
        'name' => 'John Doe',
        'username' => 'newusername',
        'bio' => 'Hello world!',
        'gender' => 'male',
    ]);

    $response->assertForbidden()
        ->assertJson([
            'message' => 'Profile already set up. Use update endpoint instead.',
        ]);
});

it('validates profile setup fields', function () {
    $response = $this->postJson('/api/profile/setup', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'username', 'gender']);
});

it('validates username format', function () {
    $response = $this->postJson('/api/profile/setup', [
        'name' => 'John Doe',
        'username' => 'invalid-username!',
        'gender' => 'male',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['username']);
});

it('validates username is unique', function () {
    User::factory()->create(['username' => 'takenusername']);

    $response = $this->postJson('/api/profile/setup', [
        'name' => 'John Doe',
        'username' => 'takenusername',
        'gender' => 'male',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['username']);
});

it('validates gender value', function () {
    $response = $this->postJson('/api/profile/setup', [
        'name' => 'John Doe',
        'username' => 'johndoe',
        'gender' => 'invalid',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['gender']);
});

it('can update name and bio', function () {
    $this->user->update(['username' => 'johndoe']);

    $response = $this->putJson('/api/profile', [
        'name' => 'New Name',
        'bio' => 'New bio',
    ]);

    $response->assertSuccessful()
        ->assertJson([
            'message' => 'Profile updated successfully.',
            'user' => [
                'name' => 'New Name',
                'bio' => 'New bio',
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'name' => 'New Name',
        'bio' => 'New bio',
    ]);
});

it('validates update profile fields', function () {
    $response = $this->putJson('/api/profile', [
        'name' => '',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('can show profile', function () {
    $this->user->update([
        'username' => 'johndoe',
        'bio' => 'My bio',
        'gender' => 'male',
    ]);

    $response = $this->getJson('/api/profile');

    $response->assertSuccessful()
        ->assertJson([
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'username' => 'johndoe',
                'bio' => 'My bio',
                'gender' => 'male',
                'email' => $this->user->email,
            ],
        ]);
});

it('requires authentication for profile endpoints', function () {
    // Create a new test without authentication
    $this->app->get('auth')->forgetUser();

    $this->postJson('/api/profile/setup', [])->assertUnauthorized();
    $this->putJson('/api/profile', [])->assertUnauthorized();
    $this->getJson('/api/profile')->assertUnauthorized();
});
