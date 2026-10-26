@extends('layouts.auth')

@section('title', 'Forgot password')

@section('content')
    <h1 class="font-display text-xl font-semibold tracking-tight">Reset password</h1>
    <p class="mt-1 text-sm" style="color: var(--hub-muted);">We will email a secure link that expires in 60 minutes.</p>

    @if (session('status'))
        <div class="mt-4 rounded-lg px-3 py-2 text-sm" style="background: color-mix(in srgb, var(--hub-success) 12%, transparent); color: var(--hub-success);" role="status">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label for="email" class="mb-1.5 block text-sm font-medium">Email <span aria-hidden="true">*</span></label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center" style="color: var(--hub-muted);" aria-hidden="true">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </span>
                <input id="email" name="email" type="email" required value="{{ old('email') }}" class="hub-input" aria-required="true">
            </div>
        </div>
        <button type="submit" class="hub-btn-primary w-full">Send reset link</button>
    </form>

    <p class="mt-6 text-center text-sm">
        <a href="{{ route('login') }}" class="font-medium" style="color: var(--hub-primary);">Back to sign in</a>
    </p>
@endsection
