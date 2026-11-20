@extends('layouts.kader')

@section('title', 'Akun Orang Tua')

@section('content')
    <div class="fade-up">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="eyebrow">Akses keluarga</p>
                <h1 class="page-title mt-1.5">Akun Orang Tua / Wali</h1>
                <p class="mt-1.5 max-w-xl text-[13px] leading-relaxed text-muted">
                    Kader membuat akun, lalu menghubungkannya ke balita dari halaman detail. Tidak ada pendaftaran mandiri.
                </p>
            </div>
            <a href="{{ route('kader.orang-tua.create') }}" class="btn btn-primary">
                <x-icon name="user-plus" class="size-4" />
                Buat akun
            </a>
        </div>

        <form method="GET" action="{{ route('kader.orang-tua.index') }}" class="mt-5">
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-slate-400">
                    <x-icon name="search" class="size-[18px]" />
                </span>
                <input type="search" name="q" value="{{ $search }}" placeholder="Cari nama, email, atau nomor HP…" class="input h-12 rounded-2xl pl-11 pr-20" aria-label="Cari akun orang tua">
                <button type="submit" class="absolute inset-y-1.5 right-1.5 rounded-xl bg-brand-500 px-3.5 text-[12px] font-semibold text-white transition hover:bg-brand-600">Cari</button>
            </div>
        </form>

        <div class="mt-4 flex items-center justify-between gap-2">
            <p class="text-[11px] text-muted">{{ $parents->total() }} akun{{ $search !== '' ? ' cocok dengan “'.$search.'”' : ' terdaftar' }}</p>
            @if ($search !== '')
                <a href="{{ route('kader.orang-tua.index') }}" class="text-[11px] font-semibold text-danger">Hapus pencarian</a>
            @endif
        </div>

        @if ($parents->isEmpty())
            <div class="mt-3">
                <x-empty-state
                    :title="$search !== '' ? 'Akun tidak ditemukan' : 'Belum ada akun orang tua'"
                    :description="$search !== '' ? 'Coba kata kunci lain.' : 'Buat akun terlebih dahulu, lalu hubungkan ke balita melalui halaman detail balita.'"
                    icon="user-plus"
                >
                    <a href="{{ route('kader.orang-tua.create') }}" class="btn btn-primary btn-sm">Buat akun</a>
                </x-empty-state>
            </div>
        @else
            <div class="mt-3 space-y-2.5">
                @foreach ($parents as $parent)
                    <article class="card p-4 transition hover:border-brand-200">
                        <div class="flex items-start gap-3">
                            <x-avatar :name="$parent->name" tone="parent" class="size-10 text-[12px]" />
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="truncate text-[13px] font-semibold text-ink">{{ $parent->name }}</h2>
                                    <x-status-badge :label="$parent->is_active ? 'Aktif' : 'Nonaktif'" :tone="$parent->is_active ? 'success' : 'danger'" />
                                </div>
                                <p class="mt-1 truncate text-[11px] text-muted">{{ $parent->email }}</p>
                                <p class="mt-0.5 text-[11px] text-muted">{{ $parent->phone ?: 'Nomor HP belum diisi' }}</p>
                                <p class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-canvas px-2.5 py-1 text-[10px] font-medium text-muted">
                                    <x-icon name="baby" class="size-3" />
                                    {{ $parent->children_count }} balita terhubung
                                </p>
                            </div>
                            <a href="{{ route('kader.orang-tua.edit', $parent) }}" class="btn btn-sm border border-line bg-white text-brand-700 hover:bg-brand-50">
                                <x-icon name="edit" class="size-3.5" />
                                <span class="hidden sm:inline">Ubah</span>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($parents->hasPages())
                <div class="mt-5">{{ $parents->links() }}</div>
            @endif
        @endif

        <div class="mt-5 flex items-start gap-2.5 rounded-2xl border border-parent-100 bg-parent-50/70 p-4 text-[11px] leading-relaxed text-parent-700">
            <x-icon name="shield" class="mt-0.5 size-4 shrink-0" />
            <p>Akun tidak otomatis memiliki akses ke balita. Hubungkan akun dengan anak melalui tombol “Hubungkan” di halaman detail balita, lalu verifikasi. Jangan membagikan kata sandi melalui saluran publik.</p>
        </div>
    </div>
@endsection
