<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Note;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Note $note)
    {
        if (! $note->is_public && auth()->id() !== $note->user_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $comments = $note->comments()
            ->with('user:id,name')
            ->latest()
            ->paginate(10);

        return CommentResource::collection($comments);
    }

    public function store(CommentRequest $request, Note $note)
    {
        if (! $note->is_public && $note->user_id !== $request->user()->id) {
            return response()->json(['message' => 'You cannot comment on this note'], 403);
        }

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'note_id' => $note->id,
            'body' => $request['body'],
        ]);

        $comment->load('user:id,name');

        return response()->json(new CommentResource($comment), 201);
    }

    public function destroy(Request $request, Comment $comment)
    {
        if ($comment->user_id !== $request->user()->id && $comment->note->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted']);
    }
}
