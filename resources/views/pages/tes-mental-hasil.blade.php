@extends('layouts.app', ['activeNav' => 'tes'])

@section('title', 'Hasil Tes Kesehatan Mental')

@push('styles')
<style>
    @keyframes fade-in-up {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .fade-in-up { animation: fade-in-up 0.5s ease-out backwards; }

    @keyframes score-fill {
        from { stroke-dashoffset: 314; }
    }
    .score-fill { animation: score-fill 1.2s cubic-bezier(0.34, 1.2, 0.64, 1) backwards; }

    @keyframes breath-inhale { 0% { transform: scale(0.85); } 100% { transform: scale(1.4); } }
    @keyframes breath-hold   { 0%, 100% { transform: scale(1.4); } }
    @keyframes breath-exhale { 0% { transform: scale(1.4); } 100% { transform: scale(0.85); } }
    .breath-circle-inhale { animation: breath-inhale 4s ease-in-out forwards; }
    .breath-circle-hold   { animation: breath-hold   7s ease-in-out forwards; }
    .breath-circle-exhale { animation: breath-exhale 8s ease-in-out forwards; }

    @keyframes ring-pulse {
        0%   { transform: scale(0.9); opacity: 0.6; }
        100% { transform: scale(1.6); opacity: 0; }
    }
    .ring-pulse { animation: ring-pulse 3s ease-out infinite; }
</style>
@endpush

@section('content')

<div x-data="resultPage({{ $testId ?? 1 }})" x-init="init()" class="contents">

    {{-- HEADER --}}
    <header class="mb-2 fade-in-up flex-shrink-0">
        <div class="flex items-center gap-3">
            <a href="/tes-mental"
               class="w-10 h-10 rounded-full bg-white shadow-md
                      flex items-center justify-center
                      text-[#186369] hover:scale-105 transition active:scale-95 flex-shrink-0">
                <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none"
                     stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-[#186369]">
                    Ringkasan Kondisi Anda
                </h1>
                <p class="text-xs sm:text-sm text-[#186369]/60 mt-1"
                   x-text="testData?.deskripsi_tes || 'Hasil skrining kesehatan mental Anda.'"></p>
            </div>
        </div>
    </header>

    {{-- Banner mode riwayat --}}
    <template x-if="isHistoryMode">
        <div class="bg-amber-50 border-2 border-amber-200 rounded-2xl p-3 flex-shrink-0
                    flex items-center justify-between gap-3">
            <div class="flex items-center gap-2 min-w-0">
                <svg viewBox="0 0 24 24" class="w-4 h-4 text-amber-600 flex-shrink-0"
                     fill="none" stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 8v4M12 16h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                <p class="text-[11px] font-bold text-amber-800 truncate">
                    Kamu sedang melihat <b>hasil tes lama</b>
                </p>
            </div>
            <a href="/tes-mental/{{ $testId ?? 1 }}/hasil"
               class="text-[10px] font-bold text-amber-700 bg-amber-100
                      hover:bg-amber-200 px-3 py-1.5 rounded-lg
                      transition active:scale-95 whitespace-nowrap flex-shrink-0">
                Hasil Terbaru →
            </a>
        </div>
    </template>

    {{-- GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1.6fr_1fr] gap-5">

        {{-- KIRI --}}
        <div class="space-y-5">

            {{-- 1. HASIL DIAGNOSIS --}}
            <section class="bg-white rounded-[32px] p-5 sm:p-6 border border-gray-100 fade-in-up"
                     style="animation-delay: 80ms;">
                <p class="text-[10px] font-bold text-[#186369]/50 uppercase tracking-widest mb-3">
                    Hasil Diagnosis Menyatakan Kamu :
                </p>

                <p class="text-sm font-bold mb-1"
                   :style="`color: ${severity.warna};`"
                   x-text="emotionalHeader"></p>

                <h2 class="text-2xl sm:text-3xl font-bold text-[#186369] leading-tight mb-2"
                    x-text="currentDiagnosis.label || '—'"></h2>
                <p class="text-sm font-bold text-[#186369]/80 leading-relaxed mb-4"
                   x-text="currentDiagnosis.sublabel || ''"></p>

                <template x-if="isInvalid">
                    <div class="mb-4 bg-red-100 border-2 border-red-400 rounded-2xl p-4
                                flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-600 flex items-center justify-center flex-shrink-0">
                            <svg viewBox="0 0 24 24" class="w-5 h-5 text-white" fill="none"
                                 stroke="currentColor" stroke-width="2.5"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18.36 6.64a9 9 0 11-12.73 0M12 2v10"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-red-800">⚠️ Validitas Jawaban Rendah</p>
                            <p class="text-[11px] text-red-700 mt-1 leading-relaxed">
                                Sistem mendeteksi Anda menjawab <b>Sering/Selalu</b> pada
                                <b x-text="lieViolations"></b> dari <b x-text="lieAnswers.length"></b>
                                soal validitas.
                            </p>
                            <a href="/tes-mental"
                               class="mt-2 inline-flex items-center gap-1.5
                                      px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700
                                      text-white text-[10px] font-bold
                                      transition active:scale-95">
                                Ulangi Tes →
                            </a>
                        </div>
                    </div>
                </template>

                <template x-if="isMisfit && !isInvalid">
                    <div class="mb-4 bg-amber-50 border-2 border-amber-300 rounded-2xl p-4
                                flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center flex-shrink-0">
                            <svg viewBox="0 0 24 24" class="w-5 h-5 text-white" fill="none"
                                 stroke="currentColor" stroke-width="2.5"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-amber-800">🔍 Pola Jawaban Tidak Konsisten</p>
                            <p class="text-[11px] text-amber-700 mt-1 leading-relaxed">
                                Pola jawabanmu menunjukkan ketidaklogisan. Hasil mungkin kurang akurat.
                            </p>
                        </div>
                    </div>
                </template>

                <template x-if="highRisk && !isInvalid">
                    <div class="mb-4 bg-red-50 border-2 border-red-300 rounded-2xl p-4
                                flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-500 flex items-center justify-center flex-shrink-0">
                            <svg viewBox="0 0 24 24" class="w-5 h-5 text-white" fill="none"
                                 stroke="currentColor" stroke-width="2.5"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-red-700">⚠️ Perhatian Khusus</p>
                            <p class="text-[11px] text-red-600 mt-1 leading-relaxed">
                                Ada tanda-tanda risiko tinggi. Segera hubungi profesional.
                            </p>
                            <a href="/konsultasi"
                               class="mt-2 inline-flex items-center gap-1.5
                                      px-3 py-1.5 rounded-lg bg-red-500 hover:bg-red-600
                                      text-white text-[10px] font-bold
                                      transition active:scale-95">
                                Hubungi Bantuan →
                            </a>
                        </div>
                    </div>
                </template>

                <div class="bg-[#f5f8f7] rounded-2xl p-4 border-l-4 border-[#186369]/30">
                    <p class="text-xs sm:text-sm text-[#186369]/70 leading-relaxed"
                       x-text="currentDiagnosis.detail || ''"></p>
                </div>

                <div x-show="!isHistoryMode" class="mt-4 pt-4 border-t border-gray-100">
                    <button type="button"
                            @click="saveResult()"
                            :disabled="savedToHistory"
                            class="w-full py-3 rounded-2xl font-bold text-sm
                                   transition active:scale-95
                                   flex items-center justify-center gap-2
                                   disabled:opacity-60 disabled:cursor-not-allowed"
                            :style="savedToHistory
                                ? 'background: #10b981; color: white;'
                                : 'background: #186369; color: white;'">
                        <template x-if="!savedToHistory">
                            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none"
                                 stroke="currentColor" stroke-width="2.5"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                        </template>
                        <template x-if="savedToHistory">
                            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none"
                                 stroke="currentColor" stroke-width="2.5"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 13l4 4L19 7"/>
                            </svg>
                        </template>
                        <span x-text="savedToHistory ? 'Tersimpan di Profil' : 'Simpan Hasil ke Profil'"></span>
                    </button>
                </div>
            </section>

            {{-- 2. YANG KAMU RASAKAN --}}
            <section x-show="clusterStats.length > 0"
                     class="bg-white rounded-[32px] p-5 sm:p-6 border border-gray-100 fade-in-up"
                     style="animation-delay: 160ms;">

                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-full bg-[#186369] text-white
                                flex items-center justify-center
                                text-sm font-bold flex-shrink-0">2</div>
                    <h2 class="text-lg sm:text-xl font-bold text-[#186369]">
                        Yang Kamu Rasakan
                    </h2>
                </div>
                <p class="text-[11px] text-[#186369]/60 mb-5 ml-11 leading-relaxed">
                    Ini bukan angka atau daftar pertanyaan —
                    ini cerminan dari apa yang sedang kamu alami.
                </p>

                <div class="mb-4 p-3 bg-[#f5f8f7] rounded-2xl flex items-center justify-between flex-wrap gap-2">
                    <p class="text-[11px] font-bold text-[#186369]/70">
                        <span x-text="activeClusters"></span>
                        area sedang terpengaruh
                    </p>
                    <template x-if="topCluster">
                        <p class="text-[10px] font-bold" :style="`color: ${topCluster.levelWarna};`">
                            Paling terasa: <span x-text="topCluster.icon + ' ' + topCluster.nama"></span>
                        </p>
                    </template>
                </div>

                <div class="space-y-3">
                    <template x-for="(cluster, ci) in clusterStats" :key="cluster.id">
                        <div class="rounded-2xl overflow-hidden border-2 transition-all fade-in-up"
                             :style="`background: ${cluster.warna}05;
                                      border-color: ${cluster.warna}25;
                                      animation-delay: ${(ci + 2) * 80}ms;`">

                            <div class="p-4 flex items-start gap-3">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center
                                            flex-shrink-0 text-2xl"
                                     :style="`background: ${cluster.warna}20;`">
                                    <span x-text="cluster.icon"></span>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <p class="text-sm font-bold text-[#186369]"
                                           x-text="cluster.nama"></p>
                                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider"
                                              :style="`background: ${cluster.levelBg};
                                                       color: ${cluster.levelWarna};`"
                                              x-text="cluster.level"></span>
                                    </div>

                                    <div class="flex items-center gap-2 mt-2">
                                        <div class="flex-1 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-700"
                                                 :style="`width: ${cluster.avgPercent}%;
                                                          background: ${cluster.levelWarna};`"></div>
                                        </div>
                                        <span class="text-[9px] font-bold flex-shrink-0"
                                              :style="`color: ${cluster.levelWarna};`"
                                              x-text="cluster.avgPercent + '%'"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="px-4 pb-4">
                                <div class="bg-white/80 rounded-xl p-3.5 border"
                                     :style="`border-color: ${cluster.warna}20;`">
                                    <p class="text-xs text-[#186369]/85 leading-relaxed font-bold"
                                       x-text="cluster.impactText"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </section>

            {{-- 3. DAMPAK --}}
            <section x-show="impacts.length > 0"
                     class="bg-white rounded-[32px] p-5 sm:p-6 border border-gray-100 fade-in-up"
                     style="animation-delay: 240ms;">

                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-full bg-[#186369] text-white
                                flex items-center justify-center
                                text-sm font-bold flex-shrink-0">3</div>
                    <h2 class="text-lg sm:text-xl font-bold text-[#186369]">
                        Dampaknya pada Kehidupan Sehari-hari
                    </h2>
                </div>
                <p class="text-[11px] text-[#186369]/60 mb-5 ml-11">
                    Yang mungkin juga kamu rasakan di area ini.
                </p>

                <div class="space-y-3">
                    <template x-for="(imp, i) in impacts" :key="i">
                        <div class="rounded-2xl p-4 bg-[#f9fafb] fade-in-up"
                             :style="`animation-delay: ${(i + 3) * 80}ms;`">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center
                                            flex-shrink-0 text-lg"
                                     :style="`background: ${imp.color}15;`">
                                    <span x-text="imp.icon"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-0.5 flex-wrap">
                                        <p class="text-sm font-bold text-[#186369]"
                                           x-text="imp.title"></p>
                                        <span class="text-[9px] font-bold px-2 py-0.5 rounded-md"
                                              :style="`background: ${imp.color}15; color: ${imp.color};`"
                                              x-text="imp.level"></span>
                                    </div>
                                    <p class="text-[11px] text-[#186369]/60 mt-0.5 leading-relaxed"
                                       x-text="imp.description"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </section>

            {{-- 4. YANG BISA DILAKUKAN --}}
            <section x-show="recommendations.length > 0"
                     class="bg-white rounded-[32px] p-5 sm:p-6 border border-gray-100 fade-in-up"
                     style="animation-delay: 320ms;">

                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-full bg-[#186369] text-white
                                flex items-center justify-center
                                text-sm font-bold flex-shrink-0">4</div>
                    <h2 class="text-lg sm:text-xl font-bold text-[#186369]">
                        Langkah Kecil untuk Kamu
                    </h2>
                </div>
                <p class="text-[11px] text-[#186369]/60 mb-5 ml-11">
                    Tidak harus sempurna—cukup mulai dari satu.
                </p>

                <div class="space-y-3">
                    <template x-for="(rec, i) in recommendations" :key="i">
                        <div class="rounded-2xl p-4 flex items-start gap-3 fade-in-up"
                             :style="`background: ${rec.bg}; animation-delay: ${(i + 4) * 80}ms;`">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center
                                        flex-shrink-0 text-lg bg-white/70">
                                <span x-text="rec.icon"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-[#186369]"
                                   x-text="rec.title"></p>
                                <p class="text-[11px] text-[#186369]/60 mt-0.5 leading-relaxed"
                                   x-text="rec.description"></p>
                            </div>
                            <template x-if="rec.title === 'Latihan Pernapasan'">
                                <button type="button"
                                        @click="openBreathing()"
                                        class="self-center px-3 py-1.5 rounded-lg flex-shrink-0
                                               text-[10px] font-bold whitespace-nowrap
                                               transition active:scale-95"
                                        :style="`background: ${rec.buttonColor}; color: white;`">
                                    <span x-text="rec.action"></span>
                                    <span class="ml-0.5">→</span>
                                </button>
                            </template>
                            <template x-if="rec.title !== 'Latihan Pernapasan'">
                                <a :href="rec.link"
                                   class="self-center px-3 py-1.5 rounded-lg flex-shrink-0
                                          text-[10px] font-bold whitespace-nowrap
                                          transition active:scale-95"
                                   :style="`background: ${rec.buttonColor}; color: white;`">
                                    <span x-text="rec.action"></span>
                                    <span class="ml-0.5">→</span>
                                </a>
                            </template>
                        </div>
                    </template>
                </div>
            </section>

            {{-- 5. KAPAN BANTUAN --}}
            <section x-show="whenToSeekHelp.length > 0"
                     class="bg-gradient-to-br from-[#186369] to-[#0e4348]
                            rounded-[32px] p-5 sm:p-6 text-white fade-in-up"
                     style="animation-delay: 400ms;">

                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-full bg-white/20 text-white
                                flex items-center justify-center
                                text-sm font-bold flex-shrink-0">5</div>
                    <h2 class="text-lg sm:text-xl font-bold">
                        Kapan Perlu Mencari Bantuan?
                    </h2>
                </div>
                <p class="text-xs text-white/70 mb-4 ml-11 leading-relaxed">
                    Hasil tes bukan diagnosis. Segera cari bantuan profesional jika kamu mengalami:
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 ml-11">
                    <template x-for="(w, i) in whenToSeekHelp" :key="i">
                        <div class="flex items-start gap-2 bg-white/10 rounded-xl px-3 py-2.5">
                            <div class="w-1.5 h-1.5 rounded-full bg-white/70 mt-1.5 flex-shrink-0"></div>
                            <p class="text-[11px] font-bold leading-relaxed" x-text="w"></p>
                        </div>
                    </template>
                </div>

                <div class="mt-5 ml-11 flex flex-wrap gap-2">
                    <a href="/konsultasi"
                       class="px-5 py-2.5 rounded-xl bg-white text-[#186369]
                              text-sm font-bold hover:bg-white/90
                              transition active:scale-95
                              flex items-center gap-2">
                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none"
                             stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                        </svg>
                        Konsultasi Sekarang
                    </a>
                </div>
            </section>
        </div>

        {{-- KANAN --}}
        <div class="space-y-5 lg:sticky lg:top-8 lg:self-start">

            {{-- SCORE CARD --}}
            <section class="bg-white rounded-[32px] p-5 sm:p-6 border border-gray-100 fade-in-up"
                     style="animation-delay: 120ms;">

                <div class="flex justify-center mb-4">
                    <div class="relative w-[180px] h-[180px]">
                        <svg viewBox="0 0 120 120" class="w-full h-full -rotate-90">
                            <defs>
                                <linearGradient id="score-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#10b981"/>
                                    <stop offset="35%" stop-color="#3a8a8c"/>
                                    <stop offset="65%" stop-color="#f59e0b"/>
                                    <stop offset="100%" stop-color="#e63946"/>
                                </linearGradient>
                            </defs>
                            <circle cx="60" cy="60" r="50" fill="none"
                                    stroke="#e5e7eb" stroke-width="10"/>
                            <circle cx="60" cy="60" r="50" fill="none"
                                    stroke="url(#score-grad)" stroke-width="10"
                                    stroke-linecap="round"
                                    :stroke-dasharray="`${scoreArc} 314`"
                                    :stroke-dashoffset="0"
                                    class="score-fill"
                                    style="transform-origin: center;"/>
                        </svg>

                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">
                                Tingkat Gejala
                            </p>
                            <p class="text-4xl font-bold text-[#186369] leading-none">
                                <span x-text="scorePercent"></span>
                                <span class="text-2xl text-gray-300">%</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="text-center mb-4">
                    <p class="text-2xl font-bold" :style="`color: ${severity.warna};`"
                       x-text="severity.label"></p>
                    <p class="text-[11px] text-[#186369]/60 mt-2 leading-relaxed">
                        Ini bukan nilai—ini gambaran seberapa berat beban yang kamu rasakan.
                    </p>
                </div>

                <div class="h-px bg-gray-100 my-4"></div>

                <p class="text-[10px] font-bold text-[#186369]/60 uppercase tracking-widest mb-3">
                    Kategori Tingkat Keparahan
                </p>

                <div class="relative mb-4">
                    <div class="flex h-3 rounded-full overflow-hidden">
                        <div class="flex-1" style="background: #10b981;"></div>
                        <div class="flex-1" style="background: #3a8a8c;"></div>
                        <div class="flex-1" style="background: #f59e0b;"></div>
                        <div class="flex-1" style="background: #e63946;"></div>
                    </div>
                    <div class="absolute -top-2 transition-all duration-700"
                         :style="`left: calc(${pointerPosition}% - 8px);`">
                        <svg viewBox="0 0 16 16" class="w-4 h-4 drop-shadow-md" fill="#186369">
                            <path d="M8 14l-4-4h8z"/>
                        </svg>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-1 text-center">
                    <div>
                        <p class="text-[10px] font-bold text-[#186369]">0-19%</p>
                        <p class="text-[9px] text-gray-400 mt-0.5">Normal</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-[#186369]">20-38%</p>
                        <p class="text-[9px] text-gray-400 mt-0.5">Ringan</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-[#186369]">39-63%</p>
                        <p class="text-[9px] text-gray-400 mt-0.5">Sedang</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-[#186369]">64-100%</p>
                        <p class="text-[9px] text-gray-400 mt-0.5">Berat</p>
                    </div>
                </div>
            </section>

            {{-- RINGKASAN PER AREA --}}
            <section x-show="clusterStats.length > 0"
                     class="bg-white rounded-[32px] p-5 sm:p-6 border border-gray-100 fade-in-up"
                     style="animation-delay: 180ms;">

                <h2 class="text-base font-bold text-[#186369] mb-1">
                    Ringkasan Per Area
                </h2>
                <p class="text-[10px] text-[#186369]/50 mb-4 leading-relaxed">
                    Mana yang paling perlu perhatianmu
                </p>

                <div class="space-y-2.5">
                    <template x-for="(cluster, ci) in clusterStats" :key="'sum-' + cluster.id">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 text-base"
                                 :style="`background: ${cluster.warna}20;`">
                                <span x-text="cluster.icon"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[11px] font-bold text-[#186369] truncate"
                                   x-text="cluster.nama"></p>
                                <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden mt-1">
                                    <div class="h-full rounded-full transition-all duration-700"
                                         :style="`width: ${cluster.avgPercent}%;
                                                  background: ${cluster.levelWarna};`"></div>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold flex-shrink-0"
                                  :style="`color: ${cluster.levelWarna};`"
                                  x-text="cluster.level"></span>
                        </div>
                    </template>
                </div>
            </section>

            {{-- REKOMENDASI --}}
            <section x-show="recommendations.length > 0"
                     class="bg-white rounded-[32px] p-5 sm:p-6 border border-gray-100 fade-in-up"
                     style="animation-delay: 200ms;">

                <h2 class="text-lg font-bold text-[#186369] mb-2">
                    Rekomendasi untuk Kamu
                </h2>
                <p class="text-[11px] text-[#186369]/60 mb-4 leading-relaxed">
                    Pilih satu yang paling ringan untuk kamu mulai hari ini.
                </p>

                <div class="space-y-3">
                    <template x-for="(rec, i) in recommendations" :key="i">
                        <div class="rounded-2xl p-3.5 flex items-start gap-3 fade-in-up"
                             :style="`background: ${rec.bg}; animation-delay: ${(i + 5) * 80}ms;`">
                            <div class="w-9 h-9 rounded-xl bg-white flex items-center justify-center
                                        flex-shrink-0 text-lg shadow-sm">
                                <span x-text="rec.icon"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-[#186369] leading-tight"
                                   x-text="rec.title"></p>
                                <p class="text-[10px] text-[#186369]/60 mt-0.5 leading-relaxed"
                                   x-text="rec.description"></p>
                            </div>
                            <template x-if="rec.title === 'Latihan Pernapasan'">
                                <button type="button"
                                        @click="openBreathing()"
                                        class="self-center px-3 py-1.5 rounded-lg flex-shrink-0
                                               text-[10px] font-bold whitespace-nowrap
                                               transition active:scale-95"
                                        :style="`background: ${rec.buttonColor}; color: white;`">
                                    <span x-text="rec.action"></span>
                                    <span class="ml-0.5">→</span>
                                </button>
                            </template>
                            <template x-if="rec.title !== 'Latihan Pernapasan'">
                                <a :href="rec.link"
                                   class="self-center px-3 py-1.5 rounded-lg flex-shrink-0
                                          text-[10px] font-bold whitespace-nowrap
                                          transition active:scale-95"
                                   :style="`background: ${rec.buttonColor}; color: white;`">
                                    <span x-text="rec.action"></span>
                                    <span class="ml-0.5">→</span>
                                </a>
                            </template>
                        </div>
                    </template>
                </div>
            </section>
        </div>
    </div>

    <p class="text-[10px] text-gray-400 text-center mt-4 mb-4">
        Hasil tes ini bukan diagnosis medis. Untuk bantuan lebih lanjut, konsultasikan dengan profesional.
    </p>


    {{-- BREATHING POPUP --}}
    <div x-show="breathOpen" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[200] bg-[#0a3d42]/60 backdrop-blur-md
                flex items-center justify-center p-4">

        <div class="relative bg-gradient-to-br from-[#0e4348] via-[#186369] to-[#0a3d42]
                    rounded-[40px] shadow-2xl w-full max-w-md
                    p-6 sm:p-8 text-white overflow-hidden">

            <button type="button" @click="closeBreathing()"
                    class="absolute top-5 right-5 w-9 h-9 rounded-full
                           bg-white/10 hover:bg-white/20
                           flex items-center justify-center
                           transition active:scale-90 z-10">
                <svg viewBox="0 0 16 16" class="w-4 h-4 text-white" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <path d="M4 4l8 8M12 4l-8 8"/>
                </svg>
            </button>

            <div class="text-center mb-6 pt-4">
                <p class="text-[10px] font-bold text-white/50 uppercase tracking-widest mb-2">
                    4-7-8 Breathing
                </p>
                <h2 class="text-2xl font-bold">Latihan Pernapasan</h2>
                <p class="text-[11px] text-white/60 mt-1">
                    Berdasarkan penelitian untuk menenangkan saraf vagus
                </p>
            </div>

            <div class="flex items-center justify-center mb-6" style="height: 220px;">
                <div class="relative flex items-center justify-center">
                    <div class="absolute w-40 h-40 rounded-full border-2 border-white/20 ring-pulse"
                         x-show="breathPhase === 'inhale' || breathPhase === 'hold' || breathPhase === 'exhale'"></div>

                    <div class="w-40 h-40 rounded-full flex items-center justify-center
                                bg-gradient-to-br from-white/30 to-white/10
                                backdrop-blur-sm border-2 border-white/40
                                shadow-2xl transition-transform"
                         :class="breathCircleClass">
                        <div class="text-center">
                            <p class="text-5xl font-bold leading-none" x-text="breathTimer"></p>
                            <p class="text-[10px] font-bold text-white/60 mt-1 uppercase tracking-wider"
                               x-text="breathPhaseText"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mb-6">
                <p class="text-base font-bold text-white mb-1" x-text="breathPhaseText"></p>
                <p class="text-[11px] text-white/50"
                   x-show="breathPhase !== 'ready' && breathPhase !== 'done'"
                   x-text="breathProgressText"></p>
            </div>

            <template x-if="breathPhase === 'ready'">
                <div class="space-y-3 mb-4">
                    <div class="bg-white/10 rounded-2xl p-4 border border-white/20">
                        <p class="text-[11px] text-white/80 leading-relaxed">
                            <b>Cara kerja:</b> Tarik napas 4 detik, tahan 7 detik,
                            hembuskan perlahan 8 detik. Ulangi 3 siklus.
                        </p>
                    </div>
                    <div class="bg-white/5 rounded-2xl p-3 border border-white/10">
                        <p class="text-[9px] text-white/40 leading-relaxed">
                            📚 Ref: Gerritsen &amp; Band (2018); NCT07425847 (2025);
                            Avudaiselvi &amp; Prabha, <i>J Pharm Bioallied Sci</i> (2025)
                        </p>
                    </div>
                </div>
            </template>

            <template x-if="breathPhase === 'done'">
                <div class="bg-emerald-500/20 border-2 border-emerald-400/40 rounded-2xl p-4 mb-4">
                    <p class="text-sm font-bold text-emerald-200 text-center">
                        ✨ Selesai! Bagaimana perasaanmu?
                    </p>
                    <p class="text-[11px] text-emerald-100/70 text-center mt-1 leading-relaxed">
                        Coba rasakan: detak jantung lebih pelan, otot lebih rileks.
                    </p>
                </div>
            </template>

            <div class="flex gap-2">
                <template x-if="breathPhase === 'ready'">
                    <button type="button" @click="startBreathing()"
                            class="flex-1 py-3 rounded-2xl bg-white text-[#186369]
                                   font-bold text-sm hover:bg-white/90
                                   transition active:scale-95">
                        Mulai Latihan
                    </button>
                </template>

                <template x-if="breathPhase !== 'ready' && breathPhase !== 'done'">
                    <button type="button" @click="closeBreathing()"
                            class="flex-1 py-3 rounded-2xl bg-white/10 border border-white/20
                                   text-white font-bold text-sm hover:bg-white/20
                                   transition active:scale-95">
                        Hentikan
                    </button>
                </template>

                <template x-if="breathPhase === 'done'">
                    <button type="button" @click="startBreathing()"
                            class="flex-1 py-3 rounded-2xl bg-white/10 border border-white/20
                                   text-white font-bold text-sm hover:bg-white/20
                                   transition active:scale-95">
                        Ulangi
                    </button>
                </template>

                <template x-if="breathPhase === 'done'">
                    <button type="button" @click="closeBreathing()"
                            class="flex-1 py-3 rounded-2xl bg-white text-[#186369]
                                   font-bold text-sm hover:bg-white/90
                                   transition active:scale-95">
                        Tutup
                    </button>
                </template>
            </div>
        </div>
    </div>

</div>

@endsection


@push('scripts')
<script>
    window.serverTest = @json($test);
    window.serverResult = @json($result);

    window.resultPage = function (testId) {
        return {
            testId: testId,
            answers: {},
            testData: null,
            savedToHistory: false,
            isHistoryMode: false,
            historyId: null,

            breathOpen: false,
            breathPhase: 'ready',
            breathCycle: 0,
            breathTimer: 0,
            breathInterval: null,
            totalCycles: 3,
            phaseDurations: { inhale: 4, hold: 7, exhale: 8 },

            clusterMap: [
                { id: 'mood', nama: 'Suasana Hati', icon: '🌧️', warna: '#3a8a8c', nums: [1, 3, 4, 9],
                  impact: {
                      1: 'Ada kalanya suasana hatimu turun tanpa alasan yang jelas, tapi kamu masih bisa melewatinya.',
                      2: 'Kamu cukup sering merasa sedih, hampa, atau kehilangan harapan — meskipun tidak selalu terlihat dari luar.',
                      3: 'Suasana hatimu sering kali berat. Kamu mungkin merasa sedih mendalam dan sulit menemukan alasan untuk tersenyum.',
                      4: 'Kamu sedang membawa beban emosi yang sangat berat. Rasa sedih, hampa, dan putus asa hampir selalu hadir.',
                  } },
                { id: 'sosial', nama: 'Koneksi Sosial', icon: '🫂', warna: '#f59e0b', nums: [2, 10],
                  impact: {
                      1: 'Kadang kamu merasa sedikit jauh dari orang lain, tapi masih nyaman berinteraksi.',
                      2: 'Ada kalanya kamu merasa kesepian atau enggan untuk terhubung — meski ada orang di sekitarmu.',
                      3: 'Kamu sering merasa terputus dari orang-orang di sekitarmu. Menjalin hubungan terasa melelahkan.',
                      4: 'Kamu merasa sangat kesepian, bahkan saat bersama orang lain. Menarik diri jadi respons alami.',
                  } },
                { id: 'cemas', nama: 'Kecemasan', icon: '⚡', warna: '#a855f7', nums: [5],
                  impact: {
                      1: 'Sesekali kamu merasa tegang, tapi masih bisa kamu kendalikan.',
                      2: 'Kamu cukup sering merasa cemas atau tidak tenang, meskipun tidak selalu mengganggu aktivitasmu.',
                      3: 'Rasa cemas dan tegang sering menghampirimu. Sulit untuk benar-benar rileks.',
                      4: 'Kamu hampir selalu merasa gelisah atau tegang. Ketidaktenangan ini terasa menguras energi.',
                  } },
                { id: 'motivasi', nama: 'Motivasi & Energi', icon: '🔋', warna: '#4090f2', nums: [6, 7],
                  impact: {
                      1: 'Kamu masih bisa menemukan semangat, meskipun sesekali energi terasa menurun.',
                      2: 'Ada kalanya kamu kehilangan minat atau merasa lelah — meski biasanya bisa kamu paksakan.',
                      3: 'Kamu sering kesulitan memulai atau menyelesaikan hal-hal yang biasanya kamu sukai. Energi terasa cepat habis.',
                      4: 'Minat dan energi untuk beraktivitas hampir tidak ada. Bangun saja terasa sangat berat.',
                  } },
                { id: 'kognitif', nama: 'Fokus & Berpikir', icon: '🧠', warna: '#8b7bd8', nums: [8, 15, 16, 17],
                  impact: {
                      1: 'Konsentrasimu masih baik, meskipun sesekali ada gangguan kecil.',
                      2: 'Kamu cukup sering kesulitan fokus atau mengingat hal-hal kecil. Pikiran terasa lebih lambat dari biasanya.',
                      3: 'Fokus dan daya ingatmu sering terganggu. Mengambil keputusan sederhana pun terasa melelahkan.',
                      4: 'Pikiranmu terasa berkabut hampir sepanjang waktu. Sangat sulit untuk berpikir jernih atau mengingat.',
                  } },
                { id: 'somatik', nama: 'Tubuh & Fisik', icon: '🫀', warna: '#14b8a6', nums: [11, 12, 13, 14],
                  impact: {
                      1: 'Tubuhmu masih cukup responsif, sesekali terasa lelah wajar.',
                      2: 'Kamu mulai merasakan sinyal dari tubuhmu — mungkin sulit tidur, nafsu makan berubah, atau cepat lelah.',
                      3: 'Tubuhmu sering merespons beban ini: gangguan tidur, perubahan nafsu makan, atau keluhan fisik seperti sakit kepala.',
                      4: 'Tubuhmu menanggung beban berat — tidur, nafsu makan, dan energi fisik sangat terganggu.',
                  } },
                { id: 'selfworth', nama: 'Harga Diri', icon: '💔', warna: '#e879a4', nums: [18, 19],
                  impact: {
                      1: 'Kamu masih memandang dirimu dengan baik, sesekali ada keraguan wajar.',
                      2: 'Ada kalanya kamu merasa tidak cukup baik atau menyalahkan diri sendiri atas hal-hal di luar kendalimu.',
                      3: 'Kamu sering merasa tidak berharga, gagal, atau menyalahkan diri sendiri berlebihan.',
                      4: 'Hampir setiap saat kamu merasa dirimu tidak berharga atau gagal sebagai manusia.',
                  } },
                { id: 'kritis', nama: 'Pikiran Berat', icon: '🚨', warna: '#e63946', nums: [20],
                  impact: {
                      1: '',
                      2: 'Sesekali muncul pikiran yang gelap tentang dirimu sendiri. Penting untuk kamu perhatikan.',
                      3: 'Pikiran tentang tidak ingin melanjutkan hidup cukup sering muncul. Kamu tidak sendiri, dan bantuan itu ada.',
                      4: 'Kamu sedang menanggung pikiran yang sangat berat tentang dirimu sendiri. Sangat penting untuk segera mencari bantuan profesional.',
                  } },
            ],

            init() {
                this.testData = window.serverTest;
                this.answers = window.serverResult?.answers || {};
                this.syncHistoryStorage();
            },

            syncHistoryStorage() {
                if (!this.testData || !window.serverResult?.id) return;

                const existing = this.readHistoryStorage();
                const finishedAt = window.serverResult?.finished_at || new Date().toISOString();
                const entry = {
                    id: String(window.serverResult.id),
                    testId: Number(this.testId),
                    testJenis: this.testData.jenis || 'Tes',
                    testJudul: this.testData.judul || 'Kesehatan Mental',
                    testWarna: this.testData.warna || '#186369',
                    tanggalLabel: new Date(finishedAt).toLocaleDateString('id-ID', {
                        day: 'numeric', month: 'long', year: 'numeric',
                    }),
                    totalScore: this.scorePercent,
                    severityLabel: this.severity?.label || '—',
                    severityWarna: this.severity?.warna || '#186369',
                    diagnosisLabel: this.currentDiagnosis?.label || this.severity?.label || '—',
                    diagnosisSublabel: this.currentDiagnosis?.sublabel || '',
                    isValid: !this.isInvalid,
                    isHighRisk: this.highRisk,
                    clusters: this.clusterStats.slice(0, 3).map(cluster => ({
                        id: cluster.id,
                        nama: cluster.nama,
                        icon: cluster.icon,
                        warna: cluster.warna,
                        level: cluster.level,
                        levelWarna: cluster.levelWarna,
                    })),
                };

                const filtered = existing.filter(item => String(item.id) !== String(window.serverResult.id));
                filtered.push(entry);
                filtered.sort((a, b) => new Date(b.tanggalLabel) - new Date(a.tanggalLabel));

                try {
                    localStorage.setItem('mindo_test_history', JSON.stringify(filtered));
                    this.savedToHistory = true;
                } catch (e) {
                    this.savedToHistory = false;
                }
            },

            readHistoryStorage() {
                try {
                    const raw = localStorage.getItem('mindo_test_history');
                    const parsed = raw ? JSON.parse(raw) : [];
                    return Array.isArray(parsed) ? parsed : [];
                } catch (e) {
                    return [];
                }
            },

            buildCombinedOrder(soal, lie) {
                const total = soal.length + lie.length;
                const positions = [];
                const interval = total / (lie.length + 1);
                for (let i = 1; i <= lie.length; i++) {
                    positions.push(Math.round(interval * i));
                }

                const combined = [];
                let soalIdx = 0, lieIdx = 0, pos = 1;
                while (soalIdx < soal.length || lieIdx < lie.length) {
                    if (positions.includes(pos) && lieIdx < lie.length) {
                        combined.push({ isLie: true, originalNumber: null });
                        lieIdx++;
                    } else if (soalIdx < soal.length) {
                        combined.push({ isLie: false, originalNumber: soalIdx + 1 });
                        soalIdx++;
                    } else break;
                    pos++;
                }
                return combined;
            },

            findPos(origNum) {
                if (!this.testData) return null;
                const soal = this.testData.soal || [];
                const lie = this.testData.lie_soal || [];
                const combined = this.buildCombinedOrder(soal, lie);
                for (let i = 0; i < combined.length; i++) {
                    if (combined[i].originalNumber === origNum) return i + 1;
                }
                return null;
            },

            get rawScore() {
                if (!this.testData) {
                    return Object.values(this.answers).reduce((s, v) => s + v, 0);
                }
                const soal = this.testData.soal || [];
                const lie = this.testData.lie_soal || [];
                const weights = this.testData.critical_weights || {};
                const combined = this.buildCombinedOrder(soal, lie);
                let sum = 0;
                combined.forEach((q, idx) => {
                    const pos = idx + 1;
                    const answer = this.answers[pos];
                    if (answer === undefined) return;
                    if (q.isLie) return;
                    const w = weights[q.originalNumber] || 1.0;
                    sum += answer * w;
                });
                return sum;
            },

            get weightedMax() {
                const soal = this.testData?.soal || [];
                const weights = this.testData?.critical_weights || {};
                let max = 0;
                soal.forEach((_, i) => {
                    const num = i + 1;
                    max += 4 * (weights[num] || 1.0);
                });
                return max || 80;
            },

            get scorePercent() {
                const p = (this.rawScore / this.weightedMax) * 100;
                return Math.min(100, Math.round(p));
            },

            get totalScore() { return this.scorePercent; },
            get maxScore() { return 100; },
            get scoreArc() { return (this.scorePercent / 100) * 314; },

            get lieAnswers() {
                if (!this.testData) return [];
                const soal = this.testData.soal || [];
                const lie = this.testData.lie_soal || [];
                if (!lie.length) return [];
                const combined = this.buildCombinedOrder(soal, lie);
                const arr = [];
                combined.forEach((q, idx) => {
                    if (!q.isLie) return;
                    const pos = idx + 1;
                    const answer = this.answers[pos];
                    if (answer !== undefined) arr.push(answer);
                });
                return arr;
            },

            get lieViolations() { return this.lieAnswers.filter(a => a >= 3).length; },
            get lieThreshold() { return this.testData?.lie_threshold ?? 2; },
            get isInvalid() { return this.lieViolations >= this.lieThreshold; },

            get isMisfit() {
                if (!this.testData) return false;
                const soal = this.testData.soal || [];
                if (soal.length < 20) return false;
                const mildNums = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
                const severeNums = [19, 20];
                const mildAnswers = mildNums.map(n => this.answers[this.findPos(n)]).filter(a => a !== undefined);
                const severeAnswers = severeNums.map(n => this.answers[this.findPos(n)]).filter(a => a !== undefined);
                if (!mildAnswers.length || !severeAnswers.length) return false;
                const mildAvg = mildAnswers.reduce((s, v) => s + v, 0) / mildAnswers.length;
                const severeAvg = severeAnswers.reduce((s, v) => s + v, 0) / severeAnswers.length;
                return mildAvg >= 2.5 && severeAvg <= 0.5;
            },

            get highRisk() {
                const pos20 = this.findPos(20);
                if (!pos20) return false;
                const q20 = this.answers[pos20];
                return q20 === 3 || q20 === 4;
            },

            get severity() {
                if (this.isInvalid) return { label: '⚠️ Jawaban Tidak Valid', warna: '#991b1b' };
                if (this.highRisk) return { label: '⚠️ Risiko Tinggi', warna: '#991b1b' };
                const s = this.scorePercent;
                if (s <= 19) return { label: 'Normal',                  warna: '#10b981' };
                if (s <= 38) return { label: 'Indikasi Depresi Ringan', warna: '#3a8a8c' };
                if (s <= 63) return { label: 'Indikasi Depresi Sedang', warna: '#f59e0b' };
                return { label: 'Indikasi Depresi Berat', warna: '#e63946' };
            },

            get currentDiagnosis() {
                const d = this.testData?.diagnosis;
                if (!d) return {};
                if (this.isInvalid) {
                    return {
                        label: '⚠️ Jawaban Tidak Valid',
                        sublabel: 'Terdeteksi pola jawaban yang tidak konsisten',
                        detail: 'Kamu menjawab "Sering" atau "Selalu" pada soal validitas yang secara psikologis tidak mungkin terjadi.',
                    };
                }
                if (this.highRisk) return d.berat || {};
                const s = this.scorePercent;
                if (s <= 19) return d.minimal || {};
                if (s <= 38) return d.ringan || {};
                if (s <= 63) return d.sedang || {};
                return d.cukup_berat || {};
            },

            get pointerPosition() {
                if (this.isInvalid) return 0;
                return Math.min(100, this.scorePercent);
            },

            get emotionalHeader() {
                const s = this.scorePercent;
                if (this.isInvalid) return 'Ada yang perlu kita perbaiki dari jawabanmu';
                if (s <= 19) return 'Kamu baik-baik saja 💚';
                if (s <= 38) return 'Ada beban ringan yang kamu pikul';
                if (s <= 63) return 'Kamu sedang melalui masa yang berat';
                return 'Kamu tidak sendiri 💙';
            },

            get clusterStats() {
                if (!this.testData) return [];
                const clusters = [];
                this.clusterMap.forEach(cl => {
                    let totalScore = 0;
                    let countTerasa = 0;
                    cl.nums.forEach(num => {
                        const pos = this.findPos(num);
                        if (!pos) return;
                        const score = this.answers[pos] ?? 0;
                        if (score > 0) {
                            totalScore += score;
                            countTerasa++;
                        }
                    });
                    if (countTerasa === 0) return;
                    const avg = totalScore / countTerasa;
                    const clusterLevel = this.clusterLevel(avg);
                    const avgRound = Math.max(1, Math.min(4, Math.round(avg)));
                    const impactText = cl.impact[avgRound] || '';
                    clusters.push({
                        id: cl.id, nama: cl.nama, icon: cl.icon, warna: cl.warna,
                        countTerasa, totalSoal: cl.nums.length,
                        avg, avgPercent: Math.round((avg / 4) * 100),
                        level: clusterLevel.label,
                        levelWarna: clusterLevel.warna,
                        levelBg: clusterLevel.bg,
                        impactText,
                    });
                });
                clusters.sort((a, b) => b.avg - a.avg);
                return clusters;
            },

            clusterLevel(avg) {
                if (avg < 1) return { label: 'Aman',              warna: '#10b981', bg: '#10b98115' };
                if (avg < 2) return { label: 'Ringan',            warna: '#3a8a8c', bg: '#3a8a8c15' };
                if (avg < 3) return { label: 'Cukup Mengganggu',  warna: '#f59e0b', bg: '#f59e0b15' };
                return { label: 'Sangat Mengganggu', warna: '#e63946', bg: '#e6394615' };
            },

            get activeClusters() {
                return this.clusterStats.filter(c => c.countTerasa > 0).length;
            },

            get topCluster() {
                return this.clusterStats[0] || null;
            },

            get impacts() { return this.testData?.impacts || []; },
            get recommendations() { return this.testData?.recommendations || []; },
            get whenToSeekHelp() { return this.testData?.whenToSeekHelp || []; },

            saveResult() {
                this.syncHistoryStorage();
                if (this.savedToHistory) {
                    this.showToast('Hasil sudah tersimpan di profil kamu');
                }
            },

            showToast(msg) {
                const toast = document.createElement('div');
                toast.textContent = msg;
                toast.style.cssText = `
                    position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%);
                    background: #186369; color: white; padding: 12px 24px; border-radius: 16px;
                    font-size: 13px; font-weight: 700; z-index: 9999;
                    box-shadow: 0 10px 40px rgba(24,99,105,0.3);
                `;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 2500);
            },

            openBreathing() {
                this.breathOpen = true;
                this.breathPhase = 'ready';
                this.breathCycle = 0;
                this.breathTimer = 0;
            },

            closeBreathing() {
                this.breathOpen = false;
                this.breathPhase = 'ready';
                if (this.breathInterval) {
                    clearInterval(this.breathInterval);
                    this.breathInterval = null;
                }
            },

            startBreathing() {
                this.breathPhase = 'inhale';
                this.breathCycle = 1;
                this.breathTimer = this.phaseDurations.inhale;
                this.runTimer();
            },

            runTimer() {
                if (this.breathInterval) clearInterval(this.breathInterval);
                this.breathInterval = setInterval(() => {
                    this.breathTimer--;
                    if (this.breathTimer <= 0) {
                        if (this.breathPhase === 'inhale') {
                            this.breathPhase = 'hold';
                            this.breathTimer = this.phaseDurations.hold;
                        } else if (this.breathPhase === 'hold') {
                            this.breathPhase = 'exhale';
                            this.breathTimer = this.phaseDurations.exhale;
                        } else if (this.breathPhase === 'exhale') {
                            if (this.breathCycle >= this.totalCycles) {
                                this.breathPhase = 'done';
                                clearInterval(this.breathInterval);
                                this.breathInterval = null;
                            } else {
                                this.breathCycle++;
                                this.breathPhase = 'inhale';
                                this.breathTimer = this.phaseDurations.inhale;
                            }
                        }
                    }
                }, 1000);
            },

            get breathPhaseText() {
                if (this.breathPhase === 'ready') return 'Siap?';
                if (this.breathPhase === 'inhale') return 'Tarik napas...';
                if (this.breathPhase === 'hold') return 'Tahan...';
                if (this.breathPhase === 'exhale') return 'Hembuskan...';
                return 'Selesai ✨';
            },

            get breathCircleClass() {
                if (this.breathPhase === 'inhale') return 'breath-circle-inhale';
                if (this.breathPhase === 'hold') return 'breath-circle-hold';
                if (this.breathPhase === 'exhale') return 'breath-circle-exhale';
                return '';
            },

            get breathProgressText() {
                return `Siklus ${this.breathCycle} dari ${this.totalCycles}`;
            },
        };
    };
</script>
@endpush