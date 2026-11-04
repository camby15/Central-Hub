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
    'product_id',
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
    'escalated_at',
    'csat_score',
    'csat_submitted_at',
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
            'escalated_at' => 'datetime',
            'csat_submitted_at' => 'datetime',
            'csat_score' => 'integer',
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
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
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

    public function isEscalated(): bool
    {
        return $this->escalated_at !== null;
    }

    public function isOverdue(): bool
    {
        if (in_array($this->status, [TicketStatus::Resolved, TicketStatus::Closed], true)) {
            return false;
        }

        if ($this->first_responded_at === null
            && $this->first_response_due_at !== null
            && $this->first_response_due_at->isPast()) {
            return true;
        }

        return $this->resolve_due_at !== null && $this->resolve_due_at->isPast();
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

    #[Scope]
    protected function unassigned(Builder $query): Builder
    {
        return $query->whereNull('assigned_to');
    }

    #[Scope]
    protected function escalated(Builder $query): Builder
    {
        return $query->whereNotNull('escalated_at');
    }

    #[Scope]
    protected function overdue(Builder $query): Builder
    {
        return $query->openStatuses()->where(function (Builder $builder): void {
            $builder->where(function (Builder $inner): void {
                $inner->whereNull('first_responded_at')
                    ->whereNotNull('first_response_due_at')
                    ->where('first_response_due_at', '<', now());
            })->orWhere(function (Builder $inner): void {
                $inner->whereNotNull('resolve_due_at')
                    ->where('resolve_due_at', '<', now());
            });
        });
    }

    public function statusUrl(): string
    {
        return url('/status/'.$this->customer_token);
    }
}
