<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $notes = Note::where('user_id', $request->user()->id)
            ->withCount('likes')
            ->latest()
            ->get();

        return NoteResource::collection($notes);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

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
        $this->authorizeNote($request, $note);

        $note->loadCount('likes');

        return new NoteResource($note);
    }

    public function update(Request $request, Note $note)
    {
        $this->authorizeNote($request, $note);

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|nullable|string',
            'is_public' => 'sometimes|boolean',
        ]);

        $note->update($data);

        $note->loadCount('likes');

        return new NoteResource($note);
    }

    public function destroy(Request $request, Note $note)
    {
        $this->authorizeNote($request, $note);

        $note->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function publicIndex()
    {
        $notes = Note::where('is_public', true)
            ->withCount('likes')
            ->latest()
            ->get();

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

    protected function authorizeNote(Request $request, Note $note)
    {
        if ($note->user_id !== $request->user()->id) {
            abort(403, 'Forbidden');
        }
    }
}
