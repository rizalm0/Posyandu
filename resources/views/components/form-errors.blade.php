@if ($errors->any())
    <div class="mb-5 flex items-start gap-3 rounded-2xl border border-red-200 bg-danger-soft px-4 py-3.5 text-sm text-red-800" role="alert">
        <x-icon name="alert-triangle" class="mt-0.5 size-5 shrink-0" />
        <div class="min-w-0">
            <p class="font-semibold">Ada {{ $errors->count() }} hal yang perlu diperbaiki</p>
            <ul class="mt-1.5 list-inside list-disc space-y-1 text-[13px] leading-relaxed">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
