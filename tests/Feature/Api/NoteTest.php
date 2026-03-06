<?php

use App\Models\Note;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can list own notes', function () {
    Sanctum::actingAs($this->user);

    Note::factory()->count(3)->create(['user_id' => $this->user->id]);
    Note::factory()->count(2)->create(); // Other user's notes

    $response = $this->getJson('/api/notes');

    $response->assertSuccessful()
        ->assertJsonCount(3, 'data');
});

it('can search notes', function () {
    Sanctum::actingAs($this->user);

    Note::factory()->create(['user_id' => $this->user->id, 'title' => 'Laravel Tips']);
    Note::factory()->create(['user_id' => $this->user->id, 'title' => 'Vue Guide']);

    $response = $this->getJson('/api/notes?q=Laravel');

    $response->assertSuccessful()
        ->assertJsonCount(1, 'data');
});

it('can create a note', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/notes', [
        'title' => 'Test Note',
        'content' => 'This is the content',
        'is_public' => true,
    ]);

    $response->assertCreated()
        ->assertJson([
            'data' => [
                'title' => 'Test Note',
                'content' => 'This is the content',
                'is_public' => true,
            ],
        ]);

    $this->assertDatabaseHas('notes', [
        'title' => 'Test Note',
        'user_id' => $this->user->id,
    ]);
});

it('validates note creation', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/notes', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['title']);
});

it('can view own note', function () {
    Sanctum::actingAs($this->user);

    $note = Note::factory()->create(['user_id' => $this->user->id]);

    $response = $this->getJson("/api/notes/{$note->id}");

    $response->assertSuccessful()
        ->assertJson([
            'data' => [
                'id' => $note->id,
            ],
        ]);
});

it('cannot view private note of another user', function () {
    Sanctum::actingAs($this->user);

    $otherUser = User::factory()->create();
    $note = Note::factory()->create(['user_id' => $otherUser->id, 'is_public' => false]);

    $response = $this->getJson("/api/notes/{$note->id}");

    $response->assertForbidden();
});

it('can update own note', function () {
    Sanctum::actingAs($this->user);

    $note = Note::factory()->create(['user_id' => $this->user->id]);

    $response = $this->putJson("/api/notes/{$note->id}", [
        'title' => 'Updated Title',
        'content' => 'Updated content',
    ]);

    $response->assertSuccessful()
        ->assertJson([
            'data' => [
                'title' => 'Updated Title',
            ],
        ]);

    $this->assertDatabaseHas('notes', [
        'id' => $note->id,
        'title' => 'Updated Title',
    ]);
});

it('cannot update another user\'s note', function () {
    Sanctum::actingAs($this->user);

    $otherUser = User::factory()->create();
    $note = Note::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->putJson("/api/notes/{$note->id}", [
        'title' => 'Updated Title',
    ]);

    $response->assertForbidden();
});

it('can delete own note', function () {
    Sanctum::actingAs($this->user);

    $note = Note::factory()->create(['user_id' => $this->user->id]);

    $response = $this->deleteJson("/api/notes/{$note->id}");

    $response->assertSuccessful()
        ->assertJson(['message' => 'Note deleted successfully']);

    $this->assertDatabaseMissing('notes', ['id' => $note->id]);
});

it('cannot delete another user\'s note', function () {
    Sanctum::actingAs($this->user);

    $otherUser = User::factory()->create();
    $note = Note::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->deleteJson("/api/notes/{$note->id}");

    $response->assertForbidden();
});

it('can list public notes', function () {
    Sanctum::actingAs($this->user);

    Note::factory()->count(3)->create(['is_public' => true]);
    Note::factory()->count(2)->create(['is_public' => false]);

    $response = $this->getJson('/api/public-notes');

    $response->assertSuccessful()
        ->assertJsonCount(3, 'data');
});

it('can view a public note', function () {
    Sanctum::actingAs($this->user);

    $note = Note::factory()->create(['is_public' => true]);

    $response = $this->getJson("/api/public-notes/{$note->id}");

    $response->assertSuccessful()
        ->assertJson([
            'data' => [
                'id' => $note->id,
            ],
        ]);
});

it('cannot view a private note via public endpoint', function () {
    Sanctum::actingAs($this->user);

    $note = Note::factory()->create(['is_public' => false]);

    $response = $this->getJson("/api/public-notes/{$note->id}");

    $response->assertForbidden();
});

it('requires authentication for notes endpoints', function () {
    $response = $this->getJson('/api/notes');

    $response->assertUnauthorized();
});

it('cannot create a note without profile setup', function () {
    $user = User::factory()->withoutProfile()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/notes', [
        'title' => 'Test Note',
        'content' => 'This is the content',
    ]);

    $response->assertForbidden()
        ->assertJson([
            'message' => 'Please set up your profile first.',
        ]);
});
