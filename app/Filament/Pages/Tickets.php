<?php

namespace App\Filament\Pages;

use App\Enums\TicketStatus;
use App\Models\Product;
use App\Models\Site;
use App\Models\Ticket;
use App\Support\HubViewData;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Url;
use UnitEnum;

class Tickets extends Page
{
    use AuthorizesRequests;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static ?string $navigationLabel = 'Tickets';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'tickets';

    protected string $view = 'filament.pages.tickets';

    #[Url]
    public string $viewKey = 'all';

    #[Url]
    public string $status = 'all';

    #[Url]
    public string $search = '';

    #[Url]
    public ?int $siteId = null;

    #[Url]
    public ?int $productId = null;

    #[Url]
    public string $priority = '';

    /** @var list<int> */
    public array $selected = [];

    public function getTitle(): string|Htmlable
    {
        return 'Tickets';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Tickets';
    }

    public function updatedSearch(): void
    {
        $this->resetSelected();
    }

    public function setView(string $view): void
    {
        $this->viewKey = $view;
        $this->resetSelected();
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
        $this->resetSelected();
    }

    public function bulkClose(): void
    {
        $user = Filament::auth()->user();
        $tickets = Ticket::query()->whereIn('id', $this->selected)->get();

        foreach ($tickets as $ticket) {
            $this->authorize('update', $ticket);

            if ($user !== null && ! $user->canAccessSite($ticket->site_id)) {
                abort(403);
            }

            $ticket->forceFill([
                'status' => TicketStatus::Closed,
                'closed_at' => now(),
            ])->save();
        }

        $this->resetSelected();

        Notification::make()
            ->title('Closed '.count($tickets).' ticket(s)')
            ->success()
            ->send();
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $user = Filament::auth()->user();

        $ticketsQuery = HubViewData::applyTicketFilters(
            HubViewData::scopedTickets($user)->with(['site', 'assignee', 'product'])->latest(),
            $this->viewKey,
            $this->status,
            $this->search,
            $this->siteId,
            $this->productId,
            $this->priority !== '' ? $this->priority : null,
        );

        $sitesQuery = Site::query()->orderBy('name');
        $productsQuery = Product::query()->with('site')->orderBy('name');

        if ($user !== null && ! $user->canAccessAllSites()) {
            $siteIds = $user->accessibleSiteIds();
            $sitesQuery->whereIn('id', $siteIds);
            $productsQuery->whereIn('site_id', $siteIds);
        }

        return [
            'tickets' => HubViewData::tickets($ticketsQuery->get()),
            'views' => HubViewData::savedViews(),
            'viewCounts' => HubViewData::viewCounts($user),
            'statusTabs' => HubViewData::statusTabs(),
            'statusCounts' => HubViewData::statusCounts($user, $this->viewKey),
            'statuses' => HubViewData::statusLabels(),
            'priorities' => HubViewData::priorityLabels(),
            'sites' => HubViewData::sites($sitesQuery->get()),
            'products' => HubViewData::products($productsQuery->get()),
            'activeView' => $this->viewKey,
            'activeStatus' => $this->status,
        ];
    }

    private function resetSelected(): void
    {
        $this->selected = [];
    }
}
