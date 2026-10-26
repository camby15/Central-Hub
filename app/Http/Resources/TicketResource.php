<?php

namespace App\Http\Resources;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Ticket
 */
class TicketResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'number' => $this->number,
            'status' => $this->status->value,
            'priority' => $this->priority->value,
            'subject' => $this->subject,
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'status_url' => $this->statusUrl(),
            'meta' => $this->meta,
            'created_at' => $this->created_at?->toIso8601String(),
            'replies' => ReplyResource::collection($this->whenLoaded('replies')),
        ];
    }
}
