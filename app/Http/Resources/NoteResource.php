<?php

namespace App\Http\Resources;

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
            'is_public' => $this->is_public,
            'likes_count' => $this->likes_count ?? $this->likes()->count(),
            'liked_by_me' => $this->when(auth()->check(), fn () => $this->likes()->where('user_id', auth()->id())->exists()
            ),
            'created_at' => $this->created_at,
        ];
    }
}
