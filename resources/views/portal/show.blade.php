@extends('layouts.customer')

@section('title', 'Support portal')

@section('content')
    <div class="mb-6">
        <h1 class="font-display text-2xl font-semibold tracking-tight">How can we help?</h1>
        <p class="mt-1 text-sm" style="color: var(--hub-muted);">Submit a request, track an existing ticket, or browse help articles.</p>
    </div>

    <div class="mb-6 flex flex-wrap gap-2" role="tablist" aria-label="Portal sections">
        @foreach (['submit' => 'Submit ticket', 'track' => 'Track ticket', 'help' => 'Help'] as $key => $label)
            <a
                href="{{ route('portal.show', ['slug' => strtolower($site['slug']), 'tab' => $key]) }}"
                role="tab"
                aria-selected="{{ $tab === $key ? 'true' : 'false' }}"
                class="rounded-lg px-3 py-2 text-sm font-semibold"
                style="{{ $tab === $key ? 'background: var(--hub-primary); color: #fff;' : 'background: var(--hub-surface); color: var(--hub-text); border: 1px solid var(--hub-border);' }}"
            >
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if ($tab === 'submit')
        <div class="hub-card p-5 sm:p-6">
            <form method="POST" action="{{ route('portal.submit', ['slug' => strtolower($site['slug'])]) }}" class="space-y-4" id="portal-submit-form">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="customer_name" class="mb-1.5 block text-sm font-medium">Your name <span aria-hidden="true">*</span></label>
                        <input id="customer_name" name="customer_name" required class="hub-input !pl-3" aria-required="true">
                    </div>
                    <div>
                        <label for="customer_email" class="mb-1.5 block text-sm font-medium">Email <span aria-hidden="true">*</span></label>
                        <input id="customer_email" name="customer_email" type="email" required class="hub-input !pl-3" aria-required="true">
                    </div>
                </div>
                <div>
                    <label for="subject" class="mb-1.5 block text-sm font-medium">Subject <span aria-hidden="true">*</span></label>
                    <input id="subject" name="subject" required maxlength="150" class="hub-input !pl-3" aria-required="true" aria-describedby="kb-hint">
                    <p id="kb-hint" class="mt-2 hidden text-xs" style="color: var(--hub-muted);" data-kb-hint>
                        Suggested articles: Delivery times · Refunds · Track an order
                    </p>
                </div>
                <div>
                    <label for="body" class="mb-1.5 block text-sm font-medium">Message <span aria-hidden="true">*</span></label>
                    <textarea id="body" name="body" rows="5" required class="hub-input !pl-3" aria-required="true"></textarea>
                </div>
                <button type="submit" class="hub-btn-primary">Submit ticket</button>
            </form>
            <script>
                (() => {
                    const subject = document.getElementById('subject');
                    const hint = document.querySelector('[data-kb-hint]');
                    if (!subject || !hint) return;
                    subject.addEventListener('input', () => {
                        hint.classList.toggle('hidden', subject.value.trim().length <= 3);
                    });
                })();
            </script>
        </div>
    @elseif ($tab === 'track')
        <div class="hub-card p-5 sm:p-6">
            <p class="text-sm" style="color: var(--hub-muted);">No password needed — use your ticket ID and the email you used when submitting.</p>
            <form method="POST" action="{{ route('portal.track', ['slug' => strtolower($site['slug'])]) }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label for="ticket_number" class="mb-1.5 block text-sm font-medium">Ticket ID <span aria-hidden="true">*</span></label>
                    <input id="ticket_number" name="ticket_number" required placeholder="FRG-00042" class="hub-input !pl-3 font-mono" aria-required="true">
                </div>
                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium">Email <span aria-hidden="true">*</span></label>
                    <input id="email" name="email" type="email" required class="hub-input !pl-3" aria-required="true">
                </div>
                <button type="submit" class="hub-btn-primary">Track ticket</button>
            </form>
        </div>
    @else
        <div class="hub-card p-5 sm:p-6">
            <h2 class="font-display text-lg font-semibold">Help articles</h2>
            <p class="mt-1 text-sm" style="color: var(--hub-muted);">Knowledge base placeholders for Phase 2.</p>
            <ul class="mt-4 space-y-3">
                @foreach (['How long does delivery take?', 'Request a refund', 'Change my order address'] as $article)
                    <li>
                        <a href="#" class="block rounded-lg border px-4 py-3 text-sm font-medium hover:opacity-90" style="border-color: var(--hub-border); color: var(--hub-primary);">
                            {{ $article }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection
