@extends('layouts.auth')

@section('title', 'Masuk Orang Tua')
@section('accent', 'parent')

@section('content')
    <div class="fade-up" x-data="{ show: false, fillDemo() { this.$refs.login.value = 'rina@example.test'; this.$refs.password.value = 'password'; this.$refs.login.focus(); } }">
        <p class="eyebrow text-parent-600">Portal Orang Tua / Wali</p>
        <h1 class="mt-2 text-[28px] font-semibold leading-tight tracking-[-0.035em] text-ink sm:text-[32px]">Pantau si kecil,<br>dengan tenang.</h1>
        <p class="mt-2.5 max-w-sm text-[14px] leading-relaxed text-muted">
            Lihat riwayat pemeriksaan, grafik pertumbuhan, dan catatan imunisasi anak Anda.
        </p>

        <div class="mt-6 flex items-start gap-2.5 rounded-2xl border border-parent-100 bg-parent-50/70 px-4 py-3 text-[12px] leading-relaxed text-parent-700">
            <x-icon name="shield" class="mt-0.5 size-4 shrink-0" />
            <p>Akun dibuat dan diverifikasi oleh kader. Hubungi Posyandu jika Anda belum memiliki akses ke data anak.</p>
        </div>

        <form method="POST" action="{{ route('orang-tua.login.store') }}" class="mt-6 space-y-4" novalidate>
            @csrf

            <div>
                <label for="login" class="label">Email atau nomor HP</label>
                <input
                    id="login"
                    name="login"
                    type="text"
                    x-ref="login"
                    value="{{ old('login') }}"
                    placeholder="nama@example.test"
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
                    <button type="button" x-on:click="show = !show" class="text-[12px] font-semibold text-parent-600 hover:text-parent-700" x-text="show ? 'Sembunyikan' : 'Tampilkan'">Tampilkan</button>
                </div>
                <div class="relative">
                    <input
                        id="password"
                        name="password"
                        x-ref="password"
                        :type="show ? 'text' : 'password'"
                        placeholder="Masukkan kata sandi"
                        autocomplete="current-password"
                        class="input input-parent auth-input pr-12 @error('password') field-error @enderror"
                        required
                    >
                    <button type="button" x-on:click="show = !show" class="absolute inset-y-0 right-1 flex w-10 items-center justify-center rounded-lg text-muted hover:text-parent-600" :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'">
                        <x-icon name="eye" class="size-5" x-show="!show" />
                        <x-icon name="eye-off" class="size-5" x-show="show" x-cloak />
                    </button>
                </div>
                @error('password')
                    <p class="error-text"><x-icon name="alert-triangle" class="mt-px size-3.5 shrink-0" />{{ $message }}</p>
                @enderror
            </div>

            <label class="flex cursor-pointer items-center gap-2 pt-1 text-[13px] font-medium text-muted">
                <input type="checkbox" name="remember" value="1" @checked(old('remember')) class="size-4 rounded border-line text-parent-500 focus:ring-parent-500/30">
                Ingat saya di perangkat ini
            </label>

            <button type="submit" class="btn btn-parent mt-1 w-full">
                Masuk ke portal
                <x-icon name="arrow-right" class="size-4" />
            </button>
        </form>

        <div class="mt-5 rounded-2xl border border-parent-100 bg-parent-50/70 p-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-parent-700">Akun demo orang tua</p>
                    <p class="mt-1 font-mono text-[12px] text-parent-700">rina@example.test<span class="mx-1.5 text-parent-300">/</span>password</p>
                </div>
                <button type="button" x-on:click="fillDemo()" class="btn btn-sm border border-parent-200 bg-white text-parent-700 hover:bg-parent-100">Isi otomatis</button>
            </div>
        </div>

        <div class="mt-7 border-t border-line pt-5">
            <p class="text-center text-[13px] text-muted">
                Anda kader Posyandu?
                <a href="{{ route('kader.login') }}" class="font-semibold text-parent-600 hover:text-parent-700">Masuk ke portal kader</a>
            </p>
        </div>
    </div>
@endsection
