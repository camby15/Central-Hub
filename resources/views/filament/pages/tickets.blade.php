<x-filament-panels::page>
    <div
        x-data="{
            selected: [],
            showShortcuts: false,
            loading: false,
            toggleAll(checked, ids) {
                this.selected = checked ? [...ids] : [];
            },
            toggle(id) {
                if (this.selected.includes(id)) {
                    this.selected = this.selected.filter(i => i !== id);
                } else {
                    this.selected.push(id);
                }
            }
        }"
        @keydown.window.prevent.slash="$refs.search.focus()"
        @keydown.window.question="showShortcuts = true"
        class="space-y-4"
    >
        <div class="flex flex-wrap items-center gap-2">
            @foreach ($views as $view)
                <a
                    href="{{ \App\Filament\Pages\Tickets::getUrl(['view' => $view['key']]) }}"
                    @class([
                        'rounded-full border px-3 py-1 text-sm font-bold transition',
                        'border-sky-400/60 bg-sky-500/20 text-sky-100 shadow-[0_0_18px_rgba(0,180,255,0.25)]' => ($view['key'] === 'all'),
                        'border-sky-500/20 bg-slate-950/40 text-sky-200/80 hover:border-sky-400/40 hover:bg-sky-500/10' => ($view['key'] !== 'all'),
                    ])
                >
                    {{ $view['label'] }}
                </a>
            @endforeach
            <button
                type="button"
                class="ml-auto inline-flex items-center gap-1 rounded-lg border border-sky-500/30 bg-slate-950/40 px-2.5 py-1 text-xs font-bold text-sky-200 hover:border-sky-400/50 hover:bg-sky-500/10"
                @click="showShortcuts = true"
                aria-haspopup="dialog"
            >
                <span aria-hidden="true">?</span> Shortcuts
            </button>
        </div>

        <div class="hub-glass sticky top-0 z-10 p-3">
            <div class="grid gap-2 md:grid-cols-2 xl:grid-cols-5">
                <label class="sr-only" for="ticket-search">Search tickets</label>
                <input
                    id="ticket-search"
                    x-ref="search"
                    type="search"
                    placeholder="Search number or email…"
                    class="rounded-lg border border-sky-500/30 bg-slate-950/70 text-sm text-sky-50 placeholder:text-sky-400/50 xl:col-span-2"
                >
                <select class="rounded-lg border border-sky-500/30 bg-slate-950/70 text-sm text-sky-50" aria-label="Filter by site">
                    <option value="">All sites</option>
                    @foreach ($sites as $site)
                        <option value="{{ $site['id'] }}">{{ $site['name'] }}</option>
                    @endforeach
                </select>
                <select class="rounded-lg border border-sky-500/30 bg-slate-950/70 text-sm text-sky-50" aria-label="Filter by status">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select class="rounded-lg border border-sky-500/30 bg-slate-950/70 text-sm text-sky-50" aria-label="Filter by priority">
                    <option value="">All priorities</option>
                    @foreach ($priorities as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div
            x-show="selected.length"
            x-cloak
            class="flex flex-wrap items-center gap-3 rounded-xl border border-sky-400/40 bg-sky-500/10 px-4 py-3 text-sm text-sky-100 shadow-[0_0_20px_rgba(0,180,255,0.15)]"
            role="status"
        >
            <span class="font-bold" x-text="selected.length + ' selected'"></span>
            <button type="button" class="rounded-lg border border-sky-400/30 bg-slate-950/60 px-2.5 py-1 font-bold">Assign</button>
            <button type="button" class="rounded-lg border border-sky-400/30 bg-slate-950/60 px-2.5 py-1 font-bold">Close</button>
            <button type="button" class="font-bold text-sky-300" @click="selected = []">Clear</button>
        </div>

        <div class="hub-glass overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-sky-500/15 text-sm">
                    <thead class="bg-slate-950/50">
                        <tr class="text-left text-xs font-bold uppercase tracking-[0.08em] text-sky-300/80">
                            <th class="px-3 py-3">
                                <input
                                    type="checkbox"
                                    class="rounded border-sky-500/40 bg-slate-950"
                                    aria-label="Select all tickets"
                                    @change="toggleAll($event.target.checked, {{ Js::from(collect($tickets)->pluck('id')) }})"
                                >
                            </th>
                            <th class="px-3 py-3">Number</th>
                            <th class="px-3 py-3">Subject</th>
                            <th class="px-3 py-3">Site</th>
                            <th class="px-3 py-3">Status</th>
                            <th class="px-3 py-3">Priority</th>
                            <th class="px-3 py-3">Assignee</th>
                            <th class="px-3 py-3">Age</th>
                            <th class="min-w-36 px-3 py-3">SLA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sky-500/10">
                        @foreach ($tickets as $ticket)
                            <tr class="hover:bg-sky-500/5" wire:key="ticket-{{ $ticket['id'] }}">
                                <td class="px-3 py-3">
                                    <input
                                        type="checkbox"
                                        class="rounded border-sky-500/40 bg-slate-950"
                                        aria-label="Select {{ $ticket['number'] }}"
                                        @change="toggle({{ $ticket['id'] }})"
                                        :checked="selected.includes({{ $ticket['id'] }})"
                                    >
                                </td>
                                <td class="px-3 py-3 font-mono text-xs font-bold text-sky-300">
                                    <a href="{{ \App\Filament\Pages\ViewTicket::getUrl(['ticket' => $ticket['id']]) }}" class="hover:underline">
                                        {{ $ticket['number'] }}
                                    </a>
                                </td>
                                <td class="max-w-xs px-3 py-3">
                                    <a href="{{ \App\Filament\Pages\ViewTicket::getUrl(['ticket' => $ticket['id']]) }}" class="font-bold text-sky-50 hover:underline">
                                        {{ $ticket['subject'] }}
                                    </a>
                                    <p class="truncate text-xs text-sky-300/60">{{ $ticket['customer_email'] }}</p>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="rounded-md border border-sky-500/25 bg-sky-500/10 px-2 py-0.5 text-xs font-bold text-sky-100">{{ $ticket['site'] }}</span>
                                </td>
                                <td class="px-3 py-3">
                                    <x-hub.status-chip :status="$ticket['status']" :labels="$statuses" />
                                </td>
                                <td class="px-3 py-3">
                                    <x-hub.priority-chip :priority="$ticket['priority']" :labels="$priorities" />
                                </td>
                                <td class="px-3 py-3 text-sky-200/80">{{ $ticket['assignee'] ?? 'Unassigned' }}</td>
                                <td class="px-3 py-3 text-sky-300/70">{{ $ticket['age'] }}</td>
                                <td class="px-3 py-3">
                                    <x-hub.sla-bar :percent="$ticket['sla_percent']" :label="$ticket['sla_label']" :state="$ticket['sla_state']" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div
            x-show="showShortcuts"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm"
            role="dialog"
            aria-modal="true"
            aria-labelledby="shortcuts-title"
            @keydown.escape.window="showShortcuts = false"
            @click.self="showShortcuts = false"
        >
            <div class="hub-glass w-full max-w-md p-6">
                <h2 id="shortcuts-title" class="font-display text-lg font-bold text-white">Keyboard shortcuts</h2>
                <dl class="mt-4 space-y-2 text-sm text-sky-100">
                    <div class="flex justify-between gap-4"><dt class="text-sky-300/70">Move</dt><dd><kbd class="rounded border border-sky-500/30 bg-slate-950 px-1.5 py-0.5">j</kbd> / <kbd class="rounded border border-sky-500/30 bg-slate-950 px-1.5 py-0.5">k</kbd></dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-sky-300/70">Select</dt><dd><kbd class="rounded border border-sky-500/30 bg-slate-950 px-1.5 py-0.5">x</kbd></dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-sky-300/70">Assign</dt><dd><kbd class="rounded border border-sky-500/30 bg-slate-950 px-1.5 py-0.5">a</kbd></dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-sky-300/70">Search</dt><dd><kbd class="rounded border border-sky-500/30 bg-slate-950 px-1.5 py-0.5">/</kbd></dd></div>
                </dl>
                <button type="button" class="mt-6 w-full rounded-xl bg-gradient-to-b from-sky-300 to-sky-500 px-3 py-2 text-sm font-bold text-slate-950 shadow-[0_0_20px_rgba(0,180,255,0.35)]" @click="showShortcuts = false">Close</button>
            </div>
        </div>
    </div>
</x-filament-panels::page>
