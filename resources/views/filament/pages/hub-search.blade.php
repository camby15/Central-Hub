<x-filament-panels::page>
    <div class="space-y-6">
        <p class="text-sm text-sky-300/70">Search tickets, sites, or reference numbers.</p>

        <div class="hub-glass p-3">
            <label class="sr-only" for="hub-search">Search</label>
            <input
                id="hub-search"
                type="search"
                wire:model.live.debounce.300ms="q"
                autofocus
                placeholder="Search tickets, clients or reference..."
                class="w-full rounded-lg border border-sky-500/30 bg-slate-950/70 text-sm text-sky-50 placeholder:text-sky-400/50"
            >
        </div>

        @if (! $hasQuery)
            <p class="text-sm text-sky-300/70">Type to search across tickets and sites.</p>
        @else
            <div class="grid gap-6 xl:grid-cols-2">
                <section class="hub-glass p-4">
                    <h2 class="font-display text-sm font-bold text-white">Tickets</h2>
                    <ul class="mt-3 divide-y divide-sky-500/15">
                        @forelse ($tickets as $ticket)
                            <li class="py-3">
                                <a href="{{ \App\Filament\Pages\ViewTicket::getUrl(['ticket' => $ticket['id']]) }}" class="font-bold text-sky-50 hover:underline">
                                    {{ $ticket['subject'] }}
                                </a>
                                <p class="text-xs text-sky-300/70">{{ $ticket['number'] }} · {{ $ticket['site'] }}</p>
                            </li>
                        @empty
                            <li class="py-4 text-sm text-sky-300/70">No tickets found.</li>
                        @endforelse
                    </ul>
                </section>

                <section class="hub-glass p-4">
                    <h2 class="font-display text-sm font-bold text-white">Sites</h2>
                    <ul class="mt-3 divide-y divide-sky-500/15">
                        @forelse ($sites as $site)
                            <li class="py-3">
                                <a href="{{ \App\Filament\Pages\ViewSite::getUrl(['site' => $site['id']]) }}" class="font-bold text-sky-50 hover:underline">
                                    {{ $site['name'] }}
                                </a>
                                <p class="text-xs text-sky-300/70">{{ $site['url'] }}</p>
                            </li>
                        @empty
                            <li class="py-4 text-sm text-sky-300/70">No sites found.</li>
                        @endforelse
                    </ul>
                </section>
            </div>
        @endif
    </div>
</x-filament-panels::page>
