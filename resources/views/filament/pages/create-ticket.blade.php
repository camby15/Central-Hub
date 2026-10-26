<x-filament-panels::page>
    <form class="mx-auto max-w-2xl space-y-5 hub-glass p-6" onsubmit="event.preventDefault(); window.location='{{ \App\Filament\Pages\ViewTicket::getUrl(['ticket' => 1]) }}';">
        <p class="text-sm text-sky-300/70">Used for phone or walk-in requests. Same fields power the widget and portal.</p>

        <div>
            <label for="site_id" class="mb-1.5 block text-sm font-medium">Site <span class="text-danger-600">*</span></label>
            <select id="site_id" name="site_id" required class="w-full rounded-lg border-sky-500/30 bg-slate-950/70 text-sm text-sky-50">
                <option value="">Select a site</option>
                @foreach ($sites as $site)
                    <option value="{{ $site['id'] }}" @selected($site['id'] === 1)>{{ $site['name'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="customer_name" class="mb-1.5 block text-sm font-medium">Customer name <span class="text-danger-600">*</span></label>
                <input id="customer_name" name="customer_name" required maxlength="120" class="w-full rounded-lg border-sky-500/30 bg-slate-950/70 text-sm text-sky-50">
            </div>
            <div>
                <label for="customer_email" class="mb-1.5 block text-sm font-medium">Customer email <span class="text-danger-600">*</span></label>
                <input id="customer_email" name="customer_email" type="email" required class="w-full rounded-lg border-sky-500/30 bg-slate-950/70 text-sm text-sky-50">
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="phone" class="mb-1.5 block text-sm font-medium">Phone <span class="text-sky-400/60">(optional)</span></label>
                <input id="phone" name="phone" type="tel" class="w-full rounded-lg border-sky-500/30 bg-slate-950/70 text-sm text-sky-50">
            </div>
            <div>
                <label for="order_id" class="mb-1.5 block text-sm font-medium">Order / reference <span class="text-sky-400/60">(optional)</span></label>
                <input id="order_id" name="order_id" class="w-full rounded-lg border-sky-500/30 bg-slate-950/70 text-sm text-sky-50">
            </div>
        </div>

        <div>
            <label for="subject" class="mb-1.5 block text-sm font-medium">Subject <span class="text-danger-600">*</span></label>
            <input id="subject" name="subject" required maxlength="150" class="w-full rounded-lg border-sky-500/30 bg-slate-950/70 text-sm text-sky-50">
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="category_id" class="mb-1.5 block text-sm font-medium">Category</label>
                <select id="category_id" name="category_id" class="w-full rounded-lg border-sky-500/30 bg-slate-950/70 text-sm text-sky-50">
                    <option value="">None</option>
                    <option>Order issue</option>
                    <option>Billing</option>
                    <option>Bug</option>
                    <option>Account</option>
                </select>
            </div>
            <div>
                <label for="priority" class="mb-1.5 block text-sm font-medium">Priority</label>
                <select id="priority" name="priority" class="w-full rounded-lg border-sky-500/30 bg-slate-950/70 text-sm text-sky-50">
                    @foreach ($priorities as $key => $label)
                        <option value="{{ $key }}" @selected($key === 'normal')>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label for="body" class="mb-1.5 block text-sm font-medium">Message <span class="text-danger-600">*</span></label>
            <textarea id="body" name="body" required maxlength="10000" rows="6" class="w-full rounded-lg border-sky-500/30 bg-slate-950/70 text-sm text-sky-50"></textarea>
        </div>

        <div>
            <label for="attachments" class="mb-1.5 block text-sm font-medium">Attachments <span class="text-sky-400/60">(up to 5)</span></label>
            <input id="attachments" name="attachments[]" type="file" multiple class="block w-full text-sm">
        </div>

        <div class="flex justify-end gap-3 border-t border-sky-500/15 pt-4">
            <a href="{{ \App\Filament\Pages\Tickets::getUrl() }}" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600 hover:bg-sky-500/10 text-sky-200">Cancel</a>
            <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-400 shadow-[0_0_12px_rgba(0,180,255,0.6)]">Create ticket</button>
        </div>
    </form>
</x-filament-panels::page>
