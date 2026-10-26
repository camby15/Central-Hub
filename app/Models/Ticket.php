<?php

namespace App\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketSource;
use App\Enums\TicketStatus;
use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'number',
    'site_id',
    'assigned_to',
    'customer_name',
    'customer_email',
    'customer_token',
    'subject',
    'body',
    'status',
    'priority',
    'source',
    'meta',
    'first_response_due_at',
    'resolve_due_at',
    'first_responded_at',
    'resolved_at',
    'closed_at',
])]
class Ticket extends Model
{
    /** @use HasFactory<TicketFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class,
            'priority' => TicketPriority::class,
            'source' => TicketSource::class,
            'meta' => 'array',
            'first_response_due_at' => 'datetime',
            'resolve_due_at' => 'datetime',
            'first_responded_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Site, $this>
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * @return HasMany<TicketReply, $this>
     */
    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class)->orderBy('created_at');
    }

    /**
     * @return HasMany<TicketReply, $this>
     */
    public function publicReplies(): HasMany
    {
        return $this->replies()->where('is_internal', false);
    }

    #[Scope]
    protected function openStatuses(Builder $query): Builder
    {
        return $query->whereIn('status', [
            TicketStatus::Open,
            TicketStatus::InProgress,
            TicketStatus::WaitingCustomer,
        ]);
    }

    #[Scope]
    protected function forSites(Builder $query, iterable $siteIds): Builder
    {
        return $query->whereIn('site_id', $siteIds);
    }

    public function statusUrl(): string
    {
        return url('/status/'.$this->customer_token);
    }
}
