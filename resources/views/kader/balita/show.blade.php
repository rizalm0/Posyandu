@extends('layouts.kader')

@section('title', 'Detail Balita')

@section('content')
    @php
        $statusData = $balita->data_status;
        $guardianError = $errors->hasAny(['email', 'relationship', 'verify_now', 'is_primary']);
        $immunizationError = $errors->hasAny(['name', 'administered_at', 'status', 'notes']);
        $availableParents = $parentAccounts->whereNotIn('id', $balita->guardians->pluck('id'));
        $relationshipLabels = ['father' => 'Ayah', 'mother' => 'Ibu', 'guardian' => 'Wali'];
    @endphp

    <div class="fade-up" x-data="{ addImmunization: {{ $immunizationError ? 'true' : 'false' }}, linkGuardian: {{ $guardianError ? 'true' : 'false' }} }">
        <a href="{{ route('kader.balita.index') }}" class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-muted transition hover:text-brand-700">
            <x-icon name="arrow-left" class="size-4" />
            Kembali ke data balita
        </a>

        <div class="mt-4 flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="eyebrow">Profil balita</p>
                <h1 class="page-title mt-1.5">{{ $balita->full_name }}</h1>
                <p class="mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-[12px] text-muted">
                    <span class="font-mono text-[11px]">{{ $balita->registration_number }}</span>
                    <span class="text-line">•</span>
                    <span>{{ $balita->age_label }}</span>
                    <span class="text-line">•</span>
                    <span>{{ $balita->gender_label }}</span>
                </p>
            </div>
            <div class="grid w-full grid-cols-1 gap-2 sm:flex sm:w-auto">
                <a href="{{ route('kader.balita.edit', $balita) }}" class="btn btn-secondary w-full sm:w-auto">
                    <x-icon name="edit" class="size-4" />
                    Ubah data
                </a>
                <a href="{{ route('kader.pemeriksaan.create', $balita) }}" class="btn btn-primary w-full sm:w-auto">
                    <x-icon name="plus" class="size-4" />
                    Catat pemeriksaan
                </a>
            </div>
        </div>

        <x-flash />

        @if ($statusData['tone'] === 'warning')
            <div class="mt-4 flex flex-wrap items-center gap-3 rounded-2xl border border-amber-200 bg-warm px-4 py-3.5">
                <span class="flex size-9 items-center justify-center rounded-xl bg-white/70 text-warm-strong"><x-icon name="alert-triangle" class="size-4.5" /></span>
                <div class="min-w-0 flex-1">
                    <p class="text-[13px] font-semibold text-[#6b4a00]">{{ $statusData['label'] }}</p>
                    <p class="mt-0.5 text-[11px] leading-relaxed text-[#8a6208]">
                        @if ($balita->is_overdue)
                            Tidak ada pemeriksaan selama lebih dari {{ $balita->posyandu->alert_no_exam_days }} hari. Jadwalkan kunjungan atau catat pemeriksaan baru.
                        @else
                            Lengkapi hasil pengukuran atau hubungkan akun orang tua/wali agar data dan akses lebih lengkap.
                        @endif
                    </p>
                </div>
                <a href="{{ route('kader.pemeriksaan.create', $balita) }}" class="btn btn-sm w-full justify-center border border-[#e7cd8d] bg-white/80 text-[#7a5605] hover:bg-white sm:w-auto">Catat sekarang</a>
            </div>
        @endif

        <div class="mt-4 grid gap-4 xl:grid-cols-[minmax(0,1.35fr)_minmax(340px,0.65fr)]">
            <div class="space-y-4">
                <section class="card overflow-hidden">
                    <div class="h-1.5 w-full bg-gradient-to-r from-brand-500 via-brand-400 to-brand-200"></div>
                    <div class="p-4 sm:p-5">
                        <div class="flex flex-wrap items-start gap-4">
                            <x-avatar :name="$balita->full_name" tone="{{ $balita->gender === 'P' ? 'brand' : 'parent' }}" class="size-14 text-[18px]" />
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-[17px] font-semibold tracking-[-0.02em] text-ink">{{ $balita->full_name }}</h2>
                                    <x-status-badge :label="$statusData['label']" :tone="$statusData['tone']" />
                                </div>
                                <p class="mt-1 text-[12px] text-muted">Lahir {{ tanggal_id($balita->date_of_birth) }} · {{ $balita->age_label }}</p>
                                <div class="mt-3 grid gap-2 text-[11px] sm:grid-cols-2">
                                    <p class="flex items-start gap-2 text-muted"><x-icon name="users" class="mt-px size-3.5 shrink-0 text-slate-400" /><span>Orang tua: <span class="font-medium text-ink">{{ $balita->parent_name }}</span></span></p>
                                    <p class="flex items-start gap-2 text-muted"><x-icon name="phone" class="mt-px size-3.5 shrink-0 text-slate-400" /><span>{{ $balita->phone ?: 'Nomor HP belum diisi' }}</span></p>
                                    <p class="flex items-start gap-2 text-muted sm:col-span-2"><x-icon name="map-pin" class="mt-px size-3.5 shrink-0 text-slate-400" /><span>{{ $balita->address ?: 'Alamat belum diisi' }}</span></p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-2.5 sm:grid-cols-4">
                            <div class="rounded-xl bg-canvas p-3">
                                <p class="flex items-center gap-1.5 text-[10px] font-medium text-muted"><x-icon name="scale" class="size-3.5" />Berat</p>
                                <p class="mt-1 text-[17px] font-semibold tracking-tight text-ink tabular-nums">{{ $latestExamination?->weight_kg ?? '—' }}@if($latestExamination?->weight_kg)<span class="ml-0.5 text-[10px] font-medium text-muted">kg</span>@endif</p>
                            </div>
                            <div class="rounded-xl bg-canvas p-3">
                                <p class="flex items-center gap-1.5 text-[10px] font-medium text-muted"><x-icon name="ruler" class="size-3.5" />Tinggi</p>
                                <p class="mt-1 text-[17px] font-semibold tracking-tight text-ink tabular-nums">{{ $latestExamination?->height_cm ?? '—' }}@if($latestExamination?->height_cm)<span class="ml-0.5 text-[10px] font-medium text-muted">cm</span>@endif</p>
                            </div>
                            <div class="rounded-xl bg-canvas p-3">
                                <p class="flex items-center gap-1.5 text-[10px] font-medium text-muted"><x-icon name="heart" class="size-3.5" />Lingkar kepala</p>
                                <p class="mt-1 text-[17px] font-semibold tracking-tight text-ink tabular-nums">{{ $latestExamination?->head_circumference_cm ?? '—' }}@if($latestExamination?->head_circumference_cm)<span class="ml-0.5 text-[10px] font-medium text-muted">cm</span>@endif</p>
                            </div>
                            <div class="rounded-xl bg-canvas p-3">
                                <p class="flex items-center gap-1.5 text-[10px] font-medium text-muted"><x-icon name="clipboard" class="size-3.5" />Kunjungan</p>
                                <p class="mt-1 text-[17px] font-semibold tracking-tight text-ink tabular-nums">{{ $examinations->count() }}<span class="ml-0.5 text-[10px] font-medium text-muted">kali</span></p>
                            </div>
                        </div>

                        <p class="mt-3 text-[10px] text-slate-400">
                            @if ($latestExamination)
                                Pengukuran terakhir {{ tanggal_id($latestExamination->examined_at) }} · dicatat oleh {{ $latestExamination->recorder?->name ?? 'kader' }}
                            @else
                                Belum ada pemeriksaan tercatat untuk balita ini.
                            @endif
                        </p>
                    </div>
                </section>

                <section class="card">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                        <div>
                            <h2 class="section-title">Pertumbuhan dari waktu ke waktu</h2>
                            <p class="mt-0.5 text-[11px] text-muted">Hanya berisi hasil pengukuran yang tercatat.</p>
                        </div>
                        <span class="badge badge-neutral"><span class="dot"></span>{{ $examinations->count() }} titik data</span>
                    </div>

                    @if ($examinations->isEmpty())
                        <div class="p-4 sm:p-5">
                            <x-empty-state title="Belum ada grafik" description="Grafik akan muncul setelah pemeriksaan pertama tersimpan." icon="chart">
                                <a href="{{ route('kader.pemeriksaan.create', $balita) }}" class="btn btn-primary btn-sm">Catat pemeriksaan</a>
                            </x-empty-state>
                        </div>
                    @else
                        <div class="space-y-4 p-4 sm:p-5">
                            <div>
                                <div class="mb-2 flex items-center justify-between">
                                    <p class="flex items-center gap-1.5 text-[11px] font-semibold text-ink"><span class="size-2 rounded-full bg-brand-500"></span>Berat badan</p>
                                    <span class="text-[10px] text-muted">kg</span>
                                </div>
                                <div class="h-[190px] w-full">
                                    <canvas
                                        data-chart
                                        data-label="Berat badan"
                                        data-unit="kg"
                                        data-color="#138B68"
                                        data-labels="{{ json_encode($chartData['labels']) }}"
                                        data-values="{{ json_encode($chartData['weight']) }}"
                                        aria-label="Grafik berat badan"
                                        role="img"
                                    ></canvas>
                                </div>
                            </div>
                            <div class="border-t border-line pt-4">
                                <div class="mb-2 flex items-center justify-between">
                                    <p class="flex items-center gap-1.5 text-[11px] font-semibold text-ink"><span class="size-2 rounded-full bg-parent-500"></span>Tinggi / panjang badan</p>
                                    <span class="text-[10px] text-muted">cm</span>
                                </div>
                                <div class="h-[190px] w-full">
                                    <canvas
                                        data-chart
                                        data-label="Tinggi badan"
                                        data-unit="cm"
                                        data-color="#2F6FED"
                                        data-labels="{{ json_encode($chartData['labels']) }}"
                                        data-values="{{ json_encode($chartData['height']) }}"
                                        aria-label="Grafik tinggi badan"
                                        role="img"
                                    ></canvas>
                                </div>
                            </div>
                            <p class="flex items-start gap-1.5 rounded-xl bg-canvas px-3 py-2.5 text-[10px] leading-relaxed text-muted">
                                <x-icon name="info" class="mt-px size-3.5 shrink-0" />
                                Grafik bukan penilaian status gizi. Bandingkan hasil pengukuran dengan buku KIA atau konsultasikan ke tenaga kesehatan.
                            </p>
                        </div>
                    @endif
                </section>

                <section class="card overflow-hidden">
                    <div class="flex items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                        <div>
                            <h2 class="section-title">Riwayat pemeriksaan</h2>
                            <p class="mt-0.5 text-[11px] text-muted">Diurutkan dari yang terbaru.</p>
                        </div>
                        <a href="{{ route('kader.pemeriksaan.create', $balita) }}" class="btn btn-sm border border-line bg-white text-brand-700 hover:bg-brand-50">
                            <x-icon name="plus" class="size-3.5" />
                            Tambah
                        </a>
                    </div>

                    @if ($examinations->isEmpty())
                        <div class="p-4 sm:p-5">
                            <x-empty-state title="Belum ada pemeriksaan" description="Hasil pengukuran yang dicatat kader akan tersusun di sini." icon="clipboard" />
                        </div>
                    @else
                        <ul class="divide-y divide-line/80">
                            @foreach ($examinations->reverse() as $examination)
                                <li class="px-4 py-4 sm:px-5">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div class="flex items-start gap-3">
                                            <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                                                <x-icon name="scale" class="size-4" />
                                            </span>
                                            <div>
                                                <p class="text-[13px] font-semibold text-ink">{{ tanggal_id($examination->examined_at) }}</p>
                                                <p class="mt-0.5 text-[10px] text-muted">Usia {{ umur_balita($balita->date_of_birth, $examination->examined_at) }} · dicatat {{ $examination->recorder?->name ?? 'kader' }}</p>
                                            </div>
                                        </div>
                                        <a href="{{ route('kader.pemeriksaan.edit', $examination) }}" class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-[11px] font-semibold text-muted transition hover:bg-brand-50 hover:text-brand-700">
                                            <x-icon name="edit" class="size-3.5" />
                                            Ubah
                                        </a>
                                    </div>

                                    <div class="mt-3 grid grid-cols-3 gap-2">
                                        <div class="rounded-lg border border-line/70 px-2.5 py-2">
                                            <p class="text-[9px] text-muted">Berat</p>
                                            <p class="mt-0.5 text-[13px] font-semibold text-ink tabular-nums">{{ $examination->weight_kg }} <span class="text-[9px] font-normal text-muted">kg</span></p>
                                        </div>
                                        <div class="rounded-lg border border-line/70 px-2.5 py-2">
                                            <p class="text-[9px] text-muted">Tinggi</p>
                                            <p class="mt-0.5 text-[13px] font-semibold text-ink tabular-nums">{{ $examination->height_cm ?? '—' }} @if($examination->height_cm)<span class="text-[9px] font-normal text-muted">cm</span>@endif</p>
                                        </div>
                                        <div class="rounded-lg border border-line/70 px-2.5 py-2">
                                            <p class="text-[9px] text-muted">Lingkar kepala</p>
                                            <p class="mt-0.5 text-[13px] font-semibold text-ink tabular-nums">{{ $examination->head_circumference_cm ?? '—' }} @if($examination->head_circumference_cm)<span class="text-[9px] font-normal text-muted">cm</span>@endif</p>
                                        </div>
                                    </div>

                                    @if ($examination->notes)
                                        <p class="mt-2.5 rounded-lg bg-canvas px-3 py-2 text-[11px] leading-relaxed text-muted">{{ $examination->notes }}</p>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            </div>

            <div class="space-y-4">
                <section class="card overflow-hidden">
                    <div class="flex items-start justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                        <div>
                            <h2 class="section-title">Imunisasi</h2>
                            <p class="mt-0.5 text-[11px] text-muted">Catatan pemberian, bukan jadwal otomatis.</p>
                        </div>
                        <button type="button" x-on:click="addImmunization = true" class="btn btn-sm border border-line bg-white text-brand-700 hover:bg-brand-50">
                            <x-icon name="plus" class="size-3.5" />
                            Tambah
                        </button>
                    </div>

                    @if ($balita->immunizations->isEmpty())
                        <div class="p-4 sm:p-5">
                            <x-empty-state title="Belum ada catatan" description="Tambahkan imunisasi yang sudah diberikan dan tercatat." icon="shield" />
                        </div>
                    @else
                        <ul class="divide-y divide-line/80">
                            @foreach ($balita->immunizations->take(8) as $immunization)
                                <li class="flex items-start gap-2.5 px-4 py-3 sm:px-5">
                                    <span class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                                        <x-icon name="shield" class="size-4" />
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[12px] font-semibold text-ink">{{ $immunization->name }}</p>
                                        <p class="mt-0.5 text-[10px] text-muted">{{ tanggal_id($immunization->administered_at) }}</p>
                                        <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                                            <x-status-badge :label="$immunization->status_label" :tone="$immunization->status === 'needs_review' ? 'warning' : 'success'" />
                                            @if ($immunization->notes)
                                                <span class="text-[10px] text-muted">{{ \Illuminate\Support\Str::limit($immunization->notes, 48) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{ route('kader.imunisasi.edit', $immunization) }}" class="mt-0.5 flex size-7 items-center justify-center rounded-lg text-muted transition hover:bg-brand-50 hover:text-brand-700" title="Ubah catatan" aria-label="Ubah catatan imunisasi">
                                        <x-icon name="edit" class="size-3.5" />
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                <section class="card overflow-hidden">
                    <div class="flex items-start justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                        <div>
                            <h2 class="section-title">Akses orang tua / wali</h2>
                            <p class="mt-0.5 text-[11px] text-muted">Hanya akun terverifikasi yang dapat melihat.</p>
                        </div>
                        <button type="button" x-on:click="linkGuardian = true" class="btn btn-sm border border-line bg-white text-brand-700 hover:bg-brand-50">
                            <x-icon name="link" class="size-3.5" />
                            Hubungkan
                        </button>
                    </div>

                    @if ($balita->guardians->isEmpty())
                        <div class="p-4 sm:p-5">
                            <div class="flex items-start gap-2.5 rounded-xl bg-warm px-3 py-3 text-[11px] leading-relaxed text-[#7a5605]">
                                <x-icon name="alert-triangle" class="mt-px size-4 shrink-0" />
                                Belum ada akun orang tua/wali terhubung. Data tetap dapat dikelola kader, tetapi orang tua belum bisa masuk.
                            </div>
                        </div>
                    @else
                        <ul class="divide-y divide-line/80">
                            @foreach ($balita->guardians as $guardian)
                                <li class="px-4 py-3 sm:px-5">
                                    <div class="flex items-center gap-2.5">
                                        <x-avatar :name="$guardian->name" tone="parent" class="size-8 text-[10px]" />
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-[12px] font-semibold text-ink">{{ $guardian->name }}</p>
                                            <p class="mt-0.5 truncate text-[10px] text-muted">{{ $relationshipLabels[$guardian->pivot->relationship] ?? 'Wali' }}{{ $guardian->pivot->is_primary ? ' · utama' : '' }}</p>
                                        </div>
                                        <x-status-badge
                                            :label="$guardian->pivot->verified_at ? 'Terverifikasi' : 'Menunggu verifikasi'"
                                            :tone="$guardian->pivot->verified_at ? 'success' : 'warning'"
                                        />
                                    </div>
                                    <div class="mt-2 flex flex-wrap items-center gap-2 pl-10">
                                        @if (! $guardian->pivot->verified_at)
                                            <form method="POST" action="{{ route('kader.wali.verify', [$balita, $guardian]) }}">
                                                @csrf
                                                <button type="submit" class="text-[10px] font-semibold text-brand-600 hover:text-brand-700">Verifikasi akses</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('kader.wali.destroy', [$balita, $guardian]) }}" onsubmit="return confirm('Lepas hubungan akun ini dari balita?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-[10px] font-semibold text-danger/80 hover:text-danger">Lepas hubungan</button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="border-t border-line bg-canvas/60 px-4 py-3 text-[10px] leading-relaxed text-muted sm:px-5">
                        Satu anak dapat terhubung ke beberapa akun (ayah, ibu, atau wali). Akses tidak pernah dicocokkan hanya dari nama atau tanggal lahir.
                    </div>
                </section>

                <section class="card p-4 sm:p-5">
                    <h2 class="section-title">Informasi pendaftaran</h2>
                    <dl class="mt-3 space-y-2 text-[11px]">
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-muted">Terdaftar sejak</dt>
                            <dd class="font-medium text-ink">{{ tanggal_id($balita->registered_at) }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-muted">Posyandu</dt>
                            <dd class="text-right font-medium text-ink">{{ $balita->posyandu->name }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-muted">Akses orang tua</dt>
                            <dd class="font-medium {{ $balita->has_verified_guardian ? 'text-brand-700' : 'text-warm-strong' }}">{{ $balita->has_verified_guardian ? 'Aktif' : 'Belum aktif' }}</dd>
                        </div>
                    </dl>
                </section>
            </div>
        </div>

        <div
            x-show="addImmunization"
            x-cloak
            x-transition.opacity.duration.150ms
            class="fixed inset-0 z-50 flex items-end justify-center bg-slate-900/40 p-0 backdrop-blur-[2px] sm:items-center sm:p-4"
            x-on:keydown.escape.window="addImmunization = false"
        >
            <div x-show="addImmunization" x-transition.scale.origin.bottom.duration.200ms class="w-full max-w-lg overflow-hidden rounded-t-3xl bg-white shadow-float sm:rounded-3xl" role="dialog" aria-modal="true" aria-labelledby="immunization-modal-title">
                <div class="flex items-start justify-between gap-3 border-b border-line px-5 py-4">
                    <div>
                        <h2 id="immunization-modal-title" class="section-title">Tambah catatan imunisasi</h2>
                        <p class="mt-0.5 text-[11px] text-muted">{{ $balita->full_name }}</p>
                    </div>
                    <button type="button" x-on:click="addImmunization = false" class="flex size-8 items-center justify-center rounded-lg text-muted hover:bg-canvas" aria-label="Tutup">
                        <x-icon name="x" class="size-4" />
                    </button>
                </div>
                <form method="POST" action="{{ route('kader.imunisasi.store', $balita) }}" class="max-h-[75vh] space-y-4 overflow-y-auto p-5">
                    @csrf
                    <div>
                        <label for="immunization_name" class="label">Nama imunisasi <span class="text-danger">*</span></label>
                        <input id="immunization_name" name="name" type="text" value="{{ old('name') }}" placeholder="Contoh: Campak/MR" required maxlength="120" class="input @error('name') field-error @enderror">
                        @error('name')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="administered_at" class="label">Tanggal pemberian <span class="text-danger">*</span></label>
                            <input id="administered_at" name="administered_at" type="date" value="{{ old('administered_at', today()->toDateString()) }}" max="{{ today()->toDateString() }}" required class="input @error('administered_at') field-error @enderror">
                            @error('administered_at')<p class="error-text">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="immunization_status" class="label">Status pencatatan <span class="text-danger">*</span></label>
                            <select id="immunization_status" name="status" class="select @error('status') field-error @enderror">
                                <option value="recorded" @selected(old('status', 'recorded') === 'recorded')>Tercatat</option>
                                <option value="needs_review" @selected(old('status') === 'needs_review')>Perlu ditinjau</option>
                            </select>
                            @error('status')<p class="error-text">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label for="immunization_notes" class="label">Catatan <span class="optional">(opsional)</span></label>
                        <textarea id="immunization_notes" name="notes" rows="3" maxlength="1000" class="textarea" placeholder="Contoh: tanggal dikonfirmasi dari buku KIA">{{ old('notes') }}</textarea>
                        @error('notes')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                    <p class="flex items-start gap-2 rounded-xl bg-canvas px-3 py-2.5 text-[10px] leading-relaxed text-muted">
                        <x-icon name="info" class="mt-px size-3.5 shrink-0" />
                        Sistem tidak menandai imunisasi sebagai diberikan secara otomatis. Isi hanya berdasarkan catatan yang benar-benar tersedia.
                    </p>
                    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        <button type="button" x-on:click="addImmunization = false" class="btn btn-secondary">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan catatan</button>
                    </div>
                </form>
            </div>
        </div>

        <div
            x-show="linkGuardian"
            x-cloak
            x-transition.opacity.duration.150ms
            class="fixed inset-0 z-50 flex items-end justify-center bg-slate-900/40 p-0 backdrop-blur-[2px] sm:items-center sm:p-4"
            x-on:keydown.escape.window="linkGuardian = false"
        >
            <div x-show="linkGuardian" x-transition.scale.origin.bottom.duration.200ms class="w-full max-w-lg overflow-hidden rounded-t-3xl bg-white shadow-float sm:rounded-3xl" role="dialog" aria-modal="true" aria-labelledby="guardian-modal-title">
                <div class="flex items-start justify-between gap-3 border-b border-line px-5 py-4">
                    <div>
                        <h2 id="guardian-modal-title" class="section-title">Hubungkan akun orang tua/wali</h2>
                        <p class="mt-0.5 text-[11px] text-muted">Hanya akun yang dibuat kader.</p>
                    </div>
                    <button type="button" x-on:click="linkGuardian = false" class="flex size-8 items-center justify-center rounded-lg text-muted hover:bg-canvas" aria-label="Tutup">
                        <x-icon name="x" class="size-4" />
                    </button>
                </div>
                <form method="POST" action="{{ route('kader.wali.store', $balita) }}" class="max-h-[75vh] space-y-4 overflow-y-auto p-5">
                    @csrf
                    <div>
                        <label for="guardian_email" class="label">Akun orang tua/wali <span class="text-danger">*</span></label>
                        <select id="guardian_email" name="email" class="select @error('email') field-error @enderror" required>
                            <option value="" disabled @selected(! old('email'))>Pilih akun…</option>
                            @foreach ($availableParents as $parent)
                                <option value="{{ $parent->email }}" @selected(old('email') === $parent->email)>{{ $parent->name }} — {{ $parent->email }}</option>
                            @endforeach
                        </select>
                        @error('email')<p class="error-text">{{ $message }}</p>@enderror
                        @if ($availableParents->isEmpty())
                            <p class="help-text">Semua akun aktif di Posyandu ini sudah terhubung. <a href="{{ route('kader.orang-tua.create') }}" class="font-semibold text-brand-600">Buat akun baru</a> bila diperlukan.</p>
                        @else
                            <p class="help-text">Belum menemukan akun? <a href="{{ route('kader.orang-tua.create') }}" class="font-semibold text-brand-600">Buat akun orang tua</a> terlebih dahulu.</p>
                        @endif
                    </div>
                    <div>
                        <label for="relationship" class="label">Hubungan dengan balita <span class="text-danger">*</span></label>
                        <select id="relationship" name="relationship" class="select @error('relationship') field-error @enderror" required>
                            <option value="mother" @selected(old('relationship') === 'mother')>Ibu</option>
                            <option value="father" @selected(old('relationship') === 'father')>Ayah</option>
                            <option value="guardian" @selected(old('relationship', 'guardian') === 'guardian')>Wali</option>
                        </select>
                        @error('relationship')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-2.5 rounded-xl border border-line bg-canvas px-3.5 py-3">
                        <label class="flex cursor-pointer items-start gap-2.5 text-[12px] font-medium text-ink">
                            <input type="checkbox" name="verify_now" value="1" @checked(old('verify_now')) class="mt-0.5 size-4 rounded border-line text-brand-500 focus:ring-brand-500/30">
                            <span>Verifikasi akses sekarang <span class="block text-[10px] font-normal leading-relaxed text-muted">Orang tua dapat langsung melihat data setelah disimpan.</span></span>
                        </label>
                        <label class="flex cursor-pointer items-start gap-2.5 text-[12px] font-medium text-ink">
                            <input type="checkbox" name="is_primary" value="1" @checked(old('is_primary')) class="mt-0.5 size-4 rounded border-line text-brand-500 focus:ring-brand-500/30">
                            <span>Jadikan kontak utama <span class="block text-[10px] font-normal leading-relaxed text-muted">Kontak utama akan ditandai pada daftar wali.</span></span>
                        </label>
                    </div>
                    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        <button type="button" x-on:click="linkGuardian = false" class="btn btn-secondary">Batal</button>
                        <button type="submit" class="btn btn-primary">Hubungkan akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
