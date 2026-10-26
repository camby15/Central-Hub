<?php

namespace App\Filament\Widgets;

use App\Support\DemoData;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $stats = DemoData::stats();

        return [
            Stat::make('Open tickets', (string) $stats['open_tickets'])
                ->description('Across all sites')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary'),
            Stat::make('Overdue SLA', (string) $stats['overdue_sla'])
                ->description('Needs attention now')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
            Stat::make('New today', (string) $stats['new_today'])
                ->description('Since midnight Accra')
                ->descriptionIcon('heroicon-m-plus-circle')
                ->color('success'),
            Stat::make('Avg first response', $stats['avg_first_response'])
                ->description('Business hours')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
