<x-filament-panels::page>
    <div class="space-y-4">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <p class="text-sm text-sky-300/70">Manage the client organisations and apps you support.</p>
            <button
                type="button"
                class="rounded-xl bg-gradient-to-b from-sky-300 to-sky-500 px-3 py-2 text-sm font-bold text-slate-950 shadow-[0_0_20px_rgba(0,180,255,0.35)]"
            >
                + New
            </button>
        </div>

        <div class="hub-glass p-3">
            <label class="sr-only" for="site-search">Search sites</label>
            <input
                id="site-search"
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Search companies..."
                class="w-full rounded-lg border border-sky-500/30 bg-slate-950/70 text-sm text-sky-50 placeholder:text-sky-400/50"
            >
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($sites as $site)
                <a
                    href="{{ \App\Filament\Pages\ViewSite::getUrl(['site' => $site['id']]) }}"
                    class="hub-glass block p-4 transition hover:border-sky-400/40 hover:shadow-[0_0_24px_rgba(0,180,255,0.12)]"
                    wire:key="site-card-{{ $site['id'] }}"
                >
                    <div class="flex items-start gap-3">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-sky-500/30 bg-sky-500/10 text-xs font-bold text-sky-100"
                            style="color: {{ $site['primary_color'] }}"
                        >
                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($site['logo_label'], 0, 2)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="truncate font-bold text-sky-50">{{ $site['name'] }}</h3>
                                <span class="inline-flex shrink-0 items-center gap-1 text-xs font-semibold {{ $site['is_active'] ? 'text-emerald-300' : 'text-sky-400/70' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $site['is_active'] ? 'bg-emerald-400' : 'bg-sky-500/50' }}"></span>
                                    {{ $site['is_active'] ? 'Active' : 'Off' }}
                                </span>
                            </div>
                            <p class="mt-2 truncate text-xs text-sky-300/70">
                                {{ $site['contact_email'] ?: $site['url'] }}
                            </p>
                            @if ($site['contact_phone'])
                                <p class="mt-1 truncate text-xs text-sky-300/70">{{ $site['contact_phone'] }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between border-t border-sky-500/15 pt-3 text-[11px] font-bold uppercase tracking-wide text-sky-300/70">
                        <span>{{ $site['products_count'] }} {{ \Illuminate\Support\Str::plural('product', $site['products_count']) }}</span>
                        <span>{{ $site['tickets_count'] }} {{ \Illuminate\Support\Str::plural('ticket', $site['tickets_count']) }}</span>
                    </div>
                </a>
            @empty
                <p class="text-sm text-sky-300/70 sm:col-span-2 xl:col-span-3">No sites match your search.</p>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>
