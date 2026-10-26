@extends('layouts.auth')

@section('title', 'Verify email')

@section('content')
    <h1 class="font-display text-xl font-semibold tracking-tight">Verify your email</h1>
    <p class="mt-1 text-sm" style="color: var(--hub-muted);">
        We sent a verification link to your inbox. You must verify before first login.
    </p>

    <div class="mt-6 rounded-lg border border-dashed p-4 text-sm" style="border-color: var(--hub-border); color: var(--hub-muted);">
        UI demo: verification is simulated. Continue to set up two-factor authentication.
    </div>

    <a href="{{ route('two-factor.show') }}" class="hub-btn-primary mt-6 w-full">Continue to 2FA setup</a>
@endsection
