@extends('layouts.kader')

@section('title', 'Pemeriksaan Tersimpan')

@section('content')
    <div class="fade-up mx-auto max-w-xl">
        <div class="card overflow-hidden">
            <div class="relative overflow-hidden bg-gradient-to-br from-brand-600 to-brand-700 px-6 py-10 text-center text-white">
                <div class="pointer-events-none absolute -right-12 -top-16 size-40 rounded-full border-[28px] border-white/10"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 size-36 rounded-full bg-white/5"></div>
                <span class="relative mx-auto flex size-16 items-center justify-center rounded-full bg-white/15 ring-1 ring-white/25 backdrop-blur">
                    <x-icon name="check" class="size-8" />
                </span>
                <h1 class="relative mt-5 text-[22px] font-semibold tracking-[-0.03em]">Pemeriksaan berhasil disimpan</h1>
                <p class="relative mx-auto mt-2 max-w-sm text-[12px] leading-relaxed text-white/70">
                    Catatan {{ $balita->full_name }} sudah masuk ke riwayat dan grafik pertumbuhan.
                </p>
            </div>

            <div class="p-5 sm:p-6">
                <div class="flex items-center gap-3 rounded-2xl border border-line bg-canvas p-3.5">
                    <x-avatar :name="$balita->full_name" tone="{{ $balita->gender === 'P' ? 'brand' : 'parent' }}" class="size-11 text-[13px]" />
                    <div class="min-w-0 flex-1">
                        <p class="text-[14px] font-semibold leading-snug text-ink">{{ $balita->full_name }}</p>
                        <p class="mt-0.5 text-[11px] text-muted">{{ tanggal_id($examination->examined_at) }} · {{ umur_balita($balita->date_of_birth, $examination->examined_at) }}</p>
                    </div>
                    <x-status-badge label="Tersimpan" tone="success" />
                </div>

                <div class="mt-4 grid grid-cols-3 gap-2.5">
                    <div class="rounded-xl border border-line p-3 text-center">
                        <p class="text-[9px] font-medium uppercase tracking-[0.08em] text-muted">Berat</p>
                        <p class="mt-1 text-[18px] font-semibold text-ink tabular-nums">{{ $examination->weight_kg }}</p>
                        <p class="text-[9px] text-muted">kg</p>
                    </div>
                    <div class="rounded-xl border border-line p-3 text-center">
                        <p class="text-[9px] font-medium uppercase tracking-[0.08em] text-muted">Tinggi</p>
                        <p class="mt-1 text-[18px] font-semibold text-ink tabular-nums">{{ $examination->height_cm ?? '—' }}</p>
                        <p class="text-[9px] text-muted">cm</p>
                    </div>
                    <div class="rounded-xl border border-line p-3 text-center">
                        <p class="text-[9px] font-medium uppercase tracking-[0.08em] text-muted">Lingkar kepala</p>
                        <p class="mt-1 text-[18px] font-semibold text-ink tabular-nums">{{ $examination->head_circumference_cm ?? '—' }}</p>
                        <p class="text-[9px] text-muted">cm</p>
                    </div>
                </div>

                @if ($examination->notes)
                    <div class="mt-3 rounded-xl bg-canvas px-3.5 py-3">
                        <p class="text-[9px] font-semibold uppercase tracking-[0.08em] text-muted">Catatan</p>
                        <p class="mt-1 text-[11px] leading-relaxed text-ink">{{ $examination->notes }}</p>
                    </div>
                @endif

                <p class="mt-4 flex items-center justify-center gap-1.5 text-[10px] text-muted">
                    <x-icon name="clock" class="size-3.5" />
                    Dicatat oleh {{ $examination->recorder?->name ?? 'kader' }} · {{ $examination->created_at?->format('H:i') }}
                </p>

                <div class="mt-5 grid gap-2.5">
                    <a href="{{ route('kader.balita.show', $balita) }}" class="btn btn-primary w-full">
                        Lihat detail balita
                        <x-icon name="arrow-right" class="size-4" />
                    </a>
                    <div class="grid grid-cols-2 gap-2.5">
                        <a href="{{ route('kader.pemeriksaan.create', $balita) }}" class="btn btn-secondary">
                            <x-icon name="plus" class="size-4" />
                            Catat lagi
                        </a>
                        <a href="{{ route('kader.dashboard') }}" class="btn btn-secondary">
                            <x-icon name="home" class="size-4" />
                            Beranda
                        </a>
                    </div>
                </div>

                <p class="mt-4 text-center text-[10px] leading-relaxed text-slate-400">
                    Tidak ada notifikasi otomatis yang dikirim ke orang tua. Sampaikan hasil kunjungan secara langsung bila diperlukan.
                </p>
            </div>
        </div>
    </div>
@endsection
