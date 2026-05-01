<?php

use App\Models\Note;
use App\Models\Tag;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can list all tags', function () {
    Sanctum::actingAs($this->user);

    Tag::factory()->count(3)->create();

    $response = $this->getJson('/api/tags');

    $response->assertSuccessful()
        ->assertJsonCount(3, 'data');
});

it('can get popular tags', function () {
    Sanctum::actingAs($this->user);

    $tag1 = Tag::factory()->create(['name' => 'Popular']);
    $tag2 = Tag::factory()->create(['name' => 'Less Popular']);

    $note = Note::factory()->create(['user_id' => $this->user->id]);
    $note2 = Note::factory()->create(['user_id' => $this->user->id]);

    $note->tags()->attach($tag1);
    $note2->tags()->attach($tag1);
    $note->tags()->attach($tag2);

    $response = $this->getJson('/api/tags/popular');

    $response->assertSuccessful()
        ->assertJsonCount(2, 'data');

    // First tag should be the most popular one
    $response->assertJsonPath('data.0.name', 'Popular');
});

it('can view a tag', function () {
    Sanctum::actingAs($this->user);

    $tag = Tag::factory()->create();

    $response = $this->getJson("/api/tags/{$tag->id}");

    $response->assertSuccessful()
        ->assertJson([
            'data' => [
                'id' => $tag->id,
                'name' => $tag->name,
            ],
        ]);
});

it('requires authentication for tag endpoints', function () {
    $response = $this->getJson('/api/tags');

    $response->assertUnauthorized();
});

// Note Tag Tests
it('can create a note with tags', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/notes', [
        'title' => 'Test Note',
        'content' => 'This is the content',
        'is_public' => true,
        'tags' => ['Laravel', 'PHP'],
    ]);

    $response->assertCreated()
        ->assertJson([
            'data' => [
                'title' => 'Test Note',
            ],
        ]);

    $this->assertDatabaseHas('tags', ['name' => 'Laravel']);
    $this->assertDatabaseHas('tags', ['name' => 'PHP']);

    $note = Note::where('title', 'Test Note')->first();
    expect($note->tags)->toHaveCount(2);
});

it('can update note tags', function () {
    Sanctum::actingAs($this->user);

    $note = Note::factory()->create(['user_id' => $this->user->id]);
    $tag = Tag::factory()->create(['name' => 'Old Tag']);
    $note->tags()->attach($tag);

    $response = $this->putJson("/api/notes/{$note->id}", [
        'tags' => ['New Tag'],
    ]);

    $response->assertSuccessful();

    $note->refresh();
    expect($note->tags)->toHaveCount(1);
    expect($note->tags->first()->name)->toBe('New Tag');
});

it('can filter notes by tag', function () {
    Sanctum::actingAs($this->user);

    $tag = Tag::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);
    $noteWithTag = Note::factory()->create(['user_id' => $this->user->id, 'title' => 'Laravel Note']);
    $noteWithoutTag = Note::factory()->create(['user_id' => $this->user->id, 'title' => 'Other Note']);

    $noteWithTag->tags()->attach($tag);

    $response = $this->getJson('/api/notes?tag=laravel');

    $response->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Laravel Note');
});

it('can filter public notes by tag', function () {
    Sanctum::actingAs($this->user);

    $tag = Tag::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);
    $noteWithTag = Note::factory()->create(['is_public' => true, 'title' => 'Laravel Note']);
    $noteWithoutTag = Note::factory()->create(['is_public' => true, 'title' => 'Other Note']);

    $noteWithTag->tags()->attach($tag);

    $response = $this->getJson('/api/public-notes?tag=laravel');

    $response->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Laravel Note');
});

it('returns tags with note', function () {
    Sanctum::actingAs($this->user);

    $tag = Tag::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);
    $note = Note::factory()->create(['user_id' => $this->user->id]);
    $note->tags()->attach($tag);

    $response = $this->getJson("/api/notes/{$note->id}");

    $response->assertSuccessful()
        ->assertJson([
            'data' => [
                'id' => $note->id,
                'tags' => [
                    [
                        'name' => 'Laravel',
                        'slug' => 'laravel',
                    ],
                ],
            ],
        ]);
});

it('returns tags in notes list', function () {
    Sanctum::actingAs($this->user);

    $tag = Tag::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);
    $note = Note::factory()->create(['user_id' => $this->user->id]);
    $note->tags()->attach($tag);

    $response = $this->getJson('/api/notes');

    $response->assertSuccessful()
        ->assertJsonPath('data.0.tags.0.name', 'Laravel');
});

it('can clear note tags by passing empty array', function () {
    Sanctum::actingAs($this->user);

    $note = Note::factory()->create(['user_id' => $this->user->id]);
    $tag = Tag::factory()->create();
    $note->tags()->attach($tag);

    $response = $this->putJson("/api/notes/{$note->id}", [
        'tags' => [],
    ]);

    $response->assertSuccessful();

    $note->refresh();
    expect($note->tags)->toHaveCount(0);
});

it('reuses existing tags when creating notes', function () {
    Sanctum::actingAs($this->user);

    $existingTag = Tag::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);

    $response = $this->postJson('/api/notes', [
        'title' => 'Test Note',
        'tags' => ['Laravel'],
    ]);

    $response->assertCreated();

    // Should not create a new tag
    $this->assertDatabaseCount('tags', 1);

    $note = Note::where('title', 'Test Note')->first();
    expect($note->tags->first()->id)->toBe($existingTag->id);
});
