@extends('layouts.kader')

@section('title', 'Data Balita')

@section('content')
    @php
        $chips = [
            ['key' => 'semua', 'label' => 'Semua', 'count' => $counts['semua']],
            ['key' => 'belum_diperiksa', 'label' => 'Belum diperiksa', 'count' => $counts['belum_diperiksa']],
            ['key' => 'perlu_dilengkapi', 'label' => 'Perlu dilengkapi', 'count' => $counts['perlu_dilengkapi']],
        ];
    @endphp

    <div class="fade-up">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="eyebrow">Registri Posyandu</p>
                <h1 class="page-title mt-1.5">Data Balita</h1>
                <p class="mt-1.5 text-[13px] leading-relaxed text-muted">
                    {{ $counts['semua'] }} balita aktif terdaftar di {{ $posyandu->name }}.
                </p>
            </div>
            <a href="{{ route('kader.balita.create') }}" class="btn btn-primary">
                <x-icon name="plus" class="size-4" />
                Tambah balita
            </a>
        </div>

        <form method="GET" action="{{ route('kader.balita.index') }}" class="mt-5">
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-slate-400">
                    <x-icon name="search" class="size-[18px]" />
                </span>
                <input
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Cari balita atau orang tua"
                    class="input h-12 rounded-2xl pl-11 pr-24"
                    aria-label="Cari balita"
                >
                <input type="hidden" name="status" value="{{ $status }}">
                <button type="submit" class="absolute inset-y-1.5 right-1.5 rounded-xl bg-brand-500 px-3.5 text-[12px] font-semibold text-white transition hover:bg-brand-600">
                    Cari
                </button>
            </div>
        </form>

        <div class="-mr-4 mt-3 flex gap-2 overflow-x-auto pb-1 pr-4 sm:-mr-0 sm:pr-0">
            @foreach ($chips as $chip)
                <a
                    href="{{ route('kader.balita.index', array_filter(['q' => $search, 'status' => $chip['key']])) }}"
                    class="chip {{ $status === $chip['key'] ? 'chip-active' : '' }}"
                >
                    {{ $chip['label'] }}
                    <span class="rounded-full px-1.5 py-0.5 text-[10px] {{ $status === $chip['key'] ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $chip['count'] }}</span>
                </a>
            @endforeach
            @if ($search !== '')
                <a href="{{ route('kader.balita.index', ['status' => $status]) }}" class="chip border-red-200 text-danger hover:border-red-300 hover:text-danger">
                    <x-icon name="x" class="size-3" />
                    Hapus pencarian
                </a>
            @endif
        </div>

        <div class="mt-4 flex items-center justify-between gap-2">
            <p class="text-[11px] text-muted">
                @if ($search !== '')
                    Hasil untuk “{{ $search }}” · {{ $balitas->total() }} balita
                @else
                    Menampilkan {{ $balitas->count() }} dari {{ $balitas->total() }} balita
                @endif
            </p>
            @if ($status !== 'semua')
                <span class="badge {{ $status === 'belum_diperiksa' ? 'badge-warning' : 'badge-neutral' }}">
                    {{ $status === 'belum_diperiksa' ? 'Belum diperiksa' : 'Perlu dilengkapi' }}
                </span>
            @endif
        </div>

        @if ($balitas->isEmpty())
            <div class="mt-3">
                <x-empty-state
                    :title="$search !== '' ? 'Balita tidak ditemukan' : 'Belum ada data balita'"
                    :description="$search !== '' ? 'Coba kata kunci lain atau hapus filter yang aktif.' : 'Tambahkan data balita pertama untuk mulai mencatat pemeriksaan.'"
                    icon="users"
                >
                    @if ($search !== '')
                        <a href="{{ route('kader.balita.index') }}" class="btn btn-secondary btn-sm">Hapus filter</a>
                    @else
                        <a href="{{ route('kader.balita.create') }}" class="btn btn-primary btn-sm">
                            <x-icon name="plus" class="size-3.5" />
                            Tambah balita
                        </a>
                    @endif
                </x-empty-state>
            </div>
        @else
            <div class="mt-3 space-y-2.5">
                @foreach ($balitas as $balita)
                    @php($statusData = $balita->data_status)
                    <article class="card overflow-hidden transition hover:border-brand-200">
                        <a href="{{ route('kader.balita.show', $balita) }}" class="flex items-start gap-3 p-4 sm:items-center sm:gap-4 sm:p-4.5">
                            <x-avatar :name="$balita->full_name" tone="{{ $balita->gender === 'P' ? 'brand' : 'parent' }}" class="size-11 text-[13px]" />

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                    <h2 class="truncate text-[14px] font-semibold text-ink">{{ $balita->full_name }}</h2>
                                    <span class="text-[10px] font-medium text-slate-400">{{ $balita->registration_number }}</span>
                                </div>
                                <p class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-[11px] text-muted">
                                    <span>{{ $balita->age_label }}</span>
                                    <span class="text-line">•</span>
                                    <span>{{ $balita->gender_label }}</span>
                                    <span class="hidden text-line sm:inline">•</span>
                                    <span class="hidden sm:inline">Orang tua: {{ $balita->parent_name }}</span>
                                </p>
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    <x-status-badge :label="$statusData['label']" :tone="$statusData['tone']" />
                                    <span class="text-[10px] text-muted">
                                        @if ($balita->latestExamination)
                                            Terakhir: {{ tanggal_singkat_id($balita->latestExamination->examined_at) }}
                                        @else
                                            Belum ada pemeriksaan
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <div class="flex shrink-0 flex-col items-end gap-2 self-center">
                                <span class="hidden text-right sm:block">
                                    <span class="block text-[11px] font-semibold text-ink">{{ $balita->latestExamination?->weight_kg ?? '—' }}@if($balita->latestExamination?->weight_kg)<span class="text-[9px] font-normal text-muted"> kg</span>@endif</span>
                                    <span class="mt-0.5 block text-[10px] text-muted">Berat terakhir</span>
                                </span>
                                <span class="flex size-8 items-center justify-center rounded-full bg-canvas text-muted">
                                    <x-icon name="chevron-right" class="size-4" />
                                </span>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>

            @if ($balitas->hasPages())
                <div class="mt-5">
                    {{ $balitas->onEachSide(1)->links() }}
                </div>
            @endif
        @endif

        <div class="mt-6 grid gap-3 sm:grid-cols-2">
            <div class="rounded-2xl border border-line bg-white p-4">
                <div class="flex items-center gap-2.5">
                    <span class="flex size-8 items-center justify-center rounded-lg bg-warm text-warm-strong"><x-icon name="clock" class="size-4" /></span>
                    <p class="text-[12px] font-semibold text-ink">Batas pemeriksaan</p>
                </div>
                <p class="mt-2 text-[11px] leading-relaxed text-muted">Balita ditandai belum diperiksa bila tidak ada catatan selama {{ $posyandu->alert_no_exam_days }} hari. Ambang ini dapat diubah pada data Posyandu.</p>
            </div>
            <div class="rounded-2xl border border-line bg-white p-4">
                <div class="flex items-center gap-2.5">
                    <span class="flex size-8 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><x-icon name="link" class="size-4" /></span>
                    <p class="text-[12px] font-semibold text-ink">Akses orang tua</p>
                </div>
                <p class="mt-2 text-[11px] leading-relaxed text-muted">Orang tua hanya dapat melihat data anak setelah hubungan akun diverifikasi kader. Buka detail balita untuk mengelola akses.</p>
            </div>
        </div>
    </div>
@endsection
