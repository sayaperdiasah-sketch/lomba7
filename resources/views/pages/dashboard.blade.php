@extends('layouts.app', ['activeNav' => 'home'])

@section('title', 'Beranda')

@push('styles')
<style>
    @keyframes draw-line {
        from { stroke-dashoffset: var(--line-length); }
        to { stroke-dashoffset: 0; }
    }
    @keyframes pop-in {
        0% { transform: scale(0); opacity: 0; }
        60% { transform: scale(1.3); opacity: 1; }
        100% { transform: scale(1); opacity: 1; }
    }
    .chart-line-animate { stroke-dasharray: var(--line-length); animation: draw-line 1.5s ease-out forwards; }
    .chart-dot { transform-origin: center; transform-box: fill-box; animation: pop-in 0.4s ease-out backwards; }

    /* ═══ SEGMENT GROW-IN ═══ */
    @keyframes seg-grow {
        from {
            stroke-dasharray: 0 var(--circumference);
            opacity: 0.3;
        }
    }
    .seg-grow {
        animation: seg-grow 1s cubic-bezier(0.34, 1.2, 0.64, 1) backwards;
    }

    /* ═══ WARNA MUTER — highlight yang mengalir di sepanjang segment ═══ */
    @keyframes color-flow-1 {
        from { stroke-dashoffset: 0; }
        to   { stroke-dashoffset: -40; }
    }
    .color-flow-1 {
        animation: color-flow-1 3s linear infinite;
    }

    @keyframes color-flow-2 {
        from { stroke-dashoffset: 0; }
        to   { stroke-dashoffset: 40; }
    }
    .color-flow-2 {
        animation: color-flow-2 4s linear infinite;
    }

    @keyframes color-shimmer {
        0%, 100% { opacity: 0.4; }
        50%      { opacity: 0.85; }
    }
    .color-shimmer {
        animation: color-shimmer 2.5s ease-in-out infinite;
    }

    /* ═══ WAVE BERGELOMBANG di dalam segment ═══ */
    @keyframes wave-flow {
        from { stroke-dashoffset: 0; }
        to   { stroke-dashoffset: -24; }
    }
    .wave-flow {
        animation: wave-flow 1.5s linear infinite;
    }

    @keyframes donut-spin {
        to { transform: rotate(360deg); }
    }
    .donut-ring-spin {
        transform-box: fill-box;
        transform-origin: center;
        will-change: transform;
    }
    .donut-hover-active .donut-ring-spin {
        animation: donut-spin 4s linear infinite;
    }
    .donut-hover-ring { transition: opacity 0.25s ease; }
</style>
@endpush

@section('content')
<script>
    window.serverDashboardCategories = @json($categories);
    window.serverDashboardSchedules = @json($schedules);
    window.serverDashboardArticles = @json($articles);
    window.serverDashboardSearchArticles = @json($searchArticles);
    window.serverDashboardDoctors = @json($doctors);
    window.serverDashboardAvatar = @json($avatarUrl);
