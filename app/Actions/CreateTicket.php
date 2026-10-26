<?php

namespace App\Actions;

use App\Enums\AuthorType;
use App\Enums\TicketPriority;
use App\Enums\TicketSource;
use App\Enums\TicketStatus;
use App\Models\Site;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateTicket
{
    /**
     * @param  array{
     *     customer_name: string,
     *     customer_email: string,
     *     subject: string,
     *     body: string,
     *     priority?: string|TicketPriority|null,
     *     meta?: array<string, mixed>|null,
     *     source?: TicketSource|null,
     *     assigned_to?: int|null,
     * }  $data
     */
    public function handle(Site $site, array $data, ?User $actor = null): Ticket
    {
        return DB::transaction(function () use ($site, $data): Ticket {
            $lockedSite = Site::query()->whereKey($site->id)->lockForUpdate()->firstOrFail();

            $sequence = Ticket::withTrashed()
                ->where('site_id', $lockedSite->id)
                ->count() + 1;

            $number = sprintf('%s-%05d', Str::upper($lockedSite->slug), $sequence);

            $priority = $data['priority'] ?? TicketPriority::Normal;
            if (is_string($priority)) {
                $priority = TicketPriority::from($priority);
            }

            $source = $data['source'] ?? TicketSource::Api;

            $ticket = Ticket::query()->create([
                'number' => $number,
                'site_id' => $lockedSite->id,
                'assigned_to' => $data['assigned_to'] ?? null,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_token' => Str::random(64),
                'subject' => $data['subject'],
                'body' => $data['body'],
                'status' => TicketStatus::Open,
                'priority' => $priority,
                'source' => $source,
                'meta' => $data['meta'] ?? [],
            ]);

            $ticket->replies()->create([
                'user_id' => null,
                'author_type' => AuthorType::Customer,
                'body' => $data['body'],
                'is_internal' => false,
            ]);

            $lockedSite->touchLastSeen();

            return $ticket->fresh(['site', 'assignee', 'replies']) ?? $ticket;
        });
    }
}
