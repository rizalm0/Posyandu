@extends('layouts.parent')

@section('title', 'Riwayat Imunisasi')

@section('content')
    @php
        $recordedCount = $immunizations->getCollection()->where('status', 'recorded')->count();
        $reviewCount = $immunizations->getCollection()->where('status', 'needs_review')->count();
    @endphp

    <div class="fade-up">
        <a href="{{ route('orang-tua.dashboard', ['anak' => $balita->id]) }}" class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-muted transition hover:text-parent-700">
            <x-icon name="arrow-left" class="size-4" />
            Kembali ke beranda
        </a>

        <div class="mt-4 flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="eyebrow text-parent-600">Catatan pemberian</p>
                <h1 class="page-title mt-1.5">Riwayat Imunisasi</h1>
                <p class="mt-1.5 max-w-xl text-[13px] leading-relaxed text-muted">
                    Imunisasi yang tercatat untuk {{ $balita->full_name }}. Catatan berasal dari kader Posyandu.
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

        <div class="mt-5 grid grid-cols-3 gap-2.5">
            <div class="card p-3.5">
                <p class="text-[9px] font-medium text-muted">Total catatan</p>
                <p class="mt-2 text-[22px] font-semibold leading-none text-ink tabular-nums">{{ $immunizations->total() }}</p>
                <p class="mt-1 text-[9px] text-muted">imunisasi</p>
            </div>
            <div class="card p-3.5">
                <p class="text-[9px] font-medium text-muted">Tercatat</p>
                <p class="mt-2 text-[22px] font-semibold leading-none text-brand-700 tabular-nums">{{ $recordedCount }}</p>
                <p class="mt-1 text-[9px] text-muted">di halaman ini</p>
            </div>
            <div class="card p-3.5">
                <p class="text-[9px] font-medium text-muted">Perlu ditinjau</p>
                <p class="mt-2 text-[22px] font-semibold leading-none text-warm-strong tabular-nums">{{ $reviewCount }}</p>
                <p class="mt-1 text-[9px] text-muted">di halaman ini</p>
            </div>
        </div>

        @if ($immunizations->isEmpty())
            <div class="card mt-4 p-5">
                <x-empty-state title="Belum ada catatan imunisasi" description="Catatan imunisasi yang diberikan di Posyandu akan muncul di sini setelah disimpan kader." icon="shield" />
            </div>
        @else
            <div class="mt-4 space-y-2.5">
                @foreach ($immunizations as $immunization)
                    <article class="card flex items-start gap-3 p-4 sm:p-5">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl {{ $immunization->status === 'needs_review' ? 'bg-warm text-warm-strong' : 'bg-parent-50 text-parent-600' }}">
                            <x-icon name="shield" class="size-5" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <h2 class="text-[13px] font-semibold text-ink">{{ $immunization->name }}</h2>
                                    <p class="mt-0.5 text-[10px] text-muted">Diberikan {{ tanggal_id($immunization->administered_at) }}</p>
                                </div>
                                <x-status-badge :label="$immunization->status_label" :tone="$immunization->status === 'needs_review' ? 'warning' : 'success'" />
                            </div>
                            @if ($immunization->notes)
                                <p class="mt-2.5 rounded-xl bg-canvas-parent px-3 py-2 text-[11px] leading-relaxed text-muted">{{ $immunization->notes }}</p>
                            @endif
                            <p class="mt-2 flex items-center gap-1.5 text-[9px] text-slate-400">
                                <x-icon name="user" class="size-3" />
                                Dicatat oleh {{ $immunization->recorder?->name ?? 'kader Posyandu' }}
                            </p>
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($immunizations->hasPages())
                <div class="mt-5">{{ $immunizations->links() }}</div>
            @endif
        @endif

        <div class="mt-4 flex items-start gap-2.5 rounded-2xl border border-warm bg-warm/60 p-4 text-[10px] leading-relaxed text-[#7a5605]">
            <x-icon name="info" class="mt-0.5 size-4 shrink-0" />
            <p>Status “perlu ditinjau” berarti catatan belum final dan perlu dikonfirmasi dengan kader. Aplikasi tidak menandai imunisasi sebagai sudah/belum lengkap secara otomatis. Bawa buku KIA saat berkunjung.</p>
        </div>
    </div>
@endsection
