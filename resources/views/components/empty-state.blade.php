@props(['title', 'description' => null, 'icon' => 'baby'])

<div {{ $attributes->merge(['class' => 'empty-state']) }}>
    <span class="mb-3 flex size-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
        <x-icon :name="$icon" class="size-6" />
    </span>
    <h3 class="text-[15px] font-semibold text-ink">{{ $title }}</h3>
    @if ($description)
        <p class="mt-1 max-w-sm text-[13px] leading-relaxed text-muted">{{ $description }}</p>
    @endif
    @if (! $slot->isEmpty())
        <div class="mt-4">{{ $slot }}</div>
    @endif
</div>
