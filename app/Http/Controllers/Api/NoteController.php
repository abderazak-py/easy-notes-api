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
            ->latest();

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $notes = $query->paginate(10);

        return NoteResource::collection($notes);
    }

    public function myNotes(Request $request)
    {
        $query = Note::where('user_id', $request->user()->id)
            ->withCount('likes')
            ->latest();

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
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

        $note->loadCount('likes');

        return new NoteResource($note);
    }

    public function show(Request $request, Note $note)
    {
        $this->authorize('view', $note);

        $note->loadCount('likes');

        return new NoteResource($note);
    }

    public function update(NoteRequest $request, Note $note)
    {
        $this->authorize('update', $note);

        $note->update($request->validated());

        $note->loadCount('likes');

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
            ->orderByDesc('likes_count')
            ->orderByDesc('created_at');

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
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

        return new NoteResource($note);
    }
}
