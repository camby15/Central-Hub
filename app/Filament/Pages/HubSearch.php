<?php

namespace App\Filament\Pages;

use App\Models\Site;
use App\Support\HubViewData;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Url;
use UnitEnum;

class HubSearch extends Page
{
    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static ?string $navigationLabel = 'Search';

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'search';

    protected string $view = 'filament.pages.hub-search';

    #[Url]
    public string $q = '';

    public function getTitle(): string|Htmlable
    {
        return 'Search';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Search';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $user = Filament::auth()->user();
        $query = trim($this->q);

        $tickets = [];
        $sites = [];

        if ($query !== '') {
            $tickets = HubViewData::tickets(
                HubViewData::applyTicketFilters(
                    HubViewData::scopedTickets($user)->with(['site', 'assignee', 'product'])->latest(),
                    search: $query,
                )->limit(25)->get()
            );

            $sitesQuery = Site::query()
                ->withCount(['products', 'tickets'])
                ->where(function ($builder) use ($query): void {
                    $builder->where('name', 'like', "%{$query}%")
                        ->orWhere('slug', 'like', "%{$query}%")
                        ->orWhere('contact_email', 'like', "%{$query}%")
                        ->orWhere('url', 'like', "%{$query}%");
                })
                ->orderBy('name')
                ->limit(12);

            if ($user !== null && ! $user->canAccessAllSites()) {
                $sitesQuery->whereIn('id', $user->accessibleSiteIds());
            }

            $sites = HubViewData::sites($sitesQuery->get());
        }

        return [
            'tickets' => $tickets,
            'sites' => $sites,
            'hasQuery' => $query !== '',
            'statuses' => HubViewData::statusLabels(),
            'priorities' => HubViewData::priorityLabels(),
        ];
    }
}
