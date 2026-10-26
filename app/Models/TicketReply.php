<?php

namespace App\Models;

use App\Enums\AuthorType;
use Database\Factories\TicketReplyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'ticket_id',
    'user_id',
    'author_type',
    'body',
    'is_internal',
])]
class TicketReply extends Model
{
    /** @use HasFactory<TicketReplyFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'author_type' => AuthorType::class,
            'is_internal' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Ticket, $this>
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function authorName(): string
    {
        return match ($this->author_type) {
            AuthorType::Agent => $this->user?->name ?? 'Agent',
            AuthorType::Customer => $this->ticket?->customer_name ?? 'Customer',
            AuthorType::System => 'System',
        };
    }
}
