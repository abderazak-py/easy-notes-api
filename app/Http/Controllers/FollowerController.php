<?php

namespace App\Http\Controllers;

use App\Http\Resources\FollowerResource;
use Illuminate\Http\Request;

class FollowerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Return users that this user is following
        $following = $user->following()->with('user')->get();

        return response()->json(FollowerResource::collection($following));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Prevent user from following themselves
        if ($request->user_id == $user->id) {
            return response()->json(['error' => 'Cannot follow yourself'], 400);
        }

        // Check if the follow relationship already exists
        $existingFollow = $user->following()->where('user_id', $request->user_id)->first();
        if ($existingFollow) {
            return response()->json(['error' => 'Already following this user'], 400);
        }

        $follow = $user->following()->create([
            'user_id' => $request->input('user_id'),
        ]);

        return response()->json(new FollowerResource($follow), 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $user = $request->user();

        // Find the follow relationship where the authenticated user is the follower and $id is the user being followed
        $follow = $user->following()->where('user_id', $id)->first();

        if (! $follow) {
            return response()->json(['error' => 'Follow relationship not found'], 404);
        }

        $follow->delete();

        return response()->json(['message' => 'Unfollowed successfully']);
    }
}
