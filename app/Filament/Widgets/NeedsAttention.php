<?php

namespace App\Filament\Widgets;

use App\Enums\TicketPriority;
use App\Models\Ticket;
use App\Support\HubViewData;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class NeedsAttention extends Widget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.needs-attention';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $user = Filament::auth()->user();

        $query = Ticket::query()
            ->with(['site', 'assignee'])
            ->openStatuses()
            ->where(function ($builder): void {
                $builder->where('priority', TicketPriority::Urgent)
                    ->orWhere('priority', TicketPriority::High);
            })
            ->latest()
            ->limit(8);

        if ($user !== null && ! $user->canAccessAllSites()) {
            $query->forSites($user->accessibleSiteIds());
        }

        return [
            'tickets' => HubViewData::tickets($query->get()),
            'bySite' => HubViewData::ticketsBySite($user),
            'statuses' => HubViewData::statusLabels(),
            'priorities' => HubViewData::priorityLabels(),
        ];
    }
}
