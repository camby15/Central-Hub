<?php

namespace App\Support;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Product;
use App\Models\Site;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
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
            ['key' => 'unassigned', 'label' => 'Unassigned'],
            ['key' => 'overdue', 'label' => 'Overdue'],
            ['key' => 'escalated', 'label' => 'Escalated'],
        ];
    }

    /**
     * @return list<array{key: string, label: string}>
     */
    public static function statusTabs(): array
    {
        return [
            ['key' => 'all', 'label' => 'All'],
            ['key' => TicketStatus::Open->value, 'label' => 'Open'],
            ['key' => TicketStatus::InProgress->value, 'label' => 'In Progress'],
            ['key' => TicketStatus::WaitingCustomer->value, 'label' => 'Waiting'],
            ['key' => TicketStatus::Resolved->value, 'label' => 'Resolved'],
            ['key' => TicketStatus::Closed->value, 'label' => 'Closed'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function site(Site $site): array
    {
        $site->loadCount(['products', 'tickets']);

        return [
            'id' => $site->id,
            'name' => $site->name,
            'slug' => $site->slug,
            'url' => $site->url,
            'contact_email' => $site->contact_email,
            'contact_phone' => $site->contact_phone,
            'framework' => $site->framework->value,
            'is_active' => $site->is_active,
            'last_seen_at' => $site->last_seen_at?->diffForHumans() ?? 'Never',
            'api_key_prefix' => $site->api_key_prefix ?? 'unset',
            'allowed_origins' => $site->allowed_origins ?? [],
            'primary_color' => $site->primary_color ?? '#4338CA',
            'logo_label' => $site->logo_path ? basename($site->logo_path) : Str::of($site->name)->explode(' ')->map(fn (string $part): string => Str::substr($part, 0, 1))->implode(''),
            'products_count' => (int) ($site->products_count ?? $site->products()->count()),
            'tickets_count' => (int) ($site->tickets_count ?? $site->tickets()->count()),
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
    public static function product(Product $product): array
    {
        $product->loadMissing('site');
        $product->loadCount('tickets');

        return [
            'id' => $product->id,
            'site_id' => $product->site_id,
            'site' => $product->site?->name ?? 'Unknown',
            'name' => $product->name,
            'slug' => $product->slug,
            'is_active' => $product->is_active,
            'tickets_count' => (int) ($product->tickets_count ?? 0),
        ];
    }

    /**
     * @param  Collection<int, Product>|iterable<int, Product>  $products
     * @return list<array<string, mixed>>
     */
    public static function products(iterable $products): array
    {
        return collect($products)->map(fn (Product $product): array => self::product($product))->values()->all();
    }

    /**
     * @return array<string, mixed>
     */
    public static function ticket(Ticket $ticket): array
    {
        $ticket->loadMissing(['site', 'assignee', 'product']);

        $sla = self::slaPresentation($ticket);

        return [
            'id' => $ticket->id,
            'number' => $ticket->number,
            'subject' => $ticket->subject,
            'body' => $ticket->body,
            'snippet' => Str::limit(strip_tags((string) $ticket->body), 80),
            'site_id' => $ticket->site_id,
            'site' => $ticket->site?->name ?? 'Unknown',
            'product_id' => $ticket->product_id,
            'product' => $ticket->product?->name,
            'status' => $ticket->status->value,
            'priority' => $ticket->priority->value,
            'assignee' => $ticket->assignee?->name,
            'customer_name' => $ticket->customer_name,
            'customer_email' => $ticket->customer_email,
            'customer_token' => $ticket->customer_token,
            'age' => $ticket->created_at?->diffForHumans(short: true) ?? '',
            'created_date' => $ticket->created_at?->format('d/m/Y') ?? '',
            'sla_percent' => $sla['percent'],
            'sla_label' => $sla['label'],
            'sla_state' => $sla['state'],
            'is_escalated' => $ticket->isEscalated(),
            'is_overdue' => $ticket->isOverdue(),
            'csat_score' => $ticket->csat_score,
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
        if ($ticket->status === TicketStatus::WaitingCustomer) {
            return ['percent' => 40, 'label' => 'Paused', 'state' => 'paused'];
        }

        if (in_array($ticket->status, [TicketStatus::Resolved, TicketStatus::Closed], true)) {
            return ['percent' => 100, 'label' => 'Met', 'state' => 'ok'];
        }

        if ($ticket->isOverdue()) {
            return ['percent' => 100, 'label' => 'Breached', 'state' => 'danger'];
        }

        $deadline = null;

        if ($ticket->first_responded_at === null && $ticket->first_response_due_at !== null) {
            $deadline = $ticket->first_response_due_at;
        } elseif ($ticket->resolve_due_at !== null) {
            $deadline = $ticket->resolve_due_at;
        }

        if ($deadline === null || $ticket->created_at === null) {
            return ['percent' => 35, 'label' => 'On track', 'state' => 'ok'];
        }

        $total = max(1, $ticket->created_at->diffInSeconds($deadline));
        $elapsed = max(0, $ticket->created_at->diffInSeconds(now()));
        $percent = (int) min(99, round(($elapsed / $total) * 100));

        if ($percent >= 85) {
            return ['percent' => $percent, 'label' => 'At risk', 'state' => 'danger'];
        }

        if ($percent >= 60) {
            return ['percent' => $percent, 'label' => 'On track', 'state' => 'warning'];
        }

        return ['percent' => $percent, 'label' => 'On track', 'state' => 'ok'];
    }

    /**
     * Apply solo triage view + filters to a ticket query.
     *
     * @param  Builder<Ticket>  $query
     * @return Builder<Ticket>
     */
    public static function applyTicketFilters(
        Builder $query,
        string $view = 'all',
        string $status = 'all',
        string $search = '',
        ?int $siteId = null,
        ?int $productId = null,
        ?string $priority = null,
    ): Builder {
        $query = match ($view) {
            'unassigned' => $query->unassigned(),
            'overdue' => $query->overdue(),
            'escalated' => $query->escalated(),
            default => $query,
        };

        if ($status !== 'all' && $status !== '') {
            $query->where('status', $status);
        }

        if ($siteId !== null) {
            $query->where('site_id', $siteId);
        }

        if ($productId !== null) {
            $query->where('product_id', $productId);
        }

        if ($priority !== null && $priority !== '') {
            $query->where('priority', $priority);
        }

        $search = trim($search);

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhereHas('product', fn (Builder $productQuery): Builder => $productQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('assignee', fn (Builder $assigneeQuery): Builder => $assigneeQuery->where('name', 'like', "%{$search}%"));
            });
        }

        return $query;
    }

    /**
     * @return array<string, int>
     */
    public static function viewCounts(?User $user = null): array
    {
        $base = self::scopedTickets($user);

        return [
            'all' => (clone $base)->count(),
            'unassigned' => (clone $base)->unassigned()->count(),
            'overdue' => (clone $base)->overdue()->count(),
            'escalated' => (clone $base)->escalated()->count(),
        ];
    }

    /**
     * @return array<string, int>
     */
    public static function statusCounts(?User $user = null, string $view = 'all'): array
    {
        $base = self::applyTicketFilters(self::scopedTickets($user), $view);

        $counts = ['all' => (clone $base)->count()];

        foreach (TicketStatus::cases() as $status) {
            $counts[$status->value] = (clone $base)->where('status', $status)->count();
        }

        return $counts;
    }

    /**
     * @return Builder<Ticket>
     */
    public static function scopedTickets(?User $user = null): Builder
    {
        $query = Ticket::query();

        if ($user !== null && ! $user->canAccessAllSites()) {
            $query->forSites($user->accessibleSiteIds());
        }

        return $query;
    }

    /**
     * @return array{open_tickets: int, overdue_sla: int, new_today: int, avg_first_response: string, total: int, in_progress: int, unassigned: int}
     */
    public static function stats(?User $user = null): array
    {
        $scoped = self::scopedTickets($user);
        $open = (clone $scoped)->openStatuses();

        $responded = (clone $scoped)
            ->whereNotNull('first_responded_at')
            ->whereNotNull('created_at')
            ->get(['created_at', 'first_responded_at']);

        $avgMinutes = $responded->isEmpty()
            ? null
            : (int) round($responded->avg(fn (Ticket $ticket): float => $ticket->created_at->diffInMinutes($ticket->first_responded_at)));

        return [
            'total' => (clone $scoped)->count(),
            'open_tickets' => (clone $open)->where('status', TicketStatus::Open)->count(),
            'in_progress' => (clone $open)->where('status', TicketStatus::InProgress)->count(),
            'overdue_sla' => (clone $scoped)->overdue()->count(),
            'unassigned' => (clone $scoped)->unassigned()->openStatuses()->count(),
            'new_today' => (clone $scoped)->whereDate('created_at', today())->count(),
            'avg_first_response' => $avgMinutes === null ? '—' : ($avgMinutes < 60 ? $avgMinutes.'m' : round($avgMinutes / 60, 1).'h'),
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
            $overdue = $site->tickets()->overdue()->count();

            return [
                'site' => $site->name,
                'open' => $open,
                'overdue' => $overdue,
            ];
        })->all();
    }
}
