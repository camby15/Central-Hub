<x-filament-panels::page>
    <div class="space-y-4">
        <p class="text-sm text-sky-300/70">
            Hot pile — tickets flagged for escalation. {{ $count }} currently escalated.
        </p>

        <div class="hub-glass overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-sky-500/15 text-sm">
                    <thead class="bg-slate-950/50">
                        <tr class="text-left text-xs font-bold uppercase tracking-[0.08em] text-sky-300/80">
                            <th class="px-3 py-3">Title</th>
                            <th class="px-3 py-3">Product</th>
                            <th class="px-3 py-3">Status</th>
                            <th class="px-3 py-3">Priority</th>
                            <th class="px-3 py-3">SLA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sky-500/10">
                        @forelse ($tickets as $ticket)
                            <tr class="hover:bg-sky-500/5" wire:key="escalation-{{ $ticket['id'] }}">
                                <td class="max-w-sm px-3 py-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ \App\Filament\Pages\ViewTicket::getUrl(['ticket' => $ticket['id']]) }}" class="font-bold text-sky-50 hover:underline">
                                            {{ $ticket['subject'] }}
                                        </a>
                                        <span class="inline-flex items-center rounded-md border border-rose-400/40 bg-rose-500/15 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-rose-200">
                                            Escalated
                                        </span>
                                    </div>
                                    <p class="mt-0.5 font-mono text-[11px] text-sky-400/70">{{ $ticket['number'] }}</p>
                                </td>
                                <td class="px-3 py-3 text-sky-200/80">{{ $ticket['product'] ?? $ticket['site'] }}</td>
                                <td class="px-3 py-3">
                                    <x-hub.status-chip :status="$ticket['status']" :labels="$statuses" />
                                </td>
                                <td class="px-3 py-3">
                                    <x-hub.priority-chip :priority="$ticket['priority']" :labels="$priorities" />
                                </td>
                                <td class="px-3 py-3">
                                    <x-hub.sla-bar :percent="$ticket['sla_percent']" :label="$ticket['sla_label']" :state="$ticket['sla_state']" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-10 text-center text-sm text-sky-300/70">No escalated tickets.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
