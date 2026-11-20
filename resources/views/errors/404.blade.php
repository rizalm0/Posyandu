@php($home = auth()->check() ? auth()->user()->homeRoute() : route('kader.login'))
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Halaman tidak ditemukan — {{ config('app.name') }}</title>
    @vite('resources/css/app.css')
</head>
<body class="flex min-h-full items-center justify-center bg-canvas px-5 py-10">
    <div class="w-full max-w-md text-center">
        <p class="text-[64px] font-semibold leading-none tracking-[-0.06em] text-brand-200">404</p>
        <h1 class="mt-3 text-[24px] font-semibold tracking-[-0.03em] text-ink">Halaman tidak ditemukan</h1>
        <p class="mt-2 text-[13px] leading-relaxed text-muted">Tautan mungkin sudah berubah atau data yang Anda cari tidak tersedia.</p>
        <a href="{{ $home }}" class="btn btn-primary mt-6">Kembali ke beranda</a>
    </div>
</body>
</html>
