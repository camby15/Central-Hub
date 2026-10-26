<x-filament-panels::page>
    <div class="mb-4 rounded-xl border border-amber-400/40 bg-amber-500/10 px-4 py-3 text-sm text-amber-100 shadow-[0_0_18px_rgba(251,191,36,0.15)]" role="status">
        Another agent is viewing this ticket.
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_20rem]">
        <div class="space-y-4">
            <div class="hub-glass p-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="font-mono text-xs font-semibold text-sky-300">{{ $ticket['number'] }}</p>
                        <h2 class="mt-1 text-xl font-semibold tracking-tight text-white">{{ $ticket['subject'] }}</h2>
                        <p class="mt-1 text-sm text-sky-300/70">{{ $ticket['customer_name'] }} · {{ $ticket['customer_email'] }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <x-hub.status-chip :status="$ticket['status']" :labels="$statuses" />
                        <x-hub.priority-chip :priority="$ticket['priority']" :labels="$priorities" />
                    </div>
                </div>
            </div>

            <div class="hub-glass p-4">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-sky-300/70">Conversation</h3>
                <div class="hub-ticket-thread" aria-live="polite">
                    @foreach ($replies as $reply)
                        @php
                            $bubbleClass = $reply['is_internal']
                                ? 'hub-bubble hub-bubble-internal'
                                : ($reply['author_type'] === 'customer' ? 'hub-bubble hub-bubble-customer' : 'hub-bubble hub-bubble-agent');
                        @endphp
                        <article class="{{ $bubbleClass }}">
                            <div class="mb-1 flex flex-wrap items-center gap-2 text-xs">
                                <span class="font-semibold text-sky-50">{{ $reply['author'] }}</span>
                                @if ($reply['is_internal'])
                                    <span class="hub-chip border border-amber-300/50 bg-amber-500/15 text-amber-100">Internal</span>
                                @endif
                                <span class="text-sky-300/70">{{ $reply['created_at'] }}</span>
                            </div>
                            <p class="text-sm leading-relaxed text-sky-100">{{ $reply['body'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>

            <form wire:submit="sendComposer" class="hub-glass p-4">
                <div class="mb-3 flex gap-2" role="tablist" aria-label="Composer mode">
                    <button
                        type="button"
                        role="tab"
                        wire:click="$set('composerMode', 'reply')"
                        @class([
                            'rounded-lg px-3 py-1.5 text-sm font-medium',
                            'bg-gradient-to-b from-sky-300 to-sky-500 text-slate-950' => $this->composerMode === 'reply',
                            'bg-sky-500/10 text-sky-100' => $this->composerMode !== 'reply',
                        ])
                    >
                        Reply
                    </button>
                    <button
                        type="button"
                        role="tab"
                        wire:click="$set('composerMode', 'note')"
                        @class([
                            'rounded-lg px-3 py-1.5 text-sm font-medium',
                            'bg-amber-500 text-white' => $this->composerMode === 'note',
                            'bg-sky-500/10 text-sky-100' => $this->composerMode !== 'note',
                        ])
                    >
                        Internal note
                    </button>
                </div>
                <label for="composer" class="sr-only">Message</label>
                <textarea
                    id="composer"
                    wire:model="composerBody"
                    rows="4"
                    class="w-full rounded-lg border border-sky-500/30 bg-slate-950/70 text-sm text-sky-50"
                    placeholder="{{ $this->composerMode === 'note' ? 'Private note — never sent to the customer' : 'Write a reply to the customer…' }}"
                ></textarea>
                @error('composerBody') <p class="mt-1 text-sm text-rose-300">{{ $message }}</p> @enderror
                <div class="mt-3 flex flex-wrap items-center justify-end gap-3">
                    <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-[0_0_12px_rgba(0,180,255,0.6)] hover:bg-sky-400">
                        {{ $this->composerMode === 'note' ? 'Save note' : 'Send reply' }}
                    </button>
                </div>
            </form>
        </div>

        <aside class="space-y-4">
            <div class="hub-glass p-4">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-sky-300/70">Properties</h3>
                <dl class="mt-3 space-y-3 text-sm">
                    <div>
                        <dt class="text-sky-300/70">Status</dt>
                        <dd class="mt-1">
                            <select
                                class="w-full rounded-lg border border-sky-500/30 bg-slate-950/70 text-sm text-sky-50"
                                aria-label="Status"
                                wire:change="updateStatus($event.target.value)"
                            >
                                @foreach ($statuses as $key => $label)
                                    <option value="{{ $key }}" @selected($ticket['status'] === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sky-300/70">Priority</dt>
                        <dd class="mt-1">
                            <select
                                class="w-full rounded-lg border border-sky-500/30 bg-slate-950/70 text-sm text-sky-50"
                                aria-label="Priority"
                                wire:change="updatePriority($event.target.value)"
                            >
                                @foreach ($priorities as $key => $label)
                                    <option value="{{ $key }}" @selected($ticket['priority'] === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sky-300/70">Assignee</dt>
                        <dd class="mt-1 font-medium">{{ $ticket['assignee'] ?? 'Unassigned' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sky-300/70">Category</dt>
                        <dd class="mt-1 font-medium">{{ $ticket['category'] }}</dd>
                    </div>
                </dl>
                <div class="mt-4">
                    <x-hub.sla-bar :percent="$ticket['sla_percent']" :label="$ticket['sla_label']" :state="$ticket['sla_state']" />
                </div>
            </div>

            <div class="hub-glass p-4">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-sky-300/70">Customer context</h3>
                <dl class="mt-3 space-y-2 text-sm">
                    <div>
                        <dt class="text-sky-300/70">Site</dt>
                        <dd class="font-medium">{{ $ticket['site'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sky-300/70">Page</dt>
                        <dd class="break-all text-sky-300">{{ $ticket['meta']['page'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sky-300/70">Browser</dt>
                        <dd>{{ $ticket['meta']['browser'] }}</dd>
                    </div>
                    @if ($ticket['meta']['order_id'])
                        <div>
                            <dt class="text-sky-300/70">Order ID</dt>
                            <dd class="font-mono">{{ $ticket['meta']['order_id'] }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="hub-glass p-4">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-sky-300/70">Activity</h3>
                <ol class="mt-3 space-y-3 border-l border-sky-500/30 pl-4">
                    @foreach ($activity as $item)
                        <li class="relative text-sm">
                            <span class="absolute -left-[1.15rem] top-1.5 h-2 w-2 rounded-full bg-sky-400 shadow-[0_0_12px_rgba(0,180,255,0.6)]" aria-hidden="true"></span>
                            <p class="font-medium text-sky-50">{{ $item['event'] }}</p>
                            <p class="text-xs text-sky-300/70">{{ $item['actor'] }} · {{ $item['at'] }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </aside>
    </div>
</x-filament-panels::page>
