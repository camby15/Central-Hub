<?php

namespace App\Filament\Pages;

use App\Support\HubDashboardData;
use App\Support\HubViewData;
use Filament\Facades\Filament;
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

    public function getHeading(): string|Htmlable
    {
        return 'Reports';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $user = Filament::auth()->user();
        $stats = HubViewData::stats($user);
        $bySite = HubViewData::ticketsBySite($user);
        $csat = HubDashboardData::csatSummary($user);
        $byStatus = HubDashboardData::statusBreakdown($user);

        $openTotal = max(1, collect($bySite)->sum('open'));

        return [
            'stats' => $stats,
            'bySite' => $bySite,
            'csat' => $csat,
            'byStatus' => $byStatus,
            'openTotal' => $openTotal,
        ];
    }
}
