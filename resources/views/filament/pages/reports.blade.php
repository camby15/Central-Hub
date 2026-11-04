<x-filament-panels::page>
    <div class="space-y-6">
        <p class="text-sm text-sky-300/70">Volume by site, SLA pressure, and satisfaction for your queue.</p>

        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <div class="hub-glass p-4">
                <p class="text-xs font-bold uppercase tracking-wide text-sky-300/70">Open</p>
                <p class="mt-2 font-display text-3xl font-bold text-white">{{ $stats['open_tickets'] }}</p>
            </div>
            <div class="hub-glass p-4">
                <p class="text-xs font-bold uppercase tracking-wide text-sky-300/70">Overdue SLA</p>
                <p class="mt-2 font-display text-3xl font-bold text-white">{{ $stats['overdue_sla'] }}</p>
            </div>
            <div class="hub-glass p-4">
                <p class="text-xs font-bold uppercase tracking-wide text-sky-300/70">Avg first response</p>
                <p class="mt-2 font-display text-3xl font-bold text-white">{{ $stats['avg_first_response'] }}</p>
            </div>
            <div class="hub-glass p-4">
                <p class="text-xs font-bold uppercase tracking-wide text-sky-300/70">CSAT</p>
                <p class="mt-2 font-display text-3xl font-bold text-white">
                    {{ $csat['average'] !== null ? $csat['average'].' / 5' : '—' }}
                </p>
                <p class="mt-1 text-xs text-sky-300/70">{{ $csat['scored'] }} scored</p>
            </div>
        </div>

        <div class="grid gap-4 xl:grid-cols-2">
            <div class="hub-glass p-4">
                <h2 class="font-display text-base font-bold text-white">Open by site</h2>
                <ul class="mt-4 space-y-3">
                    @forelse ($bySite as $row)
                        <li class="rounded-xl border border-sky-500/20 bg-slate-950/40 p-3">
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="font-bold text-sky-50">{{ $row['site'] }}</span>
                                <span class="text-sky-300/70">{{ $row['open'] }} open</span>
                            </div>
                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-800">
                                <div class="h-full rounded-full bg-sky-400" style="width: {{ min(100, (int) round(($row['open'] / $openTotal) * 100)) }}%"></div>
                            </div>
                            @if ($row['overdue'] > 0)
                                <p class="mt-1 text-xs font-semibold text-rose-300">{{ $row['overdue'] }} overdue</p>
                            @endif
                        </li>
                    @empty
                        <li class="text-sm text-sky-300/70">No sites yet.</li>
                    @endforelse
                </ul>
            </div>

            <div class="space-y-4">
                <div class="hub-glass p-4">
                    <h2 class="font-display text-base font-bold text-white">Status mix</h2>
                    <ul class="mt-4 space-y-2 text-sm">
                        @foreach ($byStatus as $row)
                            <li class="flex items-center justify-between gap-3">
                                <span class="text-sky-100">{{ $row['label'] }}</span>
                                <span class="text-sky-300/70">{{ $row['count'] }} ({{ $row['percent'] }}%)</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="hub-glass p-4">
                    <h2 class="font-display text-base font-bold text-white">CSAT distribution</h2>
                    <ul class="mt-4 space-y-2 text-sm">
                        @foreach ($csat['distribution'] as $score => $count)
                            <li class="flex items-center justify-between gap-3">
                                <span class="text-sky-100">{{ $score }} star{{ $score === 1 ? '' : 's' }}</span>
                                <span class="text-sky-300/70">{{ $count }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
