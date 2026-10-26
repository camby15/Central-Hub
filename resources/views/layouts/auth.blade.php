<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sign in') — Central Hub</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full antialiased">
    <a href="#main" class="skip-link">Skip to content</a>

    <div class="relative flex min-h-screen flex-col items-center justify-center px-4 py-10">
        <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
            <div class="absolute inset-0 opacity-[0.12]"
                 style="background-image: linear-gradient(rgba(0,180,255,0.35) 1px, transparent 1px), linear-gradient(90deg, rgba(0,180,255,0.35) 1px, transparent 1px); background-size: 36px 36px;"></div>
        </div>

        <div class="relative mb-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl text-lg font-bold text-slate-950"
                 style="background: linear-gradient(180deg, #22c8ff, #0090d9); box-shadow: 0 0 28px rgba(0,180,255,0.45); font-family: var(--font-display);">
                CH
            </div>
            <p class="font-display text-4xl font-bold tracking-tight text-white" style="text-shadow: 0 0 24px rgba(0,180,255,0.35);">Central Hub</p>
            <p class="mt-2 text-sm font-semibold uppercase tracking-[0.16em] text-sky-300/80">Control center</p>
        </div>

        <main id="main" class="hub-card relative w-full max-w-md p-7 sm:p-9">
            @yield('content')
        </main>

        <p class="relative mt-7 text-center text-xs font-semibold uppercase tracking-[0.14em] text-sky-400/70">
            Staff access is invite-only
        </p>
    </div>
</body>
</html>
