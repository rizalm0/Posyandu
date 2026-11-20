@if (session('success') || session('error') || session('status'))
    @php
        $type = session('success') ? 'success' : (session('error') ? 'error' : 'status');
        $message = session($type);
    @endphp

    <div
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 7000)"
        x-transition.opacity.duration.300ms
        class="mb-4 flex items-start gap-3 rounded-2xl border px-4 py-3.5 text-sm shadow-card {{ $type === 'success' ? 'border-brand-100 bg-brand-50 text-brand-800' : ($type === 'error' ? 'border-red-200 bg-danger-soft text-red-800' : 'border-parent-100 bg-parent-50 text-parent-700') }}"
        role="status"
    >
        <x-icon :name="$type === 'success' ? 'check-circle' : ($type === 'error' ? 'alert-triangle' : 'info')" class="mt-0.5 size-5 shrink-0" />
        <p class="min-w-0 flex-1 font-medium leading-relaxed">{{ $message }}</p>
        <button type="button" x-on:click="show = false" class="rounded-lg p-0.5 opacity-60 transition hover:opacity-100" aria-label="Tutup pemberitahuan">
            <x-icon name="x" class="size-4" />
        </button>
    </div>
@endif
