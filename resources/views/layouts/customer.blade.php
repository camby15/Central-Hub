<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Support') — {{ $brandName ?? 'Central Hub' }}</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --hub-primary: {{ $primaryColor ?? '#00B4FF' }};
            --hub-primary-hover: color-mix(in srgb, {{ $primaryColor ?? '#00B4FF' }} 70%, white);
            --hub-ring: {{ $primaryColor ?? '#00B4FF' }};
            --hub-border: color-mix(in srgb, {{ $primaryColor ?? '#00B4FF' }} 35%, transparent);
            --hub-border-strong: color-mix(in srgb, {{ $primaryColor ?? '#00B4FF' }} 60%, transparent);
        }
    </style>
</head>
<body class="min-h-full antialiased">
    <a href="#main" class="skip-link">Skip to content</a>

    <header class="border-b px-4 py-5 sm:px-6" style="background: rgba(3,12,28,0.85); border-color: var(--hub-border); backdrop-filter: blur(14px);">
        <div class="mx-auto flex max-w-3xl items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl text-sm font-bold text-slate-950"
                 style="background: linear-gradient(180deg, color-mix(in srgb, var(--hub-primary) 90%, white), var(--hub-primary)); box-shadow: 0 0 24px color-mix(in srgb, var(--hub-primary) 45%, transparent); font-family: var(--font-display);">
                {{ strtoupper(substr($logoLabel ?? 'CH', 0, 2)) }}
            </div>
            <div>
                <p class="font-display text-xl font-bold leading-tight tracking-tight text-white">{{ $brandName ?? 'Support' }}</p>
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-sky-300/80">Customer support</p>
            </div>
        </div>
    </header>

    <main id="main" class="mx-auto w-full max-w-3xl px-4 py-8 sm:px-6">
        @yield('content')
    </main>
</body>
</html>
