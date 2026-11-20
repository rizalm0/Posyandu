@extends('layouts.kader')

@section('title', 'Laporan Posyandu')

@section('content')
    @php
        $filter = ['month' => $period['month'], 'year' => $period['year']];
        $statCards = [
            ['label' => 'Balita terdaftar', 'value' => $stats['registered'], 'unit' => 'aktif', 'icon' => 'users'],
            ['label' => 'Pendaftaran baru', 'value' => $stats['new_registrations'], 'unit' => 'pada periode', 'icon' => 'user-plus'],
            ['label' => 'Pemeriksaan', 'value' => $stats['examinations'], 'unit' => 'catatan', 'icon' => 'clipboard'],
            ['label' => 'Anak diperiksa', 'value' => $stats['children_examined'], 'unit' => 'balita unik', 'icon' => 'baby'],
            ['label' => 'Catatan lengkap', 'value' => $stats['complete_records'], 'unit' => 'berat & tinggi', 'icon' => 'check-circle'],
            ['label' => 'Imunisasi dicatat', 'value' => $stats['immunizations'], 'unit' => 'pada periode', 'icon' => 'shield'],
        ];
    @endphp

    <div class="fade-up">
        <div class="flex flex-wrap items-start justify-between gap-3 print-hidden">
            <div>
                <p class="eyebrow">Rekap bulanan</p>
                <h1 class="page-title mt-1.5">Laporan Posyandu</h1>
                <p class="mt-1.5 max-w-xl text-[13px] leading-relaxed text-muted">
                    Ringkasan administratif untuk {{ $period['label'] }}. Angka dihitung langsung dari catatan pemeriksaan dan imunisasi.
                </p>
            </div>
            <div class="flex w-full flex-wrap gap-2 sm:w-auto">
                <a href="{{ route('kader.laporan.csv', $filter) }}" class="btn btn-secondary flex-1 sm:flex-none">
                    <x-icon name="download" class="size-4" />
                    Unduh CSV
                </a>
                <a href="{{ route('kader.laporan.print', $filter) }}" target="_blank" rel="noopener" class="btn btn-primary flex-1 sm:flex-none">
                    <x-icon name="printer" class="size-4" />
                    Cetak laporan
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('kader.laporan.index') }}" class="card card-pad mt-5 print-hidden">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="grid flex-1 grid-cols-2 gap-3">
                    <div>
                        <label for="month" class="label">Bulan</label>
                        <select id="month" name="month" class="select">
                            @foreach ($months as $monthValue => $monthLabel)
                                <option value="{{ $monthValue }}" @selected($period['month'] === $monthValue)>{{ $monthLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="year" class="label">Tahun</label>
                        <select id="year" name="year" class="select">
                            @foreach ($years as $yearValue)
                                <option value="{{ $yearValue }}" @selected($period['year'] === $yearValue)>{{ $yearValue }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary flex-1 sm:flex-none">
                        <x-icon name="search" class="size-4" />
                        Tampilkan
                    </button>
                    <a href="{{ route('kader.laporan.index') }}" class="btn btn-secondary">Bulan ini</a>
                </div>
            </div>
            <p class="mt-2.5 flex items-center gap-1.5 text-[10px] text-muted">
                <x-icon name="info" class="size-3.5" />
                Periode: {{ tanggal_id($period['start']) }} – {{ tanggal_id($period['end']) }}
            </p>
        </form>

        <div class="mt-4 grid grid-cols-2 gap-2.5 sm:grid-cols-3 lg:grid-cols-6">
            @foreach ($statCards as $card)
                <div class="stat-card p-3.5">
                    <span class="flex size-8 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                        <x-icon :name="$card['icon']" class="size-4" />
                    </span>
                    <p class="mt-3 text-[22px] font-semibold leading-none text-ink tabular-nums">{{ $card['value'] }}</p>
                    <p class="mt-1.5 text-[10px] font-semibold text-ink">{{ $card['label'] }}</p>
                    <p class="mt-0.5 text-[9px] text-muted">{{ $card['unit'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-5 grid gap-5 xl:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]">
            <section class="card overflow-hidden">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h2 class="section-title">Rekap per tanggal</h2>
                    <p class="mt-0.5 text-[11px] text-muted">Jumlah pemeriksaan yang tercatat pada tiap tanggal kunjungan.</p>
                </div>

                @if ($recap->isEmpty())
                    <div class="p-4 sm:p-5">
                        <x-empty-state title="Belum ada pemeriksaan" description="Tidak ada catatan pemeriksaan pada {{ $period['label'] }}." icon="calendar" />
                    </div>
                @else
                    <div class="table-wrap">
                        <table class="data-table min-w-[480px]">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th class="text-center">Pemeriksaan</th>
                                    <th class="text-center">Anak</th>
                                    <th class="text-center">Lengkap</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recap as $row)
                                    <tr>
                                        <td>
                                            <p class="font-semibold">{{ tanggal_id($row['date']) }}</p>
                                            <p class="mt-0.5 max-w-[150px] truncate text-[10px] text-muted">{{ $row['children']->join(', ') }}</p>
                                        </td>
                                        <td class="text-center font-semibold tabular-nums">{{ $row['total'] }}</td>
                                        <td class="text-center tabular-nums">{{ $row['children']->count() }}</td>
                                        <td class="text-center tabular-nums">{{ $row['complete'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <section class="card overflow-hidden">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h2 class="section-title">Rincian pemeriksaan</h2>
                    <p class="mt-0.5 text-[11px] text-muted">{{ $stats['examinations'] }} catatan pada {{ $period['label'] }}.</p>
                </div>

                @if ($examinations->isEmpty())
                    <div class="p-4 sm:p-5">
                        <x-empty-state title="Belum ada rincian" description="Catatan pemeriksaan akan muncul di sini setelah tersimpan." icon="clipboard" />
                    </div>
                @else
                    <div class="table-wrap max-h-[520px] overflow-y-auto">
                        <table class="data-table min-w-[520px]">
                            <thead class="sticky top-0 z-10">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Balita</th>
                                    <th class="text-right">Berat</th>
                                    <th class="text-right">Tinggi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($examinations as $examination)
                                    <tr>
                                        <td class="whitespace-nowrap">
                                            <p class="font-medium">{{ tanggal_singkat_id($examination->examined_at) }}</p>
                                            <p class="mt-0.5 text-[10px] text-muted">{{ umur_balita($examination->balita->date_of_birth, $examination->examined_at) }}</p>
                                        </td>
                                        <td>
                                            <a href="{{ route('kader.balita.show', $examination->balita) }}" class="font-medium hover:text-brand-700">{{ $examination->balita->full_name }}</a>
                                            <p class="mt-0.5 text-[10px] text-muted">{{ $examination->balita->registration_number }}</p>
                                        </td>
                                        <td class="whitespace-nowrap text-right tabular-nums">{{ $examination->weight_kg }} kg</td>
                                        <td class="whitespace-nowrap text-right tabular-nums">{{ $examination->height_cm ?? '—' }} @if($examination->height_cm)cm @endif</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>

        <div class="mt-4 flex items-start gap-2.5 rounded-2xl border border-line bg-white p-4 text-[10px] leading-relaxed text-muted">
            <x-icon name="info" class="mt-0.5 size-4 shrink-0 text-brand-500" />
            <p>Laporan hanya merekap data administratif yang tercatat. Tidak menilai status gizi, tidak mendiagnosis, dan tidak menyimpulkan kelengkapan imunisasi anak. CSV mengikuti periode yang dipilih.</p>
        </div>
    </div>
@endsection
