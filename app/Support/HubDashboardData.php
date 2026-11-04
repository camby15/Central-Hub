<?php

namespace App\Support;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class HubDashboardData
{
    public static function rangeStart(string $range): CarbonImmutable
    {
        $days = match ($range) {
            '30' => 29,
            'quarter' => 89,
            default => 6,
        };

        return now()->startOfDay()->subDays($days)->toImmutable();
    }

    public static function rangeDayCount(string $range): int
    {
        return match ($range) {
            '30' => 30,
            'quarter' => 90,
            default => 7,
        };
    }

    /**
     * @return array{
     *     range: string,
     *     stats: array<string, int|string>,
     *     volume: list<array{label: string, count: int}>,
     *     by_status: list<array{key: string, label: string, count: int, percent: int}>,
     *     activity: list<array{title: string, meta: string, at: string, ticket_id: int|null}>
     * }
     */
    public static function forUser(?User $user, string $range = '7'): array
    {
        $scoped = HubViewData::scopedTickets($user);
        $start = self::rangeStart($range);
        $stats = HubViewData::stats($user);

        $daySpan = self::rangeDayCount($range);
        $previousStart = $start->subDays($daySpan);
        $previousCount = (clone $scoped)
            ->where('created_at', '>=', $previousStart)
            ->where('created_at', '<', $start)
            ->count();
        $currentCount = (clone $scoped)->where('created_at', '>=', $start)->count();

        $stats['total_in_range'] = $currentCount;
        $stats['total_delta_percent'] = $previousCount === 0
            ? ($currentCount > 0 ? 100 : 0)
            : (int) round((($currentCount - $previousCount) / $previousCount) * 100);

        return [
            'range' => $range,
            'stats' => $stats,
            'volume' => self::dailyVolume($user, $range),
            'by_status' => self::statusBreakdown($user),
            'activity' => self::activityFeed($user),
        ];
    }

    /**
     * @return list<array{label: string, count: int}>
     */
    public static function dailyVolume(?User $user, string $range = '7'): array
    {
        $maxOffset = self::rangeDayCount($range) - 1;
        $start = now()->startOfDay()->subDays($maxOffset);

        /** @var Collection<string, int> $counts */
        $counts = HubViewData::scopedTickets($user)
            ->where('created_at', '>=', $start)
            ->get(['created_at'])
            ->groupBy(fn (Ticket $ticket): string => $ticket->created_at?->toDateString() ?? '')
            ->map(fn (Collection $group): int => $group->count());

        $series = [];

        for ($offset = $maxOffset; $offset >= 0; $offset--) {
            $date = now()->startOfDay()->subDays($offset);
            $key = $date->toDateString();
            $series[] = [
                'label' => $maxOffset > 13 ? $date->format('j M') : $date->format('D'),
                'count' => (int) ($counts[$key] ?? 0),
            ];
        }

        return $series;
    }

    /**
     * @return list<array{key: string, label: string, count: int, percent: int}>
     */
    public static function statusBreakdown(?User $user): array
    {
        $scoped = HubViewData::scopedTickets($user);
        $total = max(1, (clone $scoped)->count());

        return collect(TicketStatus::cases())
            ->map(function (TicketStatus $status) use ($scoped, $total): array {
                $count = (clone $scoped)->where('status', $status)->count();

                return [
                    'key' => $status->value,
                    'label' => $status->label(),
                    'count' => $count,
                    'percent' => (int) round(($count / $total) * 100),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return list<array{title: string, meta: string, at: string, ticket_id: int|null}>
     */
    public static function activityFeed(?User $user, int $limit = 8): array
    {
        $siteIds = $user !== null && ! $user->canAccessAllSites()
            ? $user->accessibleSiteIds()
            : null;

        $replies = TicketReply::query()
            ->with(['ticket.site', 'user'])
            ->when($siteIds !== null, fn ($query) => $query->whereHas(
                'ticket',
                fn ($ticketQuery) => $ticketQuery->whereIn('site_id', $siteIds)
            ))
            ->latest()
            ->limit($limit)
            ->get();

        if ($replies->isNotEmpty()) {
            return $replies->map(function (TicketReply $reply): array {
                $ticket = $reply->ticket;

                return [
                    'title' => $ticket?->subject ?? 'Ticket updated',
                    'meta' => ($reply->is_internal ? 'Internal note' : 'Reply').' · '.($ticket?->number ?? ''),
                    'at' => $reply->created_at?->diffForHumans() ?? '',
                    'ticket_id' => $ticket?->id,
                ];
            })->all();
        }

        return HubViewData::scopedTickets($user)
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (Ticket $ticket): array => [
                'title' => $ticket->subject,
                'meta' => 'Ticket created · '.$ticket->number,
                'at' => $ticket->created_at?->diffForHumans() ?? '',
                'ticket_id' => $ticket->id,
            ])
            ->all();
    }

    /**
     * @return array{scored: int, average: float|null, distribution: array<int, int>}
     */
    public static function csatSummary(?User $user): array
    {
        $scored = HubViewData::scopedTickets($user)
            ->whereNotNull('csat_score')
            ->get(['csat_score']);

        $distribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

        foreach ($scored as $ticket) {
            $score = (int) $ticket->csat_score;
            if (isset($distribution[$score])) {
                $distribution[$score]++;
            }
        }

        return [
            'scored' => $scored->count(),
            'average' => $scored->isEmpty() ? null : round((float) $scored->avg('csat_score'), 1),
            'distribution' => $distribution,
        ];
    }
}
