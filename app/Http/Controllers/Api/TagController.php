<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;

class TagController extends Controller
{
    /**
     * Display a listing of all tags.
     */
    public function index()
    {
        $tags = Tag::withCount('notes')->orderBy('name')->get();

        return TagResource::collection($tags);
    }

    /**
     * Display popular tags ordered by note count.
     */
    public function popular()
    {
        $tags = Tag::withCount('notes')
            ->orderByDesc('notes_count')
            ->limit(20)
            ->get();

        return TagResource::collection($tags);
    }

    /**
     * Display the specified tag.
     */
    public function show(Tag $tag)
    {
        $tag->loadCount('notes');

        return new TagResource($tag);
    }
}
