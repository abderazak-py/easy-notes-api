<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
     * Store a newly created tag.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
        ]);

        $tag = Tag::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return new TagResource($tag);
    }

    /**
     * Display the specified tag.
     */
    public function show(Tag $tag)
    {
        $tag->loadCount('notes');

        return new TagResource($tag);
    }

    /**
     * Update the specified tag.
     */
    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:tags,name,'.$tag->id,
        ]);

        if ($request->has('name')) {
            $tag->name = $request->name;
            $tag->slug = Str::slug($request->name);
            $tag->save();
        }

        return new TagResource($tag);
    }

    /**
     * Remove the specified tag.
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json(['message' => 'Tag deleted successfully'], 200);
    }
}
