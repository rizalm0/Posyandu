@extends('layouts.kader')

@section('title', $mode === 'create' ? 'Buat Akun Orang Tua' : 'Ubah Akun Orang Tua')

@section('content')
    @php($isEdit = $mode === 'edit')

    <div class="fade-up mx-auto max-w-2xl">
        <a href="{{ route('kader.orang-tua.index') }}" class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-muted transition hover:text-brand-700">
            <x-icon name="arrow-left" class="size-4" />
            Kembali ke daftar akun
        </a>

        <div class="mt-4">
            <p class="eyebrow">Akun akses keluarga</p>
            <h1 class="page-title mt-1.5">{{ $isEdit ? 'Ubah Akun Orang Tua' : 'Buat Akun Orang Tua' }}</h1>
            <p class="mt-1.5 max-w-xl text-[13px] leading-relaxed text-muted">
                {{ $isEdit ? 'Perbarui data akun atau atur ulang kata sandi bila diperlukan.' : 'Akun ini dibuat oleh kader. Sampaikan kredensial secara langsung kepada orang tua/wali.' }}
            </p>
        </div>

        <form method="POST" action="{{ $isEdit ? route('kader.orang-tua.update', $parent) : route('kader.orang-tua.store') }}" class="mt-5 space-y-4" novalidate>
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <x-form-errors />

            <section class="card card-pad space-y-4">
                <div class="flex items-center gap-2.5 border-b border-line pb-4">
                    <span class="flex size-9 items-center justify-center rounded-xl bg-parent-50 text-parent-600">
                        <x-icon name="user" class="size-[18px]" />
                    </span>
                    <div>
                        <h2 class="section-title">Identitas akun</h2>
                        <p class="mt-0.5 text-[11px] text-muted">Nama dan kontak yang digunakan untuk masuk.</p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="label">Nama lengkap <span class="text-danger">*</span></label>
                        <input id="name" name="name" type="text" value="{{ old('name', $parent->name) }}" required maxlength="120" class="input @error('name') field-error @enderror" placeholder="Contoh: Rina Kartika">
                        @error('name')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="label">Email <span class="text-danger">*</span></label>
                        <input id="email" name="email" type="email" value="{{ old('email', $parent->email) }}" required maxlength="255" autocomplete="off" class="input @error('email') field-error @enderror" placeholder="nama@example.test">
                        @error('email')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="phone" class="label">Nomor HP <span class="optional">(opsional)</span></label>
                        <input id="phone" name="phone" type="tel" value="{{ old('phone', $parent->phone) }}" maxlength="25" class="input @error('phone') field-error @enderror" placeholder="08xxxxxxxxxx">
                        @error('phone')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            <section class="card card-pad space-y-4">
                <div class="flex items-center gap-2.5 border-b border-line pb-4">
                    <span class="flex size-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                        <x-icon name="lock" class="size-[18px]" />
                    </span>
                    <div>
                        <h2 class="section-title">{{ $isEdit ? 'Atur ulang kata sandi' : 'Kata sandi awal' }}</h2>
                        <p class="mt-0.5 text-[11px] text-muted">{{ $isEdit ? 'Biarkan kosong jika tidak ingin mengubah.' : 'Minimal 8 karakter.' }}</p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="password" class="label">{{ $isEdit ? 'Kata sandi baru' : 'Kata sandi' }} @if(! $isEdit)<span class="text-danger">*</span>@else<span class="optional">(opsional)</span>@endif</label>
                        <input id="password" name="password" type="password" @required(! $isEdit) autocomplete="new-password" class="input @error('password') field-error @enderror" placeholder="Minimal 8 karakter">
                        @error('password')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="label">Ulangi kata sandi @if(! $isEdit)<span class="text-danger">*</span>@endif</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" @required(! $isEdit) autocomplete="new-password" class="input" placeholder="Ulangi kata sandi">
                    </div>
                </div>

                @if ($isEdit)
                    <div class="rounded-xl border border-line bg-canvas px-3.5 py-3">
                        <label class="flex cursor-pointer items-start gap-2.5 text-[12px] font-medium text-ink">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $parent->is_active)) class="mt-0.5 size-4 rounded border-line text-brand-500 focus:ring-brand-500/30">
                            <span>Akun aktif <span class="block text-[10px] font-normal leading-relaxed text-muted">Jika dinonaktifkan, orang tua tidak dapat masuk. Hubungan dengan balita tetap tersimpan.</span></span>
                        </label>
                    </div>
                @endif
            </section>

            <div class="flex items-start gap-2.5 rounded-2xl border border-warm bg-warm/70 p-4 text-[11px] leading-relaxed text-[#7a5605]">
                <x-icon name="info" class="mt-0.5 size-4 shrink-0" />
                <p>Akun belum terhubung ke balita mana pun. Setelah akun dibuat, buka detail balita dan gunakan tombol “Hubungkan” untuk memberikan akses.</p>
            </div>

            <div class="flex flex-col-reverse gap-2.5 sm:flex-row sm:justify-end">
                <a href="{{ route('kader.orang-tua.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="check" class="size-4" />
                    {{ $isEdit ? 'Simpan perubahan' : 'Buat akun' }}
                </button>
            </div>
        </form>
    </div>
@endsection
