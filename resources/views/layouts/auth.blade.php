@php($accent = trim($__env->yieldContent('accent', 'kader')))
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="{{ $accent === 'parent' ? '#2F6FED' : '#138B68' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Masuk') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full {{ $accent === 'parent' ? 'bg-canvas-parent' : 'bg-canvas' }}">
    <div class="mx-auto flex min-h-screen max-w-[1400px] flex-col lg:grid lg:grid-cols-[minmax(0,1fr)_minmax(440px,0.82fr)] lg:gap-6 lg:p-6">
        <aside class="relative hidden overflow-hidden rounded-[30px] p-9 text-white lg:flex lg:flex-col lg:justify-between xl:p-12 {{ $accent === 'parent' ? 'bg-parent-700' : 'bg-brand-700' }}">
            <div class="pointer-events-none absolute -right-24 -top-28 size-[340px] rounded-full border-[56px] border-white/10"></div>
            <div class="pointer-events-none absolute -bottom-32 -left-20 size-[300px] rounded-full bg-white/5"></div>

            <div class="relative">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <span class="flex size-11 items-center justify-center rounded-[15px] bg-white/15 ring-1 ring-white/20 backdrop-blur">
                        <x-icon name="plus" class="size-6" />
                    </span>
                    <span>
                        <span class="block text-[15px] font-semibold tracking-tight">{{ config('app.name') }}</span>
                        <span class="block text-[11px] font-medium uppercase tracking-[0.16em] text-white/60">Gegerkalong · RW 03</span>
                    </span>
                </a>

                <div class="mt-20 max-w-md xl:mt-24">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/60">Tumbuh kembang terpantau</p>
                    <h1 class="mt-3 text-[34px] font-semibold leading-[1.15] tracking-[-0.035em] xl:text-[42px]">
                        Catatan balita yang rapi, dari kader untuk keluarga.
                    </h1>
                    <p class="mt-5 max-w-sm text-[14px] leading-relaxed text-white/70">
                        Satu tempat untuk pemeriksaan rutin, riwayat imunisasi, dan laporan Posyandu yang mudah dibaca.
                    </p>
                </div>
            </div>

            <div class="relative mt-14">
                <div class="rounded-3xl border border-white/10 bg-white/10 p-5 backdrop-blur-sm">
                    <div class="flex items-center justify-between gap-4">
                        <p class="text-[12px] font-semibold uppercase tracking-[0.12em] text-white/60">Ringkasan hari ini</p>
                        <span class="rounded-full bg-white/15 px-2.5 py-1 text-[10px] font-semibold text-white/80">Demo</span>
                    </div>
                    <div class="mt-4 grid grid-cols-3 gap-3">
                        <div>
                            <p class="text-2xl font-semibold tabular-nums">10</p>
                            <p class="mt-1 text-[11px] leading-snug text-white/60">Balita terdaftar</p>
                        </div>
                        <div>
                            <p class="text-2xl font-semibold tabular-nums">6</p>
                            <p class="mt-1 text-[11px] leading-snug text-white/60">Pemeriksaan bulan ini</p>
                        </div>
                        <div>
                            <p class="text-2xl font-semibold tabular-nums">2</p>
                            <p class="mt-1 text-[11px] leading-snug text-white/60">Perlu tindak lanjut</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-2 text-[11px] text-white/50">
                    <x-icon name="shield" class="size-4" />
                    Data hanya dapat diakses oleh akun yang terhubung dan terverifikasi.
                </div>
            </div>
        </aside>

        <main class="flex flex-1 flex-col px-5 pb-8 pt-5 sm:px-8 lg:px-10 lg:py-6">
            <div class="flex items-center justify-between lg:hidden">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                    <span class="flex size-9 items-center justify-center rounded-xl text-white {{ $accent === 'parent' ? 'bg-parent-500' : 'bg-brand-500' }}">
                        <x-icon name="plus" class="size-5" />
                    </span>
                    <span class="text-[14px] font-semibold tracking-tight text-ink">{{ config('app.name') }}</span>
                </a>
                <span class="badge {{ $accent === 'parent' ? 'badge-info' : 'badge-success' }}">
                    <span class="dot"></span>
                    {{ $accent === 'parent' ? 'Orang tua' : 'Kader' }}
                </span>
            </div>

            <div class="mx-auto flex w-full max-w-[430px] flex-1 flex-col justify-center py-8 lg:py-4">
                @yield('content')
            </div>

            <p class="mx-auto w-full max-w-[430px] text-center text-[11px] leading-relaxed text-muted">
                {{ config('app.name') }} · Data demo lokal<br class="sm:hidden">
                <span class="mx-1 hidden text-line sm:inline">|</span>
                Bukan pengganti pemeriksaan tenaga kesehatan.
            </p>
        </main>
    </div>
</body>
</html>
