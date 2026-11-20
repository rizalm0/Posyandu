@php($home = auth()->check() ? auth()->user()->homeRoute() : route('kader.login'))
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akses ditolak — {{ config('app.name') }}</title>
    @vite('resources/css/app.css')
</head>
<body class="flex min-h-full items-center justify-center bg-canvas px-5 py-10">
    <div class="w-full max-w-md text-center">
        <span class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-white text-brand-600 shadow-card">
            <x-icon name="lock" class="size-6" />
        </span>
        <p class="mt-5 text-[11px] font-semibold uppercase tracking-[0.16em] text-muted">403 · Akses ditolak</p>
        <h1 class="mt-2 text-[24px] font-semibold tracking-[-0.03em] text-ink">Halaman ini bukan untuk akun Anda</h1>
        <p class="mt-2 text-[13px] leading-relaxed text-muted">Anda tidak memiliki izin membuka halaman ini. Jika merasa ini keliru, hubungi kader Posyandu.</p>
        <div class="mt-6 flex flex-col justify-center gap-2 sm:flex-row">
            <a href="{{ $home }}" class="btn btn-primary">Kembali ke beranda</a>
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary w-full">Keluar dari akun</button>
                </form>
            @endauth
        </div>
    </div>
</body>
</html>
