<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NoteResource;
use App\Models\Like;
use App\Models\Note;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function like(Request $request, Note $note)
    {
        // only like public notes or your own (optional rule)
        if (! $note->is_public && $note->user_id !== $request->user()->id) {
            return response()->json(['message' => 'You cannot like this note'], 403);
        }

        // create like if not exists
        $like = Like::firstOrCreate([
            'user_id' => $request->user()->id,
            'note_id' => $note->id,
        ]);

        return response()->json([
            'message' => 'Liked',
        ], 201);
    }

    public function unlike(Request $request, Note $note)
    {
        Like::where('user_id', $request->user()->id)
            ->where('note_id', $note->id)
            ->delete();

        return response()->json([
            'message' => 'Unliked',
        ]);
    }

    public function myLikes(Request $request)
    {
        $userId = $request->user()->id;

        $notes = Note::whereHas('likes', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->withCount('likes')
            ->with('user')
            ->latest()
            ->paginate(10);

        return NoteResource::collection($notes);
    }
}
