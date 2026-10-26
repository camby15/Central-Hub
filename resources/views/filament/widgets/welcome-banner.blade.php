<x-filament-widgets::widget>
    <div class="hub-welcome">
        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-2xl">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-sky-300/80">Central Hub · Operations</p>
                <h2 class="mt-2 font-display text-2xl font-bold tracking-tight text-white sm:text-3xl">
                    Welcome back{{ auth()->user()?->name ? ', '.explode(' ', auth()->user()->name)[0] : '' }}
                </h2>
                <p class="mt-2 text-sm leading-relaxed text-sky-100/80">
                    One control center for tickets, sites, and activity across every connected project.
                    Review open work, clear SLA risks, and keep every site linked.
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a
                    href="{{ \App\Filament\Pages\Tickets::getUrl() }}"
                    class="inline-flex items-center rounded-xl bg-gradient-to-b from-sky-300 to-sky-500 px-4 py-2.5 text-sm font-bold text-slate-950 shadow-[0_0_24px_rgba(0,180,255,0.35)]"
                >
                    Manage tickets
                </a>
                <a
                    href="{{ \App\Filament\Pages\ManageSites::getUrl() }}"
                    class="inline-flex items-center rounded-xl border border-sky-400/40 bg-slate-950/40 px-4 py-2.5 text-sm font-bold text-sky-100 shadow-[0_0_18px_rgba(0,180,255,0.12)] hover:border-sky-300/70"
                >
                    Refresh sites
                </a>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
