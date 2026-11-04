<x-filament-panels::page>
    <div class="space-y-6">
        <p class="text-sm text-sky-300/70">Label tickets by product under each connected site.</p>

        <form wire:submit="createProduct" class="hub-glass grid gap-3 p-4 md:grid-cols-4">
            <div>
                <label for="product-site" class="mb-1 block text-xs font-bold uppercase tracking-wide text-sky-300/70">Site</label>
                <select id="product-site" wire:model="site_id" class="w-full rounded-lg border border-sky-500/30 bg-slate-950/70 text-sm text-sky-50" required>
                    <option value="">Select site</option>
                    @foreach ($sites as $site)
                        <option value="{{ $site['id'] }}">{{ $site['name'] }}</option>
                    @endforeach
                </select>
                @error('site_id') <p class="mt-1 text-xs text-rose-300">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label for="product-name" class="mb-1 block text-xs font-bold uppercase tracking-wide text-sky-300/70">Product name</label>
                <input id="product-name" type="text" wire:model="name" maxlength="120" class="w-full rounded-lg border border-sky-500/30 bg-slate-950/70 text-sm text-sky-50" required>
                @error('name') <p class="mt-1 text-xs text-rose-300">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full rounded-xl bg-gradient-to-b from-sky-300 to-sky-500 px-3 py-2 text-sm font-bold text-slate-950 shadow-[0_0_20px_rgba(0,180,255,0.35)]">
                    + New product
                </button>
            </div>
        </form>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($products as $product)
                <article class="hub-glass p-4" wire:key="product-{{ $product['id'] }}">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-sky-50">{{ $product['name'] }}</h3>
                            <p class="mt-1 text-xs text-sky-300/70">{{ $product['site'] }}</p>
                        </div>
                        <span @class([
                            'hub-chip text-xs',
                            'border border-emerald-400/40 bg-emerald-500/15 text-emerald-200' => $product['is_active'],
                            'border border-sky-500/20 bg-slate-950/50 text-sky-300' => ! $product['is_active'],
                        ])>
                            {{ $product['is_active'] ? 'Active' : 'Off' }}
                        </span>
                    </div>
                    <div class="mt-4 flex items-center justify-between border-t border-sky-500/15 pt-3 text-xs font-bold uppercase tracking-wide text-sky-300/70">
                        <span>{{ $product['tickets_count'] }} {{ \Illuminate\Support\Str::plural('ticket', $product['tickets_count']) }}</span>
                        <span class="font-mono normal-case tracking-normal">{{ $product['slug'] }}</span>
                    </div>
                </article>
            @empty
                <p class="text-sm text-sky-300/70 sm:col-span-2 xl:col-span-3">No products yet. Create one above.</p>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>
