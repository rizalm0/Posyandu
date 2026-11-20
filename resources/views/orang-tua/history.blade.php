@extends('layouts.parent')

@section('title', 'Riwayat Pemeriksaan')

@section('content')
    <div class="fade-up">
        <a href="{{ route('orang-tua.dashboard', ['anak' => $balita->id]) }}" class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-muted transition hover:text-parent-700">
            <x-icon name="arrow-left" class="size-4" />
            Kembali ke beranda
        </a>

        <div class="mt-4 flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="eyebrow text-parent-600">Catatan kunjungan</p>
                <h1 class="page-title mt-1.5">Riwayat Pemeriksaan</h1>
                <p class="mt-1.5 max-w-xl text-[13px] leading-relaxed text-muted">
                    Semua pemeriksaan {{ $balita->full_name }} yang tercatat di Posyandu.
                </p>
            </div>
            <div class="flex items-center gap-2.5 rounded-2xl border border-parent-100 bg-white px-3 py-2">
                <x-avatar :name="$balita->full_name" tone="parent" class="size-8 text-[10px]" />
                <div>
                    <p class="max-w-[160px] truncate text-[12px] font-semibold text-ink">{{ $balita->full_name }}</p>
                    <p class="text-[10px] text-muted">{{ $balita->age_label }}</p>
                </div>
            </div>
        </div>

        <div class="mt-4 flex items-center justify-between gap-2">
            <p class="text-[11px] text-muted">{{ $examinations->total() }} pemeriksaan tercatat</p>
            <a href="{{ route('orang-tua.grafik', $balita) }}" class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-parent-600 hover:text-parent-700">
                <x-icon name="chart" class="size-3.5" />
                Lihat grafik
            </a>
        </div>

        @if ($examinations->isEmpty())
            <div class="card mt-3 p-5">
                <x-empty-state title="Belum ada riwayat pemeriksaan" description="Catatan akan muncul setelah kader menyimpan hasil pemeriksaan di Posyandu." icon="clipboard" />
            </div>
        @else
            <div class="mt-3 space-y-3">
                @foreach ($examinations as $examination)
                    <article class="card overflow-hidden">
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-line bg-canvas-parent/60 px-4 py-3 sm:px-5">
                            <div class="flex items-center gap-2.5">
                                <span class="flex size-9 items-center justify-center rounded-xl bg-parent-50 text-parent-600">
                                    <x-icon name="calendar" class="size-4" />
                                </span>
                                <div>
                                    <p class="text-[13px] font-semibold text-ink">{{ tanggal_id($examination->examined_at) }}</p>
                                    <p class="mt-0.5 text-[10px] text-muted">Usia {{ umur_balita($balita->date_of_birth, $examination->examined_at) }}</p>
                                </div>
                            </div>
                            <span class="badge {{ $examination->is_complete ? 'badge-success' : 'badge-neutral' }}">
                                <span class="dot"></span>
                                {{ $examination->is_complete ? 'Berat & tinggi lengkap' : 'Data belum lengkap' }}
                            </span>
                        </div>
                        <div class="p-4 sm:p-5">
                            <div class="grid grid-cols-3 gap-2.5">
                                <div class="rounded-xl bg-canvas-parent p-3">
                                    <p class="text-[9px] text-muted">Berat badan</p>
                                    <p class="mt-1 text-[18px] font-semibold leading-none text-ink tabular-nums">{{ $examination->weight_kg }}<span class="ml-0.5 text-[9px] font-medium text-muted">kg</span></p>
                                </div>
                                <div class="rounded-xl bg-canvas-parent p-3">
                                    <p class="text-[9px] text-muted">Tinggi badan</p>
                                    <p class="mt-1 text-[18px] font-semibold leading-none text-ink tabular-nums">{{ $examination->height_cm ?? '—' }}@if($examination->height_cm)<span class="ml-0.5 text-[9px] font-medium text-muted">cm</span>@endif</p>
                                </div>
                                <div class="rounded-xl bg-canvas-parent p-3">
                                    <p class="text-[9px] text-muted">Lingkar kepala</p>
                                    <p class="mt-1 text-[18px] font-semibold leading-none text-ink tabular-nums">{{ $examination->head_circumference_cm ?? '—' }}@if($examination->head_circumference_cm)<span class="ml-0.5 text-[9px] font-medium text-muted">cm</span>@endif</p>
                                </div>
                            </div>
                            @if ($examination->notes)
                                <div class="mt-3 rounded-xl bg-parent-50 px-3.5 py-2.5">
                                    <p class="text-[9px] font-semibold uppercase tracking-[0.08em] text-parent-600">Catatan kader</p>
                                    <p class="mt-1 text-[11px] leading-relaxed text-parent-800">{{ $examination->notes }}</p>
                                </div>
                            @endif
                            <p class="mt-2.5 flex items-center gap-1.5 text-[9px] text-slate-400">
                                <x-icon name="user" class="size-3" />
                                Dicatat oleh {{ $examination->recorder?->name ?? 'kader Posyandu' }}
                            </p>
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($examinations->hasPages())
                <div class="mt-5">{{ $examinations->links() }}</div>
            @endif
        @endif

        <div class="mt-4 flex items-start gap-2.5 rounded-2xl border border-parent-100 bg-white p-4 text-[10px] leading-relaxed text-muted">
            <x-icon name="info" class="mt-0.5 size-4 shrink-0 text-parent-500" />
            Catatan ditampilkan apa adanya dari Posyandu. Jika ada data yang tidak sesuai, hubungi kader untuk diperbaiki.
        </div>
    </div>
@endsection
