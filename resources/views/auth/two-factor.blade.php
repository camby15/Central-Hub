@extends('layouts.auth')

@section('title', 'Set up 2FA')

@section('content')
    <h1 class="font-display text-xl font-semibold tracking-tight">Protect your account</h1>
    <p class="mt-1 text-sm" style="color: var(--hub-muted);">
        Admin and Super Admin roles require TOTP two-factor authentication.
    </p>

    <div class="mt-6 flex flex-col items-center gap-4 rounded-lg border p-4" style="border-color: var(--hub-border);">
        <div class="flex h-36 w-36 items-center justify-center rounded-lg border border-dashed text-xs" style="border-color: var(--hub-border); color: var(--hub-muted);" aria-hidden="true">
            QR code placeholder
        </div>
        <p class="text-center text-xs" style="color: var(--hub-muted);">
            Scan with your authenticator app, then enter the 6-digit code.
        </p>
        <code class="rounded px-2 py-1 font-mono text-xs" style="background: var(--hub-bg);">CHUB-DEMO-SECRET</code>
    </div>

    <form method="POST" action="{{ route('two-factor.store') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label for="code" class="mb-1.5 block text-sm font-medium">Authentication code <span aria-hidden="true">*</span></label>
            <input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required
                   class="hub-input !pl-3 tracking-[0.3em]" placeholder="000000" aria-required="true" value="123456">
        </div>
        <button type="submit" class="hub-btn-primary w-full">Enable 2FA &amp; open dashboard</button>
    </form>
@endsection
