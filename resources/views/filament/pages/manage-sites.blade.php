<x-filament-panels::page>
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-sky-300/70">Each site gets an API key, embed code, and optional portal branding.</p>
        <button type="button" class="rounded-xl bg-gradient-to-b from-sky-300 to-sky-500 px-3 py-2 text-sm font-bold text-slate-950 shadow-[0_0_20px_rgba(0,180,255,0.35)]">Add site</button>
    </div>

    <div class="hub-glass overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-sky-300/70 dark:bg-gray-800/80">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Slug</th>
                    <th class="px-4 py-3">Framework</th>
                    <th class="px-4 py-3">Key prefix</th>
                    <th class="px-4 py-3">Last seen</th>
                    <th class="px-4 py-3">Active</th>
                    <th class="px-4 py-3"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-sky-500/10">
                @foreach ($sites as $site)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-4 py-3 font-medium">
                            <a href="{{ \App\Filament\Pages\ViewSite::getUrl(['site' => $site['id']]) }}" class="text-sky-300 hover:underline">
                                {{ $site['name'] }}
                            </a>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $site['slug'] }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-md border border-sky-500/25 bg-sky-500/10 px-2 py-0.5 text-xs font-bold capitalize text-sky-100">{{ $site['framework'] }}</span>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-sky-300/70">{{ $site['api_key_prefix'] }}…</td>
                        <td class="px-4 py-3 text-sky-300/70">{{ $site['last_seen_at'] }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'hub-chip',
                                'border border-emerald-400/40 bg-emerald-500/15 text-emerald-200' => $site['is_active'],
                                'border border-sky-500/20 bg-slate-950/50 text-sky-300' => ! $site['is_active'],
                            ])>
                                {{ $site['is_active'] ? 'Active' : 'Off' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ \App\Filament\Pages\ViewSite::getUrl(['site' => $site['id']]) }}" class="font-medium text-sky-300 hover:underline">Manage</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
