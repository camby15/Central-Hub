<?php

namespace App\Http\Resources;

use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TicketReply
 */
class ReplyResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'author_type' => $this->author_type->value,
            'body' => $this->body,
            'is_internal' => $this->is_internal,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
