@props(['label', 'tone' => 'neutral', 'dot' => true])

@php
    $classes = [
        'success' => 'badge-success',
        'warning' => 'badge-warning',
        'neutral' => 'badge-neutral',
        'info' => 'badge-info',
        'danger' => 'badge-danger',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'badge '.($classes[$tone] ?? $classes['neutral'])]) }}>
    @if ($dot)
        <span class="dot"></span>
    @endif
    {{ $label }}
</span>
