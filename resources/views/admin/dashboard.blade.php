@extends('layouts.admin', ['activeNav' => 'dashboard'])

@section('title', 'Dashboard')

@section('content')

    {{-- ═══ HEADER ═══ --}}
    <header class="flex flex-col sm:flex-row sm:items-center
                   sm:justify-between gap-4 flex-shrink-0">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-1 rounded-lg
                             bg-[#186369] text-white
                             text-[9px] font-bold uppercase tracking-wider">
                    Admin Panel
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-[#186369]">
                Halo, Admin! 👋
            </h1>
            <p class="text-sm text-[#186369]/70 mt-1">
                Selamat datang di panel admin Mindo
            </p>
        </div>

        <div class="text-right">
            <p class="text-[10px] font-bold text-[#186369]/50 uppercase tracking-wider">
                Hari Ini
            </p>
            <p class="text-sm font-bold text-[#186369]" id="tanggalHariIni"></p>
        </div>
    </header>

    {{-- ═══ STATS CARDS — angka diambil dari inputan tiap fitur ═══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 flex-shrink-0">

        {{-- Artikel → dari /admin/artikel --}}
        <a href="/admin/artikel"
           class="item-anim bg-white rounded-[28px] p-5 border border-gray-100
                  hover:shadow-xl hover:shadow-[#186369]/10 hover:-translate-y-1
                  transition-all duration-300 group"
           style="animation-delay: 0ms;">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-2xl bg-[#186369]/10 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" class="w-5 h-5 text-[#186369]" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4a2 2 0 012-2h8l6 6v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z M14 2v6h6 M9 13h6M9 17h6"/>
                    </svg>
                </div>
                <svg viewBox="0 0 24 24" class="w-4 h-4 text-[#186369]/30
                        group-hover:text-[#186369] group-hover:translate-x-1 transition-all"
                     fill="none" stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 5l7 7-7 7"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-[#186369]"
                    data-counter="artikel">{{ $stats['artikel'] }}</p>
            <p class="text-[11px] font-bold text-[#186369]/50 mt-1">Artikel</p>
        </a>

        {{-- Kategori → dari /admin/kategori --}}
        <a href="/admin/kategori"
           class="item-anim bg-white rounded-[28px] p-5 border border-gray-100
                  hover:shadow-xl hover:shadow-[#c0a05f]/10 hover:-translate-y-1
                  transition-all duration-300 group"
           style="animation-delay: 60ms;">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-2xl bg-[#c0a05f]/10 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" class="w-5 h-5 text-[#c0a05f]" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/>
                    </svg>
                </div>
                <svg viewBox="0 0 24 24" class="w-4 h-4 text-[#c0a05f]/30
                        group-hover:text-[#c0a05f] group-hover:translate-x-1 transition-all"
                     fill="none" stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 5l7 7-7 7"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-[#c0a05f]"
                    data-counter="kategori">{{ $stats['kategori'] }}</p>
            <p class="text-[11px] font-bold text-[#c0a05f]/60 mt-1">Kategori</p>
        </a>

        {{-- Aktivitas → total sub-kategori dari semua kategori --}}
        <a href="/admin/kategori"
           class="item-anim bg-white rounded-[28px] p-5 border border-gray-100
                  hover:shadow-xl hover:shadow-[#4090f2]/10 hover:-translate-y-1
                  transition-all duration-300 group"
           style="animation-delay: 120ms;">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-2xl bg-[#4090f2]/10 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" class="w-5 h-5 text-[#4090f2]" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 11l3 3L22 4 M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                    </svg>
                </div>
                <svg viewBox="0 0 24 24" class="w-4 h-4 text-[#4090f2]/30
                        group-hover:text-[#4090f2] group-hover:translate-x-1 transition-all"
                     fill="none" stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 5l7 7-7 7"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-[#4090f2]"
                    data-counter="aktivitas">{{ $stats['aktivitas'] }}</p>
            <p class="text-[11px] font-bold text-[#4090f2]/60 mt-1">Aktivitas</p>
        </a>

        {{-- Dokter → dari /admin/dokter --}}
        <a href="/admin/dokter"
           class="item-anim bg-white rounded-[28px] p-5 border border-gray-100
                  hover:shadow-xl hover:shadow-[#8b7bd8]/10 hover:-translate-y-1
                  transition-all duration-300 group"
           style="animation-delay: 180ms;">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-2xl bg-[#8b7bd8]/10 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" class="w-5 h-5 text-[#8b7bd8]" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2 M12 11a4 4 0 100-8 4 4 0 000 8z"/>
                    </svg>
                </div>
                <svg viewBox="0 0 24 24" class="w-4 h-4 text-[#8b7bd8]/30
                        group-hover:text-[#8b7bd8] group-hover:translate-x-1 transition-all"
                     fill="none" stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 5l7 7-7 7"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-[#8b7bd8]"
                    data-counter="dokter">{{ $stats['dokter'] }}</p>
            <p class="text-[11px] font-bold text-[#8b7bd8]/60 mt-1">Dokter</p>
        </a>

    </div>

    {{-- ═══ INFO PANEL ═══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 flex-1 min-h-0">

        <section class="bg-white rounded-[32px] p-6 border border-gray-100">
            <h2 class="text-lg font-bold text-[#186369] mb-4 flex items-center gap-2">
                <span class="text-xl">💡</span>
                Panduan Admin
            </h2>

            <div class="space-y-3">
                <div class="flex gap-3 p-3 rounded-2xl bg-[#f5f8f7]">
                    <div class="w-8 h-8 rounded-lg bg-[#c0a05f] text-white
                                flex items-center justify-center text-xs font-bold flex-shrink-0">1</div>
                    <div>
                        <p class="text-xs font-bold text-[#186369]">Kelola Kategori</p>
                        <p class="text-[10px] text-[#186369]/60 mt-0.5 leading-relaxed">
                            Tambah/edit/hapus kategori + isi sub-kategori aktivitas di dalamnya.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 p-3 rounded-2xl bg-[#f5f8f7]">
                    <div class="w-8 h-8 rounded-lg bg-[#186369] text-white
                                flex items-center justify-center text-xs font-bold flex-shrink-0">2</div>
                    <div>
                        <p class="text-xs font-bold text-[#186369]">Tambah Artikel</p>
                        <p class="text-[10px] text-[#186369]/60 mt-0.5 leading-relaxed">
                            Input artikel kesehatan untuk dashboard & halaman artikel.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 p-3 rounded-2xl bg-[#f5f8f7]">
                    <div class="w-8 h-8 rounded-lg bg-[#8b7bd8] text-white
                                flex items-center justify-center text-xs font-bold flex-shrink-0">3</div>
                    <div>
                        <p class="text-xs font-bold text-[#186369]">Input Dokter</p>
                        <p class="text-[10px] text-[#186369]/60 mt-0.5 leading-relaxed">
                            Tambah dokter dengan nomor WA & website untuk halaman konsultasi.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white rounded-[32px] p-6 border border-gray-100">
            <h2 class="text-lg font-bold text-[#186369] mb-4 flex items-center gap-2">
                <span class="text-xl">📊</span>
                Status Sistem
            </h2>

            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 rounded-2xl bg-green-50 border border-green-100">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                        <p class="text-xs font-bold text-green-700">Database</p>
                    </div>
                    <span class="text-[10px] font-bold text-green-600">MySQL</span>
                </div>

                <div class="flex items-center justify-between p-3 rounded-2xl bg-green-50 border border-green-100">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                        <p class="text-xs font-bold text-green-700">Framework</p>
                    </div>
                    <span class="text-[10px] font-bold text-green-600">Laravel 12</span>
                </div>

                <div class="flex items-center justify-between p-3 rounded-2xl bg-green-50 border border-green-100">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                        <p class="text-xs font-bold text-green-700">PHP</p>
                    </div>
                    <span class="text-[10px] font-bold text-green-600">8.5</span>
                </div>

                <div class="flex items-center justify-between p-3 rounded-2xl bg-blue-50 border border-blue-100">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                        <p class="text-xs font-bold text-blue-700">Data Aktivitas</p>
                    </div>
                    <span class="text-[10px] font-bold text-blue-600">{{ $stats['aktivitas'] }} tersimpan</span>
                </div>
            </div>
        </section>
    </div>

@endsection

@push('scripts')
<script>
    const months = ['Januari','Februari','Maret','April','Mei','Juni',
                    'Juli','Agustus','September','Oktober','November','Desember'];
    const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const today = new Date();
    document.getElementById('tanggalHariIni').textContent =
        `${days[today.getDay()]}, ${today.getDate()} ${months[today.getMonth()]} ${today.getFullYear()}`;
</script>
@endpush