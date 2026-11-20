@extends('layouts.kader')

@section('title', 'Beranda Kader')

@section('content')
    @php
        $firstName = explode(' ', trim(auth()->user()->name))[0];
        $statCards = [
            ['label' => 'Balita aktif', 'value' => $stats['balitas'], 'suffix' => 'anak', 'icon' => 'baby', 'tone' => 'brand'],
            ['label' => 'Pemeriksaan', 'value' => $stats['exams_this_month'], 'suffix' => 'bulan ini', 'icon' => 'scale', 'tone' => 'parent'],
            ['label' => 'Pendaftaran baru', 'value' => $stats['new_registrations'], 'suffix' => 'bulan ini', 'icon' => 'user-plus', 'tone' => 'warm'],
            ['label' => 'Imunisasi dicatat', 'value' => $stats['immunizations_this_month'], 'suffix' => 'bulan ini', 'icon' => 'shield', 'tone' => 'brand'],
        ];
    @endphp

    <div class="fade-up">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="eyebrow">{{ $greeting }}, kader</p>
                <h1 class="page-title mt-1.5">Halo, {{ $firstName }} 👋</h1>
                <p class="mt-1.5 max-w-lg text-[13px] leading-relaxed text-muted">
                    Ini ringkasan {{ $posyandu->name }} per {{ tanggal_id(today()) }}. Semua angka dihitung dari catatan yang sudah tersimpan.
                </p>
            </div>
            <div class="hidden w-full text-right sm:block xl:w-auto">
                <span class="badge badge-success"><span class="dot"></span>Posyandu aktif</span>
                <p class="mt-2 text-[11px] text-muted">{{ $posyandu->address }}</p>
            </div>
        </div>

        @if ($attentionCount > 0)
            <section class="mt-5 overflow-hidden rounded-2xl border border-amber-200/70 bg-warm p-4 sm:p-5">
                <div class="flex flex-wrap items-start gap-3">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-white/70 text-warm-strong">
                        <x-icon name="alert-triangle" class="size-5" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-[14px] font-semibold text-[#6b4a00]">{{ $attentionCount }} hal administratif perlu ditindaklanjuti</p>
                        <p class="mt-1 text-[12px] leading-relaxed text-[#8a6208]">
                            {{ $overdueCount }} balita belum diperiksa lebih dari {{ $posyandu->alert_no_exam_days }} hari, {{ $incompleteCount }} data perlu dilengkapi atau akun orang tua belum terhubung.
                        </p>
                    </div>
                    <a href="{{ route('kader.balita.index', ['status' => 'belum_diperiksa']) }}" class="btn btn-sm w-full justify-center border border-[#e7cd8d] bg-white/80 text-[#7a5605] hover:bg-white sm:w-auto">
                        Tinjau daftar
                        <x-icon name="arrow-right" class="size-3.5" />
                    </a>
                </div>
            </section>
        @endif

        <div class="mt-5 grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-4">
            @foreach ($statCards as $index => $card)
                <article class="stat-card fade-up fade-up-delay-{{ min($index + 1, 3) }}">
                    <div class="flex items-start justify-between gap-2">
                        <span class="flex size-9 items-center justify-center rounded-xl {{ $card['tone'] === 'parent' ? 'bg-parent-50 text-parent-600' : ($card['tone'] === 'warm' ? 'bg-warm text-warm-strong' : 'bg-brand-50 text-brand-600') }}">
                            <x-icon :name="$card['icon']" class="size-[18px]" />
                        </span>
                    </div>
                    <p class="mt-4 text-[26px] font-semibold leading-none tracking-[-0.03em] text-ink tabular-nums">{{ $card['value'] }}</p>
                    <p class="mt-2 text-[12px] font-semibold text-ink">{{ $card['label'] }}</p>
                    <p class="mt-0.5 text-[10px] text-muted">{{ $card['suffix'] }}</p>
                </article>
            @endforeach
        </div>

        <div class="mt-3 grid grid-cols-3 gap-2.5 sm:gap-3">
            <a href="{{ route('kader.balita.index') }}" class="group flex min-h-[68px] flex-col justify-between rounded-2xl bg-brand-500 p-3.5 text-white shadow-[0_12px_26px_-16px_rgba(19,139,104,0.85)] transition hover:bg-brand-600 active:scale-[0.99] sm:flex-row sm:items-center sm:p-4">
                <span class="flex items-center gap-2 text-[12px] font-semibold sm:text-[13px]">
                    <x-icon name="clipboard" class="size-4.5 shrink-0" />
                    Catat pemeriksaan
                </span>
                <span class="mt-2 flex size-6 items-center justify-center rounded-full bg-white/15 sm:mt-0">
                    <x-icon name="arrow-right" class="size-3.5" />
                </span>
            </a>
            <a href="{{ route('kader.balita.create') }}" class="group flex min-h-[68px] flex-col justify-between rounded-2xl border border-line bg-white p-3.5 text-ink shadow-card transition hover:border-brand-200 hover:bg-brand-50 active:scale-[0.99] sm:flex-row sm:items-center sm:p-4">
                <span class="flex items-center gap-2 text-[12px] font-semibold sm:text-[13px]">
                    <x-icon name="user-plus" class="size-4.5 shrink-0 text-brand-600" />
                    Tambah balita
                </span>
                <span class="mt-2 flex size-6 items-center justify-center rounded-full bg-brand-50 text-brand-600 sm:mt-0">
                    <x-icon name="plus" class="size-3.5" />
                </span>
            </a>
            <a href="{{ route('kader.laporan.index') }}" class="group flex min-h-[68px] flex-col justify-between rounded-2xl border border-line bg-white p-3.5 text-ink shadow-card transition hover:border-brand-200 hover:bg-brand-50 active:scale-[0.99] sm:flex-row sm:items-center sm:p-4">
                <span class="flex items-center gap-2 text-[12px] font-semibold sm:text-[13px]">
                    <x-icon name="document" class="size-4.5 shrink-0 text-brand-600" />
                    Lihat laporan
                </span>
                <span class="mt-2 flex size-6 items-center justify-center rounded-full bg-brand-50 text-brand-600 sm:mt-0">
                    <x-icon name="arrow-right" class="size-3.5" />
                </span>
            </a>
        </div>

        <div class="mt-6 grid gap-5 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)]">
            <section class="card overflow-hidden">
                <div class="flex items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                    <div>
                        <h2 class="section-title">Pemeriksaan terbaru</h2>
                        <p class="mt-0.5 text-[11px] text-muted">Empat catatan terakhir yang masuk</p>
                    </div>
                    <a href="{{ route('kader.balita.index') }}" class="text-[12px] font-semibold text-brand-600 hover:text-brand-700">Semua balita</a>
                </div>

                @if ($recentExaminations->isEmpty())
                    <div class="p-4 sm:p-5">
                        <x-empty-state title="Belum ada pemeriksaan" description="Catatan pemeriksaan yang tersimpan akan muncul di sini." icon="clipboard" />
                    </div>
                @else
                    <ul class="divide-y divide-line/80">
                        @foreach ($recentExaminations as $examination)
                            <li>
                                <a href="{{ route('kader.balita.show', $examination->balita) }}" class="flex items-center gap-3 px-4 py-3.5 transition hover:bg-canvas/80 sm:px-5">
                                    <x-avatar :name="$examination->balita->full_name" tone="brand" class="size-10 text-[12px]" />
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-[13px] font-semibold text-ink">{{ $examination->balita->full_name }}</p>
                                        <p class="mt-0.5 flex flex-wrap items-center gap-x-2 text-[11px] text-muted">
                                            <span>{{ tanggal_singkat_id($examination->examined_at) }}</span>
                                            <span class="text-line">•</span>
                                            <span>{{ $examination->weight_kg }} kg</span>
                                            @if ($examination->height_cm)
                                                <span class="text-line">•</span>
                                                <span>{{ $examination->height_cm }} cm</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="hidden text-right sm:block">
                                        <p class="text-[10px] text-muted">Dicatat oleh</p>
                                        <p class="mt-0.5 max-w-[100px] truncate text-[11px] font-medium text-ink">{{ $examination->recorder?->name ?? '-' }}</p>
                                    </div>
                                    <x-icon name="chevron-right" class="size-4 shrink-0 text-slate-300" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section class="card overflow-hidden">
                <div class="flex items-start justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                    <div>
                        <h2 class="section-title">Perlu perhatian</h2>
                        <p class="mt-0.5 text-[11px] text-muted">Tindak lanjut administratif</p>
                    </div>
                    <span class="badge badge-warning">{{ $attentionCount }}</span>
                </div>

                @if ($attentionBalitas->isEmpty())
                    <div class="p-4 sm:p-5">
                        <div class="flex items-start gap-3 rounded-2xl bg-brand-50 p-4">
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-white text-brand-600">
                                <x-icon name="check-circle" class="size-5" />
                            </span>
                            <div>
                                <p class="text-[13px] font-semibold text-brand-900">Tidak ada tindak lanjut mendesak</p>
                                <p class="mt-1 text-[11px] leading-relaxed text-brand-700">Data balita sudah tercatat dan terhubung dengan baik.</p>
                            </div>
                        </div>
                    </div>
                @else
                    <ul class="divide-y divide-line/80">
                        @foreach ($attentionBalitas as $balita)
                            @php($status = $balita->data_status)
                            <li class="px-4 py-3.5 sm:px-5">
                                <div class="flex items-start gap-3">
                                    <x-avatar :name="$balita->full_name" tone="{{ $status['tone'] === 'warning' ? 'warm' : 'neutral' }}" class="size-9 text-[11px]" />
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('kader.balita.show', $balita) }}" class="block truncate text-[13px] font-semibold text-ink hover:text-brand-700">{{ $balita->full_name }}</a>
                                        <p class="mt-1 text-[11px] leading-relaxed text-muted">
                                            @if ($balita->is_overdue)
                                                Terakhir diperiksa {{ $balita->latestExamination ? waktu_relatif_id($balita->latestExamination->examined_at) : 'belum pernah' }}
                                            @else
                                                {{ $status['label'] }}
                                            @endif
                                        </p>
                                    </div>
                                    <a href="{{ route('kader.pemeriksaan.create', $balita) }}" class="btn btn-sm border border-line bg-white text-brand-700 hover:bg-brand-50">Catat</a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="border-t border-line px-4 py-3 sm:px-5">
                        <a href="{{ route('kader.balita.index', ['status' => 'belum_diperiksa']) }}" class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-brand-600 hover:text-brand-700">
                            Lihat semua yang perlu ditindaklanjuti
                            <x-icon name="arrow-right" class="size-3.5" />
                        </a>
                    </div>
                @endif
            </section>
        </div>

        <p class="mt-5 flex items-start gap-2 rounded-xl px-1 text-[10px] leading-relaxed text-slate-400">
            <x-icon name="info" class="mt-px size-3.5 shrink-0" />
            Aplikasi ini menampilkan catatan administratif Posyandu. Tidak memberikan diagnosis atau penilaian status gizi.
        </p>
    </div>
@endsection
