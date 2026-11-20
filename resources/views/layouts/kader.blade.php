@php
    $user = auth()->user();
    $current = request()->route()?->getName() ?? '';
    $isBalita = str_starts_with($current, 'kader.balita');
    $isLaporan = str_starts_with($current, 'kader.laporan');
    $isOrangTua = str_starts_with($current, 'kader.orang-tua');
    $isDashboard = $current === 'kader.dashboard';
    $pageTitle = trim($__env->yieldContent('title', 'Portal Kader'));

    $links = [
        ['label' => 'Beranda', 'icon' => 'home', 'route' => route('kader.dashboard'), 'active' => $isDashboard, 'match' => 'kader.dashboard'],
        ['label' => 'Data Balita', 'icon' => 'users', 'route' => route('kader.balita.index'), 'active' => $isBalita, 'match' => 'kader.balita.*'],
        ['label' => 'Laporan', 'icon' => 'document', 'route' => route('kader.laporan.index'), 'active' => $isLaporan, 'match' => 'kader.laporan.*'],
        ['label' => 'Akun Orang Tua', 'icon' => 'user-plus', 'route' => route('kader.orang-tua.index'), 'active' => $isOrangTua, 'match' => 'kader.orang-tua.*'],
    ];
@endphp
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#138B68">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-full bg-canvas">
    <div class="min-h-screen lg:flex">
        <aside class="fixed inset-y-0 left-0 z-40 hidden w-[268px] flex-col border-r border-line bg-white lg:flex">
            <div class="flex h-[76px] items-center border-b border-line px-5">
                <a href="{{ route('kader.dashboard') }}" class="flex items-center gap-3">
                    <span class="flex size-10 items-center justify-center rounded-[13px] bg-brand-500 text-white shadow-[0_8px_18px_-10px_rgba(19,139,104,0.8)]">
                        <x-icon name="plus" class="size-5" />
                    </span>
                    <span>
                        <span class="block text-[14px] font-semibold tracking-tight text-ink">{{ config('app.name') }}</span>
                        <span class="block text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Portal Kader</span>
                    </span>
                </a>
            </div>

            <nav class="flex-1 px-3 py-5" aria-label="Navigasi kader">
                <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Menu utama</p>
                <div class="space-y-1">
                    @foreach ($links as $link)
                        <a href="{{ $link['route'] }}" class="nav-link {{ $link['active'] ? 'nav-link-active' : '' }}" @if($link['active']) aria-current="page" @endif>
                            <x-icon :name="$link['icon']" class="size-[19px]" />
                            {{ $link['label'] }}
                            @if ($link['active'])
                                <span class="ml-auto size-1.5 rounded-full bg-brand-500"></span>
                            @endif
                        </a>
                    @endforeach
                </div>

                <div class="mt-7 rounded-2xl border border-brand-100 bg-brand-50/70 p-4">
                    <span class="flex size-9 items-center justify-center rounded-xl bg-white text-brand-600 shadow-sm">
                        <x-icon name="clipboard" class="size-4.5" />
                    </span>
                    <p class="mt-3 text-[13px] font-semibold text-brand-900">Catat pemeriksaan rutin</p>
                    <p class="mt-1 text-[11px] leading-relaxed text-brand-700/80">Pilih balita dari daftar, lalu tambahkan hasil pengukuran hari ini.</p>
                    <a href="{{ route('kader.balita.index') }}" class="mt-3 inline-flex items-center gap-1.5 text-[12px] font-semibold text-brand-700 hover:text-brand-800">
                        Buka data balita
                        <x-icon name="arrow-right" class="size-3.5" />
                    </a>
                </div>
            </nav>

            <div class="border-t border-line p-3">
                <div class="flex items-center gap-3 rounded-xl p-2">
                    <x-avatar :name="$user->name" tone="brand" class="size-9 text-[12px]" />
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-[13px] font-semibold text-ink">{{ $user->name }}</p>
                        <p class="truncate text-[11px] text-muted">Kader Posyandu</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex size-8 items-center justify-center rounded-lg text-muted transition hover:bg-red-50 hover:text-danger" title="Keluar" aria-label="Keluar">
                            <x-icon name="logout" class="size-4" />
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="min-w-0 flex-1 lg:pl-[268px]">
            <header class="sticky top-0 z-30 border-b border-line/80 bg-canvas/90 backdrop-blur-md">
                <div class="flex h-[62px] items-center gap-3 px-4 sm:px-6 lg:h-[76px] lg:px-8">
                    <a href="{{ route('kader.dashboard') }}" class="flex items-center gap-2.5 lg:hidden">
                        <span class="flex size-8 items-center justify-center rounded-[10px] bg-brand-500 text-white">
                            <x-icon name="plus" class="size-4" />
                        </span>
                        <span class="text-[13px] font-semibold tracking-tight text-ink">{{ config('app.name') }}</span>
                    </a>

                    <div class="hidden min-w-0 items-center gap-2 lg:flex">
                        <span class="text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400">Portal Kader</span>
                        <span class="text-slate-300">/</span>
                        <span class="truncate text-[12px] font-medium text-muted">{{ $pageTitle }}</span>
                    </div>

                    <div class="ml-auto flex items-center gap-2">
                        <span class="hidden items-center gap-2 rounded-full border border-line bg-white px-3 py-1.5 text-[11px] font-medium text-muted sm:inline-flex">
                            <x-icon name="calendar" class="size-3.5 text-brand-500" />
                            {{ tanggal_id(today()) }}
                        </span>

                        <div class="relative" x-data="{ open: false }" x-on:click.outside="open = false">
                            <button type="button" x-on:click="open = !open" class="flex items-center gap-2 rounded-xl border border-transparent p-1 transition hover:border-line hover:bg-white" :aria-expanded="open" aria-label="Menu akun">
                                <x-avatar :name="$user->name" tone="brand" class="size-8 text-[11px]" />
                                <span class="hidden text-left sm:block">
                                    <span class="block text-[12px] font-semibold text-ink">{{ $user->name }}</span>
                                    <span class="block text-[10px] text-muted">Kader</span>
                                </span>
                                <x-icon name="chevron-down" class="hidden size-3.5 text-muted sm:block" />
                            </button>

                            <div
                                x-show="open"
                                x-cloak
                                x-transition.origin.top.right.duration.150ms
                                class="absolute right-0 mt-2 w-56 overflow-hidden rounded-2xl border border-line bg-white shadow-float"
                            >
                                <div class="border-b border-line px-4 py-3">
                                    <p class="text-[13px] font-semibold text-ink">{{ $user->name }}</p>
                                    <p class="truncate text-[11px] text-muted">{{ $user->email }}</p>
                                </div>
                                <form method="POST" action="{{ route('logout') }}" class="p-1.5">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-left text-[13px] font-medium text-danger transition hover:bg-red-50">
                                        <x-icon name="logout" class="size-4" />
                                        Keluar dari akun
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="mx-auto w-full max-w-[1240px] px-4 py-5 pb-28 sm:px-6 lg:px-8 lg:py-8 lg:pb-10">
                @unless (session('error') && in_array($current, ['kader.pemeriksaan.create', 'kader.pemeriksaan.edit'], true))
                    <x-flash />
                @endunless
                @yield('content')
            </main>
        </div>
    </div>

    <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-line bg-white/95 px-2 pb-[env(safe-area-inset-bottom)] shadow-[0_-8px_30px_-18px_rgba(16,24,40,0.25)] backdrop-blur-md lg:hidden" aria-label="Navigasi utama">
        <div class="mx-auto flex max-w-md items-stretch justify-between px-1 pt-1.5">
            @foreach ($links as $link)
                <a href="{{ $link['route'] }}" class="bottom-nav-item {{ $link['active'] ? 'bottom-nav-item-active' : '' }}" @if($link['active']) aria-current="page" @endif>
                    <span class="flex h-7 w-11 items-center justify-center rounded-full transition {{ $link['active'] ? 'bg-brand-50 text-brand-700' : '' }}">
                        <x-icon :name="$link['icon']" class="size-[19px]" />
                    </span>
                    <span class="truncate">{{ $link['label'] }}</span>
                </a>
            @endforeach
        </div>
    </nav>

    @stack('scripts')
</body>
</html>
