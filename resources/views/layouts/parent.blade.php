@php
    $user = auth()->user();
    $current = request()->route()?->getName() ?? '';
    $navChild = $navChild ?? null;
    $children = $children ?? collect();
    $pageTitle = trim($__env->yieldContent('title', 'Portal Orang Tua'));

    $links = [
        ['label' => 'Beranda', 'icon' => 'home', 'route' => route('orang-tua.dashboard'), 'active' => $current === 'orang-tua.dashboard'],
        ['label' => 'Grafik', 'icon' => 'chart', 'route' => $navChild ? route('orang-tua.grafik', $navChild) : route('orang-tua.dashboard'), 'active' => $current === 'orang-tua.grafik'],
        ['label' => 'Riwayat', 'icon' => 'clipboard', 'route' => $navChild ? route('orang-tua.riwayat', $navChild) : route('orang-tua.dashboard'), 'active' => $current === 'orang-tua.riwayat'],
        ['label' => 'Imunisasi', 'icon' => 'shield', 'route' => $navChild ? route('orang-tua.imunisasi', $navChild) : route('orang-tua.dashboard'), 'active' => $current === 'orang-tua.imunisasi'],
    ];
@endphp
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#2F6FED">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-full bg-canvas-parent">
    <div class="min-h-screen lg:flex">
        <aside class="fixed inset-y-0 left-0 z-40 hidden w-[268px] flex-col border-r border-parent-100/80 bg-white lg:flex">
            <div class="flex h-[76px] items-center border-b border-parent-100/70 px-5">
                <a href="{{ route('orang-tua.dashboard') }}" class="flex items-center gap-3">
                    <span class="flex size-10 items-center justify-center rounded-[13px] bg-parent-500 text-white shadow-[0_8px_18px_-10px_rgba(47,111,237,0.8)]">
                        <x-icon name="heart" class="size-5" />
                    </span>
                    <span>
                        <span class="block text-[14px] font-semibold tracking-tight text-ink">{{ config('app.name') }}</span>
                        <span class="block text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Portal Orang Tua</span>
                    </span>
                </a>
            </div>

            <nav class="flex-1 px-3 py-5" aria-label="Navigasi orang tua">
                <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Menu utama</p>
                <div class="space-y-1">
                    @foreach ($links as $link)
                        <a href="{{ $link['route'] }}" class="nav-link-parent {{ $link['active'] ? 'nav-link-parent-active' : '' }}" @if($link['active']) aria-current="page" @endif>
                            <x-icon :name="$link['icon']" class="size-[19px]" />
                            {{ $link['label'] }}
                            @if ($link['active'])
                                <span class="ml-auto size-1.5 rounded-full bg-parent-500"></span>
                            @endif
                        </a>
                    @endforeach
                </div>

                @if ($navChild)
                    <div class="mt-7 rounded-2xl border border-parent-100 bg-parent-50/70 p-4">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-parent-600">Anak yang dilihat</p>
                        <div class="mt-3 flex items-center gap-2.5">
                            <x-avatar :name="$navChild->full_name" tone="parent" class="size-9 text-[11px]" />
                            <div class="min-w-0">
                                <p class="truncate text-[13px] font-semibold text-ink">{{ $navChild->full_name }}</p>
                                <p class="text-[11px] text-muted">{{ $navChild->age_label }}</p>
                            </div>
                        </div>
                        @if ($children->count() > 1)
                            <a href="{{ route('orang-tua.dashboard') }}" class="mt-3 inline-flex items-center gap-1.5 text-[11px] font-semibold text-parent-600 hover:text-parent-700">
                                Ganti anak
                                <x-icon name="refresh" class="size-3.5" />
                            </a>
                        @endif
                    </div>
                @endif

                <div class="mt-7 rounded-2xl bg-gradient-to-br from-parent-600 to-parent-700 p-4 text-white">
                    <x-icon name="info" class="size-5 text-white/70" />
                    <p class="mt-2.5 text-[12px] font-semibold">Catatan dari kader</p>
                    <p class="mt-1 text-[11px] leading-relaxed text-white/70">Grafik menampilkan hasil pengukuran yang tercatat. Konsultasikan tumbuh kembang anak dengan tenaga kesehatan.</p>
                </div>
            </nav>

            <div class="border-t border-parent-100/70 p-3">
                <div class="flex items-center gap-3 rounded-xl p-2">
                    <x-avatar :name="$user->name" tone="parent" class="size-9 text-[12px]" />
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-[13px] font-semibold text-ink">{{ $user->name }}</p>
                        <p class="truncate text-[11px] text-muted">Orang tua / wali</p>
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
            <header class="sticky top-0 z-30 border-b border-parent-100/70 bg-canvas-parent/90 backdrop-blur-md">
                <div class="flex h-[62px] items-center gap-3 px-4 sm:px-6 lg:h-[76px] lg:px-8">
                    <a href="{{ route('orang-tua.dashboard') }}" class="flex items-center gap-2.5 lg:hidden">
                        <span class="flex size-8 items-center justify-center rounded-[10px] bg-parent-500 text-white">
                            <x-icon name="heart" class="size-4" />
                        </span>
                        <span class="text-[13px] font-semibold tracking-tight text-ink">{{ config('app.name') }}</span>
                    </a>

                    <div class="hidden min-w-0 items-center gap-2 lg:flex">
                        <span class="text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400">Portal Orang Tua</span>
                        <span class="text-slate-300">/</span>
                        <span class="truncate text-[12px] font-medium text-muted">{{ $pageTitle }}</span>
                    </div>

                    <div class="ml-auto flex items-center gap-2">
                        @if ($navChild && $children->count() > 1)
                            <div class="relative" x-data="{ open: false }" x-on:click.outside="open = false">
                                <button type="button" x-on:click="open = !open" class="hidden items-center gap-2 rounded-full border border-parent-100 bg-white px-3 py-1.5 text-[11px] font-semibold text-parent-700 sm:inline-flex">
                                    <x-avatar :name="$navChild->full_name" tone="parent" class="size-5 text-[8px]" />
                                    {{ $navChild->full_name }}
                                    <x-icon name="chevron-down" class="size-3" />
                                </button>
                                <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-60 overflow-hidden rounded-2xl border border-line bg-white p-1.5 shadow-float">
                                    @foreach ($children as $child)
                                        <a href="{{ route('orang-tua.dashboard', ['anak' => $child->id]) }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 transition hover:bg-parent-50 {{ $child->id === $navChild->id ? 'bg-parent-50' : '' }}">
                                            <x-avatar :name="$child->full_name" tone="parent" class="size-8 text-[10px]" />
                                            <span class="min-w-0">
                                                <span class="block truncate text-[12px] font-semibold text-ink">{{ $child->full_name }}</span>
                                                <span class="block text-[10px] text-muted">{{ $child->age_label }}</span>
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="relative" x-data="{ open: false }" x-on:click.outside="open = false">
                            <button type="button" x-on:click="open = !open" class="flex items-center gap-2 rounded-xl border border-transparent p-1 transition hover:border-parent-100 hover:bg-white" :aria-expanded="open" aria-label="Menu akun">
                                <x-avatar :name="$user->name" tone="parent" class="size-8 text-[11px]" />
                                <span class="hidden text-left sm:block">
                                    <span class="block text-[12px] font-semibold text-ink">{{ $user->name }}</span>
                                    <span class="block text-[10px] text-muted">Orang tua</span>
                                </span>
                                <x-icon name="chevron-down" class="hidden size-3.5 text-muted sm:block" />
                            </button>

                            <div x-show="open" x-cloak x-transition.origin.top.right.duration.150ms class="absolute right-0 mt-2 w-56 overflow-hidden rounded-2xl border border-line bg-white shadow-float">
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

            <main class="mx-auto w-full max-w-[1120px] px-4 py-5 pb-28 sm:px-6 lg:px-8 lg:py-8 lg:pb-10">
                <x-flash />
                @yield('content')
            </main>
        </div>
    </div>

    <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-parent-100 bg-white/95 px-2 pb-[env(safe-area-inset-bottom)] shadow-[0_-8px_30px_-18px_rgba(16,24,40,0.25)] backdrop-blur-md lg:hidden" aria-label="Navigasi utama">
        <div class="mx-auto flex max-w-md items-stretch justify-between px-1 pt-1.5">
            @foreach ($links as $link)
                <a href="{{ $link['route'] }}" class="bottom-nav-item {{ $link['active'] ? 'bottom-nav-item-parent-active' : '' }}" @if($link['active']) aria-current="page" @endif>
                    <span class="flex h-7 w-11 items-center justify-center rounded-full transition {{ $link['active'] ? 'bg-parent-50 text-parent-700' : '' }}">
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
