@php($home = auth()->check() ? auth()->user()->homeRoute() : route('kader.login'))
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sesi berakhir — {{ config('app.name') }}</title>
    @vite('resources/css/app.css')
</head>
<body class="flex min-h-full items-center justify-center bg-canvas px-5 py-10">
    <div class="w-full max-w-md text-center">
        <span class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-warm text-warm-strong shadow-card">
            <x-icon name="clock" class="size-6" />
        </span>
        <p class="mt-5 text-[11px] font-semibold uppercase tracking-[0.16em] text-muted">419 · Sesi berakhir</p>
        <h1 class="mt-2 text-[24px] font-semibold tracking-[-0.03em] text-ink">Halaman kedaluwarsa</h1>
        <p class="mt-2 text-[13px] leading-relaxed text-muted">Sesi Anda telah berakhir. Muat ulang halaman lalu coba lagi.</p>
        <a href="{{ $home }}" class="btn btn-primary mt-6">Kembali ke beranda</a>
    </div>
</body>
</html>
