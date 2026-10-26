@extends('layouts.auth')

@section('title', 'Sign in')

@section('content')
    <h1 class="font-display text-3xl font-bold tracking-tight text-white">Sign in</h1>
    <p class="mt-2 text-sm font-semibold text-sky-300/80">Use the email from your staff invite.</p>

    @if (session('status'))
        <div class="mt-4 rounded-lg px-3 py-2 text-sm" style="background: color-mix(in srgb, var(--hub-success) 12%, transparent); color: var(--hub-success);" role="status">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mt-4 rounded-lg px-3 py-2 text-sm" style="background: color-mix(in srgb, var(--hub-danger) 12%, transparent); color: var(--hub-danger);" role="alert">
            <p class="font-medium">Fix the following:</p>
            <ul class="mt-1 list-disc pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4" novalidate>
        @csrf

        <div>
            <label for="email" class="mb-1.5 block text-sm font-bold">Email <span class="sr-only">(required)</span></label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center" style="color: var(--hub-muted);" aria-hidden="true">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </span>
                <input id="email" name="email" type="email" autocomplete="username" required value="{{ old('email', 'admin@centralhub.test') }}"
                       class="hub-input" aria-required="true" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}">
            </div>
        </div>

        <div>
            <div class="mb-1.5 flex items-center justify-between">
                <label for="password" class="block text-sm font-bold">Password</label>
                <a href="{{ route('password.request') }}" class="text-sm font-medium" style="color: var(--hub-primary);">Forgot password?</a>
            </div>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center" style="color: var(--hub-muted);" aria-hidden="true">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </span>
                <input id="password" name="password" type="password" autocomplete="current-password" required
                       class="hub-input" value="password" aria-required="true">
            </div>
        </div>

        <label class="flex items-center gap-2 text-sm" style="color: var(--hub-muted);">
            <input type="checkbox" name="remember" value="1" class="rounded border" style="accent-color: var(--hub-primary);">
            Remember me on this device
        </label>

        <button type="submit" class="hub-btn-primary w-full">Sign in</button>
    </form>

    <p class="mt-6 text-center text-sm" style="color: var(--hub-muted);">
        Have an invite?
        <a href="{{ route('invite.show', ['token' => 'demo-invite']) }}" class="font-medium" style="color: var(--hub-primary);">Accept invitation</a>
    </p>
@endsection
