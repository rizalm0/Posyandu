@extends('layouts.kader')

@section('title', $mode === 'create' ? 'Catat Pemeriksaan' : 'Ubah Pemeriksaan')

@section('content')
    @php($isEdit = $mode === 'edit')

    <div class="fade-up mx-auto max-w-3xl">
        <a href="{{ route('kader.balita.show', $balita) }}" class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-muted transition hover:text-brand-700">
            <x-icon name="arrow-left" class="size-4" />
            Kembali ke detail balita
        </a>

        <div class="mt-4 flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="eyebrow">{{ $isEdit ? 'Perbarui catatan' : 'Pemeriksaan rutin' }}</p>
                <h1 class="page-title mt-1.5">{{ $isEdit ? 'Ubah Pemeriksaan' : 'Catat Pemeriksaan' }}</h1>
                <p class="mt-1.5 max-w-xl text-[13px] leading-relaxed text-muted">
                    Masukkan hasil pengukuran sesuai yang tercatat saat kunjungan. Kolom bertanda bintang wajib diisi.
                </p>
            </div>
            <x-status-badge :label="$isEdit ? 'Mode ubah' : 'Baru'" :tone="$isEdit ? 'info' : 'success'" />
        </div>

        <section class="card card-pad mt-5">
            <div class="flex items-center gap-3">
                <x-avatar :name="$balita->full_name" tone="{{ $balita->gender === 'P' ? 'brand' : 'parent' }}" class="size-11 text-[13px]" />
                <div class="min-w-0 flex-1">
                    <p class="truncate text-[14px] font-semibold text-ink">{{ $balita->full_name }}</p>
                    <p class="mt-0.5 text-[11px] text-muted">{{ $balita->age_label }} · {{ $balita->gender_label }} · {{ $balita->registration_number }}</p>
                </div>
                <div class="hidden text-right sm:block">
                    <p class="text-[10px] text-muted">Pemeriksaan terakhir</p>
                    <p class="mt-0.5 text-[11px] font-semibold text-ink">{{ $latestExamination ? tanggal_singkat_id($latestExamination->examined_at) : 'Belum ada' }}</p>
                </div>
            </div>
            @if ($latestExamination)
                <div class="mt-3 grid grid-cols-3 gap-2 border-t border-line pt-3">
                    <div>
                        <p class="text-[9px] text-muted">Berat sebelumnya</p>
                        <p class="mt-0.5 text-[12px] font-semibold text-ink tabular-nums">{{ $latestExamination->weight_kg }} kg</p>
                    </div>
                    <div>
                        <p class="text-[9px] text-muted">Tinggi sebelumnya</p>
                        <p class="mt-0.5 text-[12px] font-semibold text-ink tabular-nums">{{ $latestExamination->height_cm ?? '—' }} @if($latestExamination->height_cm)cm @endif</p>
                    </div>
                    <div>
                        <p class="text-[9px] text-muted">Lingkar kepala</p>
                        <p class="mt-0.5 text-[12px] font-semibold text-ink tabular-nums">{{ $latestExamination->head_circumference_cm ?? '—' }} @if($latestExamination->head_circumference_cm)cm @endif</p>
                    </div>
                </div>
            @endif
        </section>

        <x-form-errors />

        <form method="POST" action="{{ $isEdit ? route('kader.pemeriksaan.update', $examination) : route('kader.pemeriksaan.store', $balita) }}" class="mt-4 space-y-4" novalidate>
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            @if (session('error'))
                <div class="flex items-start gap-3 rounded-2xl border border-red-200 bg-danger-soft px-4 py-3.5 text-red-800" role="alert">
                    <x-icon name="alert-triangle" class="mt-0.5 size-5 shrink-0" />
                    <div>
                        <p class="text-[13px] font-semibold">Pemeriksaan belum tersimpan</p>
                        <p class="mt-0.5 text-[12px] leading-relaxed">{{ session('error') }} Data yang sudah Anda isi tetap ada di formulir ini.</p>
                    </div>
                </div>
            @endif

            <section class="card card-pad">
                <div class="flex items-center gap-2.5 border-b border-line pb-4">
                    <span class="flex size-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                        <x-icon name="calendar" class="size-[18px]" />
                    </span>
                    <div>
                        <h2 class="section-title">Waktu pemeriksaan</h2>
                        <p class="mt-0.5 text-[11px] text-muted">Tanggal kunjungan ke Posyandu.</p>
                    </div>
                </div>
                <div class="mt-4">
                    <label for="examined_at" class="label">Tanggal pemeriksaan <span class="text-danger">*</span></label>
                    <input id="examined_at" name="examined_at" type="date" value="{{ old('examined_at', $examination->examined_at?->format('Y-m-d') ?? today()->toDateString()) }}" max="{{ today()->toDateString() }}" required class="input @error('examined_at') field-error @enderror sm:max-w-xs">
                    @error('examined_at')<p class="error-text"><x-icon name="alert-triangle" class="mt-px size-3.5 shrink-0" />{{ $message }}</p>@enderror
                </div>
            </section>

            <section class="card card-pad">
                <div class="flex items-center gap-2.5 border-b border-line pb-4">
                    <span class="flex size-9 items-center justify-center rounded-xl bg-parent-50 text-parent-600">
                        <x-icon name="scale" class="size-[18px]" />
                    </span>
                    <div>
                        <h2 class="section-title">Hasil pengukuran</h2>
                        <p class="mt-0.5 text-[11px] text-muted">Gunakan angka desimal bila hasil timbangan/ukur tidak bulat.</p>
                    </div>
                </div>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="weight_kg" class="label">Berat badan <span class="text-danger">*</span></label>
                        <div class="relative">
                            <input id="weight_kg" name="weight_kg" type="number" inputmode="decimal" step="0.01" min="0.1" max="100" value="{{ old('weight_kg', $examination->weight_kg) }}" required placeholder="Contoh: 9.8" class="input pr-12 @error('weight_kg') field-error @enderror">
                            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[11px] font-semibold text-muted">kg</span>
                        </div>
                        @error('weight_kg')<p class="error-text"><x-icon name="alert-triangle" class="mt-px size-3.5 shrink-0" />{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="height_cm" class="label">Tinggi / panjang badan <span class="text-danger">*</span></label>
                        <div class="relative">
                            <input id="height_cm" name="height_cm" type="number" inputmode="decimal" step="0.1" min="1" max="200" value="{{ old('height_cm', $examination->height_cm) }}" required placeholder="Contoh: 80.0" class="input pr-12 @error('height_cm') field-error @enderror">
                            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[11px] font-semibold text-muted">cm</span>
                        </div>
                        @error('height_cm')<p class="error-text"><x-icon name="alert-triangle" class="mt-px size-3.5 shrink-0" />{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2 sm:max-w-[calc(50%-0.5rem)]">
                        <label for="head_circumference_cm" class="label">Lingkar kepala <span class="optional">(opsional)</span></label>
                        <div class="relative">
                            <input id="head_circumference_cm" name="head_circumference_cm" type="number" inputmode="decimal" step="0.1" min="1" max="100" value="{{ old('head_circumference_cm', $examination->head_circumference_cm) }}" placeholder="Contoh: 45.6" class="input pr-12 @error('head_circumference_cm') field-error @enderror">
                            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[11px] font-semibold text-muted">cm</span>
                        </div>
                        @error('head_circumference_cm')<p class="error-text"><x-icon name="alert-triangle" class="mt-px size-3.5 shrink-0" />{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            <section class="card card-pad">
                <label for="notes" class="label">Catatan pemeriksaan <span class="optional">(opsional)</span></label>
                <textarea id="notes" name="notes" rows="4" maxlength="1000" class="textarea @error('notes') field-error @enderror" placeholder="Contoh: anak aktif, tidak ada keluhan saat kunjungan. Catatan ini akan terlihat oleh orang tua terverifikasi.">{{ old('notes', $examination->notes) }}</textarea>
                <div class="mt-1.5 flex items-center justify-between gap-2">
                    @error('notes')
                        <p class="error-text"><x-icon name="alert-triangle" class="mt-px size-3.5 shrink-0" />{{ $message }}</p>
                    @else
                        <p class="help-text">Tuliskan catatan faktual. Hindari diagnosis atau kesimpulan status gizi.</p>
                    @enderror
                    <span class="shrink-0 text-[10px] text-slate-400">maks. 1000</span>
                </div>
            </section>

            <div class="flex items-start gap-2.5 rounded-2xl border border-brand-100 bg-brand-50/70 p-4 text-[11px] leading-relaxed text-brand-800">
                <x-icon name="info" class="mt-0.5 size-4 shrink-0" />
                <p>Setelah disimpan, data dapat dilihat oleh kader dan orang tua/wali yang sudah terverifikasi. Pemeriksaan tidak dapat dihapus; perbaiki melalui tombol ubah pada riwayat.</p>
            </div>

            <div class="flex flex-col-reverse gap-2.5 pb-2 sm:flex-row sm:justify-end">
                <a href="{{ route('kader.balita.show', $balita) }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="check" class="size-4" />
                    {{ $isEdit ? 'Simpan perubahan' : 'Simpan pemeriksaan' }}
                </button>
            </div>
        </form>
    </div>
@endsection
