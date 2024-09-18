<?php

namespace App\Http\Resources;

use App\Models\SubComment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            
            'user_id' => $this->user_id,
            'thread_id' => $this->thread_id,
            'body' => $this->body,
            
            'subcomments' => SubCommentResource::collection($this->subcomments),
            'created_at' => $this->created_at->diffForHumans()
        ];

    }
}
