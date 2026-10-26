<?php

namespace App\Filament\Pages;

use App\Actions\CreateTicket as CreateTicketAction;
use App\Enums\TicketPriority;
use App\Enums\TicketSource;
use App\Models\Site;
use App\Models\Ticket;
use App\Support\HubViewData;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use UnitEnum;

class CreateTicket extends Page
{
    use AuthorizesRequests;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPlusCircle;

    protected static ?string $navigationLabel = 'New ticket';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'tickets/create';

    protected string $view = 'filament.pages.create-ticket';

    public ?int $site_id = null;

    public string $customer_name = '';

    public string $customer_email = '';

    public string $phone = '';

    public string $order_id = '';

    public string $subject = '';

    public string $priority = 'normal';

    public string $body = '';

    public function getTitle(): string|Htmlable
    {
        return 'New ticket';
    }

    public function create(CreateTicketAction $createTicket): void
    {
        $this->authorize('create', Ticket::class);

        $validated = $this->validate([
            'site_id' => ['required', 'integer', 'exists:sites,id'],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'order_id' => ['nullable', 'string', 'max:80'],
            'subject' => ['required', 'string', 'max:150'],
            'priority' => ['required', 'string'],
            'body' => ['required', 'string', 'max:10000'],
        ]);

        $site = Site::query()->findOrFail($validated['site_id']);
        $user = Filament::auth()->user();

        abort_unless($user?->canAccessSite($site) ?? false, 403);

        $meta = array_filter([
            'phone' => $validated['phone'] ?: null,
            'order_id' => $validated['order_id'] ?: null,
        ]);

        $ticket = $createTicket->handle($site, [
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'priority' => TicketPriority::from($validated['priority']),
            'source' => TicketSource::Manual,
            'meta' => $meta,
            'assigned_to' => $user?->id,
        ], $user);

        Notification::make()
            ->title('Ticket '.$ticket->number.' created')
            ->success()
            ->send();

        $this->redirect(ViewTicket::getUrl(['ticket' => $ticket->id]));
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $user = Filament::auth()->user();

        $sitesQuery = Site::query()->active()->orderBy('name');

        if ($user !== null && ! $user->canAccessAllSites()) {
            $sitesQuery->whereIn('id', $user->accessibleSiteIds());
        }

        return [
            'sites' => HubViewData::sites($sitesQuery->get()),
            'priorities' => HubViewData::priorityLabels(),
        ];
    }
}
