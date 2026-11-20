@extends('layouts.print')

@section('content')
    @php($filter = ['month' => $period['month'], 'year' => $period['year']])

    <div class="print-hidden sticky top-0 z-10 border-b border-line bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-[210mm] items-center gap-3 px-4 py-3">
            <a href="{{ route('kader.laporan.index', $filter) }}" class="btn btn-secondary btn-sm">
                <x-icon name="arrow-left" class="size-3.5" />
                Kembali
            </a>
            <p class="hidden text-[11px] text-muted sm:block">Pratinjau cetak · {{ $period['label'] }}</p>
            <button type="button" onclick="window.print()" class="btn btn-primary btn-sm ml-auto">
                <x-icon name="printer" class="size-3.5" />
                Cetak sekarang
            </button>
        </div>
    </div>

    <article class="print-page mx-auto my-4 max-w-[210mm] bg-white p-5 shadow-sm sm:my-8 sm:p-10">
        <header class="flex items-start justify-between gap-6 border-b-2 border-brand-700 pb-5">
            <div class="flex items-start gap-3">
                <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-brand-500 text-white print:bg-brand-500">
                    <x-icon name="plus" class="size-6" />
                </span>
                <div>
                    <h1 class="text-[18px] font-semibold tracking-tight text-ink">{{ $posyandu->name }}</h1>
                    <p class="mt-0.5 max-w-xs text-[10px] leading-relaxed text-muted">{{ $posyandu->address }}</p>
                    <p class="mt-0.5 text-[10px] text-muted">{{ $posyandu->phone ? 'Telp. '.$posyandu->phone : '' }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Laporan bulanan</p>
                <p class="mt-1 text-[18px] font-semibold tracking-tight text-ink">{{ $period['label'] }}</p>
                <p class="mt-1 text-[9px] text-muted">{{ tanggal_id($period['start']) }} – {{ tanggal_id($period['end']) }}</p>
            </div>
        </header>

        <section class="mt-5">
            <h2 class="text-[11px] font-semibold uppercase tracking-[0.1em] text-muted">Ringkasan</h2>
            <div class="mt-2 grid grid-cols-3 divide-x divide-line rounded-lg border border-line">
                <div class="p-3">
                    <p class="text-[9px] text-muted">Balita terdaftar aktif</p>
                    <p class="mt-1 text-[18px] font-semibold text-ink tabular-nums">{{ $stats['registered'] }}</p>
                </div>
                <div class="p-3">
                    <p class="text-[9px] text-muted">Pendaftaran baru</p>
                    <p class="mt-1 text-[18px] font-semibold text-ink tabular-nums">{{ $stats['new_registrations'] }}</p>
                </div>
                <div class="p-3">
                    <p class="text-[9px] text-muted">Pemeriksaan tercatat</p>
                    <p class="mt-1 text-[18px] font-semibold text-ink tabular-nums">{{ $stats['examinations'] }}</p>
                </div>
                <div class="border-t border-line p-3">
                    <p class="text-[9px] text-muted">Balita diperiksa</p>
                    <p class="mt-1 text-[18px] font-semibold text-ink tabular-nums">{{ $stats['children_examined'] }}</p>
                </div>
                <div class="border-t border-line p-3">
                    <p class="text-[9px] text-muted">Catatan berat & tinggi lengkap</p>
                    <p class="mt-1 text-[18px] font-semibold text-ink tabular-nums">{{ $stats['complete_records'] }}</p>
                </div>
                <div class="border-t border-line p-3">
                    <p class="text-[9px] text-muted">Imunisasi dicatat</p>
                    <p class="mt-1 text-[18px] font-semibold text-ink tabular-nums">{{ $stats['immunizations'] }}</p>
                </div>
            </div>
        </section>

        <section class="mt-5 print-break-avoid">
            <h2 class="text-[11px] font-semibold uppercase tracking-[0.1em] text-muted">Rekap per tanggal kunjungan</h2>
            @if ($recap->isEmpty())
                <p class="mt-2 rounded border border-dashed border-line px-3 py-4 text-center text-[10px] text-muted">Tidak ada pemeriksaan pada periode ini.</p>
            @else
                <table class="mt-2 w-full border-collapse text-left text-[10px]">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="border border-line px-2.5 py-2 font-semibold text-muted">Tanggal</th>
                            <th class="border border-line px-2.5 py-2 text-center font-semibold text-muted">Pemeriksaan</th>
                            <th class="border border-line px-2.5 py-2 text-center font-semibold text-muted">Anak</th>
                            <th class="border border-line px-2.5 py-2 text-center font-semibold text-muted">Catatan lengkap</th>
                            <th class="border border-line px-2.5 py-2 font-semibold text-muted">Nama anak</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recap as $row)
                            <tr>
                                <td class="border border-line px-2.5 py-1.5 whitespace-nowrap">{{ tanggal_id($row['date']) }}</td>
                                <td class="border border-line px-2.5 py-1.5 text-center tabular-nums">{{ $row['total'] }}</td>
                                <td class="border border-line px-2.5 py-1.5 text-center tabular-nums">{{ $row['children']->count() }}</td>
                                <td class="border border-line px-2.5 py-1.5 text-center tabular-nums">{{ $row['complete'] }}</td>
                                <td class="border border-line px-2.5 py-1.5">{{ $row['children']->join(', ') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>

        <section class="mt-5">
            <h2 class="text-[11px] font-semibold uppercase tracking-[0.1em] text-muted">Rincian pemeriksaan</h2>
            @if ($examinations->isEmpty())
                <p class="mt-2 rounded border border-dashed border-line px-3 py-4 text-center text-[10px] text-muted">Belum ada rincian pemeriksaan.</p>
            @else
                <table class="mt-2 w-full border-collapse text-left text-[9.5px]">
                    <thead class="table-header-group">
                        <tr class="bg-slate-50">
                            <th class="border border-line px-2 py-1.5 font-semibold text-muted">No.</th>
                            <th class="border border-line px-2 py-1.5 font-semibold text-muted">Tanggal</th>
                            <th class="border border-line px-2 py-1.5 font-semibold text-muted">Nama balita</th>
                            <th class="border border-line px-2 py-1.5 font-semibold text-muted">L/P</th>
                            <th class="border border-line px-2 py-1.5 font-semibold text-muted">Usia saat periksa</th>
                            <th class="border border-line px-2 py-1.5 text-right font-semibold text-muted">Berat (kg)</th>
                            <th class="border border-line px-2 py-1.5 text-right font-semibold text-muted">Tinggi (cm)</th>
                            <th class="border border-line px-2 py-1.5 text-right font-semibold text-muted">L. kepala (cm)</th>
                            <th class="border border-line px-2 py-1.5 font-semibold text-muted">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($examinations as $index => $examination)
                            <tr>
                                <td class="border border-line px-2 py-1.5 text-center tabular-nums">{{ $index + 1 }}</td>
                                <td class="border border-line px-2 py-1.5 whitespace-nowrap">{{ $examination->examined_at->format('d/m/Y') }}</td>
                                <td class="border border-line px-2 py-1.5">{{ $examination->balita->full_name }}</td>
                                <td class="border border-line px-2 py-1.5 text-center">{{ $examination->balita->gender }}</td>
                                <td class="border border-line px-2 py-1.5 whitespace-nowrap">{{ umur_balita($examination->balita->date_of_birth, $examination->examined_at) }}</td>
                                <td class="border border-line px-2 py-1.5 text-right tabular-nums">{{ number_format((float) $examination->weight_kg, 2, ',', '.') }}</td>
                                <td class="border border-line px-2 py-1.5 text-right tabular-nums">{{ $examination->height_cm !== null ? number_format((float) $examination->height_cm, 1, ',', '.') : '—' }}</td>
                                <td class="border border-line px-2 py-1.5 text-right tabular-nums">{{ $examination->head_circumference_cm !== null ? number_format((float) $examination->head_circumference_cm, 1, ',', '.') : '—' }}</td>
                                <td class="border border-line px-2 py-1.5">{{ \Illuminate\Support\Str::limit((string) $examination->notes, 70) ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>

        <section class="mt-6 grid grid-cols-2 gap-8 print-break-avoid">
            <div>
                <p class="text-[10px] text-muted">Dibuat oleh,</p>
                <div class="mt-12 border-t border-slate-300 pt-1.5">
                    <p class="text-[10px] font-semibold text-ink">{{ auth()->user()->name }}</p>
                    <p class="text-[9px] text-muted">Kader Posyandu</p>
                </div>
            </div>
            <div>
                <p class="text-[10px] text-muted">Mengetahui,</p>
                <div class="mt-12 border-t border-slate-300 pt-1.5">
                    <p class="text-[10px] font-semibold text-ink">&nbsp;</p>
                    <p class="text-[9px] text-muted">Ketua / Pengelola Posyandu</p>
                </div>
            </div>
        </section>

        <footer class="mt-6 flex items-center justify-between gap-4 border-t border-line pt-3 text-[8.5px] text-slate-400">
            <p>Dicetak dari aplikasi {{ $posyandu->name }} pada {{ $generatedAt->format('d/m/Y H:i') }} WIB.</p>
            <p>Laporan administratif — bukan diagnosis atau penilaian status gizi.</p>
        </footer>
    </article>
@endsection
