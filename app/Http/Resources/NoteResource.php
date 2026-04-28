<?php

namespace App\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'is_public' => (bool) $this->is_public,
            'user' => new UserResource($this->whenLoaded('user')),
            'likes_count' => $this->likes_count ?? $this->likes()->count(),
            'liked_by_me' => $this->when(auth()->check(), fn () => $this->likes()->where('user_id', auth()->id())->exists()),
            'tags' => $this->when($this->relationLoaded('tags'), fn () => $this->tags->map(fn ($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
            ])),
            'created_at' => $this->created_at,
        ];
    }
}
