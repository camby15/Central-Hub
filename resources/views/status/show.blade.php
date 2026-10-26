@extends('layouts.customer')

@section('title', $ticket['number'].' status')

@section('content')
    @if (session('status'))
        <div class="mb-6 rounded-lg px-4 py-3 text-sm" style="background: color-mix(in srgb, var(--hub-success) 12%, transparent); color: var(--hub-success);" role="status">
            {{ session('status') }}
        </div>
    @endif

    <div class="hub-card p-5 sm:p-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="font-mono text-xs font-semibold" style="color: var(--hub-primary);">{{ $ticket['number'] }}</p>
                <h1 class="font-display mt-1 text-2xl font-semibold tracking-tight">{{ $ticket['subject'] }}</h1>
                <p class="mt-1 text-sm" style="color: var(--hub-muted);">Opened {{ $ticket['created_at'] }}</p>
            </div>
            <x-hub.status-chip :status="$ticket['status']" :labels="$statuses" />
        </div>

        <p class="mt-4 text-sm" style="color: var(--hub-muted);">
            Status shown to you:
            <strong style="color: var(--hub-text);">
                @switch($ticket['status'])
                    @case('waiting_customer') Awaiting your reply @break
                    @case('new') Received @break
                    @default {{ $statuses[$ticket['status']] ?? $ticket['status'] }}
                @endswitch
            </strong>
        </p>
    </div>

    <section class="hub-card mt-6 p-5 sm:p-6" aria-labelledby="timeline-heading">
        <h2 id="timeline-heading" class="font-display text-lg font-semibold">Conversation</h2>
        <ol class="mt-4 space-y-4">
            @foreach ($replies as $reply)
                <li class="rounded-lg border p-4" style="border-color: var(--hub-border); background: {{ $reply['author_type'] === 'customer' ? 'var(--hub-bg)' : 'var(--hub-surface)' }};">
                    <div class="mb-1 flex flex-wrap items-center gap-2 text-xs" style="color: var(--hub-muted);">
                        <span class="font-semibold" style="color: var(--hub-text);">{{ $reply['author'] }}</span>
                        <span>{{ $reply['created_at'] }}</span>
                    </div>
                    <p class="text-sm leading-relaxed">{{ $reply['body'] }}</p>
                </li>
            @endforeach
        </ol>
    </section>

    <section class="hub-card mt-6 p-5 sm:p-6" aria-labelledby="reply-heading">
        <h2 id="reply-heading" class="font-display text-lg font-semibold">Reply</h2>
        <form method="POST" action="{{ route('status.reply', ['token' => $token]) }}" class="mt-4 space-y-4">
            @csrf
            <div>
                <label for="body" class="mb-1.5 block text-sm font-medium">Your message <span aria-hidden="true">*</span></label>
                <textarea id="body" name="body" rows="4" required maxlength="10000" class="hub-input !pl-3" aria-required="true"></textarea>
            </div>
            <button type="submit" class="hub-btn-primary">Send reply</button>
        </form>
    </section>
@endsection
