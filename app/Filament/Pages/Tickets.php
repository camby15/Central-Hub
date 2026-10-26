<?php

namespace App\Filament\Pages;

use App\Models\Site;
use App\Models\Ticket;
use App\Support\HubViewData;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

class Tickets extends Page
{
    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static ?string $navigationLabel = 'Tickets';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'tickets';

    protected string $view = 'filament.pages.tickets';

    public function getTitle(): string|Htmlable
    {
        return 'Tickets';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Tickets';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $user = Filament::auth()->user();

        $ticketsQuery = Ticket::query()
            ->with(['site', 'assignee'])
            ->latest();

        if ($user !== null && ! $user->canAccessAllSites()) {
            $ticketsQuery->forSites($user->accessibleSiteIds());
        }

        $sitesQuery = Site::query()->orderBy('name');

        if ($user !== null && ! $user->canAccessAllSites()) {
            $sitesQuery->whereIn('id', $user->accessibleSiteIds());
        }

        return [
            'tickets' => HubViewData::tickets($ticketsQuery->get()),
            'views' => HubViewData::savedViews(),
            'statuses' => HubViewData::statusLabels(),
            'priorities' => HubViewData::priorityLabels(),
            'sites' => HubViewData::sites($sitesQuery->get()),
        ];
    }
}
