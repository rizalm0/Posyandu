@extends('layouts.parent')

@section('title', 'Grafik Pertumbuhan')

@section('content')
    <div class="fade-up">
        <a href="{{ route('orang-tua.dashboard', ['anak' => $balita->id]) }}" class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-muted transition hover:text-parent-700">
            <x-icon name="arrow-left" class="size-4" />
            Kembali ke beranda
        </a>

        <div class="mt-4 flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="eyebrow text-parent-600">Pemantauan pertumbuhan</p>
                <h1 class="page-title mt-1.5">Grafik Pertumbuhan</h1>
                <p class="mt-1.5 max-w-xl text-[13px] leading-relaxed text-muted">
                    Berat dan tinggi badan berdasarkan tanggal pemeriksaan yang tercatat.
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

        @if ($examinations->isEmpty())
            <div class="card mt-5 p-5">
                <x-empty-state title="Belum ada data pertumbuhan" description="Grafik akan tampil setelah kader mencatat pemeriksaan pertama untuk {{ $balita->full_name }}." icon="chart" />
            </div>
        @else
            @if ($latestExamination)
                <div class="mt-5 grid grid-cols-3 gap-2.5">
                    <div class="card p-3.5">
                        <p class="flex items-center gap-1.5 text-[9px] font-medium text-muted"><x-icon name="scale" class="size-3.5 text-parent-500" />Berat terakhir</p>
                        <p class="mt-2 text-[22px] font-semibold leading-none text-ink tabular-nums">{{ $latestExamination->weight_kg }}<span class="ml-0.5 text-[10px] font-medium text-muted">kg</span></p>
                        <p class="mt-1 text-[9px] text-muted">{{ tanggal_singkat_id($latestExamination->examined_at) }}</p>
                    </div>
                    <div class="card p-3.5">
                        <p class="flex items-center gap-1.5 text-[9px] font-medium text-muted"><x-icon name="ruler" class="size-3.5 text-parent-500" />Tinggi terakhir</p>
                        <p class="mt-2 text-[22px] font-semibold leading-none text-ink tabular-nums">{{ $latestExamination->height_cm ?? '—' }}@if($latestExamination->height_cm)<span class="ml-0.5 text-[10px] font-medium text-muted">cm</span>@endif</p>
                        <p class="mt-1 text-[9px] text-muted">{{ tanggal_singkat_id($latestExamination->examined_at) }}</p>
                    </div>
                    <div class="card p-3.5">
                        <p class="flex items-center gap-1.5 text-[9px] font-medium text-muted"><x-icon name="clipboard" class="size-3.5 text-parent-500" />Total catatan</p>
                        <p class="mt-2 text-[22px] font-semibold leading-none text-ink tabular-nums">{{ $examinations->count() }}</p>
                        <p class="mt-1 text-[9px] text-muted">pemeriksaan</p>
                    </div>
                </div>
            @endif

            <div class="mt-4 grid gap-4 lg:grid-cols-2">
                <section class="card">
                    <div class="flex items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                        <div>
                            <h2 class="section-title flex items-center gap-2"><span class="size-2 rounded-full bg-parent-500"></span>Berat badan</h2>
                            <p class="mt-0.5 text-[10px] text-muted">Satuan kilogram (kg)</p>
                        </div>
                        <x-icon name="scale" class="size-5 text-parent-300" />
                    </div>
                    <div class="p-4 sm:p-5">
                        <div class="h-[240px] w-full">
                            <canvas
                                data-chart
                                data-label="Berat badan"
                                data-unit="kg"
                                data-color="#2F6FED"
                                data-labels="{{ json_encode($chartData['labels']) }}"
                                data-values="{{ json_encode($chartData['weight']) }}"
                                aria-label="Grafik berat badan {{ $balita->full_name }}"
                                role="img"
                            ></canvas>
                        </div>
                    </div>
                </section>

                <section class="card">
                    <div class="flex items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                        <div>
                            <h2 class="section-title flex items-center gap-2"><span class="size-2 rounded-full bg-brand-500"></span>Tinggi / panjang badan</h2>
                            <p class="mt-0.5 text-[10px] text-muted">Satuan sentimeter (cm)</p>
                        </div>
                        <x-icon name="ruler" class="size-5 text-brand-300" />
                    </div>
                    <div class="p-4 sm:p-5">
                        <div class="h-[240px] w-full">
                            <canvas
                                data-chart
                                data-label="Tinggi badan"
                                data-unit="cm"
                                data-color="#138B68"
                                data-labels="{{ json_encode($chartData['labels']) }}"
                                data-values="{{ json_encode($chartData['height']) }}"
                                aria-label="Grafik tinggi badan {{ $balita->full_name }}"
                                role="img"
                            ></canvas>
                        </div>
                    </div>
                </section>
            </div>

            <section class="card mt-4 overflow-hidden">
                <div class="flex items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                    <div>
                        <h2 class="section-title">Data pengukuran</h2>
                        <p class="mt-0.5 text-[10px] text-muted">Sumber angka pada grafik di atas.</p>
                    </div>
                    <span class="badge badge-info">{{ $examinations->count() }} catatan</span>
                </div>
                <div class="table-wrap">
                    <table class="data-table min-w-[560px]">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Usia saat periksa</th>
                                <th class="text-right">Berat</th>
                                <th class="text-right">Tinggi</th>
                                <th class="text-right">Lingkar kepala</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($examinations->reverse() as $examination)
                                <tr>
                                    <td class="whitespace-nowrap font-medium">{{ tanggal_id($examination->examined_at) }}</td>
                                    <td class="whitespace-nowrap text-muted">{{ umur_balita($balita->date_of_birth, $examination->examined_at) }}</td>
                                    <td class="text-right tabular-nums">{{ $examination->weight_kg }} kg</td>
                                    <td class="text-right tabular-nums">{{ $examination->height_cm ?? '—' }} @if($examination->height_cm)cm @endif</td>
                                    <td class="text-right tabular-nums">{{ $examination->head_circumference_cm ?? '—' }} @if($examination->head_circumference_cm)cm @endif</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="mt-4 flex items-start gap-2.5 rounded-2xl border border-parent-100 bg-white p-4 text-[10px] leading-relaxed text-muted">
                <x-icon name="info" class="mt-0.5 size-4 shrink-0 text-parent-500" />
                Grafik hanya menampilkan hasil pengukuran yang tercatat, tanpa penilaian normal/tidak normal. Bandingkan dengan buku KIA dan konsultasikan ke tenaga kesehatan untuk interpretasi tumbuh kembang.
            </div>
        @endif
    </div>
@endsection