</script>
<div x-data="dashboardApp()"
     @open-chart.window="openChartDetail($event.detail.item)"
     @open-chart-with-video.window="playVideoThenChart($event.detail.item)"
     class="contents">

    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 flex-shrink-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-[#186369]">Halo, {{ Auth::user()->name ?? 'Pengguna' }}!</h1>
            <p class="text-sm text-[#186369]/70 mt-1">Selamat datang di mindo</p>
        </div>

        <div class="flex items-center gap-3">
            <form role="search" class="relative flex-1 sm:w-72 lg:w-80"
                  @submit.prevent="searchOpen = searchQuery.trim().length > 0">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 bg-gray-400 rounded"></div>
                <input type="search" x-model="searchQuery" @input="broadcastSearch()"
                       @focus="searchOpen = searchQuery.trim().length > 0"
                       placeholder="Cari aktivitas, artikel, atau fitur"
                       class="w-full pl-11 pr-4 py-2.5 text-xs font-bold
                              bg-white border-2 border-gray-300 rounded-full
                              focus:outline-none focus:border-[#186369]">
                <div x-show="searchOpen && searchQuery.trim()" x-cloak
                     @click.outside="searchOpen = false"
                     class="absolute left-0 right-0 top-full mt-2 z-40 bg-white rounded-2xl
                            border border-gray-100 shadow-xl p-2 max-h-80 overflow-y-auto">
                    <template x-if="searchResults.length > 0">
                        <div class="space-y-1">
                            <template x-for="result in searchResults" :key="result.key">
                                <a :href="result.href || '#'"
                                   @click="selectSearchResult(result)"
                                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl
                                          hover:bg-[#f5f8f7] transition">
                                    <span class="w-8 h-8 rounded-lg flex items-center justify-center
                                                 bg-[#186369]/10 text-[#186369] text-xs font-bold"
                                          x-text="result.icon"></span>
                                    <span class="flex-1 min-w-0">
                                        <span class="block text-xs font-bold text-[#186369] truncate"
                                              x-text="result.label"></span>
                                        <span class="block text-[10px] text-[#186369]/50"
                                              x-text="result.type"></span>
                                    </span>
                                    <span class="text-[#186369]/30">→</span>
                                </a>
                            </template>
                        </div>
                    </template>
                    <p x-show="searchResults.length === 0"
                       class="px-3 py-4 text-center text-xs font-bold text-[#186369]/50">
                        Tidak ada hasil pencarian
                    </p>
                </div>
            </form>

            <div class="relative flex-shrink-0">
                <span x-show="rippleActive"
                      class="absolute inset-0 rounded-full bg-[#186369] pointer-events-none avatar-ripple"></span>
                <button type="button" @click="toggleProfile()"
                        :title="profileOpen ? 'Tutup profil' : 'Buka profil'"
                        class="relative flex-shrink-0 w-11 h-11 lg:w-12 lg:h-12
                               rounded-full overflow-hidden border-2 lg:border-[3px]
                               transition-all duration-300 hover:scale-105 active:scale-95"
                        :class="profileOpen
                            ? 'border-[#186369] shadow-lg shadow-[#186369]/20'
                            : 'border-white shadow-md'">
                    <img :src="avatarUrl" alt="Profil" class="w-full h-full object-cover">
                    <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full
                                 border-2 border-white transition-colors duration-300"
                          :class="profileOpen ? 'bg-emerald-400' : 'bg-gray-300'"></span>
                </button>
            </div>
        </div>
    </header>

    {{-- ═══ CEK MENTAL + DONUT STATS ═══ --}}
    <div class="grid grid-cols-1 md:grid-cols-[1.3fr_1fr] gap-4 flex-shrink-0">
        <section class="relative bg-[#186369] rounded-[40px]
                        p-6 sm:p-8 min-h-[240px] sm:min-h-[280px]
                        overflow-hidden text-white flex flex-col justify-center">
            <div class="relative z-10">
                <h2 class="text-2xl sm:text-3xl font-bold">Cek Mental Yuk</h2>
                <p class="text-sm sm:text-base mt-2 opacity-70 max-w-[60%] leading-relaxed">
                    Mari Kenali Gejala yang sedang kamu alami sekarang
                </p>
                <a href="{{ route('tes-mental') }}" data-auth
                   class="mt-6 px-7 py-3 bg-white/25 rounded-2xl
                          text-white font-bold text-sm inline-block
                          hover:bg-white/35 transition active:scale-95 backdrop-blur-sm">
                    Mulai
                </a>
            </div>
            <img src="{{ asset('images/dashboard/cek-mental.png') }}" alt="Ilustrasi Cek Mental"
                 class="absolute right-4 bottom-4 w-32 h-28 sm:w-44 sm:h-40
                        object-contain pointer-events-none select-none drop-shadow-2xl">
        </section>

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- DONUT: warna mengalir di dalam tiap segment             --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        <section class="relative bg-gradient-to-br from-[#9fd6f8] to-[#7ec3ec]
                        rounded-[40px] p-5 sm:p-6 min-h-[240px] sm:max-h-[320px]
                        overflow-hidden" x-data="activityStats()" x-init="init()">
            <div class="absolute top-4 left-4 z-20" @click.outside="periodOpen = false">
                <button type="button"
                        @click="periodOpen = !periodOpen"
                        :aria-expanded="periodOpen"
                        aria-label="Pilih rentang waktu rekapan"
                        class="flex items-center gap-2 px-3 py-2 rounded-xl
                               bg-white/65 hover:bg-white/85 text-[#186369]
                               shadow-sm backdrop-blur-sm transition active:scale-95">
                    <svg viewBox="0 0 16 16" class="w-3.5 h-3.5" fill="none"
                         stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2.5" y="3.5" width="11" height="10" rx="1.5"/>
                        <path d="M5 2.5v2M11 2.5v2M2.5 6.5h11"/>
                    </svg>
                    <span class="text-[9px] font-bold" x-text="periodLabel()"></span>
                    <svg viewBox="0 0 16 16" class="w-3 h-3 transition-transform"
                         :class="periodOpen ? 'rotate-180' : ''" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 6l4 4 4-4"/>
                    </svg>
                </button>

                <div x-show="periodOpen" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
                     class="absolute left-0 mt-2 w-32 p-1.5 rounded-2xl
                            bg-white/95 shadow-xl border border-white/80 backdrop-blur-sm">
                    <template x-for="option in periodOptions" :key="option.key">
                        <button type="button"
                                @click="setPeriod(option.key)"
                                class="w-full text-left px-3 py-2 rounded-xl text-[9px] font-bold transition"
                                :class="period === option.key
                                    ? 'bg-[#186369] text-white'
                                    : 'text-[#186369]/70 hover:bg-[#f5f8f7]'"
                                x-text="option.label"></button>
                    </template>
                </div>
            </div>
            <div class="h-full flex items-center gap-4 sm:gap-5">

                {{-- ═══ DONUT ═══ --}}
                <div class="relative flex-shrink-0 w-[140px] h-[140px] sm:w-[160px] sm:h-[160px]
                            cursor-pointer select-none transition-transform duration-300
                        hover:scale-105 active:scale-95"
                    :class="{ 'donut-hover-active': donutHover }"
                    @mouseenter="donutHover = true"
                    @mouseleave="donutHover = false"
                     @click="explodeDonut($event)" data-auth>

                      <svg viewBox="0 0 120 120" class="w-full h-full donut-ring-spin">
                        <defs>
                            {{-- Mask per segment --}}
                            <template x-for="(seg, i) in segments" :key="'m'+i">
                                <mask :id="'seg-mask-' + i"
                                      maskUnits="userSpaceOnUse" x="0" y="0" width="120" height="120">
                                    <circle cx="60" cy="60" r="46"
                                            stroke="white" stroke-width="12" fill="none"
                                            :stroke-dasharray="`${seg.length} ${circumference}`"
                                            :stroke-dashoffset="seg.offset"
                                            stroke-linecap="round" />
                                </mask>
                            </template>
                        </defs>

                        {{-- Track abu-abu --}}
                        <circle cx="60" cy="60" r="46" fill="none"
                                stroke="rgba(255,255,255,0.3)" stroke-width="10" />

                        {{-- ═══ LAYER 1: BASE SEGMENT — warna kategori solid ═══ --}}
                        <template x-for="(seg, i) in segments" :key="i">
                            <circle cx="60" cy="60" r="46"
                                    :stroke="items[i].warna"
                                    stroke-width="10" fill="none"
                                    :stroke-dasharray="`${seg.length} ${circumference}`"
                                    :stroke-dashoffset="seg.offset"
                                    stroke-linecap="round"
                                    class="seg-grow"
                                    :style="`--circumference: ${circumference};
                                             animation-delay: ${i * 80}ms;`" />
                        </template>

                        {{-- Highlight kategori di atas semua lapisan saat donut diarahkan --}}
                        <template x-for="(seg, i) in segments" :key="'hover-ring-' + i">
                            <circle cx="60" cy="60" r="46"
                                    :stroke="items[i].warna"
                                    stroke-width="16" fill="none"
                                    :stroke-dasharray="`${seg.length} ${circumference}`"
                                    :stroke-dashoffset="seg.offset"
                                    stroke-linecap="round"
                                    class="donut-hover-ring"
                                    :style="`opacity: ${donutHover ? 0.95 : 0};
                                              filter: drop-shadow(0 0 5px ${items[i].warna});`" />
                        </template>

                        {{-- ═══ LAYER 2: COLOR FLOW — highlight warna terang mengalir ═══ --}}
                        <template x-for="(seg, i) in segments" :key="'cf'+i">
                            <g :mask="`url(#seg-mask-${i})`">
                                {{-- Flow 1: highlight terang, mengalir maju --}}
                                <circle cx="60" cy="60" r="46"
                                        stroke="white" stroke-opacity="0.55"
                                        stroke-width="8" fill="none"
                                        stroke-dasharray="8 30"
                                        stroke-linecap="round"
                                        class="color-flow-1"
                                        :style="`animation-delay: ${i * 300}ms;`" />

                                {{-- Flow 2: highlight tipis, mengalir balik --}}
                                <circle cx="60" cy="60" r="46"
                                        stroke="white" stroke-opacity="0.3"
                                        stroke-width="4" fill="none"
                                        stroke-dasharray="3 18"
                                        stroke-linecap="round"
                                        class="color-flow-2"
                                        :style="`animation-delay: ${i * 200}ms;`" />

                                {{-- Wave shimmer — sparkle yang muncul tenggelam --}}
                                <circle cx="60" cy="60" r="46"
                                        stroke="white" stroke-opacity="0.7"
                                        stroke-width="10" fill="none"
                                        stroke-dasharray="2 50"
                                        stroke-linecap="round"
                                        class="wave-flow color-shimmer"
                                        :style="`animation-delay: ${i * 400}ms;`" />
                            </g>
                        </template>
                    </svg>

                    {{-- Angka tengah --}}
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-3xl sm:text-4xl font-bold text-[#186369] leading-none"
                              x-text="totalDone"></span>
                        <span class="text-[9px] font-bold text-[#186369]/60 mt-1 uppercase tracking-wider">
                            Selesai
                        </span>
                        <span class="text-[8px] font-bold text-[#186369]/40 mt-0.5"
                            x-text="percent + '% ' + periodLabel()"></span>
                    </div>
                </div>

                {{-- ═══ LEGEND ═══ --}}
                <div class="flex-1 min-w-0 flex flex-col" style="max-height: 200px;">
                    <template x-if="items.length === 0">
                        <div class="text-center py-4 my-auto">
                            <p class="text-[11px] font-bold text-[#186369]/60">
                                Belum ada aktivitas selesai
                            </p>
                            <a href="/jadwal"
                               class="mt-2 inline-flex items-center gap-1
                                      px-3 py-1.5 rounded-lg bg-white/60 hover:bg-white
                                      text-[10px] font-bold text-[#186369]
                                      transition active:scale-95">
                                Buka To Do List
                                <svg viewBox="0 0 16 16" class="w-2.5 h-2.5" fill="none"
                                     stroke="currentColor" stroke-width="2.5"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 4l4 4-4 4"/>
                                </svg>
                            </a>
                        </div>
                    </template>

                    <template x-if="items.length > 0">
                        <div class="flex-1 min-h-0 overflow-y-auto scroll-thin pr-1 space-y-1.5">
                            <template x-for="(item, i) in items" :key="i">
                                <button type="button" @click="openChart(item, i)" data-auth
                                        class="w-full text-left flex items-center gap-2.5
                                               bg-white/40 hover:bg-white/70 backdrop-blur-sm
                                               rounded-xl px-3 py-2 border border-white/60
                                               transition-all duration-200 group
                                               hover:translate-x-0.5">
                                    <div class="w-3 h-3 rounded-full flex-shrink-0
                                                transition-transform group-hover:scale-125"
                                         :style="`background:${item.warna};
                                                  box-shadow: 0 0 6px ${item.warna}80;`"></div>
                                    <p class="flex-1 text-[11px] sm:text-xs font-bold text-[#186369] truncate"
                                       x-text="item.label"></p>
                                    <p class="text-sm sm:text-base font-bold text-[#186369]"
                                       x-text="item.value"></p>
                                </button>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </section>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- AKTIVITAS FILTER ════════════════════════════════════════ --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div x-data="activityFilter()" x-init="init()" class="flex-shrink-0">
        <div class="flex items-center gap-3 mb-3 overflow-x-auto scroll-thin-h pb-1">
            <h2 class="text-lg font-bold text-[#186369] flex-shrink-0">Aktivitas :</h2>
            <div class="flex items-center gap-2 flex-shrink-0 ml-auto">
                <div class="flex items-center gap-1 bg-white rounded-xl border border-gray-200 p-1 shadow-sm">
                    <template x-for="option in periodOptions" :key="option.key">
                        <button type="button"
                                @click="setPeriod(option.key)"
                                class="px-2 py-1 rounded-lg text-[9px] font-bold transition"
                                :class="period === option.key
                                    ? 'bg-[#186369] text-white shadow-sm'
                                    : 'text-[#186369]/60 hover:bg-[#f5f8f7]'"
                                x-text="option.label"></button>
                    </template>
                </div>
                <span class="text-[10px] font-bold text-[#186369]/40" x-text="stats.length + ' kategori'"></span>
                <button type="button"
                        @click="resetFilter()"
                        x-show="activeCategory !== null"
                        class="text-[10px] font-bold text-[#186369]/60 hover:text-[#186369]
                               bg-white px-2 py-1 rounded-lg border border-gray-200
                               transition flex items-center gap-1 shadow-sm">
                    <svg viewBox="0 0 16 16" class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <path d="M4 4l8 8M12 4l-8 8"/>
                    </svg>
                    Reset
                </button>
            </div>
        </div>

        <template x-if="stats.length > 0">
            <div class="flex gap-3 overflow-x-auto pb-2 scroll-thin-h snap-x snap-mandatory">
                <button type="button"
                        @click="resetFilter()"
                        :style="`background: ${activeCategory === null ? '#186369' : '#ffffff'}; color: ${activeCategory === null ? '#ffffff' : '#186369'}; border-color: ${activeCategory === null ? '#186369' : '#e5e7eb'}; box-shadow: ${activeCategory === null ? '0 12px 28px rgba(24,99,105,0.18)' : '0 4px 12px rgba(15,23,42,0.04)'};`"
                        class="snap-start flex-shrink-0 w-[180px] sm:w-[200px]
                               rounded-[28px] p-4 sm:p-5 text-left
                               transition-all duration-200 hover:scale-[1.01] active:scale-[0.98]
                               border-2 shadow-sm">
                    <div class="flex justify-between items-center mb-3">
                        <p class="text-sm font-bold">Semua</p>
                        <div class="text-right">
                            <p class="text-2xl sm:text-3xl font-bold leading-none" x-text="totalPercent + '%'"></p>
                        </div>
                    </div>
                    <div class="h-1.5 rounded-full overflow-hidden mb-2"
                         :style="`background: ${activeCategory === null ? 'rgba(255,255,255,0.25)' : 'rgba(24,99,105,0.08)'};`">
                        <div class="h-full rounded-full transition-all duration-700"
                             :style="`width: ${totalPercent}%; background: ${activeCategory === null ? '#ffffff' : '#186369'};`"></div>
                    </div>
                    <p class="text-[10px] font-bold opacity-70" x-text="totalDone + ' / ' + totalTask + ' task'"></p>
                </button>

                <template x-for="cat in stats" :key="cat.nama">
                    <button type="button"
                            @click="selectCategory(cat.nama)"
                            :style="`background: ${activeCategory === cat.nama ? cat.warna : '#ffffff'}; color: ${activeCategory === cat.nama ? cat.teksWarna : '#186369'}; border-color: ${activeCategory === cat.nama ? cat.warna : '#e5e7eb'}; box-shadow: ${activeCategory === cat.nama ? '0 12px 28px ' + cat.warna + '33' : '0 4px 12px rgba(15,23,42,0.04)'};`"
                            class="snap-start flex-shrink-0 w-[180px] sm:w-[200px]
                                   rounded-[28px] p-4 sm:p-5 text-left
                                   transition-all duration-200 hover:scale-[1.01] active:scale-[0.98]
                                   border-2 shadow-sm">
                        <div class="flex justify-between items-center mb-3">
                            <p class="text-sm font-bold truncate max-w-[110px]" x-text="cat.nama"></p>
                            <div class="text-right flex-shrink-0">
                                <p class="text-2xl sm:text-3xl font-bold leading-none" x-text="cat.percent + '%'"></p>
                            </div>
                        </div>
                        <div class="h-1.5 rounded-full overflow-hidden mb-2"
                             :style="`background: ${activeCategory === cat.nama ? 'rgba(255,255,255,0.25)' : `${cat.warna}20`};`">
                            <div class="h-full rounded-full transition-all duration-700"
                                 :style="`width: ${cat.percent}%; background: ${activeCategory === cat.nama ? cat.teksWarna : cat.warna};`"></div>
                        </div>
                        <p class="text-[10px] font-bold opacity-70" x-text="cat.done + ' / ' + cat.total + ' task'"></p>
                    </button>
                </template>
            </div>
        </template>

        <template x-if="allActivities.length > 0">
            <div class="bg-white rounded-[40px] p-5 sm:p-6 mt-4 border border-gray-100 shadow-[0_12px_30px_rgba(24,99,105,0.06)]">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-bold text-[#186369] flex items-center gap-2">
                            <span x-text="activeCategory ? '📋 ' + activeCategory : '📋 Semua Aktivitas'"></span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#186369]/10 text-[#186369]"
                                  x-text="visibleActivities.length + ' task'"></span>
                        </h3>
                        <p class="text-[10px] text-[#186369]/50 mt-0.5">
                            <span x-text="periodLabel()"></span>
                            <span x-show="activeCategory === null">· semua kategori</span>
                        </p>
                    </div>
                    <button type="button"
                            @click="onlyPending = !onlyPending"
                            class="text-[10px] font-bold px-3 py-1.5 rounded-lg
                                   transition active:scale-95 flex items-center gap-1"
                            :class="onlyPending ? 'bg-amber-100 text-amber-700' : 'bg-[#f5f8f7] text-[#186369]'">
                        <svg viewBox="0 0 16 16" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 8l4 4 8-8"/>
                        </svg>
                        <span x-text="onlyPending ? 'Belum Selesai' : 'Semua'"></span>
                    </button>
                </div>

                <template x-if="visibleActivities.length === 0">
                    <div class="text-center py-8">
                        <p class="text-xs font-bold text-[#186369]/60" x-text="'Belum ada aktivitas ' + activeCategory + ' minggu ini'"></p>
                        <button type="button"
                                @click="resetFilter()"
                                class="mt-3 text-[10px] font-bold text-[#186369]
                                       bg-[#f5f8f7] hover:bg-[#eef3f2]
                                       px-3 py-1.5 rounded-lg transition">
                            Tampilkan Semua
                        </button>
                    </div>
                </template>

                <div class="space-y-2 max-h-[340px] overflow-y-auto scroll-thin pr-1">
                    <template x-for="(act, i) in visibleActivities" :key="act.uid">
                        <div class="rounded-2xl p-3.5 flex items-center gap-3
                                    transition-all duration-200 hover:shadow-sm"
                             :style="`background: ${act.warna}08; border-left: 4px solid ${act.warna};`">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                                 :style="act.done ? 'background: #10b981;' : `background: ${act.warna}20;`">
                                <svg x-show="act.done" viewBox="0 0 16 16" class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 8l4 4 7-7"/>
                                </svg>
                                <svg x-show="!act.done" viewBox="0 0 24 24" class="w-4 h-4" :style="`color: ${act.warna};`" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="9"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <p class="text-xs sm:text-sm font-bold text-[#186369] truncate"
                                       :class="act.done ? 'line-through opacity-60' : ''"
                                       x-text="act.nama"></p>
                                    <span x-show="act.done"
                                          class="text-[8px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-emerald-500 text-white flex-shrink-0">
                                        Selesai
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 text-[10px] text-[#186369]/50 font-bold flex-wrap">
                                    <span class="px-1.5 py-0.5 rounded" :style="`background: ${act.warna}20; color: ${act.warna};`" x-text="act.kategori"></span>
                                    <span>·</span>
                                    <span>Target: <span x-text="act.target + ' ' + act.satuan"></span></span>
                                    <span>·</span>
                                    <span x-text="act.dateLabel"></span>
                                </div>
                            </div>
                            <div class="w-5 h-5 rounded-full flex-shrink-0 flex items-center justify-center border-2 transition"
                                 :class="act.done ? 'border-emerald-500 bg-emerald-500' : 'border-gray-300'">
                                <svg x-show="act.done" viewBox="0 0 16 16" class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 8l4 4 7-7"/>
                                </svg>
                            </div>
                        </div>
                    </template>
                </div>

                <template x-if="visibleActivities.length > 0">
                    <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-[10px]">
                        <div class="flex items-center gap-3 font-bold text-[#186369]/60">
                            <span class="flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span x-text="activeDone"></span> selesai
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                                <span x-text="activePending"></span> belum
                            </span>
                        </div>
                        <a href="/jadwal" class="font-bold text-[#186369] hover:underline flex items-center gap-1">
                            Kelola di To Do List
                            <svg viewBox="0 0 16 16" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 4l4 4-4 4"/>
                            </svg>
                        </a>
                    </div>
                </template>
            </div>
        </template>
    </div>

    {{-- ═══ ARTIKEL + JURNAL ═══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1.5fr_1fr] gap-4 flex-1 min-h-[280px]">
        <section class="bg-[#CBCBCB] rounded-[40px] p-6 sm:p-7 overflow-hidden flex flex-col"
                 x-data="kategoriFilter()" x-init="loadArticles()">
            <div class="flex items-center justify-between mb-4 flex-shrink-0">
                <div>
                    <h3 class="text-lg sm:text-xl font-bold text-[#186369]">Baca Artikel Kesehatan Terkini :</h3>
                    <p class="text-xs mt-1 text-[#186369]/60">Informasi kesehatan ditinjau oleh dokter.</p>
                </div>
                <a href="/artikel" data-auth class="text-[10px] font-bold text-[#186369]/50 hover:text-[#186369] transition">
                    Lihat semua &rarr;
                </a>
            </div>

            <div class="flex-1 min-h-0 overflow-y-auto scroll-thin pr-1">
                <div class="space-y-3">
                    <template x-for="(art, i) in articleList" :key="art.id">
                        <a :href="`/artikel/${art.id}`" data-auth
                           class="flex gap-3 sm:gap-4 items-center p-2 rounded-2xl
                                  bg-white/40 hover:bg-white/70 border border-white/60 group transition">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 flex-shrink-0 rounded-2xl overflow-hidden bg-white">
                                <img :src="art.cover" :alt="art.judul" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1.5">
                                    <span class="inline-block px-2 py-0.5 bg-[#186369]/15 rounded-md
                                                 text-[9px] font-bold uppercase tracking-wider text-[#186369]"
                                          x-text="art.tag"></span>
                                    <span class="text-[9px] font-bold text-[#186369]/40" x-text="art.tanggal"></span>
                                </div>
                                <p class="text-xs sm:text-sm font-bold leading-snug line-clamp-2 text-[#186369]"
                                   x-text="art.judul"></p>
                            </div>
                            <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0 text-[#186369]/30">
                                <path d="M9 5l7 7-7 7" fill="none" stroke="currentColor"
                                      stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                    </template>

                    <template x-if="articleList.length === 0">
                        <p class="text-center text-sm text-[#186369]/60 py-8">
                            Belum ada artikel. Tambahkan di
                            <a href="/admin/artikel" class="underline font-bold">Admin</a>.
                        </p>
                    </template>
                </div>
            </div>
        </section>

        <section class="bg-[#e8e8e8] rounded-[40px] p-6 flex flex-col justify-between gap-6">
            <h3 class="text-xl sm:text-2xl font-bold text-[#186369] text-center leading-tight">
                Ceritakan Hari Mu Disini......
            </h3>
            <a href="{{ route('jurnal') }}" data-auth
               class="w-full py-4 bg-[#186369] text-white font-bold text-lg rounded-3xl text-center
                      hover:opacity-90 transition active:scale-95 block">
                Mulai
            </a>
        </section>
    </div>

    {{-- VIDEO TRANSITION --}}
    <div x-show="videoPlaying" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-400"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[95] bg-black flex items-center justify-center">
        <video x-ref="transitionVideo" src="{{ asset('videos/donut-transition.mp4') }}"
               class="w-full h-full object-contain" autoplay muted playsinline preload="auto"
               @ended="onVideoEnd()"></video>
        <button type="button" @click="skipVideo()"
                class="absolute bottom-6 right-6 px-4 py-2 rounded-full
                       bg-white/20 hover:bg-white/30 backdrop-blur-sm
                       text-white text-xs font-bold transition active:scale-95">
            Lewati →
        </button>
    </div>

    {{-- MODAL CHART --}}
    <div x-show="chartOpen" x-cloak
         x-transition:enter="transition ease-out duration-400"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click.self="closeChart()" @keydown.escape.window="closeChart()"
         class="fixed inset-0 z-[100] bg-[#0a3d42]/40 backdrop-blur-sm
                flex items-center justify-center p-4">
        <div x-show="chartOpen"
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 scale-50 -translate-y-20"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-75 translate-y-10"
             class="bg-white rounded-3xl shadow-2xl w-full max-w-3xl max-h-[90vh]
                    overflow-hidden flex flex-col"
             x-data="chartModal()">

            <div class="flex items-center justify-between p-6 pb-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center"
                         :style="`background:${selectedChart?.warna || '#186369'}20;`">
                        <div class="w-4 h-4 rounded-full"
                             :style="`background:${selectedChart?.warna || '#186369'};`"></div>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-[#186369]"
                            x-text="selectedChart?.label || 'Rekapan Aktivitas'"></h2>
                        <p class="text-xs text-[#186369]/50 mt-0.5">
                            <span class="font-bold" x-text="selectedChart?.value || 0"></span> aktivitas selesai
                            &middot; <span x-text="periodLabel()"></span>
                        </p>
                    </div>
                </div>
                <button type="button" @click="closeChart()"
                        class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200
                               flex items-center justify-center transition active:scale-90">
                    <svg viewBox="0 0 16 16" class="w-4 h-4 text-[#186369]">
                        <path d="M4 4l8 8M12 4l-8 8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>

            <div class="px-6 py-6 flex-1 min-h-0 overflow-y-auto scroll-thin">
                <template x-if="selectedChart?.period === 'today'">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-[#186369]/50">Aktivitas hari ini</p>
                                <p class="text-xs font-bold text-[#186369]" x-text="todayActivities.length + ' aktivitas'"></p>
                            </div>
                            <div class="text-right text-[10px] font-bold text-[#186369]/60">
                                <span class="text-emerald-600" x-text="todayActivities.filter(item => item.done).length + ' selesai'"></span>
                                <span> · </span>
                                <span x-text="todayActivities.filter(item => !item.done).length + ' belum'"></span>
                            </div>
                        </div>

                        <template x-if="todayActivities.length > 0">
                            <div class="space-y-2">
                                <template x-for="activity in todayActivities" :key="activity.uid">
                                    <div class="flex items-center gap-3 rounded-2xl px-3 py-2.5"
                                         :style="`background: ${activity.warna}08; border-left: 4px solid ${activity.warna};`">
                                        <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0"
                                             :class="activity.done ? 'bg-emerald-500' : 'bg-gray-100'">
                                            <svg x-show="activity.done" viewBox="0 0 16 16" class="w-4 h-4 text-white"
                                                 fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 8l4 4 7-7"/>
                                            </svg>
                                            <svg x-show="!activity.done" viewBox="0 0 16 16" class="w-4 h-4 text-gray-400"
                                                 fill="none" stroke="currentColor" stroke-width="1.8">
                                                <circle cx="8" cy="8" r="5.5"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-[#186369] truncate"
                                               :class="activity.done ? 'line-through opacity-60' : ''"
                                               x-text="activity.nama"></p>
                                            <p class="text-[9px] font-bold text-[#186369]/50 mt-0.5" x-text="activity.kategori"></p>
                                        </div>
                                        <span class="text-[9px] font-bold px-2 py-1 rounded-lg"
                                              :class="activity.done ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500'"
                                              x-text="activity.done ? 'Selesai' : 'Belum'"></span>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="todayActivities.length === 0">
                            <div class="rounded-2xl bg-gray-50 py-8 text-center">
                                <p class="text-xs font-bold text-[#186369]/50">Belum ada aktivitas untuk hari ini.</p>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="selectedChart?.period !== 'today'">
                    <div>
                <div class="relative w-full h-[300px]">
                    <div class="absolute left-0 top-0 bottom-8 w-10 flex flex-col justify-between
                                text-[10px] font-bold text-[#186369]/40 text-right pr-2">
                        <template x-for="(label, i) in yAxisLabels" :key="i">
                            <span x-text="label"></span>
                        </template>
                    </div>
                    <div class="absolute left-10 right-0 top-0 bottom-8">
                        <svg viewBox="0 0 800 300" preserveAspectRatio="none" class="w-full h-full overflow-visible">
                            <defs>
                                <linearGradient id="chart-gradient" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" :stop-color="selectedChart?.warna || '#186369'" stop-opacity="0.25" />
                                    <stop offset="100%" :stop-color="selectedChart?.warna || '#186369'" stop-opacity="0" />
                                </linearGradient>
                            </defs>
                            <template x-for="(y, i) in [0, 75, 150, 225, 300]" :key="i">
                                <line x1="0" :y1="y" x2="800" :y2="y" stroke="#f0f0f0" stroke-width="1" />
                            </template>
                            <path :d="fillPath(currentData)" fill="url(#chart-gradient)" />
                            <path :d="linePath(currentData)" fill="none"
                                  :stroke="selectedChart?.warna || '#186369'"
                                  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                                  class="chart-line-animate"
                                  :style="`--line-length:${lineLength};`"
                                  :key="'line-' + (selectedChart?.label || '')" />
                            <template x-for="(val, i) in currentData" :key="'dot-' + i">
                                <circle :cx="xPos(i)" :cy="yPos(val)" r="4" fill="white"
                                        :stroke="selectedChart?.warna || '#186369'"
                                        stroke-width="2.5" class="chart-dot"
                                        :style="`animation-delay:${i * 60}ms;`"
                                        :key="'dot-' + (selectedChart?.label || '') + i" />
                            </template>
                        </svg>
                    </div>
                    <div class="absolute left-10 right-0 bottom-0 h-8 flex justify-between items-center
                                px-2 text-[10px] font-bold text-[#186369]/40">
                        <template x-for="(label, i) in xLabels" :key="i">
                            <span x-text="label"></span>
                        </template>
                    </div>
                </div>

                <template x-if="subBreakdown.length > 0">
                    <div class="mt-6 pt-5 border-t border-gray-100">
                        <p class="text-[10px] font-bold text-[#186369]/60 uppercase tracking-wider mb-3">
                            🎯 Aktivitas yang Sudah Dilakukan
                            <span x-show="!selectedChart?.isTotal">— <span x-text="selectedChart?.label"></span></span>
                        </p>
                        <div class="space-y-2 max-h-[240px] overflow-y-auto scroll-thin pr-1">
                            <template x-for="(sub, i) in subBreakdown" :key="i">
                                <div class="flex items-center gap-3 px-3 py-2 rounded-xl bg-emerald-50
                                            border border-emerald-100">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-500
                                                flex items-center justify-center flex-shrink-0">
                                        <svg viewBox="0 0 16 16" class="w-3.5 h-3.5 text-white"
                                             fill="none" stroke="currentColor" stroke-width="3"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 8l4 4 7-7"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-[#186369] truncate"
                                           x-text="sub.nama"></p>
                                        <p class="text-[9px] font-bold text-[#186369]/50 mt-0.5">
                                            <span class="px-1.5 py-0.5 rounded"
                                                  :style="`background: ${sub.warna}20; color: ${sub.warna};`"
                                                  x-text="sub.kategori"></span>
                                        </p>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <p class="text-sm font-bold text-emerald-600"
                                           x-text="sub.count + 'x'"></p>
                                        <p class="text-[9px] font-bold text-emerald-600/60">
                                            selesai
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <template x-if="subBreakdown.length === 0">
                    <div class="mt-6 pt-5 border-t border-gray-100 text-center py-6">
                        <p class="text-xs font-bold text-[#186369]/50">
                            Belum ada aktivitas yang diselesaikan
                        </p>
                    </div>
                </template>
                    </div>
                </template>
            </div>

            <div x-show="selectedChart?.period !== 'today'"
                 class="px-6 py-4 border-t border-gray-100 flex items-center justify-between flex-wrap gap-3">
                <div class="flex gap-6">
                    <div>
                        <p class="text-[10px] font-bold text-[#186369]/50 uppercase">Total Selesai</p>
                        <p class="text-2xl font-bold text-emerald-600" x-text="sum(currentData)"></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-[#186369]/50 uppercase">Rata-rata/hari</p>
                        <p class="text-2xl font-bold text-[#186369]" x-text="avg(currentData)"></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-[#186369]/50 uppercase">Puncak</p>
                        <p class="text-2xl font-bold text-[#186369]" x-text="Math.max(...currentData, 0)"></p>
                    </div>
                </div>
                <button type="button" @click="closeChart()"
                        class="px-6 py-2.5 bg-[#186369] text-white text-sm font-bold rounded-xl
                               hover:opacity-90 transition active:scale-95">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>
@endsection


@push('scripts')
<script>
    function dashboardApp() {
        return {
            chartOpen: false, selectedChart: null,
            videoPlaying: false, videoQueueChart: null,
            avatarUrl: window.serverDashboardAvatar || '{{ asset('images/avatar.png') }}',
            searchQuery: '',
            searchOpen: false,
            searchResults: [],

            init() {
                this.broadcastSearch();
            },
            broadcastSearch() {
                const query = this.searchQuery.trim().toLowerCase();
                this.searchOpen = query.length > 0;
                this.searchResults = [];

                const features = [
                    { key: 'feature-schedule', label: 'Jadwal Aktivitas', type: 'Fitur', icon: 'J', href: '/jadwal' },
                    { key: 'feature-journal', label: 'Jurnal Harian', type: 'Fitur', icon: 'J', href: '/jurnal' },
                    { key: 'feature-test', label: 'Tes Kesehatan Mental', type: 'Fitur', icon: 'T', href: '/tes-mental' },
                    { key: 'feature-consultation', label: 'Konsultasi', type: 'Fitur', icon: 'K', href: '/konsultasi' },
                ];
                const schedules = window.serverDashboardSchedules || {};
                const activities = Object.entries(schedules).flatMap(([date, items]) =>
                    (Array.isArray(items) ? items : []).map(item => ({ ...item, date }))
                );
                const articles = window.serverDashboardSearchArticles || window.serverDashboardArticles || [];
                const doctors = window.serverDashboardDoctors || [];
                const categories = window.serverDashboardCategories || [];

                if (query) {
                    this.searchResults = [
                        ...features.filter(item => item.label.toLowerCase().includes(query)),
                        ...activities
                            .filter(item => [item.nama, item.kategori, item.keterangan].some(value =>
                                String(value || '').toLowerCase().includes(query)
                            ))
                            .slice(0, 5)
                            .map(item => ({
                                key: `activity-${item.date}-${item.id || item.nama}`,
                                label: item.nama,
                                type: `Aktivitas · ${item.kategori || 'Lainnya'}`,
                                icon: item.done ? '✓' : '○',
                                href: '/jadwal',
                            })),
                        ...categories
                            .filter(category => [category.nama, ...(category.jenis || []).map(item => item.nama)]
                                .some(value => String(value || '').toLowerCase().includes(query)))
                            .slice(0, 5)
                            .map(category => ({
                                key: `category-${category.id}`,
                                label: category.nama,
                                type: 'Kategori aktivitas',
                                icon: 'K',
                                href: '/jadwal',
                            })),
                        ...doctors
                            .filter(doctor => [doctor.name, doctor.spesialisasi, doctor.bio]
                                .some(value => String(value || '').toLowerCase().includes(query)))
                            .slice(0, 5)
                            .map(doctor => ({
                                key: `doctor-${doctor.id}`,
                                label: doctor.name,
                                type: `Dokter · ${doctor.spesialisasi || 'Konsultasi'}`,
                                icon: 'D',
                                href: '/konsultasi',
                            })),
                        ...articles
                            .filter(article => [article.title, article.content].some(value =>
                                String(value || '').toLowerCase().includes(query)
                            ))
                            .slice(0, 5)
                            .map(article => ({
                                key: `article-${article.id}`,
                                label: article.title,
                                type: 'Artikel kesehatan',
                                icon: 'A',
                                href: `/artikel/${article.id}`,
                            })),
                    ].slice(0, 8);
                }

                window.dispatchEvent(new CustomEvent('dashboard-search', { detail: { query } }));
            },
            selectSearchResult(result) {
                this.searchOpen = false;
                if (result.href === '/jadwal') {
                    window.dispatchEvent(new CustomEvent('dashboard-search-focus', { detail: result.label }));
                }
            },
            openChartDetail(item) {
                this.selectedChart = item;
                this.chartOpen = true;
                window.dispatchEvent(new CustomEvent('dashboard-chart-selected', { detail: item }));
            },
            closeChart() { this.chartOpen = false; this.selectedChart = null; },
            playVideoThenChart(item) {
                this.videoQueueChart = item; this.videoPlaying = true;
                this.$nextTick(() => {
                    const v = this.$refs.transitionVideo;
                    if (v) { v.currentTime = 0; v.play().catch(() => this.onVideoEnd()); }
                });
            },
            onVideoEnd() {
                this.videoPlaying = false;
                if (this.videoQueueChart) {
                    this.openChartDetail(this.videoQueueChart);
                    this.videoQueueChart = null;
                }
            },
            skipVideo() { this.onVideoEnd(); },
        };
    }

    /* ═══════════════════════════════════════════════════════════
       ACTIVITY STATS — Donut dengan COLOR FLOW di tiap segment
       ═══════════════════════════════════════════════════════════ */
    function activityStats() {
        return {
            items: [],
            donutHover: false,
            circumference: 2 * Math.PI * 46,
            period: 'week',
            periodOptions: [
                { key: 'today', label: 'Hari Ini' },
                { key: 'week', label: '1 Minggu' },
                { key: 'month', label: '1 Bulan' },
                { key: 'year', label: '1 Tahun' },
            ],

            totalDone: 0,
            totalTask: 0,
            percent: 0,
            periodOpen: false,

            init() {
                this.calculate();
            },

            calculate() {
                const schedules = window.serverDashboardSchedules || {};
                const categories = window.serverDashboardCategories || [];
                const periodKeys = this.periodKeys(new Date());

                if (!schedules || typeof schedules !== 'object') schedules = {};
                if (!Array.isArray(categories)) categories = [];

                const catMap = {};
                categories.forEach(c => {
                    catMap[c.nama] = {
                        warna: c.warna || '#186369',
                        teksWarna: c.teksWarna || '#ffffff',
                    };
                });

                const counts = {};
                let totalTask = 0;
                let totalDone = 0;
                const todayKey = this.dateKey(new Date());

                Object.entries(schedules)
                    .filter(([key]) => periodKeys.includes(key) && key <= todayKey)
                    .forEach(([key, items]) => {
                    if (!Array.isArray(items)) return;

                    items.forEach(item => {
                        totalTask++;
                        const nama = item.kategori || 'Lainnya';
                        if (!counts[nama]) {
                            const cat = catMap[nama] || {
                                warna: item.warna || '#186369',
                                teksWarna: item.teksWarna || '#ffffff',
                            };
                            counts[nama] = {
                                label: nama,
                                value: 0,
                                total: 0,
                                warna: cat.warna,
                                teksWarna: cat.teksWarna,
                            };
                        }
                        counts[nama].total += 1;
                        if (item.done !== true) return;
                        totalDone++;
                        counts[nama].value += 1;
                    });
                    });

                this.items = Object.values(counts).sort((a, b) => b.value - a.value);
                this.totalDone = totalDone;
                this.totalTask = totalTask;
                this.percent = totalTask > 0
                    ? Math.round((totalDone / totalTask) * 100)
                    : 0;
            },

            get segments() {
                const total = this.totalTasks || 1;
                const circumference = this.circumference;
                const gap = this.items.length > 6 ? 2 : 4;
                let offset = 0;

                return this.items.map(item => {
                    const portion = item.total / total;
                    const segment = {
                        length: Math.max(0, portion * circumference - gap),
                        offset: -offset,
                    };
                    offset += portion * circumference;
                    return segment;
                });
            },

            get total() {
                return this.items.reduce((s, i) => s + i.value, 0);
            },

            get totalTasks() {
                return this.items.reduce((s, i) => s + i.total, 0);
            },

            openChart(item, index) {
                this.$dispatch('open-chart', { item: { ...item, period: this.period }, index });
            },

            explodeDonut() {
                this.$dispatch('open-chart-with-video', {
                    item: {
                        label: 'Total Aktivitas Selesai',
                        warna: '#10b981',
                        value: this.totalDone,
                        isTotal: true,
                        period: this.period,
                    },
                });
            },

            setPeriod(period) {
                this.period = period;
                this.periodOpen = false;
                this.calculate();
            },

            periodKeys(now) {
                const lengths = { today: 1, week: 7, month: 30, year: 365 };
                const days = [];
                const length = lengths[this.period] || lengths.week;

                for (let i = length - 1; i >= 0; i--) {
                    const date = new Date(now);
                    date.setDate(now.getDate() - i);
                    days.push(this.dateKey(date));
                }

                return days;
            },

            periodLabel() {
                const option = this.periodOptions.find(item => item.key === this.period);
                return option ? option.label.toLowerCase() : '1 minggu';
            },

            dateKey(d) {
                return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
            },
        };
    }

    /* ═══════════════════════════════════════════════════════════
       ACTIVITY FILTER
       ═══════════════════════════════════════════════════════════ */
    function activityFilter() {
        return {
            activeCategory: null,
            onlyPending: false,
            period: 'week',
            periodOptions: [
                { key: 'today', label: 'Hari Ini' },
                { key: 'week', label: '1 Minggu' },
                { key: 'month', label: '1 Bulan' },
                { key: 'year', label: '1 Tahun' },
            ],
            stats: [],
            allActivities: [],
            searchQuery: '',
            totalTask: 0,
            totalDone: 0,
            totalPercent: 0,

            init() {
                window.addEventListener('dashboard-search', event => {
                    this.searchQuery = event.detail.query || '';
                });
                this.calculate();
            },

            calculate() {
                const now = new Date();
                const days = this.periodKeys(now);

                const schedules = window.serverDashboardSchedules || {};
                const categories = window.serverDashboardCategories || [];

                if (!schedules || typeof schedules !== 'object') schedules = {};
                if (!Array.isArray(categories)) categories = [];

                const catMap = {};
                categories.forEach(c => {
                    catMap[c.nama] = {
                        warna: c.warna || '#186369',
                        teksWarna: c.teksWarna || '#ffffff',
                    };
                });

                const tasks = [];

                days.forEach(key => {
                    const items = schedules[key] || [];
                    if (!Array.isArray(items)) return;

                    const [y, m, d] = key.split('-').map(Number);
                    const date = new Date(y, m-1, d);
                    const dateLabel = this.shortDate(date, key, now);

                    items.forEach(item => {
                        const catInfo = catMap[item.kategori] || {
                            warna: item.warna || '#186369',
                            teksWarna: item.teksWarna || '#ffffff',
                        };

                        tasks.push({
                            uid: `${key}-${item.id || item.nama}`,
                            nama: item.nama,
                            kategori: item.kategori || 'Lainnya',
                            target: item.target,
                            satuan: item.satuan,
                            done: item.done === true,
                            tanggalKey: key,
                            dateLabel,
                            warna: catInfo.warna,
                            teksWarna: catInfo.teksWarna,
                        });
                    });
                });

                const map = {};
                tasks.forEach(t => {
                    if (!map[t.kategori]) {
                        map[t.kategori] = {
                            nama: t.kategori,
                            warna: t.warna,
                            teksWarna: t.teksWarna,
                            done: 0,
                            total: 0,
                        };
                    }
                    map[t.kategori].total += 1;
                    if (t.done) map[t.kategori].done += 1;
                });

                const list = Object.values(map)
                    .map(c => ({
                        ...c,
                        percent: c.total > 0 ? Math.round((c.done / c.total) * 100) : 0,
                    }))
                    .sort((a, b) => {
                        if (b.total !== a.total) return b.total - a.total;
                        return b.percent - a.percent;
                    });

                this.stats = list;
                this.allActivities = tasks.sort((a, b) => {
                    if (a.done !== b.done) return a.done ? 1 : -1;
                    return b.tanggalKey.localeCompare(a.tanggalKey);
                });

                this.totalTask = tasks.length;
                this.totalDone = tasks.filter(t => t.done).length;
                this.totalPercent = this.totalTask > 0
                    ? Math.round((this.totalDone / this.totalTask) * 100)
                    : 0;
            },

            get visibleActivities() {
                let list = this.allActivities;
                if (this.searchQuery) {
                    list = list.filter(activity => [activity.nama, activity.kategori, activity.keterangan]
                        .some(value => String(value || '').toLowerCase().includes(this.searchQuery)));
                }
                if (this.activeCategory) {
                    list = list.filter(a => a.kategori === this.activeCategory);
                }
                if (this.onlyPending) {
                    list = list.filter(a => !a.done);
                }
                return list;
            },

            get activeDone() { return this.visibleActivities.filter(a => a.done).length; },
            get activePending() { return this.visibleActivities.filter(a => !a.done).length; },

            selectCategory(nama) {
                this.activeCategory = this.activeCategory === nama ? null : nama;
            },

            resetFilter() {
                this.activeCategory = null;
                this.onlyPending = false;
            },

            setPeriod(period) {
                this.period = period;
                this.calculate();
            },

            periodKeys(now) {
                const lengths = { today: 1, week: 7, month: 30, year: 365 };
                const days = [];
                const length = lengths[this.period] || lengths.week;

                for (let i = length - 1; i >= 0; i--) {
                    const date = new Date(now);
                    date.setDate(now.getDate() - i);
                    days.push(this.dateKey(date));
                }

                return days;
            },

            periodLabel() {
                const option = this.periodOptions.find(item => item.key === this.period);
                return option ? option.label : '1 Minggu';
            },

            dateKey(d) {
                return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
            },

            shortDate(date, key, today) {
                const todayKey = this.dateKey(today);
                if (key === todayKey) return 'Hari ini';

                const yesterday = new Date(today);
                yesterday.setDate(today.getDate() - 1);
                if (key === this.dateKey(yesterday)) return 'Kemarin';

                const dayNames = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
                const monthNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                return `${dayNames[date.getDay()]}, ${date.getDate()} ${monthNames[date.getMonth()]}`;
            },
        };
    }

    /* ═══════════════════════════════════════════════════════════
       KATEGORI FILTER
       ═══════════════════════════════════════════════════════════ */
    function kategoriFilter() {
        return {
            activeCategory: 1,
            articleList: [],
            allArticles: [],
            searchQuery: '',

            categories: (() => {
                return window.serverDashboardCategories || [];
            })(),

            loadArticles() {
                this.allArticles = (window.serverDashboardArticles || []).map(a => ({
                    id: a.id,
                    tag: 'umum',
                    tanggal: a.published_at,
                    judul: a.title,
                    cover: a.thumbnail || 'https://via.placeholder.com/200',
                }));
                this.articleList = this.allArticles;
                window.addEventListener('dashboard-search', event => {
                    this.searchQuery = event.detail.query || '';
                    this.articleList = this.allArticles.filter(article =>
                        [article.judul, article.tag].some(value =>
                            String(value || '').toLowerCase().includes(this.searchQuery)
                        )
                    );
                });
            },
        };
    }

    /* ═══════════════════════════════════════════════════════════
       CHART MODAL
       ═══════════════════════════════════════════════════════════ */
    function chartModal() {
        return {
            selectedChart: null,
            lineLength: 2500, svgWidth: 800, svgHeight: 300,
            xLabels: [],
            dayKeys: [],

            init() {
                window.addEventListener('dashboard-chart-selected', event => {
                    this.setChart(event.detail);
                });
            },

            setChart(chart) {
                this.selectedChart = chart || null;
                this.buildTimeline();
            },

            buildTimeline() {
                const now = new Date();
                const dayNames = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
                this.xLabels = [];
                this.dayKeys = [];

                if (this.selectedChart?.period === 'year') {
                    for (let i = 11; i >= 0; i--) {
                        const date = new Date(now.getFullYear(), now.getMonth() - i, 1);
                        this.dayKeys.push(`${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`);
                        this.xLabels.push(date.toLocaleDateString('id-ID', { month: 'short' }));
                    }
                    return;
                }

                const lengths = { today: 1, week: 7, month: 30, year: 365 };
                const length = lengths[this.selectedChart?.period] || lengths.week;
                for (let i = length - 1; i >= 0; i--) {
                    const d = new Date(now);
                    d.setDate(now.getDate() - i);
                    this.dayKeys.push(this.dateKey(d));
                    if (this.selectedChart?.period === 'month') {
                        this.xLabels.push(String(d.getDate()));
                    } else if (this.selectedChart?.period === 'year') {
                        this.xLabels.push(d.toLocaleDateString('id-ID', { month: 'short' }));
                    } else {
                        this.xLabels.push(dayNames[d.getDay()]);
                    }
                }
            },

            itemsForKey(key) {
                const schedules = window.serverDashboardSchedules || {};

                if (this.selectedChart?.period === 'year') {
                    return Object.entries(schedules)
                        .filter(([dateKey]) => dateKey.startsWith(key))
                        .flatMap(([, items]) => Array.isArray(items) ? items : []);
                }

                return Array.isArray(schedules[key]) ? schedules[key] : [];
            },

            get currentData() {
                const chart = this.selectedChart;

                return this.dayKeys.map(key => {
                    const items = this.itemsForKey(key);

                    const doneItems = items.filter(i => i.done === true);

                    if (!chart || chart.isTotal) {
                        return doneItems.length;
                    }
                    return doneItems.filter(i => i.kategori === chart.label).length;
                });
            },

            get todayActivities() {
                const schedules = window.serverDashboardSchedules || {};
                const todayKey = this.dateKey(new Date());
                const chart = this.selectedChart;
                const items = Array.isArray(schedules[todayKey]) ? schedules[todayKey] : [];

                return items
                    .filter(item => !chart || chart.isTotal || item.kategori === chart.label)
                    .map((item, index) => ({
                        uid: `${todayKey}-${item.id || item.nama}-${index}`,
                        nama: item.nama,
                        kategori: item.kategori || 'Lainnya',
                        done: item.done === true,
                        warna: item.warna || '#186369',
                    }))
                    .sort((a, b) => Number(a.done) - Number(b.done));
            },

            get subBreakdown() {
                const chart = this.selectedChart;
                const counts = {};

                this.dayKeys.forEach(dateKey => {
                    const items = this.itemsForKey(dateKey);
                    items.forEach(item => {
                        if (item.done !== true) return;
                        if (chart && !chart.isTotal && item.kategori !== chart.label) return;

                        const key = `${item.kategori}::${item.nama}`;
                        if (!counts[key]) {
                            counts[key] = {
                                nama: item.nama,
                                kategori: item.kategori || 'Lainnya',
                                warna: item.warna || '#186369',
                                count: 0,
                            };
                        }
                        counts[key].count += 1;
                    });
                });

                return Object.values(counts)
                    .sort((a, b) => b.count - a.count)
                    .slice(0, 10);
            },

            get maxY() {
                const dataMax = Math.max(...this.currentData, 0);
                return Math.max(5, Math.ceil(dataMax / 5) * 5);
            },

            get yAxisLabels() {
                const max = this.maxY;
                return [max, Math.round(max * 0.75), Math.round(max * 0.5), Math.round(max * 0.25), 0];
            },

            xPos(i) {
                const denominator = Math.max(this.xLabels.length - 1, 1);
                return (i / denominator) * this.svgWidth;
            },
            yPos(value) { return this.svgHeight - (value / this.maxY) * this.svgHeight; },
            linePath(data) {
                return data.map((value, i) =>
                    `${i === 0 ? 'M' : 'L'} ${this.xPos(i)} ${this.yPos(value)}`
                ).join(' ');
            },
            fillPath(data) {
                const line = this.linePath(data);
                const lastX = this.xPos(data.length - 1);
                return `${line} L ${lastX} ${this.svgHeight} L 0 ${this.svgHeight} Z`;
            },
            sum(data) { return data.reduce((a, b) => a + b, 0); },
            avg(data) { return data.length > 0 ? Math.round(this.sum(data) / data.length) : 0; },

            periodLabel() {
                const labels = {
                    today: 'hari ini',
                    week: '1 minggu terakhir',
                    month: '1 bulan terakhir',
                    year: '1 tahun terakhir',
                };
                return labels[this.selectedChart?.period] || labels.week;
            },

            dateKey(d) {
                return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
            },
        };
    }
</script>
@endpush