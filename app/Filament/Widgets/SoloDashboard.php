<?php

namespace App\Filament\Widgets;

use App\Support\HubDashboardData;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;
use Livewire\Attributes\Url;

class SoloDashboard extends Widget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.solo-dashboard';

    #[Url]
    public string $range = '7';

    public function setRange(string $range): void
    {
        if (! in_array($range, ['7', '30', 'quarter'], true)) {
            return;
        }

        $this->range = $range;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $user = Filament::auth()->user();
        $dashboard = HubDashboardData::forUser($user, $this->range);
        $maxVolume = max(1, collect($dashboard['volume'])->max('count') ?? 1);

        return [
            ...$dashboard,
            'maxVolume' => $maxVolume,
        ];
    }
}
