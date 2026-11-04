<x-filament-widgets::widget>
    <div class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-sky-300/70">
                {{ $stats['unassigned'] }} unassigned · {{ $stats['overdue_sla'] }} past SLA
                @if (($stats['avg_first_response'] ?? '—') !== '—')
                    · avg first response {{ $stats['avg_first_response'] }}
                @endif
            </p>
            <div class="inline-flex rounded-full border border-sky-500/25 bg-slate-950/50 p-1 text-xs font-bold">
                @foreach (['7' => '7 days', '30' => '30 days', 'quarter' => 'Quarter'] as $key => $label)
                    <button
                        type="button"
                        wire:click="setRange('{{ $key }}')"
                        @class([
                            'rounded-full px-3 py-1 transition',
                            'bg-sky-500/25 text-sky-50' => $range === $key,
                            'text-sky-300/70 hover:text-sky-100' => $range !== $key,
                        ])
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @php
                $cards = [
                    ['label' => 'Total tickets', 'value' => $stats['total_in_range'], 'meta' => ($stats['total_delta_percent'] >= 0 ? '+' : '').$stats['total_delta_percent'].'% vs prior', 'tone' => 'sky'],
                    ['label' => 'Open', 'value' => $stats['open_tickets'], 'meta' => 'Currently open', 'tone' => 'sky'],
                    ['label' => 'In progress', 'value' => $stats['in_progress'], 'meta' => 'Being worked', 'tone' => 'sky'],
                    ['label' => 'Overdue', 'value' => $stats['overdue_sla'], 'meta' => 'Past SLA across the queue', 'tone' => 'rose'],
                ];
            @endphp
            @foreach ($cards as $card)
                <div class="hub-glass p-4">
                    <div class="flex items-center gap-2">
                        <span @class([
                            'h-2 w-2 rounded-full',
                            'bg-sky-400 shadow-[0_0_10px_rgba(56,189,248,0.8)]' => $card['tone'] === 'sky',
                            'bg-rose-400 shadow-[0_0_10px_rgba(251,113,133,0.8)]' => $card['tone'] === 'rose',
                        ])></span>
                        <p class="text-xs font-bold uppercase tracking-wide text-sky-300/70">{{ $card['label'] }}</p>
                    </div>
                    <p class="mt-3 font-display text-3xl font-bold text-white">{{ $card['value'] }}</p>
                    <p class="mt-1 text-xs text-sky-300/70">{{ $card['meta'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid gap-4 xl:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">
            <div class="hub-glass p-4">
                <h3 class="font-display text-sm font-bold text-white">Ticket overview</h3>
                <p class="mt-1 text-xs text-sky-300/70">New tickets raised per day.</p>
                <div class="mt-6 flex h-40 items-end gap-1.5">
                    @foreach ($volume as $point)
                        <div class="flex min-w-0 flex-1 flex-col items-center justify-end gap-2">
                            <div
                                class="w-full rounded-t bg-sky-400/80 shadow-[0_0_12px_rgba(56,189,248,0.35)]"
                                style="height: {{ max(4, (int) round(($point['count'] / $maxVolume) * 100)) }}%"
                                title="{{ $point['count'] }}"
                            ></div>
                            <span class="truncate text-[10px] font-semibold text-sky-400/70">{{ $point['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="hub-glass p-4">
                <h3 class="font-display text-sm font-bold text-white">Tickets by status</h3>
                <ul class="mt-4 space-y-3">
                    @foreach ($by_status as $row)
                        <li class="flex items-center justify-between gap-3 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-sky-400"></span>
                                <span class="font-semibold text-sky-100">{{ $row['label'] }}</span>
                            </div>
                            <span class="text-sky-300/70">{{ $row['count'] }} <span class="text-xs">({{ $row['percent'] }}%)</span></span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="hub-glass p-4">
            <h3 class="font-display text-sm font-bold text-white">Activity</h3>
            <ul class="mt-3 divide-y divide-sky-500/15">
                @forelse ($activity as $item)
                    <li class="flex items-start justify-between gap-3 py-3">
                        <div class="min-w-0">
                            @if ($item['ticket_id'])
                                <a href="{{ \App\Filament\Pages\ViewTicket::getUrl(['ticket' => $item['ticket_id']]) }}" class="truncate font-semibold text-sky-50 hover:underline">
                                    {{ $item['title'] }}
                                </a>
                            @else
                                <p class="truncate font-semibold text-sky-50">{{ $item['title'] }}</p>
                            @endif
                            <p class="text-xs text-sky-300/70">{{ $item['meta'] }}</p>
                        </div>
                        <span class="shrink-0 text-[11px] font-bold uppercase tracking-wide text-sky-400/70">{{ $item['at'] }}</span>
                    </li>
                @empty
                    <li class="py-6 text-sm text-sky-300/70">No recent activity.</li>
                @endforelse
            </ul>
        </div>
    </div>
</x-filament-widgets::widget>
