@props(['status', 'labels' => []])

@php
    $label = $labels[$status] ?? str_replace('_', ' ', ucfirst($status));
    $styles = match ($status) {
        'new' => 'color:#7dd3fc; border-color:#00b4ff; box-shadow:0 0 12px rgba(0,180,255,.25);',
        'open' => 'color:#67e8f9; border-color:#22d3ee;',
        'in_progress' => 'color:#c4b5fd; border-color:#a78bfa;',
        'waiting_customer' => 'color:#fcd34d; border-color:#fbbf24;',
        'on_hold' => 'color:#94a3b8; border-color:#64748b;',
        'resolved' => 'color:#6ee7b7; border-color:#34d399;',
        'closed' => 'color:#94a3b8; border-color:#475569;',
        default => 'color:#94a3b8; border-color:#64748b;',
    };
@endphp

<span {{ $attributes->merge(['class' => 'hub-chip']) }} style="{{ $styles }}">
    <span class="h-1.5 w-1.5 rounded-full bg-current shadow-[0_0_8px_currentColor]" aria-hidden="true"></span>
    <span>{{ $label }}</span>
</span>
