<x-filament-panels::page>
    <div
        x-data="{
            selected: @entangle('selected'),
            showShortcuts: false,
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
        <p class="text-sm text-sky-300/70">Track, manage and get updates on your support requests.</p>

        <div class="flex flex-wrap items-center gap-2">
            @foreach ($views as $view)
                <button
                    type="button"
                    wire:click="setView('{{ $view['key'] }}')"
                    @class([
                        'rounded-full border px-3 py-1 text-sm font-bold transition',
                        'border-sky-400/60 bg-sky-500/20 text-sky-100 shadow-[0_0_18px_rgba(0,180,255,0.25)]' => $activeView === $view['key'],
                        'border-sky-500/20 bg-slate-950/40 text-sky-200/80 hover:border-sky-400/40 hover:bg-sky-500/10' => $activeView !== $view['key'],
                    ])
                >
                    {{ $view['label'] }}
                    <span class="ml-1 text-xs opacity-70">{{ $viewCounts[$view['key']] ?? 0 }}</span>
                </button>
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

        <div class="flex flex-wrap gap-4 border-b border-sky-500/15 pb-1 text-sm">
            @foreach ($statusTabs as $tab)
                <button
                    type="button"
                    wire:click="setStatus('{{ $tab['key'] }}')"
                    @class([
                        'border-b-2 pb-2 font-semibold transition',
                        'border-sky-400 text-sky-100' => $activeStatus === $tab['key'],
                        'border-transparent text-sky-300/70 hover:text-sky-100' => $activeStatus !== $tab['key'],
                    ])
                >
                    {{ $tab['label'] }}
                    <span class="ml-1 rounded-full bg-sky-500/15 px-1.5 py-0.5 text-xs">{{ $statusCounts[$tab['key']] ?? 0 }}</span>
                </button>
            @endforeach
        </div>

        <div class="hub-glass sticky top-0 z-10 p-3">
            <div class="grid gap-2 md:grid-cols-2 xl:grid-cols-5">
                <label class="sr-only" for="ticket-search">Search tickets</label>
                <input
                    id="ticket-search"
                    x-ref="search"
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Filter by ID, title, product or assignee."
                    class="rounded-lg border border-sky-500/30 bg-slate-950/70 text-sm text-sky-50 placeholder:text-sky-400/50 xl:col-span-2"
                >
                <select wire:model.live="priority" class="rounded-lg border border-sky-500/30 bg-slate-950/70 text-sm text-sky-50" aria-label="Filter by priority">
                    <option value="">All priorities</option>
                    @foreach ($priorities as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select wire:model.live="productId" class="rounded-lg border border-sky-500/30 bg-slate-950/70 text-sm text-sky-50" aria-label="Filter by product">
                    <option value="">All products</option>
                    @foreach ($products as $product)
                        <option value="{{ $product['id'] }}">{{ $product['name'] }}</option>
                    @endforeach
                </select>
                <select wire:model.live="siteId" class="rounded-lg border border-sky-500/30 bg-slate-950/70 text-sm text-sky-50" aria-label="Filter by site">
                    <option value="">All sites</option>
                    @foreach ($sites as $site)
                        <option value="{{ $site['id'] }}">{{ $site['name'] }}</option>
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
            <button type="button" wire:click="bulkClose" class="rounded-lg border border-sky-400/30 bg-slate-950/60 px-2.5 py-1 font-bold">Close</button>
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
                            <th class="px-3 py-3">Title</th>
                            <th class="px-3 py-3">Product</th>
                            <th class="px-3 py-3">Status</th>
                            <th class="px-3 py-3">Priority</th>
                            <th class="px-3 py-3">Assignee</th>
                            <th class="px-3 py-3">Created</th>
                            <th class="min-w-36 px-3 py-3">SLA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sky-500/10">
                        @forelse ($tickets as $ticket)
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
                                <td class="max-w-sm px-3 py-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ \App\Filament\Pages\ViewTicket::getUrl(['ticket' => $ticket['id']]) }}" class="font-bold text-sky-50 hover:underline">
                                            {{ $ticket['subject'] }}
                                        </a>
                                        @if ($ticket['is_escalated'])
                                            <span class="inline-flex items-center gap-1 rounded-md border border-rose-400/40 bg-rose-500/15 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-rose-200">
                                                Escalated
                                            </span>
                                        @endif
                                    </div>
                                    <p class="mt-0.5 font-mono text-[11px] text-sky-400/70">{{ $ticket['number'] }}</p>
                                    <p class="truncate text-xs text-sky-300/60">{{ $ticket['snippet'] }}</p>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="rounded-md border border-sky-500/25 bg-sky-500/10 px-2 py-0.5 text-xs font-bold text-sky-100">
                                        {{ $ticket['product'] ?? $ticket['site'] }}
                                    </span>
                                </td>
                                <td class="px-3 py-3">
                                    <x-hub.status-chip :status="$ticket['status']" :labels="$statuses" />
                                </td>
                                <td class="px-3 py-3">
                                    <x-hub.priority-chip :priority="$ticket['priority']" :labels="$priorities" />
                                </td>
                                <td class="px-3 py-3 text-sky-200/80">{{ $ticket['assignee'] ?? 'Unassigned' }}</td>
                                <td class="px-3 py-3 text-sky-300/70">{{ $ticket['created_date'] }}</td>
                                <td class="px-3 py-3">
                                    <x-hub.sla-bar :percent="$ticket['sla_percent']" :label="$ticket['sla_label']" :state="$ticket['sla_state']" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-10 text-center text-sm text-sky-300/70">No tickets match these filters.</td>
                            </tr>
                        @endforelse
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
                    <div class="flex justify-between gap-4"><dt class="text-sky-300/70">Search</dt><dd><kbd class="rounded border border-sky-500/30 bg-slate-950 px-1.5 py-0.5">/</kbd></dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-sky-300/70">Close panel</dt><dd><kbd class="rounded border border-sky-500/30 bg-slate-950 px-1.5 py-0.5">esc</kbd></dd></div>
                </dl>
                <button type="button" class="mt-6 w-full rounded-xl bg-gradient-to-b from-sky-300 to-sky-500 px-3 py-2 text-sm font-bold text-slate-950 shadow-[0_0_20px_rgba(0,180,255,0.35)]" @click="showShortcuts = false">Close</button>
            </div>
        </div>
    </div>
</x-filament-panels::page>
