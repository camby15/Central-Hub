@extends('layouts.auth')

@section('title', 'Choose new password')

@section('content')
    <h1 class="font-display text-xl font-semibold tracking-tight">Choose a new password</h1>
    <p class="mt-1 text-sm" style="color: var(--hub-muted);">Enter your email and a strong new password.</p>

    <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="mb-1.5 block text-sm font-medium">Email <span aria-hidden="true">*</span></label>
            <input id="email" name="email" type="email" required value="{{ old('email') }}" class="hub-input !pl-3" aria-required="true">
        </div>
        <div>
            <label for="password" class="mb-1.5 block text-sm font-medium">New password <span aria-hidden="true">*</span></label>
            <input id="password" name="password" type="password" required minlength="8" class="hub-input !pl-3" aria-required="true">
        </div>
        <div>
            <label for="password_confirmation" class="mb-1.5 block text-sm font-medium">Confirm password <span aria-hidden="true">*</span></label>
            <input id="password_confirmation" name="password_confirmation" type="password" required class="hub-input !pl-3" aria-required="true">
        </div>
        <button type="submit" class="hub-btn-primary w-full">Update password</button>
    </form>
@endsection
