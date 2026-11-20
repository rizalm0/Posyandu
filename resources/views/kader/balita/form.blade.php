@extends('layouts.kader')

@section('title', $mode === 'create' ? 'Tambah Balita' : 'Ubah Data Balita')

@section('content')
    @php
        $isEdit = $mode === 'edit';
        $value = fn (string $field, $fallback = null) => old($field, $balita->{$field} ?? $fallback);
    @endphp

    <div class="fade-up mx-auto max-w-3xl">
        <a href="{{ $isEdit ? route('kader.balita.show', $balita) : route('kader.balita.index') }}" class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-muted transition hover:text-brand-700">
            <x-icon name="arrow-left" class="size-4" />
            {{ $isEdit ? 'Kembali ke detail balita' : 'Kembali ke data balita' }}
        </a>

        <div class="mt-4 flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="eyebrow">{{ $isEdit ? 'Perbarui identitas' : 'Registrasi baru' }}</p>
                <h1 class="page-title mt-1.5">{{ $isEdit ? 'Ubah Data Balita' : 'Tambah Balita' }}</h1>
                <p class="mt-1.5 max-w-xl text-[13px] leading-relaxed text-muted">
                    Catat identitas dasar yang diperlukan untuk pemantauan di Posyandu. Tidak perlu mengisi NIK atau nomor KK.
                </p>
            </div>
            <div class="rounded-xl border border-line bg-white px-3 py-2 text-right">
                <p class="text-[9px] font-semibold uppercase tracking-[0.1em] text-muted">Nomor registrasi</p>
                <p class="mt-0.5 font-mono text-[11px] font-semibold text-ink">{{ $registrationNumber }}</p>
            </div>
        </div>

        <form method="POST" action="{{ $isEdit ? route('kader.balita.update', $balita) : route('kader.balita.store') }}" class="mt-5 space-y-4" novalidate>
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <x-form-errors />

            <section class="card card-pad">
                <div class="flex items-center gap-2.5 border-b border-line pb-4">
                    <span class="flex size-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                        <x-icon name="baby" class="size-[18px]" />
                    </span>
                    <div>
                        <h2 class="section-title">Identitas balita</h2>
                        <p class="mt-0.5 text-[11px] text-muted">Wajib diisi sesuai catatan Posyandu.</p>
                    </div>
                </div>

                <div class="mt-4 space-y-4">
                    <div>
                        <label for="full_name" class="label">Nama lengkap <span class="text-danger">*</span></label>
                        <input id="full_name" name="full_name" type="text" value="{{ $value('full_name') }}" required maxlength="120" autocomplete="off" placeholder="Contoh: Aisyah Putri Santoso" class="input @error('full_name') field-error @enderror">
                        @error('full_name')<p class="error-text"><x-icon name="alert-triangle" class="mt-px size-3.5 shrink-0" />{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="date_of_birth" class="label">Tanggal lahir <span class="text-danger">*</span></label>
                            <input id="date_of_birth" name="date_of_birth" type="date" value="{{ $value('date_of_birth') instanceof \Carbon\CarbonInterface ? $value('date_of_birth')->format('Y-m-d') : $value('date_of_birth') }}" max="{{ today()->toDateString() }}" required class="input @error('date_of_birth') field-error @enderror">
                            @error('date_of_birth')<p class="error-text"><x-icon name="alert-triangle" class="mt-px size-3.5 shrink-0" />{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="registered_at" class="label">Tanggal pendaftaran <span class="text-danger">*</span></label>
                            <input id="registered_at" name="registered_at" type="date" value="{{ old('registered_at', $balita->registered_at?->format('Y-m-d') ?? today()->toDateString()) }}" max="{{ today()->toDateString() }}" required class="input @error('registered_at') field-error @enderror">
                            @error('registered_at')<p class="error-text"><x-icon name="alert-triangle" class="mt-px size-3.5 shrink-0" />{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <span class="label">Jenis kelamin <span class="text-danger">*</span></span>
                        <div class="grid grid-cols-2 gap-2.5">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="gender" value="L" class="peer sr-only" @checked(old('gender', $balita->gender) === 'L') required>
                                <span class="flex items-center gap-2.5 rounded-xl border border-line bg-white px-3.5 py-3 text-[13px] font-medium text-ink transition peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-800 peer-focus-visible:ring-4 peer-focus-visible:ring-brand-500/15">
                                    <span class="flex size-7 items-center justify-center rounded-lg bg-parent-50 text-parent-600"><x-icon name="user" class="size-4" /></span>
                                    Laki-laki
                                    <span class="ml-auto hidden size-4 items-center justify-center rounded-full bg-brand-500 text-white peer-checked:flex"><x-icon name="check" class="size-2.5" /></span>
                                </span>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="gender" value="P" class="peer sr-only" @checked(old('gender', $balita->gender) === 'P') required>
                                <span class="flex items-center gap-2.5 rounded-xl border border-line bg-white px-3.5 py-3 text-[13px] font-medium text-ink transition peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-800 peer-focus-visible:ring-4 peer-focus-visible:ring-brand-500/15">
                                    <span class="flex size-7 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><x-icon name="heart" class="size-4" /></span>
                                    Perempuan
                                    <span class="ml-auto hidden size-4 items-center justify-center rounded-full bg-brand-500 text-white peer-checked:flex"><x-icon name="check" class="size-2.5" /></span>
                                </span>
                            </label>
                        </div>
                        @error('gender')<p class="error-text"><x-icon name="alert-triangle" class="mt-px size-3.5 shrink-0" />{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            <section class="card card-pad">
                <div class="flex items-center gap-2.5 border-b border-line pb-4">
                    <span class="flex size-9 items-center justify-center rounded-xl bg-parent-50 text-parent-600">
                        <x-icon name="users" class="size-[18px]" />
                    </span>
                    <div>
                        <h2 class="section-title">Orang tua / wali & alamat</h2>
                        <p class="mt-0.5 text-[11px] text-muted">Nama kontak keluarga, bukan akun aplikasi.</p>
                    </div>
                </div>

                <div class="mt-4 space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="parent_name" class="label">Nama orang tua / wali <span class="text-danger">*</span></label>
                            <input id="parent_name" name="parent_name" type="text" value="{{ $value('parent_name') }}" required maxlength="120" placeholder="Contoh: Rina Kartika" class="input @error('parent_name') field-error @enderror">
                            @error('parent_name')<p class="error-text"><x-icon name="alert-triangle" class="mt-px size-3.5 shrink-0" />{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="phone" class="label">Nomor HP <span class="optional">(opsional)</span></label>
                            <input id="phone" name="phone" type="tel" value="{{ $value('phone') }}" maxlength="25" placeholder="08xxxxxxxxxx" class="input @error('phone') field-error @enderror">
                            @error('phone')<p class="error-text"><x-icon name="alert-triangle" class="mt-px size-3.5 shrink-0" />{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label for="address" class="label">Alamat / wilayah <span class="optional">(opsional)</span></label>
                        <textarea id="address" name="address" rows="3" maxlength="500" placeholder="Nama jalan, nomor rumah, RT/RW" class="textarea @error('address') field-error @enderror">{{ $value('address') }}</textarea>
                        @error('address')<p class="error-text"><x-icon name="alert-triangle" class="mt-px size-3.5 shrink-0" />{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            <div class="flex items-start gap-2.5 rounded-2xl border border-parent-100 bg-parent-50/70 p-4 text-[12px] leading-relaxed text-parent-700">
                <x-icon name="link" class="mt-0.5 size-4 shrink-0" />
                <p>Akun orang tua/wali tidak dibuat di sini. Setelah data balita tersimpan, hubungkan akun terverifikasi dari halaman detail balita agar orang tua dapat melihat data anaknya.</p>
            </div>

            <div class="flex flex-col-reverse gap-2.5 sm:flex-row sm:justify-end">
                <a href="{{ $isEdit ? route('kader.balita.show', $balita) : route('kader.balita.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <x-icon :name="$isEdit ? 'check' : 'plus'" class="size-4" />
                    {{ $isEdit ? 'Simpan perubahan' : 'Simpan data balita' }}
                </button>
            </div>
        </form>
    </div>
@endsection
