<?php

namespace App\Support;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Site;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class HubViewData
{
    /**
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return TicketStatus::labels();
    }

    /**
     * @return array<string, string>
     */
    public static function priorityLabels(): array
    {
        return TicketPriority::labels();
    }

    /**
     * @return list<array{key: string, label: string}>
     */
    public static function savedViews(): array
    {
        return [
            ['key' => 'all', 'label' => 'All tickets'],
            ['key' => 'mine', 'label' => 'Assigned to me'],
            ['key' => 'urgent', 'label' => 'Urgent & breached'],
            ['key' => 'unassigned', 'label' => 'Unassigned'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function site(Site $site): array
    {
        return [
            'id' => $site->id,
            'name' => $site->name,
            'slug' => $site->slug,
            'url' => $site->url,
            'framework' => $site->framework->value,
            'is_active' => $site->is_active,
            'last_seen_at' => $site->last_seen_at?->diffForHumans() ?? 'Never',
            'api_key_prefix' => $site->api_key_prefix ?? 'unset',
            'allowed_origins' => $site->allowed_origins ?? [],
            'primary_color' => $site->primary_color ?? '#4338CA',
            'logo_label' => $site->logo_path ? basename($site->logo_path) : Str::of($site->name)->explode(' ')->map(fn (string $part): string => Str::substr($part, 0, 1))->implode(''),
        ];
    }

    /**
     * @param  Collection<int, Site>|iterable<int, Site>  $sites
     * @return list<array<string, mixed>>
     */
    public static function sites(iterable $sites): array
    {
        return collect($sites)->map(fn (Site $site): array => self::site($site))->values()->all();
    }

    /**
     * @return array<string, mixed>
     */
    public static function ticket(Ticket $ticket): array
    {
        $ticket->loadMissing(['site', 'assignee']);

        $sla = self::slaPresentation($ticket);

        return [
            'id' => $ticket->id,
            'number' => $ticket->number,
            'subject' => $ticket->subject,
            'body' => $ticket->body,
            'site_id' => $ticket->site_id,
            'site' => $ticket->site?->name ?? 'Unknown',
            'status' => $ticket->status->value,
            'priority' => $ticket->priority->value,
            'assignee' => $ticket->assignee?->name,
            'customer_name' => $ticket->customer_name,
            'customer_email' => $ticket->customer_email,
            'customer_token' => $ticket->customer_token,
            'age' => $ticket->created_at?->diffForHumans(short: true) ?? '',
            'sla_percent' => $sla['percent'],
            'sla_label' => $sla['label'],
            'sla_state' => $sla['state'],
            'category' => $ticket->meta['category'] ?? null,
            'created_at' => $ticket->created_at?->toDayDateTimeString() ?? '',
            'meta' => $ticket->meta ?? [],
        ];
    }

    /**
     * @param  Collection<int, Ticket>|iterable<int, Ticket>  $tickets
     * @return list<array<string, mixed>>
     */
    public static function tickets(iterable $tickets): array
    {
        return collect($tickets)->map(fn (Ticket $ticket): array => self::ticket($ticket))->values()->all();
    }

    /**
     * @return array<string, mixed>
     */
    public static function reply(TicketReply $reply): array
    {
        $reply->loadMissing(['user', 'ticket']);

        return [
            'id' => $reply->id,
            'author_type' => $reply->author_type->value,
            'author' => $reply->authorName(),
            'body' => $reply->body,
            'is_internal' => $reply->is_internal,
            'created_at' => $reply->created_at?->diffForHumans() ?? '',
        ];
    }

    /**
     * @param  Collection<int, TicketReply>|iterable<int, TicketReply>  $replies
     * @return list<array<string, mixed>>
     */
    public static function replies(iterable $replies): array
    {
        return collect($replies)->map(fn (TicketReply $reply): array => self::reply($reply))->values()->all();
    }

    /**
     * @return array{percent: int, label: string, state: string}
     */
    public static function slaPresentation(Ticket $ticket): array
    {
        if (in_array($ticket->status, [TicketStatus::WaitingCustomer], true)) {
            return ['percent' => 40, 'label' => 'Paused', 'state' => 'paused'];
        }

        if (in_array($ticket->status, [TicketStatus::Resolved, TicketStatus::Closed], true)) {
            return ['percent' => 100, 'label' => 'Met', 'state' => 'ok'];
        }

        if ($ticket->priority === TicketPriority::Urgent) {
            return ['percent' => 85, 'label' => 'At risk', 'state' => 'danger'];
        }

        if ($ticket->priority === TicketPriority::High) {
            return ['percent' => 65, 'label' => 'On track', 'state' => 'warning'];
        }

        return ['percent' => 35, 'label' => 'On track', 'state' => 'ok'];
    }

    /**
     * @return array{open_tickets: int, overdue_sla: int, new_today: int, avg_first_response: string}
     */
    public static function stats(?User $user = null): array
    {
        $query = Ticket::query()->openStatuses();

        if ($user !== null && ! $user->canAccessAllSites()) {
            $query->forSites($user->accessibleSiteIds());
        }

        $open = (clone $query)->count();
        $urgent = (clone $query)->where('priority', TicketPriority::Urgent)->count();
        $newToday = Ticket::query()
            ->when($user !== null && ! $user->canAccessAllSites(), fn ($q) => $q->forSites($user->accessibleSiteIds()))
            ->whereDate('created_at', today())
            ->count();

        return [
            'open_tickets' => $open,
            'overdue_sla' => $urgent,
            'new_today' => $newToday,
            'avg_first_response' => '—',
        ];
    }

    /**
     * @return list<array{site: string, open: int, overdue: int}>
     */
    public static function ticketsBySite(?User $user = null): array
    {
        $sitesQuery = Site::query()->orderBy('name');

        if ($user !== null && ! $user->canAccessAllSites()) {
            $sitesQuery->whereIn('id', $user->accessibleSiteIds());
        }

        return $sitesQuery->get()->map(function (Site $site): array {
            $open = $site->tickets()->openStatuses()->count();
            $overdue = $site->tickets()->openStatuses()->where('priority', TicketPriority::Urgent)->count();

            return [
                'site' => $site->name,
                'open' => $open,
                'overdue' => $overdue,
            ];
        })->all();
    }
}
