<x-filament-panels::page>
    <form wire:submit="create" class="mx-auto max-w-2xl space-y-5 hub-glass p-6">
        <p class="text-sm text-sky-300/70">Used for phone or walk-in requests. Same fields power the widget and portal.</p>

        <div>
            <label for="site_id" class="mb-1.5 block text-sm font-medium">Site <span class="text-danger-600">*</span></label>
            <select id="site_id" wire:model="site_id" required class="w-full rounded-lg border border-sky-500/30 bg-slate-950/70 text-sm text-sky-50">
                <option value="">Select a site</option>
                @foreach ($sites as $site)
                    <option value="{{ $site['id'] }}">{{ $site['name'] }}</option>
                @endforeach
            </select>
            @error('site_id') <p class="mt-1 text-sm text-rose-300">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="customer_name" class="mb-1.5 block text-sm font-medium">Customer name <span class="text-danger-600">*</span></label>
                <input id="customer_name" wire:model="customer_name" required maxlength="120" class="w-full rounded-lg border border-sky-500/30 bg-slate-950/70 text-sm text-sky-50">
                @error('customer_name') <p class="mt-1 text-sm text-rose-300">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="customer_email" class="mb-1.5 block text-sm font-medium">Customer email <span class="text-danger-600">*</span></label>
                <input id="customer_email" wire:model="customer_email" type="email" required class="w-full rounded-lg border border-sky-500/30 bg-slate-950/70 text-sm text-sky-50">
                @error('customer_email') <p class="mt-1 text-sm text-rose-300">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="phone" class="mb-1.5 block text-sm font-medium">Phone <span class="text-sky-400/60">(optional)</span></label>
                <input id="phone" wire:model="phone" type="tel" class="w-full rounded-lg border border-sky-500/30 bg-slate-950/70 text-sm text-sky-50">
            </div>
            <div>
                <label for="order_id" class="mb-1.5 block text-sm font-medium">Order / reference <span class="text-sky-400/60">(optional)</span></label>
                <input id="order_id" wire:model="order_id" class="w-full rounded-lg border border-sky-500/30 bg-slate-950/70 text-sm text-sky-50">
            </div>
        </div>

        <div>
            <label for="subject" class="mb-1.5 block text-sm font-medium">Subject <span class="text-danger-600">*</span></label>
            <input id="subject" wire:model="subject" required maxlength="150" class="w-full rounded-lg border border-sky-500/30 bg-slate-950/70 text-sm text-sky-50">
            @error('subject') <p class="mt-1 text-sm text-rose-300">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="priority" class="mb-1.5 block text-sm font-medium">Priority</label>
            <select id="priority" wire:model="priority" class="w-full rounded-lg border border-sky-500/30 bg-slate-950/70 text-sm text-sky-50">
                @foreach ($priorities as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="body" class="mb-1.5 block text-sm font-medium">Message <span class="text-danger-600">*</span></label>
            <textarea id="body" wire:model="body" required maxlength="10000" rows="6" class="w-full rounded-lg border border-sky-500/30 bg-slate-950/70 text-sm text-sky-50"></textarea>
            @error('body') <p class="mt-1 text-sm text-rose-300">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end gap-3 border-t border-sky-500/15 pt-4">
            <a href="{{ \App\Filament\Pages\Tickets::getUrl() }}" class="rounded-lg px-4 py-2 text-sm font-medium text-sky-200 hover:bg-sky-500/10">Cancel</a>
            <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-[0_0_12px_rgba(0,180,255,0.6)] hover:bg-sky-400">Create ticket</button>
        </div>
    </form>
</x-filament-panels::page>
