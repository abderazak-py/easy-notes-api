<?php

use App\Models\Like;
use App\Models\Note;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can like a public note', function () {
    Sanctum::actingAs($this->user);

    $note = Note::factory()->create(['is_public' => true]);

    $response = $this->postJson("/api/notes/{$note->id}/like");

    $response->assertCreated()
        ->assertJson(['message' => 'Liked']);

    $this->assertDatabaseHas('likes', [
        'note_id' => $note->id,
        'user_id' => $this->user->id,
    ]);
});

it('can like own private note', function () {
    Sanctum::actingAs($this->user);

    $note = Note::factory()->create(['user_id' => $this->user->id, 'is_public' => false]);

    $response = $this->postJson("/api/notes/{$note->id}/like");

    $response->assertCreated();
});

it('cannot like another user\'s private note', function () {
    Sanctum::actingAs($this->user);

    $otherUser = User::factory()->create();
    $note = Note::factory()->create(['user_id' => $otherUser->id, 'is_public' => false]);

    $response = $this->postJson("/api/notes/{$note->id}/like");

    $response->assertForbidden();
});

it('can unlike a note', function () {
    Sanctum::actingAs($this->user);

    $note = Note::factory()->create(['is_public' => true]);
    Like::factory()->create(['note_id' => $note->id, 'user_id' => $this->user->id]);

    $response = $this->deleteJson("/api/notes/{$note->id}/like");

    $response->assertSuccessful()
        ->assertJson(['message' => 'Unliked']);

    $this->assertDatabaseMissing('likes', [
        'note_id' => $note->id,
        'user_id' => $this->user->id,
    ]);
});

it('can list liked notes', function () {
    Sanctum::actingAs($this->user);

    $likedNotes = Note::factory()->count(3)->create(['is_public' => true]);
    foreach ($likedNotes as $note) {
        Like::factory()->create(['note_id' => $note->id, 'user_id' => $this->user->id]);
    }

    // Create notes not liked by user
    Note::factory()->count(2)->create(['is_public' => true]);

    $response = $this->getJson('/api/notes/liked');

    $response->assertSuccessful()
        ->assertJsonCount(3, 'data');
});

it('cannot like the same note twice', function () {
    Sanctum::actingAs($this->user);

    $note = Note::factory()->create(['is_public' => true]);

    // First like
    $this->postJson("/api/notes/{$note->id}/like");

    // Second like (should not create duplicate)
    $response = $this->postJson("/api/notes/{$note->id}/like");

    $response->assertCreated();

    // Should still have only one like
    $this->assertEquals(1, Like::where('note_id', $note->id)->where('user_id', $this->user->id)->count());
});
