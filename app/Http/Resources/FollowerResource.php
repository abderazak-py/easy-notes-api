<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FollowerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'follower_id' => $this->follower_id,
            'user' => $this->whenLoaded('user', fn () => new UserResource($this->user)),
            'follower' => $this->whenLoaded('follower', fn () => new UserResource($this->follower)),
            'created_at' => $this->created_at,
        ];
    }
}
