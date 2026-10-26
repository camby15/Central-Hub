<?php

namespace App\Filament\Pages;

use App\Support\DemoData;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Url;
use UnitEnum;

class ViewTicket extends Page
{
    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'Ticket detail';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'tickets/view';

    protected string $view = 'filament.pages.view-ticket';

    #[Url]
    public int $ticket = 1;

    public function getTitle(): string|Htmlable
    {
        $data = DemoData::ticket($this->ticket);

        return $data['number'] ?? 'Ticket';
    }

    public function getHeading(): string|Htmlable
    {
        $data = DemoData::ticket($this->ticket);

        return ($data['number'] ?? 'Ticket').' · '.($data['subject'] ?? '');
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $ticket = DemoData::ticket($this->ticket) ?? DemoData::tickets()[0];

        return [
            'ticket' => $ticket,
            'replies' => DemoData::replies($ticket['id']),
            'activity' => DemoData::activity($ticket['id']),
            'statuses' => DemoData::statusLabels(),
            'priorities' => DemoData::priorityLabels(),
        ];
    }
}
