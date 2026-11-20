@extends('layouts.parent')

@section('title', 'Beranda Orang Tua')

@section('content')
    @php($firstName = explode(' ', trim(auth()->user()->name))[0])

    <div class="fade-up">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="eyebrow text-parent-600">Portal Orang Tua</p>
                <h1 class="page-title mt-1.5">Halo, {{ $firstName }}</h1>
                <p class="mt-1.5 max-w-lg text-[13px] leading-relaxed text-muted">
                    Ringkasan tumbuh kembang dan catatan Posyandu anak Anda.
                </p>
            </div>
            <span class="badge badge-info"><span class="dot"></span>{{ $children->count() }} anak terhubung</span>
        </div>

        @if (! $selectedChild)
            <div class="card mt-6 overflow-hidden">
                <div class="bg-gradient-to-br from-parent-500 to-parent-700 px-5 py-8 text-white sm:px-7">
                    <span class="flex size-12 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/20">
                        <x-icon name="link" class="size-6" />
                    </span>
                    <h2 class="mt-4 text-[20px] font-semibold tracking-[-0.02em]">Akun Anda belum terhubung ke data balita</h2>
                    <p class="mt-2 max-w-lg text-[12px] leading-relaxed text-white/70">
                        Hubungkan akun hanya dapat dilakukan oleh kader Posyandu. Tidak ada pencocokan otomatis berdasarkan nama atau tanggal lahir.
                    </p>
                </div>
                <div class="p-5 sm:p-6">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-muted">Langkah selanjutnya</p>
                    <ol class="mt-3 space-y-3">
                        <li class="flex items-start gap-3">
                            <span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-parent-50 text-[10px] font-semibold text-parent-700">1</span>
                            <p class="text-[12px] leading-relaxed text-muted">Datang ke Posyandu atau hubungi kader yang Anda kenal.</p>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-parent-50 text-[10px] font-semibold text-parent-700">2</span>
                            <p class="text-[12px] leading-relaxed text-muted">Tunjukkan identitas Anda dan sebutkan nama anak yang terdaftar.</p>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-parent-50 text-[10px] font-semibold text-parent-700">3</span>
                            <p class="text-[12px] leading-relaxed text-muted">Kader akan menghubungkan dan memverifikasi akun. Setelah itu data anak tampil di sini.</p>
                        </li>
                    </ol>
                    <div class="mt-5 flex items-start gap-2.5 rounded-xl bg-parent-50 px-3.5 py-3 text-[11px] leading-relaxed text-parent-700">
                        <x-icon name="info" class="mt-0.5 size-4 shrink-0" />
                        Untuk bantuan, hubungi {{ config('app.name') }}. Akun Anda aktif, tetapi belum memiliki akses ke data balita.
                    </div>
                </div>
            </div>
        @else
            @if ($children->count() > 1)
                <div class="mt-5 flex gap-2 overflow-x-auto pb-1" aria-label="Pilih anak">
                    @foreach ($children as $child)
                        <a
                            href="{{ route('orang-tua.dashboard', ['anak' => $child->id]) }}"
                            class="flex shrink-0 items-center gap-2.5 rounded-2xl border px-3 py-2.5 transition {{ $child->id === $selectedChild->id ? 'border-parent-500 bg-parent-50 text-parent-800' : 'border-line bg-white text-ink hover:border-parent-200' }}"
                        >
                            <x-avatar :name="$child->full_name" tone="parent" class="size-8 text-[10px]" />
                            <span class="text-left">
                                <span class="block max-w-[108px] truncate text-[12px] font-semibold">{{ $child->full_name }}</span>
                                <span class="block text-[10px] text-muted">{{ $child->age_label }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif

            <section class="mt-5 overflow-hidden rounded-3xl border border-parent-100 bg-white shadow-card">
                <div class="relative overflow-hidden bg-gradient-to-br from-parent-500 via-parent-600 to-parent-700 p-5 text-white sm:p-6">
                    <div class="pointer-events-none absolute -right-16 -top-20 size-48 rounded-full border-[32px] border-white/10"></div>
                    <div class="pointer-events-none absolute -bottom-16 left-1/3 size-36 rounded-full bg-white/5"></div>
                    <div class="relative flex flex-wrap items-start gap-4">
                        <x-avatar :name="$selectedChild->full_name" tone="parent" class="size-14 text-[18px]" />
                        <div class="min-w-0 flex-1">
                            <h2 class="text-[20px] font-semibold leading-tight tracking-[-0.025em]">{{ $selectedChild->full_name }}</h2>
                            <p class="mt-1 text-[11px] text-white/70">{{ $selectedChild->age_label }} · {{ $selectedChild->gender_label }}</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-2.5 py-1 text-[10px] font-medium text-white/85">
                                    <x-icon name="id-card" class="size-3" />
                                    {{ $selectedChild->registration_number }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-2.5 py-1 text-[10px] font-medium text-white/85">
                                    <x-icon name="shield" class="size-3" />
                                    Akses terverifikasi
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('orang-tua.grafik', $selectedChild) }}" class="relative inline-flex items-center gap-1.5 rounded-xl bg-white/15 px-3 py-2 text-[11px] font-semibold text-white ring-1 ring-white/20 backdrop-blur transition hover:bg-white/25">
                            Grafik
                            <x-icon name="arrow-right" class="size-3.5" />
                        </a>
                    </div>
                    <p class="relative mt-4 border-t border-white/15 pt-3 text-[10px] text-white/60">
                        Terdaftar di {{ $selectedChild->posyandu?->name ?? 'Posyandu' }} sejak {{ tanggal_id($selectedChild->registered_at) }}.
                    </p>
                </div>

                <div class="p-4 sm:p-5">
                    @if ($latestExamination)
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <p class="text-[11px] font-semibold text-ink">Pengukuran terakhir</p>
                                <p class="mt-0.5 text-[10px] text-muted">{{ tanggal_id($latestExamination->examined_at) }} · {{ umur_balita($selectedChild->date_of_birth, $latestExamination->examined_at) }}</p>
                            </div>
                            <span class="badge badge-success"><span class="dot"></span>Tercatat</span>
                        </div>
                        <div class="mt-3 grid grid-cols-3 gap-2.5">
                            <div class="rounded-xl bg-canvas-parent p-3">
                                <p class="text-[9px] text-muted">Berat badan</p>
                                <p class="mt-1 text-[20px] font-semibold leading-none text-ink tabular-nums">{{ $latestExamination->weight_kg }}<span class="ml-0.5 text-[10px] font-medium text-muted">kg</span></p>
                            </div>
                            <div class="rounded-xl bg-canvas-parent p-3">
                                <p class="text-[9px] text-muted">Tinggi badan</p>
                                <p class="mt-1 text-[20px] font-semibold leading-none text-ink tabular-nums">{{ $latestExamination->height_cm ?? '—' }}@if($latestExamination->height_cm)<span class="ml-0.5 text-[10px] font-medium text-muted">cm</span>@endif</p>
                            </div>
                            <div class="rounded-xl bg-canvas-parent p-3">
                                <p class="text-[9px] text-muted">Lingkar kepala</p>
                                <p class="mt-1 text-[20px] font-semibold leading-none text-ink tabular-nums">{{ $latestExamination->head_circumference_cm ?? '—' }}@if($latestExamination->head_circumference_cm)<span class="ml-0.5 text-[10px] font-medium text-muted">cm</span>@endif</p>
                            </div>
                        </div>
                        @if ($latestExamination->notes)
                            <p class="mt-2.5 rounded-xl bg-parent-50 px-3 py-2.5 text-[11px] leading-relaxed text-parent-800">
                                <span class="font-semibold">Catatan kader:</span> {{ $latestExamination->notes }}
                            </p>
                        @endif
                    @else
                        <div class="rounded-xl bg-canvas-parent p-4 text-center">
                            <p class="text-[12px] font-semibold text-ink">Belum ada pemeriksaan tercatat</p>
                            <p class="mt-1 text-[11px] text-muted">Datang ke Posyandu untuk pemeriksaan rutin. Hasilnya akan tampil di sini.</p>
                        </div>
                    @endif

                    <div class="mt-4 flex items-center justify-between gap-2 border-t border-line pt-3 text-[10px] text-muted">
                        <span class="inline-flex items-center gap-1.5"><x-icon name="clipboard" class="size-3.5" />{{ $visitCount }} kunjungan tercatat</span>
                        <a href="{{ route('orang-tua.riwayat', $selectedChild) }}" class="inline-flex items-center gap-1 font-semibold text-parent-600 hover:text-parent-700">
                            Semua riwayat
                            <x-icon name="arrow-right" class="size-3" />
                        </a>
                    </div>
                </div>
            </section>

            <div class="mt-5 grid gap-5 md:grid-cols-2">
                <section class="card overflow-hidden">
                    <div class="flex items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                        <div>
                            <h2 class="section-title">Pemeriksaan terakhir</h2>
                            <p class="mt-0.5 text-[10px] text-muted">Tiga catatan terbaru</p>
                        </div>
                        <a href="{{ route('orang-tua.riwayat', $selectedChild) }}" class="text-[11px] font-semibold text-parent-600 hover:text-parent-700">Lihat semua</a>
                    </div>
                    @if ($recentExaminations->isEmpty())
                        <div class="p-5">
                            <x-empty-state title="Belum ada catatan" description="Hasil pemeriksaan akan muncul setelah kader menyimpannya." icon="clipboard" />
                        </div>
                    @else
                        <ul class="divide-y divide-line/80">
                            @foreach ($recentExaminations as $examination)
                                <li class="px-4 py-3.5 sm:px-5">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-[12px] font-semibold text-ink">{{ tanggal_id($examination->examined_at) }}</p>
                                            <p class="mt-0.5 text-[10px] text-muted">Usia {{ umur_balita($selectedChild->date_of_birth, $examination->examined_at) }}</p>
                                        </div>
                                        <div class="flex gap-2 text-right">
                                            <span class="rounded-lg bg-canvas-parent px-2 py-1 text-[10px] font-semibold text-ink tabular-nums">{{ $examination->weight_kg }} kg</span>
                                            @if ($examination->height_cm)
                                                <span class="rounded-lg bg-canvas-parent px-2 py-1 text-[10px] font-semibold text-ink tabular-nums">{{ $examination->height_cm }} cm</span>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                <section class="card overflow-hidden">
                    <div class="flex items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                        <div>
                            <h2 class="section-title">Imunisasi tercatat</h2>
                            <p class="mt-0.5 text-[10px] text-muted">Catatan dari Posyandu</p>
                        </div>
                        <a href="{{ route('orang-tua.imunisasi', $selectedChild) }}" class="text-[11px] font-semibold text-parent-600 hover:text-parent-700">Lihat semua</a>
                    </div>
                    @if ($immunizations->isEmpty())
                        <div class="p-5">
                            <x-empty-state title="Belum ada catatan" description="Catatan imunisasi yang diisi kader akan tampil di sini." icon="shield" />
                        </div>
                    @else
                        <ul class="divide-y divide-line/80">
                            @foreach ($immunizations as $immunization)
                                <li class="flex items-center gap-3 px-4 py-3.5 sm:px-5">
                                    <span class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-parent-50 text-parent-600">
                                        <x-icon name="shield" class="size-4" />
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-[12px] font-semibold text-ink">{{ $immunization->name }}</p>
                                        <p class="mt-0.5 text-[10px] text-muted">{{ tanggal_id($immunization->administered_at) }}</p>
                                    </div>
                                    <x-status-badge :label="$immunization->status_label" :tone="$immunization->status === 'needs_review' ? 'warning' : 'success'" />
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            </div>

            <div class="mt-5 flex items-start gap-2.5 rounded-2xl border border-parent-100 bg-white p-4 text-[10px] leading-relaxed text-muted">
                <x-icon name="info" class="mt-0.5 size-4 shrink-0 text-parent-500" />
                Data yang tampil berasal dari catatan kader Posyandu. Aplikasi tidak memberikan diagnosis atau penilaian status gizi. Untuk pertanyaan medis, konsultasikan dengan tenaga kesehatan.
            </div>
        @endif
    </div>
@endsection
