<?php

use App\Models\Follower;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can list following users', function () {
    Sanctum::actingAs($this->user);

    $followingUsers = User::factory()->count(3)->create();
    foreach ($followingUsers as $followedUser) {
        Follower::factory()->create([
            'follower_id' => $this->user->id,
            'user_id' => $followedUser->id,
        ]);
    }

    $response = $this->getJson('/api/follow');

    $response->assertSuccessful();
});

it('can follow another user', function () {
    Sanctum::actingAs($this->user);

    $userToFollow = User::factory()->create();

    $response = $this->postJson('/api/follow', [
        'user_id' => $userToFollow->id,
    ]);

    $response->assertCreated();

    $this->assertDatabaseHas('followers', [
        'follower_id' => $this->user->id,
        'user_id' => $userToFollow->id,
    ]);
});

it('cannot follow itself', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/follow', [
        'user_id' => $this->user->id,
    ]);

    $response->assertBadRequest()
        ->assertJson(['error' => 'Cannot follow yourself']);
});

it('cannot follow the same user twice', function () {
    Sanctum::actingAs($this->user);

    $userToFollow = User::factory()->create();
    Follower::factory()->create([
        'follower_id' => $this->user->id,
        'user_id' => $userToFollow->id,
    ]);

    $response = $this->postJson('/api/follow', [
        'user_id' => $userToFollow->id,
    ]);

    $response->assertBadRequest()
        ->assertJson(['error' => 'Already following this user']);
});

it('validates follow request', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/follow', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['user_id']);
});

it('cannot follow non-existent user', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/follow', [
        'user_id' => 9999,
    ]);

    $response->assertUnprocessable();
});

it('can unfollow a user', function () {
    Sanctum::actingAs($this->user);

    $userToUnfollow = User::factory()->create();
    Follower::factory()->create([
        'follower_id' => $this->user->id,
        'user_id' => $userToUnfollow->id,
    ]);

    $response = $this->deleteJson("/api/follow/{$userToUnfollow->id}");

    $response->assertSuccessful()
        ->assertJson(['message' => 'Unfollowed successfully']);

    $this->assertDatabaseMissing('followers', [
        'follower_id' => $this->user->id,
        'user_id' => $userToUnfollow->id,
    ]);
});

it('returns 404 when unfollowing non-followed user', function () {
    Sanctum::actingAs($this->user);

    $userToUnfollow = User::factory()->create();

    $response = $this->deleteJson("/api/follow/{$userToUnfollow->id}");

    $response->assertNotFound()
        ->assertJson(['error' => 'Follow relationship not found']);
});
