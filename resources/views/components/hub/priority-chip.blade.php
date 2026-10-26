@props(['priority', 'labels' => []])

@php
    $label = $labels[$priority] ?? ucfirst($priority);
    $styles = match ($priority) {
        'urgent' => 'color:#fb7185; border-color:#fb7185; box-shadow:0 0 12px rgba(251,113,133,.3);',
        'high' => 'color:#fbbf24; border-color:#fbbf24;',
        'normal' => 'color:#7dd3fc; border-color:#00b4ff;',
        'low' => 'color:#94a3b8; border-color:#64748b;',
        default => 'color:#94a3b8; border-color:#64748b;',
    };
@endphp

<span {{ $attributes->merge(['class' => 'hub-chip']) }} style="{{ $styles }}">
    @if ($priority === 'urgent')
        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
    @endif
    <span>{{ $label }}</span>
</span>
