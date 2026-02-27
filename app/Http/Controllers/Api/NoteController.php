<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Note;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $notes = Note::where('user_id', $request->user()->id)
            ->withCount('likes')
            ->latest()
            ->get();

        return response()->json($notes);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'body'      => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $note = Note::create([
            'user_id'   => $request->user()->id,
            'title'     => $data['title'],
            'body'      => $data['body'] ?? null,
            'is_public' => $data['is_public'] ?? false,
        ]);

        return response()->json($note, 201);
    }

    public function show(Request $request, Note $note)
    {
        $this->authorizeNote($request, $note);

        $note->loadCount('likes');

        return response()->json($note);
    }

    public function update(Request $request, Note $note)
    {
        $this->authorizeNote($request, $note);

        $data = $request->validate([
            'title'     => 'sometimes|string|max:255',
            'body'      => 'sometimes|nullable|string',
            'is_public' => 'sometimes|boolean',
        ]);

        $note->update($data);

        return response()->json($note);
    }

    public function destroy(Request $request, Note $note)
    {
        $this->authorizeNote($request, $note);

        $note->delete();

        return response()->json(['message' => 'Deleted']);
    }

    protected function authorizeNote(Request $request, Note $note)
    {
        if ($note->user_id !== $request->user()->id) {
            abort(403, 'Forbidden');
        }
    }
}
