<x-filament-panels::page>
    <div class="mb-4">
        <a href="{{ \App\Filament\Pages\ManageSites::getUrl() }}" class="text-sm font-medium text-sky-300 hover:underline">← Back to sites</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="hub-glass p-5">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-sky-300/70">Overview</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-sky-300/70">URL</dt><dd class="font-medium break-all">{{ $site['url'] }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-sky-300/70">Slug</dt><dd class="font-mono">{{ $site['slug'] }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-sky-300/70">Framework</dt><dd class="capitalize">{{ $site['framework'] }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-sky-300/70">Last seen</dt><dd>{{ $site['last_seen_at'] }}</dd></div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-sky-300/70">Active</dt>
                    <dd>
                        <label class="inline-flex cursor-pointer items-center gap-2">
                            <input type="checkbox" class="rounded border-gray-300 text-primary-600" @checked($site['is_active']) aria-label="Site active">
                            <span>{{ $site['is_active'] ? 'On' : 'Off' }}</span>
                        </label>
                    </dd>
                </div>
            </dl>
        </section>

        <section class="hub-glass p-5">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-sky-300/70">API key</h2>
            <p class="mt-2 text-sm text-sky-300/70">Prefix shown for identification. Full secret is shown once on rotate.</p>
            <div class="mt-4 flex flex-wrap items-center gap-3">
                <code class="rounded-lg border border-sky-500/25 bg-slate-950/70 px-3 py-2 font-mono text-sm text-sky-100">{{ $site['api_key_prefix'] }}_••••••••</code>
                <button type="button" wire:click="rotateKey" class="rounded-lg border border-sky-500/30 px-3 py-2 text-sm font-bold text-sky-100 hover:bg-sky-500/10">Rotate key</button>
            </div>
            @if ($revealedKey)
                <div class="mt-4 rounded-lg border border-amber-400/40 bg-amber-500/10 p-3 text-sm text-amber-100">
                    <p class="font-bold">New API token (copy now):</p>
                    <code class="mt-2 block break-all font-mono text-xs">{{ $revealedKey }}</code>
                </div>
            @endif
        </section>

        <section class="hub-glass p-5 lg:col-span-2">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-sky-300/70">Embed code</h2>
            <p class="mt-2 text-sm text-sky-300/70">Paste before <code>&lt;/body&gt;</code> on the site.</p>
            <pre class="mt-3 overflow-x-auto rounded-lg bg-gray-950 p-4 text-xs text-gray-100"><code>{{ $embed }}</code></pre>
            <button type="button" class="mt-3 rounded-xl bg-gradient-to-b from-sky-300 to-sky-500 px-3 py-2 text-sm font-bold text-slate-950 shadow-[0_0_20px_rgba(0,180,255,0.35)]">Copy embed code</button>
        </section>

        <section class="hub-glass p-5">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-sky-300/70">Allowed origins</h2>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($site['allowed_origins'] as $origin)
                    <span class="rounded-md border border-sky-500/25 bg-sky-500/10 px-2 py-1 font-mono text-xs text-sky-100">{{ $origin }}</span>
                @endforeach
            </div>
            <div class="mt-4 flex gap-2">
                <input type="url" placeholder="https://…" class="flex-1 rounded-lg border-sky-500/30 bg-slate-950/70 text-sm text-sky-50" aria-label="Add origin">
                <button type="button" class="rounded-lg border border-sky-500/30 px-3 py-2 text-sm font-bold text-sky-100">Add</button>
            </div>
        </section>

        <section class="hub-glass p-5">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-sky-300/70">Portal branding</h2>
            <div class="mt-4 space-y-4">
                <div>
                    <label for="logo_label" class="mb-1.5 block text-sm font-medium">Logo label</label>
                    <input id="logo_label" type="text" value="{{ $site['logo_label'] }}" class="w-full rounded-lg border-sky-500/30 bg-slate-950/70 text-sm text-sky-50">
                </div>
                <div>
                    <label for="primary_color" class="mb-1.5 block text-sm font-medium">Primary colour</label>
                    <div class="flex items-center gap-3">
                        <input id="primary_color" type="color" value="{{ $site['primary_color'] }}" class="h-10 w-14 cursor-pointer rounded border border-sky-500/30" aria-label="Primary colour picker">
                        <input type="text" value="{{ $site['primary_color'] }}" class="flex-1 rounded-lg border border-sky-500/30 bg-slate-950/70 font-mono text-sm text-sky-50" aria-label="Primary colour hex">
                    </div>
                </div>
                <a href="{{ route('portal.show', ['slug' => strtolower($site['slug'])]) }}" class="inline-flex text-sm font-medium text-sky-300 hover:underline">
                    Preview customer portal →
                </a>
            </div>
        </section>
    </div>
</x-filament-panels::page>
