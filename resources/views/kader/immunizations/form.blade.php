@extends('layouts.kader')

@section('title', 'Ubah Imunisasi')

@section('content')
    @php($isEdit = $mode === 'edit')

    <div class="fade-up mx-auto max-w-xl">
        <a href="{{ route('kader.balita.show', $balita) }}" class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-muted transition hover:text-brand-700">
            <x-icon name="arrow-left" class="size-4" />
            Kembali ke detail balita
        </a>

        <div class="mt-4 flex items-start justify-between gap-3">
            <div>
                <p class="eyebrow">Catatan imunisasi</p>
                <h1 class="page-title mt-1.5">Ubah Imunisasi</h1>
                <p class="mt-1.5 text-[13px] text-muted">{{ $balita->full_name }} · {{ $balita->age_label }}</p>
            </div>
            <x-avatar :name="$balita->full_name" tone="brand" class="size-10 text-[12px]" />
        </div>

        <form method="POST" action="{{ route('kader.imunisasi.update', $immunization) }}" class="mt-5 space-y-4" novalidate>
            @csrf
            @method('PUT')

            <x-form-errors />

            <section class="card card-pad space-y-4">
                <div>
                    <label for="name" class="label">Nama imunisasi <span class="text-danger">*</span></label>
                    <input id="name" name="name" type="text" value="{{ old('name', $immunization->name) }}" required maxlength="120" class="input @error('name') field-error @enderror">
                    @error('name')<p class="error-text">{{ $message }}</p>@enderror
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="administered_at" class="label">Tanggal pemberian <span class="text-danger">*</span></label>
                        <input id="administered_at" name="administered_at" type="date" value="{{ old('administered_at', $immunization->administered_at?->format('Y-m-d')) }}" max="{{ today()->toDateString() }}" required class="input @error('administered_at') field-error @enderror">
                        @error('administered_at')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="status" class="label">Status pencatatan <span class="text-danger">*</span></label>
                        <select id="status" name="status" class="select @error('status') field-error @enderror">
                            <option value="recorded" @selected(old('status', $immunization->status) === 'recorded')>Tercatat</option>
                            <option value="needs_review" @selected(old('status', $immunization->status) === 'needs_review')>Perlu ditinjau</option>
                        </select>
                        @error('status')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label for="notes" class="label">Catatan <span class="optional">(opsional)</span></label>
                    <textarea id="notes" name="notes" rows="4" maxlength="1000" class="textarea @error('notes') field-error @enderror">{{ old('notes', $immunization->notes) }}</textarea>
                    @error('notes')<p class="error-text">{{ $message }}</p>@enderror
                </div>
            </section>

            <div class="flex flex-col-reverse gap-2.5 sm:flex-row sm:justify-end">
                <a href="{{ route('kader.balita.show', $balita) }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="check" class="size-4" />
                    Simpan perubahan
                </button>
            </div>
        </form>
    </div>
@endsection
