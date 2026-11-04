<?php

namespace App\Filament\Pages;

use App\Support\HubViewData;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

class Escalations extends Page
{
    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowTrendingUp;

    protected static ?string $navigationLabel = 'Escalations';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'escalations';

    protected string $view = 'filament.pages.escalations';

    public function getTitle(): string|Htmlable
    {
        return 'Escalations';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Escalations';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $user = Filament::auth()->user();

        $tickets = HubViewData::scopedTickets($user)
            ->with(['site', 'assignee', 'product'])
            ->escalated()
            ->latest('escalated_at')
            ->get();

        return [
            'tickets' => HubViewData::tickets($tickets),
            'statuses' => HubViewData::statusLabels(),
            'priorities' => HubViewData::priorityLabels(),
            'count' => $tickets->count(),
        ];
    }
}
