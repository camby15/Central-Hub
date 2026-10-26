<?php

namespace App\Filament\Pages;

use App\Support\DemoData;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

class ManageSites extends Page
{
    protected static string|UnitEnum|null $navigationGroup = 'Sites';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static ?string $navigationLabel = 'Sites';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'sites';

    protected string $view = 'filament.pages.manage-sites';

    public function getTitle(): string|Htmlable
    {
        return 'Sites';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'sites' => DemoData::sites(),
        ];
    }
}
