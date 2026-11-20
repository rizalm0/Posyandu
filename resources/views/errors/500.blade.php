@php($home = auth()->check() ? auth()->user()->homeRoute() : route('kader.login'))
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terjadi kesalahan — {{ config('app.name') }}</title>
    @vite('resources/css/app.css')
</head>
<body class="flex min-h-full items-center justify-center bg-canvas px-5 py-10">
    <div class="w-full max-w-md text-center">
        <span class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-danger-soft text-danger shadow-card">
            <x-icon name="alert-triangle" class="size-6" />
        </span>
        <p class="mt-5 text-[11px] font-semibold uppercase tracking-[0.16em] text-muted">500 · Kesalahan server</p>
        <h1 class="mt-2 text-[24px] font-semibold tracking-[-0.03em] text-ink">Ada yang tidak berjalan semestinya</h1>
        <p class="mt-2 text-[13px] leading-relaxed text-muted">Coba muat ulang halaman. Jika masalah berlanjut, hubungi pengelola aplikasi.</p>
        <a href="{{ $home }}" class="btn btn-primary mt-6">Kembali ke beranda</a>
    </div>
</body>
</html>
