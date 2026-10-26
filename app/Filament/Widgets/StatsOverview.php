<?php

namespace App\Filament\Widgets;

use App\Support\HubViewData;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $stats = HubViewData::stats(Filament::auth()->user());

        return [
            Stat::make('Open tickets', (string) $stats['open_tickets'])
                ->description('Across accessible sites')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary'),
            Stat::make('Overdue SLA', (string) $stats['overdue_sla'])
                ->description('Urgent open tickets')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
            Stat::make('New today', (string) $stats['new_today'])
                ->description('Since midnight')
                ->descriptionIcon('heroicon-m-plus-circle')
                ->color('success'),
            Stat::make('Avg first response', $stats['avg_first_response'])
                ->description('SLA tracking in Phase 2')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
