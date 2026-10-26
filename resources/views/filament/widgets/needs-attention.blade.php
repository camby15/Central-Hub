<x-filament-widgets::widget>
    <div class="hub-glass p-5">
        <div class="grid gap-6 lg:grid-cols-2">
            <div>
                <div class="mb-3 flex items-center gap-3">
                    <span class="hub-glow-icon" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    </span>
                    <div>
                        <h2 class="font-display text-base font-bold text-white">Needs attention</h2>
                        <p class="text-sm text-sky-300/70">Urgent, new, or SLA at risk.</p>
                    </div>
                </div>
                <ul class="mt-2 divide-y divide-sky-500/15">
                    @forelse ($tickets as $ticket)
                        <li class="flex items-start justify-between gap-3 py-3">
                            <div class="min-w-0">
                                <a href="{{ \App\Filament\Pages\ViewTicket::getUrl(['ticket' => $ticket['id']]) }}" class="font-mono text-xs font-bold text-sky-300 hover:underline">
                                    {{ $ticket['number'] }}
                                </a>
                                <p class="truncate text-sm font-bold text-sky-50">{{ $ticket['subject'] }}</p>
                                <div class="mt-1 flex flex-wrap gap-2">
                                    <x-hub.status-chip :status="$ticket['status']" :labels="$statuses" />
                                    <x-hub.priority-chip :priority="$ticket['priority']" :labels="$priorities" />
                                </div>
                            </div>
                            <span class="shrink-0 text-xs font-semibold text-sky-300/70">{{ $ticket['sla_label'] }}</span>
                        </li>
                    @empty
                        <li class="py-6 text-sm text-sky-300/70">Nothing urgent right now.</li>
                    @endforelse
                </ul>
            </div>

            <div>
                <div class="mb-3 flex items-center gap-3">
                    <span class="hub-glow-icon" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                    </span>
                    <div>
                        <h2 class="font-display text-base font-bold text-white">Open by site</h2>
                        <p class="text-sm text-sky-300/70">Where tickets are concentrating.</p>
                    </div>
                </div>
                <ul class="mt-2 space-y-3">
                    @foreach ($bySite as $row)
                        <li class="rounded-xl border border-sky-500/20 bg-slate-950/40 p-3">
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="font-bold text-sky-50">{{ $row['site'] }}</span>
                                <span class="text-sky-300/70">{{ $row['open'] }} open</span>
                            </div>
                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-800">
                                <div class="h-full rounded-full bg-sky-400 shadow-[0_0_12px_rgba(0,180,255,0.6)]" style="width: {{ min(100, $row['open'] * 8) }}%"></div>
                            </div>
                            @if ($row['overdue'] > 0)
                                <p class="mt-1 text-xs font-semibold text-rose-300">{{ $row['overdue'] }} overdue</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
                <div class="mt-4">
                    <a href="{{ \App\Filament\Pages\ManageSites::getUrl() }}" class="text-sm font-bold text-sky-300 hover:underline">Manage sites →</a>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
