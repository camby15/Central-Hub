<?php

namespace App\Filament\Widgets;

use App\Support\DemoData;
use Filament\Widgets\Widget;

class NeedsAttention extends Widget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.needs-attention';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $needsAttention = collect(DemoData::tickets())
            ->filter(fn (array $ticket): bool => in_array($ticket['sla_state'], ['warning', 'danger'], true)
                || $ticket['priority'] === 'urgent'
                || $ticket['status'] === 'new')
            ->values()
            ->all();

        return [
            'tickets' => $needsAttention,
            'bySite' => DemoData::ticketsBySite(),
            'statuses' => DemoData::statusLabels(),
            'priorities' => DemoData::priorityLabels(),
        ];
    }
}
