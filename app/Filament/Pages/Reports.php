<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

class Reports extends Page
{
    protected static string|UnitEnum|null $navigationGroup = 'Insights';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Reports';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'reports';

    protected string $view = 'filament.pages.reports';

    public function getTitle(): string|Htmlable
    {
        return 'Reports';
    }
}
