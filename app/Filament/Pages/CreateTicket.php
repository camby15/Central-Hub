<?php

namespace App\Filament\Pages;

use App\Support\DemoData;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

class CreateTicket extends Page
{
    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPlusCircle;

    protected static ?string $navigationLabel = 'New ticket';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'tickets/create';

    protected string $view = 'filament.pages.create-ticket';

    public function getTitle(): string|Htmlable
    {
        return 'New ticket';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'sites' => DemoData::sites(),
            'priorities' => DemoData::priorityLabels(),
        ];
    }
}
