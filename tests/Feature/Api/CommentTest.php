<?php

use App\Models\Comment;
use App\Models\Note;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can list comments for a public note', function () {
    Sanctum::actingAs($this->user);

    $note = Note::factory()->create(['is_public' => true]);
    Comment::factory()->count(3)->create(['note_id' => $note->id]);

    $response = $this->getJson("/api/notes/{$note->id}/comments");

    $response->assertSuccessful()
        ->assertJsonCount(3, 'data');
});

it('can list comments for own private note', function () {
    Sanctum::actingAs($this->user);

    $note = Note::factory()->create(['user_id' => $this->user->id, 'is_public' => false]);
    Comment::factory()->count(2)->create(['note_id' => $note->id]);

    $response = $this->getJson("/api/notes/{$note->id}/comments");

    $response->assertSuccessful()
        ->assertJsonCount(2, 'data');
});

it('cannot list comments for another user\'s private note', function () {
    Sanctum::actingAs($this->user);

    $otherUser = User::factory()->create();
    $note = Note::factory()->create(['user_id' => $otherUser->id, 'is_public' => false]);

    $response = $this->getJson("/api/notes/{$note->id}/comments");

    $response->assertForbidden();
});

it('can create a comment on a public note', function () {
    Sanctum::actingAs($this->user);

    $note = Note::factory()->create(['is_public' => true]);

    $response = $this->postJson("/api/notes/{$note->id}/comments", [
        'body' => 'This is a test comment',
    ]);

    $response->assertCreated()
        ->assertJson([
            'body' => 'This is a test comment',
        ]);

    $this->assertDatabaseHas('comments', [
        'note_id' => $note->id,
        'user_id' => $this->user->id,
        'body' => 'This is a test comment',
    ]);
});

it('can create a comment on own private note', function () {
    Sanctum::actingAs($this->user);

    $note = Note::factory()->create(['user_id' => $this->user->id, 'is_public' => false]);

    $response = $this->postJson("/api/notes/{$note->id}/comments", [
        'body' => 'This is a test comment',
    ]);

    $response->assertCreated();
});

it('cannot comment on another user\'s private note', function () {
    Sanctum::actingAs($this->user);

    $otherUser = User::factory()->create();
    $note = Note::factory()->create(['user_id' => $otherUser->id, 'is_public' => false]);

    $response = $this->postJson("/api/notes/{$note->id}/comments", [
        'body' => 'This is a test comment',
    ]);

    $response->assertForbidden();
});

it('validates comment creation', function () {
    Sanctum::actingAs($this->user);

    $note = Note::factory()->create(['is_public' => true]);

    $response = $this->postJson("/api/notes/{$note->id}/comments", []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['body']);
});

it('can delete own comment', function () {
    Sanctum::actingAs($this->user);

    $note = Note::factory()->create(['is_public' => true]);
    $comment = Comment::factory()->create(['note_id' => $note->id, 'user_id' => $this->user->id]);

    $response = $this->deleteJson("/api/comments/{$comment->id}");

    $response->assertSuccessful()
        ->assertJson(['message' => 'Comment deleted']);

    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});

it('can delete comment on own note', function () {
    Sanctum::actingAs($this->user);

    $note = Note::factory()->create(['user_id' => $this->user->id]);
    $otherUser = User::factory()->create();
    $comment = Comment::factory()->create(['note_id' => $note->id, 'user_id' => $otherUser->id]);

    $response = $this->deleteJson("/api/comments/{$comment->id}");

    $response->assertSuccessful();
});

it('cannot delete another user\'s comment on another user\'s note', function () {
    Sanctum::actingAs($this->user);

    $otherUser = User::factory()->create();
    $note = Note::factory()->create(['user_id' => $otherUser->id, 'is_public' => true]);
    $comment = Comment::factory()->create(['note_id' => $note->id, 'user_id' => $otherUser->id]);

    $response = $this->deleteJson("/api/comments/{$comment->id}");

    $response->assertForbidden();
});
