<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    /**
     * Setup user profile (one-time only).
     */
    public function setup(ProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        // Check if profile is already set up
        if ($user->username !== null) {
            return response()->json([
                'message' => 'Profile already set up. Use update endpoint instead.',
            ], 403);
        }

        $user->update($request->validated());

        return response()->json([
            'message' => 'Profile setup successfully.',
            'user' => new ProfileResource($user),
        ], 201);
    }

    /**
     * Update user name and bio.
     */
    public function updateNameBio(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->update($request->validated());

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => new ProfileResource($user),
        ]);
    }

    /**
     * Get current user profile.
     */
    public function show(): JsonResponse
    {
        $user = auth()->user();

        return response()->json([
            'user' => new ProfileResource($user),
        ]);
    }
}
