@extends('layouts.auth')

@section('title', 'Masuk Kader')

@section('content')
    <div class="fade-up" x-data="{ show: false, fillDemo() { this.$refs.login.value = 'kader@posyandu.test'; this.$refs.password.value = 'password'; this.$refs.login.focus(); } }">
        <p class="eyebrow">Portal Kader</p>
        <h1 class="mt-2 text-[28px] font-semibold leading-tight tracking-[-0.035em] text-ink sm:text-[32px]">Masuk sebagai Kader</h1>
        <p class="mt-2.5 max-w-sm text-[14px] leading-relaxed text-muted">
            Kelola data balita, catat pemeriksaan, dan pantau tindak lanjut Posyandu.
        </p>

        <form method="POST" action="{{ route('kader.login.store') }}" class="mt-7 space-y-4" novalidate>
            @csrf

            <div>
                <label for="login" class="label">Email atau nomor HP</label>
                <input
                    id="login"
                    name="login"
                    type="text"
                    x-ref="login"
                    value="{{ old('login') }}"
                    placeholder="nama@posyandu.test"
                    autocomplete="username"
                    class="input auth-input @error('login') field-error @enderror"
                    required
                    autofocus
                >
                @error('login')
                    <p class="error-text"><x-icon name="alert-triangle" class="mt-px size-3.5 shrink-0" />{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="mb-1.5 flex items-center justify-between">
                    <label for="password" class="label mb-0">Kata sandi</label>
                    <button type="button" x-on:click="show = !show" class="text-[12px] font-semibold text-brand-600 hover:text-brand-700" x-text="show ? 'Sembunyikan' : 'Tampilkan'">Tampilkan</button>
                </div>
                <div class="relative">
                    <input
                        id="password"
                        name="password"
                        x-ref="password"
                        :type="show ? 'text' : 'password'"
                        placeholder="Masukkan kata sandi"
                        autocomplete="current-password"
                        class="input auth-input pr-12 @error('password') field-error @enderror"
                        required
                    >
                    <button type="button" x-on:click="show = !show" class="absolute inset-y-0 right-1 flex w-10 items-center justify-center rounded-lg text-muted hover:text-brand-600" :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'">
                        <x-icon name="eye" class="size-5" x-show="!show" />
                        <x-icon name="eye-off" class="size-5" x-show="show" x-cloak />
                    </button>
                </div>
                @error('password')
                    <p class="error-text"><x-icon name="alert-triangle" class="mt-px size-3.5 shrink-0" />{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between gap-3 pt-1">
                <label class="flex cursor-pointer items-center gap-2 text-[13px] font-medium text-muted">
                    <input type="checkbox" name="remember" value="1" @checked(old('remember')) class="size-4 rounded border-line text-brand-500 focus:ring-brand-500/30">
                    Ingat saya
                </label>
                <span class="text-right text-[11px] leading-snug text-muted">Belum punya akses?<br>Hubungi admin Posyandu.</span>
            </div>

            <button type="submit" class="btn btn-primary mt-2 w-full">
                Masuk ke dashboard
                <x-icon name="arrow-right" class="size-4" />
            </button>
        </form>

        <div class="mt-5 rounded-2xl border border-brand-100 bg-brand-50/70 p-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-brand-700">Akun demo kader</p>
                    <p class="mt-1 font-mono text-[12px] text-brand-900">kader@posyandu.test<span class="mx-1.5 text-brand-300">/</span>password</p>
                </div>
                <button type="button" x-on:click="fillDemo()" class="btn btn-sm border border-brand-200 bg-white text-brand-700 hover:bg-brand-100">Isi otomatis</button>
            </div>
        </div>

        <div class="mt-7 border-t border-line pt-5">
            <p class="text-center text-[13px] text-muted">
                Anda orang tua/wali?
                <a href="{{ route('orang-tua.login') }}" class="font-semibold text-brand-600 hover:text-brand-700">Masuk ke portal orang tua</a>
            </p>
        </div>
    </div>
@endsection
