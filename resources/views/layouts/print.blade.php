<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle ?? 'Cetak Laporan' }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
    @stack('head')
</head>
<body class="min-h-screen bg-slate-100 print:bg-white">
    @yield('content')
</body>
</html>
