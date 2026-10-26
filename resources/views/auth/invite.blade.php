@extends('layouts.auth')

@section('title', 'Accept invite')

@section('content')
    <h1 class="font-display text-xl font-semibold tracking-tight">Accept your invite</h1>
    <p class="mt-1 text-sm" style="color: var(--hub-muted);">
        {{ $inviter }} invited you as <strong style="color: var(--hub-text);">{{ $role }}</strong>. This link expires in 72 hours.
    </p>

    <form method="POST" action="{{ route('invite.store', ['token' => $token]) }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="email" class="mb-1.5 block text-sm font-medium">Email</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center" style="color: var(--hub-muted);" aria-hidden="true">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </span>
                <input id="email" type="email" value="{{ $email }}" disabled class="hub-input opacity-70" aria-describedby="email-help">
            </div>
            <p id="email-help" class="mt-1 text-xs" style="color: var(--hub-muted);">Set by your Admin — you cannot change it.</p>
        </div>

        <div>
            <label for="name" class="mb-1.5 block text-sm font-medium">Full name <span aria-hidden="true">*</span></label>
            <input id="name" name="name" type="text" required value="{{ old('name') }}" class="hub-input !pl-3" aria-required="true">
        </div>

        <div>
            <label for="password" class="mb-1.5 block text-sm font-medium">Password <span aria-hidden="true">*</span></label>
            <input id="password" name="password" type="password" required minlength="8" class="hub-input !pl-3" aria-required="true">
        </div>

        <div>
            <label for="password_confirmation" class="mb-1.5 block text-sm font-medium">Confirm password <span aria-hidden="true">*</span></label>
            <input id="password_confirmation" name="password_confirmation" type="password" required class="hub-input !pl-3" aria-required="true">
        </div>

        <button type="submit" class="hub-btn-primary w-full">Create account &amp; continue</button>
    </form>
@endsection
