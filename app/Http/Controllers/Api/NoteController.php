<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Note::where('user_id', $request->user()->id)
            ->withCount('likes')
            ->with(['tags', 'user'])
            ->latest();

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($tagSlug = $request->query('tag')) {
            $query->whereHas('tags', function ($q) use ($tagSlug) {
                $q->where('slug', $tagSlug);
            });
        }

        $notes = $query->paginate(10);

        return NoteResource::collection($notes);
    }


    public function store(NoteRequest $request)
    {
        $data = $request->validated();

        $note = Note::create([
            'user_id' => $request->user()->id,
            'title' => $data['title'],
            'content' => $data['content'] ?? null,
            'is_public' => $data['is_public'] ?? false,
        ]);

        if (isset($data['tags'])) {
            $this->syncTags($note, $data['tags']);
        }

        $note->loadCount('likes');
        $note->load(['tags', 'user']);

        return new NoteResource($note);
    }

    public function show(Request $request, Note $note)
    {
        $this->authorize('view', $note);

        $note->loadCount('likes');
        $note->load(['tags', 'user']);

        return new NoteResource($note);
    }

    public function update(NoteRequest $request, Note $note)
    {
        $this->authorize('update', $note);

        $data = $request->validated();
        $note->update($data);

        if (isset($data['tags'])) {
            $this->syncTags($note, $data['tags']);
        }

        $note->loadCount('likes');
        $note->load(['tags', 'user']);

        return new NoteResource($note);
    }

    public function destroy(Request $request, Note $note)
    {
        $this->authorize('delete', $note);

        $note->delete();

        return response()->json(['message' => 'Note deleted successfully'], 200);
    }

    public function publicIndex(Request $request)
    {
        $query = Note::where('is_public', true)
            ->withCount('likes')
            ->with(['tags', 'user'])
            ->orderByDesc('likes_count')
            ->orderByDesc('created_at');

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($tagSlug = $request->query('tag')) {
            $query->whereHas('tags', function ($q) use ($tagSlug) {
                $q->where('slug', $tagSlug);
            });
        }

        $notes = $query->paginate(10);

        return NoteResource::collection($notes);
    }

    public function publicShow(Note $note)
    {
        if (! $note->is_public) {
            return response()->json(['message' => 'Not public'], 403);
        }

        $note->loadCount('likes');
        $note->load(['tags', 'user']);

        return new NoteResource($note);
    }

    /**
     * Get a personal feed of notes from users the authenticated user follows.
     */
    public function feed(Request $request)
    {
        $user = $request->user();

        // Get IDs of users that the authenticated user follows
        $followingIds = $user->followedUsers()->pluck('users.id');

        $query = Note::whereIn('user_id', $followingIds)
            ->where('is_public', true)
            ->withCount('likes')
            ->with(['tags', 'user'])
            ->latest();

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($tagSlug = $request->query('tag')) {
            $query->whereHas('tags', function ($q) use ($tagSlug) {
                $q->where('slug', $tagSlug);
            });
        }

        $notes = $query->paginate(10);

        return NoteResource::collection($notes);
    }

    /**
     * Sync tags for a note.
     */
    protected function syncTags(Note $note, array $tags): void
    {
        $tagIds = [];

        foreach ($tags as $tagName) {
            $tag = \App\Models\Tag::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($tagName)],
                ['name' => $tagName]
            );
            $tagIds[] = $tag->id;
        }

        $note->tags()->sync($tagIds);
    }
}
