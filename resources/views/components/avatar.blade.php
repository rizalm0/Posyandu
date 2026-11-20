@props(['name', 'tone' => 'brand', 'class' => 'size-10 text-[13px]'])

@php
    $words = preg_split('/\s+/', trim((string) $name)) ?: [];
    $initials = '';

    foreach (array_slice($words, 0, 2) as $word) {
        $initials .= mb_strtoupper(mb_substr($word, 0, 1));
    }

    $initials = $initials !== '' ? $initials : '?';
    $tones = [
        'brand' => 'bg-brand-100 text-brand-700',
        'parent' => 'bg-parent-100 text-parent-700',
        'warm' => 'bg-warm text-warm-strong',
        'neutral' => 'bg-slate-100 text-slate-600',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center justify-center rounded-full font-semibold '.($tones[$tone] ?? $tones['brand']).' '.$class]) }}>
    {{ $initials }}
</span>
