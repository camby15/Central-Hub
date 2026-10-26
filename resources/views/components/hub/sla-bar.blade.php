@props(['percent' => 0, 'label' => '', 'state' => 'ok'])

@php
    $percent = max(0, min(100, (int) $percent));
    $color = match ($state) {
        'danger' => '#B91C1C',
        'warning' => '#B45309',
        'paused' => '#64748B',
        default => '#15803D',
    };
@endphp

<div {{ $attributes }}>
    <div class="mb-1 flex items-center justify-between gap-2 text-xs">
        <span class="font-medium" style="color: {{ $color }};">SLA</span>
        <span style="color: var(--gray-500, #64748B);">{{ $label }}</span>
    </div>
    <div class="hub-sla-bar" role="progressbar" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100" aria-label="SLA progress {{ $percent }} percent used">
        <span style="width: {{ $percent }}%; background: {{ $color }};"></span>
    </div>
</div>
