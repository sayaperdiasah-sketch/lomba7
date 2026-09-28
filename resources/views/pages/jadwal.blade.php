@extends('layouts.app', ['activeNav' => 'jadwal'])

@section('title', 'To Do List')

@push('styles')
<style>
    .cal-day {
        width: 38px; height: 38px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 10px; font-size: 13px; font-weight: 700;
        color: #186369; cursor: pointer; transition: all 0.2s; position: relative;
    }
    .cal-day:hover { background: rgba(24, 99, 105, 0.08); transform: scale(1.08); }
    .cal-day.today { background: #e63946; color: #ffffff; box-shadow: 0 4px 12px rgba(230, 57, 70, 0.3); }
    .cal-day.selected { background: #186369; color: #ffffff; box-shadow: 0 4px 14px rgba(24, 99, 105, 0.4); }
    .cal-day.selected.today { background: linear-gradient(135deg, #186369 50%, #e63946 50%); }
    .cal-day.empty { cursor: default; pointer-events: none; }
    .cal-day.has-schedule::after {
        content: ''; position: absolute; bottom: 3px; left: 50%;
        transform: translateX(-50%); width: 4px; height: 4px;
        border-radius: 50%; background: #186369;
    }
    .cal-day.today.has-schedule::after,
    .cal-day.selected.has-schedule::after { background: #ffffff; }

    .sweep-container { position: absolute; inset: 0; pointer-events: none; z-index: 60; overflow: hidden; border-radius: 40px; }
    .sweep-bar {
        position: absolute; top: 0; left: 0; width: 50%; height: 100%;
        animation: sweep-pan 0.9s cubic-bezier(0.5, 0, 0.5, 1) forwards; filter: blur(2px);
    }
    @keyframes sweep-pan {
        0% { transform: translateX(-100%); opacity: 0.9; }
        60% { opacity: 0.7; }
        100% { transform: translateX(300%); opacity: 0; }
    }

    @keyframes slide-up {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .slide-up { animation: slide-up 0.3s ease-out backwards; }

    @keyframes check-bounce {
        0% { transform: scale(0); }
        60% { transform: scale(1.3); }
        100% { transform: scale(1); }
    }
    .check-bounce { animation: check-bounce 0.4s ease-out; }

    @keyframes modal-in {
        from { opacity: 0; transform: scale(0.9) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .modal-anim { animation: modal-in 0.3s ease-out; }

    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(192, 160, 95, 0.4); }
        50% { box-shadow: 0 0 0 10px rgba(192, 160, 95, 0); }
    }
    .pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }

    .num-input {
        width: 40px; text-align: center; background: transparent;
        border: none; outline: none; font-weight: 700; font-size: 11px;
        padding: 0; -moz-appearance: textfield; appearance: textfield;
    }
    .num-input::-webkit-outer-spin-button,
    .num-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

    .tracker-activity-row:hover {
        background: var(--activity-hover-bg) !important;
    }
</style>
@endpush


@section('content')
<div x-data="jadwalApp()" x-init="init()" class="contents">
    <script>
        window.serverCategories = @json($categories);
        window.serverSchedules = @json($scheduleData);
        window.serverConfirmedDates = @json($confirmedDates);
        window.serverHistory = @json($history);
        window.serverRecommendationTemplates = @json($templates);
    </script>

    {{-- HEADER --}}
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 flex-shrink-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-[#186369]">To Do List</h1>
            <p class="text-sm text-[#186369]/70 mt-1" x-text="todayString()"></p>
        </div>

        <div class="flex items-center gap-3">
            <form role="search" @submit.prevent class="relative flex-1 sm:w-64 lg:w-72">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 bg-gray-400 rounded"></div>
                <input type="search" placeholder="Cari aktivitas..."
                       x-model="search" @input="searchChanged()"
                       class="w-full pl-11 pr-4 py-2.5 text-xs font-bold bg-white border-2 border-gray-300 rounded-full focus:outline-none focus:border-[#186369]">
            </form>

            <button type="button"
                    @click="hasilOpen = !hasilOpen; profileOpen = false"
                    data-auth
                    class="relative flex-shrink-0 w-11 h-11 lg:w-12 lg:h-12 rounded-full bg-white shadow-md
                           flex items-center justify-center transition-all hover:scale-105 active:scale-95"
                    :class="hasilOpen ? 'ring-2 ring-[#186369] shadow-lg shadow-[#186369]/20' : ''">
                <svg viewBox="0 0 24 24" class="w-5 h-5 text-[#186369]" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 3v18h18M7 14l4-4 3 3 5-6" />
                </svg>
            </button>

            <div class="relative flex-shrink-0">
                <span x-show="rippleActive" class="absolute inset-0 rounded-full bg-[#186369] pointer-events-none avatar-ripple"></span>
                <button type="button" @click="toggleProfile(); hasilOpen = false"
                        class="relative flex-shrink-0 w-11 h-11 lg:w-12 lg:h-12 rounded-full overflow-hidden border-2 lg:border-[3px]
                               transition-all hover:scale-105 active:scale-95"
                        :class="profileOpen ? 'border-[#186369] shadow-lg' : 'border-white shadow-md'">
                    <img src="{{ asset('images/avatar.png') }}" alt="Profil" class="w-full h-full object-cover">
                    <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white"
                          :class="profileOpen ? 'bg-emerald-400' : 'bg-gray-300'"></span>
                </button>
            </div>
        </div>
    </header>

    {{-- KATEGORI FILTER — klik card → popup --}}
    <div class="flex-shrink-0">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-bold text-[#186369]">Kategori</h2>
            <button type="button" @click="openAllActivitiesPopup()"
                    class="text-[10px] font-bold text-[#186369]
                           bg-white px-3 py-1.5 rounded-lg border border-gray-200
                           hover:bg-[#f5f8f7] transition active:scale-95
                           flex items-center gap-1">
                <svg viewBox="0 0 16 16" class="w-3 h-3" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 4h12M2 8h12M2 12h12"/>
                </svg>
                Lihat Semua
            </button>
        </div>

        <div class="flex gap-3 overflow-x-auto pb-2 scroll-thin-h snap-x snap-mandatory">
            <template x-for="(cat, i) in categories" :key="cat.id">
                <button type="button" @click="openCategoryPopup(cat.id)" data-auth
                        class="snap-start flex-shrink-0 w-[200px] sm:w-[220px] rounded-3xl p-4 sm:p-5 text-left
                               transition-all duration-200 hover:scale-[1.02] active:scale-[0.98]
                               hover:shadow-lg"
                        :style="`background: ${cat.warna}; color: ${cat.teksWarna};
                                 box-shadow: 0 4px 16px ${cat.warna}30;`">

                    <div class="flex justify-between items-center mb-2">
                        <p class="text-sm sm:text-base font-bold" x-text="cat.nama + ' :'"></p>
                        <div class="text-right">
                            <p class="text-2xl sm:text-4xl font-bold leading-none" x-text="cat.jenis.length"></p>
                            <p class="text-[10px] sm:text-xs font-bold mt-1 opacity-80">Aktivitas</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-1 mt-2" x-show="cat.jenis && cat.jenis.length">
                        <template x-for="(j, ji) in cat.jenis.slice(0, 3)" :key="ji">
                            <span class="px-2 py-0.5 rounded-md text-[9px] font-bold"
                                  :style="`background: ${cat.teksWarna}20;`" x-text="j.nama"></span>
                        </template>
                        <span x-show="cat.jenis.length > 3"
                              class="px-2 py-0.5 rounded-md text-[9px] font-bold"
                              :style="`background: ${cat.teksWarna}20;`"
                              x-text="'+' + (cat.jenis.length - 3)"></span>
                    </div>

                    <div class="flex items-center justify-end mt-3">
                        <span class="text-[9px] font-bold opacity-70 flex items-center gap-0.5">
                            Buka
                            <svg viewBox="0 0 16 16" class="w-2.5 h-2.5" fill="none"
                                 stroke="currentColor" stroke-width="2.5"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 4l4 4-4 4"/>
                            </svg>
                        </span>
                    </div>
                </button>
            </template>
        </div>
    </div>

    {{-- KALENDER + TRACKER --}}
    <section class="bg-white rounded-[40px] overflow-hidden flex-shrink-0 relative border border-gray-100">

        <template x-if="sweepActive">
            <div class="sweep-container" x-cloak>
                <div class="sweep-bar"
                     :style="`background: linear-gradient(90deg, transparent 0%, ${sweepColor}CC 40%, ${sweepColor}FF 50%, ${sweepColor}CC 60%, transparent 100%);`"></div>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.3fr]">

            {{-- KALENDER --}}
            <div class="p-5 sm:p-7 lg:border-r border-gray-100">
                <div class="flex items-center justify-between mb-5">
                    <button type="button" @click="prevMonth()"
                            class="w-9 h-9 rounded-full hover:bg-[#186369]/10 flex items-center justify-center transition active:scale-90">
                        <svg viewBox="0 0 16 16" class="w-4 h-4 text-[#186369]" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10 4L6 8l4 4" />
                        </svg>
                    </button>
                    <h3 class="text-lg font-bold text-[#186369]" x-text="monthName() + ' ' + year"></h3>
                    <button type="button" @click="nextMonth()"
                            class="w-9 h-9 rounded-full hover:bg-[#186369]/10 flex items-center justify-center transition active:scale-90">
                        <svg viewBox="0 0 16 16" class="w-4 h-4 text-[#186369]" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 4l4 4-4 4" />
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-7 gap-1 mb-2">
                    <template x-for="(d, i) in ['S','S','F','T','W','T','M']" :key="i">
                        <div class="text-center text-[11px] font-bold text-[#186369]/40 py-1" x-text="d"></div>
                    </template>
                </div>

                <div class="grid grid-cols-7 gap-1 justify-items-center mb-5">
                    <template x-for="(day, i) in calendarDays()" :key="i">
                        <div :class="`cal-day ${day === '' ? 'empty' : ''}
                                     ${isToday(day) ? 'today' : ''}
                                     ${isSelectedDate(day) ? 'selected' : ''}
                                     ${hasScheduleOn(day) ? 'has-schedule' : ''}`"
                             @click="day && selectDate(day)"
                             x-text="day"></div>
                    </template>
                </div>

                <div class="space-y-2">
                    <button type="button" @click="goToToday()"
                            class="w-full py-2.5 rounded-xl bg-[#f5f8f7] hover:bg-[#eef3f2]
                                   text-[#186369] text-xs font-bold transition active:scale-95
                                   flex items-center justify-center gap-2">
                        <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 6v6l4 2"/>
                        </svg>
                        Kembali ke Hari Ini
                    </button>

                    <button type="button" @click="openAutoGenerate()"
                            class="w-full py-2.5 rounded-xl bg-[#c0a05f] hover:bg-[#a88f52]
                                   text-white text-xs font-bold transition active:scale-95
                                   flex items-center justify-center gap-2 pulse-glow">
                        <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                        </svg>
                        Buat Jadwal Otomatis
                    </button>
                </div>
            </div>

            {{-- TRACKER --}}
            <div class="p-5 sm:p-7 flex flex-col" :style="trackerStyle()">

                <div class="flex items-start justify-between mb-4 flex-shrink-0">
                    <div>
                        <h3 class="text-sm font-bold" :style="trackerTextStyle()">Aktivitas Hari Ini</h3>
                        <p class="text-[10px] font-bold opacity-70 mt-0.5" :style="trackerTextStyle()"
                           x-text="selectedDateLabel()"></p>
                    </div>
                    <span class="text-[10px] font-bold opacity-70 px-2 py-1 rounded-lg"
                          :style="`background: ${trackerItemBg()}; color: ${trackerTextColorRaw()};`"
                          x-text="countDone() + '/' + selectedTodos().length + ' item'"></span>
                </div>

                <template x-if="isFutureDate(selectedDate)">
                    <div class="mb-3 rounded-xl px-3 py-2 text-[10px] font-bold text-center"
                         style="background: rgba(255,255,255,0.18);">
                        Hari tersebut belum tiba. Aktivitas belum dapat diselesaikan.
                    </div>
                </template>
                <template x-if="isPastDate(selectedDate)">
                    <div class="mb-3 rounded-xl px-3 py-2 text-[10px] font-bold text-center"
                         style="background: rgba(255,255,255,0.18);">
                        Hari sudah dilewati. Jadwal hanya dapat dilihat.
                    </div>
                </template>

                <div class="flex-1 min-h-0 overflow-y-auto scroll-thin pr-1" style="max-height: 320px;">

                    <template x-if="selectedTodos().length === 0">
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <div class="w-16 h-16 rounded-full mb-3 flex items-center justify-center"
                                 style="background: rgba(255,255,255,0.15);">
                                <svg viewBox="0 0 24 24" class="w-8 h-8 opacity-50" :style="trackerTextStyle()"
                                     fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 11l3 3L22 4 M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                                </svg>
                            </div>
                            <p class="text-xs font-bold opacity-80 mb-1" :style="trackerTextStyle()">
                                Belum ada aktivitas
                            </p>
                            <p class="text-[10px] leading-relaxed opacity-60 max-w-[220px] mb-4" :style="trackerTextStyle()">
                                Pilih kategori di atas atau pakai Rekomendasi
                            </p>
                            <div class="flex gap-2">
                                <button type="button" @click="openAllActivitiesPopup()" data-auth
                                        class="px-4 py-2 rounded-xl text-[11px] font-bold transition active:scale-95"
                                        style="background: #ffffff; color: #186369;">
                                    📋 Semua
                                </button>
                                <button type="button" @click="openRekomendasi()" data-auth
                                        class="px-4 py-2 rounded-xl text-[11px] font-bold transition active:scale-95"
                                        style="background: rgba(255,255,255,0.25); color: inherit;">
                                    ⚡ Rekomendasi
                                </button>
                            </div>
                        </div>
                    </template>

                    <div class="space-y-2">
                        <template x-for="(item, i) in selectedTodos()" :key="'track-' + item.id">
                               <div class="tracker-activity-row slide-up flex items-center gap-2 rounded-xl px-3 py-2.5 transition-all"
                                   :style="`background: ${trackerItemBg()};
                                          --activity-hover-bg: ${item.warna || trackerItemAccent()}55;
                                          animation-delay: ${i * 40}ms;
                                          opacity: ${item.done ? '0.55' : '1'};`">

                                {{-- CHECKBOX --}}
                                <button type="button"
                                        @click="!isDateConfirmed(selectedDate) && canCompleteDate(selectedDate) && toggleDone(item)"
                                        :disabled="isDateConfirmed(selectedDate) || !canCompleteDate(selectedDate)"
                                        :title="item.done ? 'Tandai belum selesai' : 'Tandai selesai'"
                                        class="w-5 h-5 rounded-md flex-shrink-0 flex items-center justify-center
                                               transition active:scale-90 disabled:cursor-not-allowed"
                                        :style="item.done
                                            ? 'background: #10b981; border: 2px solid #10b981;'
                                            : `background: transparent; border: 2px solid ${trackerItemAccent()};`">
                                    <svg x-show="item.done"
                                         viewBox="0 0 16 16" class="w-3 h-3 check-bounce"
                                         fill="none" stroke="white" stroke-width="3"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 8l4 4 7-7" />
                                    </svg>
                                </button>

                                <p class="flex-1 text-[11px] font-bold truncate min-w-0"
                                   :class="item.done ? 'line-through' : ''"
                                   :style="trackerTextStyle()" x-text="item.nama"></p>

                                <span x-show="item.done"
                                      class="text-[8px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded
                                             bg-emerald-500 text-white flex-shrink-0">
                                    Selesai
                                </span>

                                {{-- TARGET EDITOR --}}
                                <div class="flex items-center gap-0.5 flex-shrink-0 rounded-lg overflow-hidden"
                                     style="background: rgba(0,0,0,0.12);"
                                     :class="item.done ? 'opacity-50' : ''">
                                    <button type="button"
                                            @click="!isDateConfirmed(selectedDate) && !isPastDate(selectedDate) && !item.done && changeTarget(item, -1)"
                                            :disabled="isDateConfirmed(selectedDate) || isPastDate(selectedDate) || item.done"
                                            class="w-6 h-6 flex items-center justify-center hover:bg-black/10
                                                   active:scale-90 disabled:cursor-not-allowed"
                                            :style="trackerTextStyle()">
                                        <svg viewBox="0 0 16 16" class="w-2.5 h-2.5" fill="none"
                                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                            <path d="M4 8h8" />
                                        </svg>
                                    </button>
                                    <input type="number" x-model.number="item.target" @change="sanitizeTarget(item)"
                                           min="0" :disabled="isDateConfirmed(selectedDate) || isPastDate(selectedDate) || item.done"
                                           class="num-input" :style="trackerTextStyle()">
                                    <button type="button"
                                            @click="!isDateConfirmed(selectedDate) && !isPastDate(selectedDate) && !item.done && changeTarget(item, 1)"
                                            :disabled="isDateConfirmed(selectedDate) || isPastDate(selectedDate) || item.done"
                                            class="w-6 h-6 flex items-center justify-center hover:bg-black/10
                                                   active:scale-90 disabled:cursor-not-allowed"
                                            :style="trackerTextStyle()">
                                        <svg viewBox="0 0 16 16" class="w-2.5 h-2.5" fill="none"
                                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                            <path d="M8 4v8M4 8h8" />
                                        </svg>
                                    </button>
                                </div>

                                <span class="text-[9px] font-bold opacity-60 flex-shrink-0 w-8"
                                      :style="trackerTextStyle()" x-text="item.satuan"></span>

                                <button type="button"
                                        @click="!isDateConfirmed(selectedDate) && !isPastDate(selectedDate) && removeFromTracker(item)"
                                        :disabled="isDateConfirmed(selectedDate) || isPastDate(selectedDate)"
                                        title="Hapus dari tracker"
                                        class="w-6 h-6 rounded-md flex items-center justify-center flex-shrink-0
                                               transition active:scale-90 disabled:cursor-not-allowed
                                               hover:bg-red-500 hover:text-white"
                                        :style="`color: ${item.done ? 'rgba(255,255,255,0.5)' : trackerItemAccent()};`">
                                    <svg viewBox="0 0 16 16" class="w-3 h-3" fill="none"
                                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                        <path d="M4 4l8 8M12 4l-8 8"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <template x-if="selectedTodos().length > 0">
                    <div class="flex-shrink-0 mt-4 pt-3 border-t" :style="`border-color: ${trackerItemBg()};`">
                        <template x-if="!isDateConfirmed(selectedDate) && canCompleteDate(selectedDate)">
                            <button type="button" @click="openConfirmModal()" data-auth
                                    class="w-full py-3 rounded-2xl font-bold text-sm transition active:scale-95
                                           flex items-center justify-center gap-2
                                           disabled:opacity-40 disabled:cursor-not-allowed"
                                    style="background: #ffffff; color: #186369;">
                                <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none"
                                     stroke="currentColor" stroke-width="2.5"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 13l4 4L19 7"/>
                                </svg>
                                <span x-text="countDone() > 0
                                    ? 'Konfirmasi ' + countDone() + ' Aktivitas Selesai'
                                    : 'Konfirmasi Aktivitas yang Ada'"></span>
                            </button>
                        </template>

                        <template x-if="isDateConfirmed(selectedDate)">
                            <div class="w-full py-3 rounded-2xl text-sm font-bold text-center flex items-center justify-center gap-2"
                                 :style="`background: ${trackerItemBg()}; color: ${trackerTextColorRaw()};`">
                                <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 13l4 4L19 7"/>
                                </svg>
                                Sudah Dikonfirmasi
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- POPUP SUB-AKTIVITAS                                          --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div x-show="popupOpen" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click.self="closeCategoryPopup()"
         @keydown.escape.window="popupOpen && closeCategoryPopup()"
         class="fixed inset-0 z-[100] bg-[#0a3d42]/40 backdrop-blur-sm
                flex items-center justify-center p-4">

        <div x-show="popupOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="bg-white rounded-3xl shadow-2xl
                    w-full max-w-lg max-h-[85vh]
                    overflow-hidden flex flex-col">

            {{-- Header --}}
            <div class="flex items-center justify-between p-5 pb-4
                        border-b border-gray-100 flex-shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                         :style="activeCategoryData
                             ? `background: ${activeCategoryData.warna}20;`
                             : 'background: #18636920;'">
                        <svg viewBox="0 0 24 24" class="w-5 h-5"
                             :style="activeCategoryData
                                 ? `color: ${activeCategoryData.warna};`
                                 : 'color: #186369;'"
                             fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 11l3 3L22 4"/>
                            <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-[#186369] truncate"
                            x-text="activeCategoryData
                                ? activeCategoryData.nama
                                : 'Semua Aktivitas'"></h3>
                        <p class="text-[10px] text-[#186369]/50 mt-0.5">
                            <span x-text="selectedDateLabel()"></span>
                            ·
                            <span x-text="filteredCategories().reduce((acc,c) => acc + c.jenis.length, 0) + ' aktivitas'"></span>
                        </p>
                    </div>
                </div>

                <button type="button" @click="closeCategoryPopup()"
                        class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200
                               flex items-center justify-center transition active:scale-90 flex-shrink-0">
                    <svg viewBox="0 0 16 16" class="w-3.5 h-3.5 text-[#186369]"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M4 4l8 8M12 4l-8 8"/>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="flex-1 min-h-0 overflow-y-auto scroll-thin p-5 space-y-4">

                <template x-if="isDateConfirmed(selectedDate)">
                    <div class="bg-emerald-50 border-2 border-emerald-200
                                rounded-2xl p-3 flex items-start gap-2.5">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5"
                             fill="none" stroke="currentColor" stroke-width="2.5"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 13l4 4L19 7"/>
                        </svg>
                        <div>
                            <p class="text-[11px] font-bold text-emerald-700">Tanggal ini sudah dikonfirmasi</p>
                            <p class="text-[10px] text-emerald-600/80 mt-0.5 leading-relaxed">
                                Pilih tanggal lain kalau mau mengubah aktivitas.
                            </p>
                        </div>
                    </div>
                </template>

                <div class="space-y-4">
                    <template x-for="cat in filteredCategories()" :key="cat.id">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-3 h-3 rounded-full" :style="`background: ${cat.warna};`"></div>
                                <p class="text-xs font-bold text-[#186369] uppercase tracking-wider"
                                   x-text="cat.nama"></p>
                                <span class="text-[9px] font-bold text-[#186369]/40"
                                      x-text="'(' + countCheckedInCategory(cat.nama) + '/' + cat.jenis.length + ')'"></span>
                            </div>

                            <div class="space-y-2">
                                <template x-for="(sub, si) in cat.jenis" :key="si">
                                    <label class="flex items-center gap-3 bg-[#f5f8f7] rounded-2xl px-4 py-3
                                                  cursor-pointer hover:bg-[#eef3f2] transition active:scale-[0.99]"
                                           :class="isDateConfirmed(selectedDate) ? 'opacity-60 cursor-not-allowed' : ''">
                                        <input type="checkbox"
                                               :checked="isTaskChecked(sub.nama, cat.nama)"
                                               @change="toggleTaskFromTemplate(sub, cat)"
                                               :disabled="isDateConfirmed(selectedDate)"
                                               class="peer sr-only">

                                        <div class="w-6 h-6 rounded-full border-2 border-[#186369]/30
                                                    peer-checked:bg-[#186369] peer-checked:border-[#186369]
                                                    flex items-center justify-center flex-shrink-0 transition">
                                            <svg x-show="isTaskChecked(sub.nama, cat.nama)"
                                                 viewBox="0 0 16 16" class="w-3 h-3 check-bounce"
                                                 fill="none" stroke="white" stroke-width="3"
                                                 stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 8l4 4 7-7" />
                                            </svg>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-[#186369]"
                                               :class="isTaskChecked(sub.nama, cat.nama) ? 'line-through opacity-50' : ''"
                                               x-text="sub.nama"></p>
                                            <p class="text-[11px] font-bold text-[#186369]/40 mt-0.5">
                                                <span x-text="sub.target + ' ' + (sub.satuan || 'x')"></span>
                                                · <span x-text="sub.keterangan"></span>
                                            </p>
                                        </div>

                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold flex-shrink-0"
                                              :style="`background: ${cat.warna}20; color: ${cat.warna};`"
                                              x-text="cat.nama"></span>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </template>

                    <template x-if="filteredCategories().length === 0">
                        <div class="text-center py-12">
                            <p class="text-sm text-[#186369]/60"
                               x-text="search.trim() ? 'Aktivitas atau kategori tidak ditemukan.' : 'Belum ada To Do di kategori ini.'"></p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Footer --}}
            <div class="p-4 border-t border-gray-100 flex-shrink-0
                        flex items-center justify-between gap-3">
                <p class="text-[10px] font-bold text-[#186369]/60">
                    <span x-text="countDone()"></span> selesai ·
                    <span x-text="selectedTodos().length"></span> total
                </p>

                <button type="button" @click="closeCategoryPopup()"
                        class="px-5 py-2.5 rounded-2xl
                               bg-[#186369] text-white text-sm font-bold
                               hover:opacity-90 transition active:scale-95
                               flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none"
                         stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 13l4 4L19 7"/>
                    </svg>
                    Selesai
                </button>
            </div>
        </div>
    </div>


    {{-- MODAL AUTO-GENERATE --}}
    <div x-show="autoGenOpen" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click.self="autoGenOpen = false"
         @keydown.escape.window="autoGenOpen = false"
         class="fixed inset-0 z-[100] bg-[#0a3d42]/40 backdrop-blur-sm flex items-center justify-center p-4">

        <div class="modal-anim bg-white rounded-3xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-hidden flex flex-col">

            <div class="p-5 pb-4 border-b border-gray-100 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#c0a05f] flex items-center justify-center">
                        <svg viewBox="0 0 24 24" class="w-5 h-5 text-white" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-[#186369]">Buat Jadwal Otomatis</h3>
                        <p class="text-[10px] text-[#186369]/50 mt-0.5">Generate jadwal beberapa hari sekaligus</p>
                    </div>
                </div>
            </div>

            <div class="flex-1 min-h-0 overflow-y-auto scroll-thin p-5 space-y-4">

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Mulai Tanggal</label>
                    <input type="date" x-model="autoGen.startDate"
                           class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-[#186369] text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Berapa Hari ke Depan</label>
                    <div class="flex gap-2">
                        <template x-for="d in [7, 14, 30]" :key="d">
                            <button type="button" @click="autoGen.days = d"
                                    class="flex-1 py-2 rounded-xl text-xs font-bold transition"
                                    :class="autoGen.days === d ? 'bg-[#186369] text-white' : 'bg-gray-100 text-[#186369]'">
                                <span x-text="d"></span> Hari
                            </button>
                        </template>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">
                        Aktivitas per Hari: <span class="text-[#186369]" x-text="autoGen.perDay"></span>
                    </label>
                    <input type="range" min="1" max="8" x-model.number="autoGen.perDay" class="w-full accent-[#186369]">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-gray-500">Kategori yang Diikutkan</label>
                        <button type="button" @click="toggleAllAutoGen()"
                                class="text-[10px] font-bold text-[#186369] hover:underline">
                            <span x-text="autoGen.categories.length === categories.length ? 'Hapus Semua' : 'Pilih Semua'"></span>
                        </button>
                    </div>
                    <div class="space-y-1 max-h-48 overflow-y-auto scroll-thin">
                        <template x-for="cat in categories" :key="cat.id">
                            <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-[#f5f8f7] cursor-pointer">
                                <input type="checkbox" :checked="autoGen.categories.includes(cat.nama)"
                                       @change="toggleAutoGenCategory(cat.nama)"
                                       class="peer sr-only">
                                <div class="w-5 h-5 rounded-md border-2 border-[#186369]/30
                                            peer-checked:bg-[#186369] peer-checked:border-[#186369]
                                            flex items-center justify-center flex-shrink-0">
                                    <svg x-show="autoGen.categories.includes(cat.nama)"
                                         viewBox="0 0 16 16" class="w-3 h-3" fill="none"
                                         stroke="white" stroke-width="3" stroke-linecap="round">
                                        <path d="M3 8l4 4 7-7"/>
                                    </svg>
                                </div>
                                <div class="w-3 h-3 rounded-full flex-shrink-0" :style="`background: ${cat.warna};`"></div>
                                <span class="text-xs font-bold text-[#186369]" x-text="cat.nama"></span>
                                <span class="text-[9px] text-[#186369]/40 ml-auto"
                                      x-text="cat.jenis.length + ' aktivitas'"></span>
                            </label>
                        </template>
                    </div>
                </div>
            </div>

            <div class="p-4 border-t border-gray-100 flex gap-2 flex-shrink-0">
                <button type="button" @click="autoGenOpen = false"
                        class="flex-1 py-2.5 rounded-2xl bg-gray-100 text-[#186369] text-sm font-bold">Batal</button>
                <button type="button" @click="executeAutoGenerate()"
                        :disabled="autoGen.categories.length === 0"
                        class="flex-1 py-2.5 rounded-2xl bg-[#c0a05f] text-white text-sm font-bold
                               disabled:opacity-40 disabled:cursor-not-allowed">
                    Generate
                </button>
            </div>
        </div>
    </div>


    {{-- MODAL REKOMENDASI PROGRAM --}}
    <div x-show="rekomendasiOpen" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click.self="closeRekomendasi()"
         @keydown.escape.window="closeRekomendasi()"
         class="fixed inset-0 z-[100] bg-[#0a3d42]/40 backdrop-blur-sm flex items-center justify-center p-4">

        <div class="modal-anim bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col">

            <div class="p-5 pb-4 border-b border-gray-100 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                            <span class="text-lg">⚡</span>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-[#186369]">Rekomendasi Program</h3>
                            <p class="text-[10px] text-[#186369]/50 mt-0.5">Pilih program yang cocok untukmu</p>
                        </div>
                    </div>
                    <button type="button" @click="closeRekomendasi()"
                            class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center active:scale-90">
                        <svg viewBox="0 0 16 16" class="w-3.5 h-3.5 text-[#186369]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M4 4l8 8M12 4l-8 8" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex-1 min-h-0 overflow-y-auto scroll-thin p-5 space-y-4">

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <template x-for="tpl in templates" :key="tpl.id">
                        <button type="button" @click="pilihTemplate(tpl.id)"
                                class="rounded-2xl p-3 text-left transition-all border-2 active:scale-[0.98]"
                                :style="`background: ${selectedTemplate === tpl.id ? tpl.warna + '15' : '#f9fafb'};
                                         border-color: ${selectedTemplate === tpl.id ? tpl.warna : 'transparent'};`">
                            <div class="text-2xl mb-1" x-text="tpl.emoji"></div>
                            <p class="text-xs font-bold text-[#186369] leading-tight" x-text="tpl.nama"></p>
                            <p class="text-[9px] text-[#186369]/50 mt-1"
                               x-text="tpl.aktivitas.length + ' aktivitas'"></p>
                        </button>
                    </template>
                </div>

                <template x-if="selectedTemplate">
                    <div class="border-t border-gray-100 pt-4 space-y-4">
                        <template x-for="tpl in [templates.find(t => t.id === selectedTemplate)]" :key="tpl.id">
                            <div>
                                <div class="flex items-start gap-3 mb-4">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0"
                                         :style="`background: ${tpl.warna}15;`"
                                         x-text="tpl.emoji"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-[#186369]" x-text="tpl.nama"></p>
                                        <p class="text-[10px] text-[#186369]/60 mt-0.5 leading-relaxed"
                                           x-text="tpl.deskripsi"></p>
                                    </div>
                                </div>

                                <div class="bg-[#f5f8f7] rounded-2xl p-4 space-y-1.5 mb-4">
                                    <p class="text-[10px] font-bold text-[#186369]/50 uppercase tracking-wider mb-2">
                                        Aktivitas Harian
                                    </p>
                                    <template x-for="(akt, ai) in tpl.aktivitas" :key="ai">
                                        <div class="flex items-center gap-2">
                                            <div class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                                 :style="`background: ${tpl.warna};`"></div>
                                            <p class="flex-1 text-[11px] font-bold text-[#186369] truncate"
                                               x-text="akt.nama"></p>
                                            <span class="text-[9px] font-bold text-[#186369]/40 flex-shrink-0">
                                                <span x-text="akt.target"></span>
                                                <span x-text="akt.satuan"></span>
                                            </span>
                                        </div>
                                    </template>
                                </div>

                                <div>
                                    <p class="text-[10px] font-bold text-[#186369]/50 uppercase tracking-wider mb-2">
                                        Terapkan untuk berapa hari?
                                    </p>
                                    <div class="grid grid-cols-4 gap-2">
                                        <template x-for="d in [1, 3, 7, 30]" :key="d">
                                            <button type="button" @click="applyDays = d"
                                                    class="py-2 rounded-xl text-xs font-bold transition"
                                                    :class="applyDays === d
                                                        ? 'bg-[#186369] text-white shadow-md'
                                                        : 'bg-gray-100 text-[#186369] hover:bg-gray-200'">
                                                <span x-text="d"></span> Hari
                                            </button>
                                        </template>
                                    </div>
                                    <p class="text-[9px] text-[#186369]/40 mt-2 leading-relaxed">
                                        💡 Dimulai dari <span class="font-bold" x-text="selectedDateLabel()"></span>.
                                        Aktivitas yang sudah ada tidak akan ditimpa.
                                    </p>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="!selectedTemplate">
                    <div class="bg-blue-50 rounded-2xl p-4 flex items-start gap-3 border border-blue-100">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/>
                        </svg>
                        <p class="text-[10px] font-bold text-blue-600 leading-relaxed">
                            Klik salah satu program di atas untuk lihat detailnya. Kamu bisa pilih durasi & langsung terapkan.
                        </p>
                    </div>
                </template>
            </div>

            <div class="p-4 border-t border-gray-100 flex gap-2 flex-shrink-0">
                <button type="button" @click="closeRekomendasi()"
                        class="flex-1 py-2.5 rounded-2xl bg-gray-100 text-[#186369] text-sm font-bold">
                    Batal
                </button>
                <button type="button" @click="applyTemplate()"
                        :disabled="!selectedTemplate"
                        class="flex-1 py-2.5 rounded-2xl bg-[#186369] text-white text-sm font-bold
                               disabled:opacity-40 disabled:cursor-not-allowed
                               flex items-center justify-center gap-2">
                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 13l4 4L19 7"/>
                    </svg>
                    Terapkan (<span x-text="applyDays"></span> hari)
                </button>
            </div>
        </div>
    </div>


    {{-- MODAL KONFIRMASI --}}
    <div x-show="confirmModalOpen" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click.self="cancelConfirm()" @keydown.escape.window="cancelConfirm()"
         class="fixed inset-0 z-[100] bg-[#0a3d42]/40 backdrop-blur-sm flex items-center justify-center p-4">

        <div class="modal-anim bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 max-h-[90vh] overflow-hidden flex flex-col">

            <div class="flex items-center justify-center mb-4 flex-shrink-0">
                <div class="w-14 h-14 rounded-full bg-emerald-50 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>

            <h3 class="text-lg font-bold text-[#186369] text-center mb-1 flex-shrink-0">
                Konfirmasi Aktivitas?
            </h3>
            <p class="text-xs text-[#186369]/60 text-center mb-4 flex-shrink-0"
               x-text="selectedDateLabel()"></p>

            <div class="flex-1 min-h-0 overflow-y-auto scroll-thin space-y-1.5 mb-4">
                <template x-for="(item, i) in selectedTodos()" :key="'c-' + item.id">
                    <div class="flex items-center gap-2 px-3 py-2 rounded-xl"
                         :class="item.done ? 'bg-emerald-50' : 'bg-red-50'">
                        <div class="w-4 h-4 rounded-full flex-shrink-0 flex items-center justify-center"
                             :class="item.done ? 'bg-emerald-500' : 'bg-red-500'">
                            <svg viewBox="0 0 16 16" class="w-2.5 h-2.5" fill="none" stroke="white" stroke-width="3" stroke-linecap="round">
                                <path d="M3 8l4 4 7-7"/>
                            </svg>
                        </div>
                        <p class="flex-1 text-[11px] font-bold truncate"
                           :class="item.done ? 'text-emerald-700' : 'text-red-700'"
                           x-text="item.nama"></p>
                        <span class="text-[9px] font-bold flex-shrink-0"
                            :class="item.done ? 'text-emerald-600' : 'text-red-600'"
                            x-text="item.done ? 'Selesai' : 'Gagal'"></span>
                        <span class="text-[9px] font-bold flex-shrink-0"
                            :class="item.done ? 'text-emerald-600' : 'text-red-600'">
                            <span x-text="item.target"></span>
                            <span x-text="item.satuan"></span>
                        </span>
                    </div>
                </template>
            </div>

            <div class="flex-shrink-0 bg-[#f5f8f7] rounded-2xl p-4 mb-4 space-y-1.5">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold text-[#186369]">Selesai</p>
                    <p class="text-sm font-bold text-emerald-600"
                       x-text="countDone() + ' item'"></p>
                </div>
                <div class="flex items-center justify-between"
                     x-show="selectedTodos().length > countDone()">
                          <p class="text-xs font-bold text-red-500">Gagal</p>
                          <p class="text-sm font-bold text-red-500"
                              x-text="(selectedTodos().length - countDone()) + ' item'"></p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 flex-shrink-0">
                <button type="button" @click="cancelConfirm()"
                        class="py-2.5 rounded-2xl bg-gray-100 text-[#186369] text-sm font-bold hover:bg-gray-200 transition active:scale-95">
                    Cek Lagi
                </button>
                <button type="button" @click="submitConfirm()"
                        class="py-2.5 rounded-2xl bg-[#186369] text-white text-sm font-bold hover:opacity-90 transition active:scale-95
                               flex items-center justify-center gap-1.5">
                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 13l4 4L19 7"/>
                    </svg>
                    Ya, Konfirmasi
                </button>
            </div>
        </div>
    </div>


    {{-- PANEL RIWAYAT --}}
    <div x-show="hasilOpen"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 scale-50 -translate-y-12"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-50 -translate-y-12"
         @click.away="hasilOpen = false"
         x-cloak
         class="fixed z-40 top-[80px] right-4 sm:right-6 lg:right-8
                w-[360px] max-w-[calc(100vw-2rem)] max-h-[calc(100vh-100px)]
                rounded-3xl bg-white border border-gray-100 flex flex-col overflow-hidden"
         style="box-shadow: 0 20px 60px rgba(24,99,105,0.15);">

        <div class="flex-1 min-h-0 overflow-y-auto scroll-thin p-6 space-y-5">

            <button type="button" @click="hasilOpen = false"
                    class="absolute top-4 right-4 w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center active:scale-90 z-10">
                <svg viewBox="0 0 16 16" class="w-3.5 h-3.5 text-[#186369]">
                    <path d="M4 4l8 8M12 4l-8 8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
            </button>

            <div>
                <h3 class="text-base font-bold text-[#186369] mb-3 flex items-center gap-2">📅 Riwayat Aktivitas</h3>

                <template x-if="history.length === 0">
                    <div class="bg-[#f5f8f7] rounded-2xl p-6 text-center">
                        <p class="text-[11px] text-[#186369]/50">Belum ada riwayat.</p>
                    </div>
                </template>

                <div class="space-y-3">
                    <template x-for="(h, hi) in history" :key="hi">
                        <div class="bg-[#f5f8f7] rounded-2xl p-4">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs font-bold text-[#186369]" x-text="h.tanggalLabel"></p>
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-[#186369]/10 text-[#186369]"
                                      x-text="h.doneCount + ' item'"></span>
                            </div>

                            <div class="space-y-1.5">
                                <template x-for="(item, ii) in h.items" :key="ii">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full flex-shrink-0"
                                             :class="(item.status || 'done') === 'failed' ? 'bg-red-500' : 'bg-emerald-500'"></div>
                                        <p class="flex-1 text-[10px] font-bold truncate"
                                           :class="(item.status || 'done') === 'failed' ? 'text-red-700' : 'text-[#186369]'"
                                           x-text="item.nama"></p>
                                        <span class="text-[9px] font-bold flex-shrink-0"
                                              :class="(item.status || 'done') === 'failed' ? 'text-red-600' : 'text-emerald-600'"
                                              x-text="(item.status || 'done') === 'failed' ? 'Gagal' : 'Selesai'"></span>
                                        <span class="text-[9px] font-bold flex-shrink-0"
                                              :class="(item.status || 'done') === 'failed' ? 'text-red-600' : 'text-emerald-600'">
                                            <span x-text="item.target"></span>
                                            <span x-text="item.satuan"></span>
                                        </span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="bg-[#f5f8f7] rounded-3xl p-5">
                <p class="text-sm font-bold text-[#186369] leading-relaxed">
                    Perjalanan mu masih panjang tuan, teruslah berjuang.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection


@push('scripts')
<script>
function jadwalApp() {
    return {
        hasilOpen: false,
        search: '',
        todayDate: null, currentMonth: 0, year: 2026,
        selectedDate: null,
        monthNames: ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'],
        activeCategory: null,
        sweepActive: false, sweepColor: '#186369',
        categories: [],
        schedules: {},
        confirmedDates: [],
        history: [],
        confirmModalOpen: false,
        autoGenOpen: false,
        autoGen: { startDate: '', days: 7, perDay: 3, categories: [] },
        rekomendasiOpen: false,
        selectedTemplate: null,
        applyDays: 3,
        templates: [],

        /* ═══ POPUP SUB-AKTIVITAS ═══ */
        popupOpen: false,

        /* ═══════════════════════════════════════════════
           DEFAULT TEMPLATE PROGRAM REKOMENDASI
           ═══════════════════════════════════════════════ */
        defaultTemplates: [
            {
                id: 'belajar', nama: 'Belajar Produktif', emoji: '📚', warna: '#f59e0b',
                deskripsi: 'Program untuk pelajar & mahasiswa yang ingin fokus belajar tanpa lelah',
                aktivitas: [
                    { nama: 'Baca Buku',      kategori: 'Belajar',       tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'novel' },
                    { nama: 'Coding',         kategori: 'Belajar',       tipe: 'duration', target: 60, satuan: 'menit', keterangan: 'project' },
                    { nama: 'Bahasa Inggris', kategori: 'Belajar',       tipe: 'duration', target: 20, satuan: 'menit', keterangan: 'vocab' },
                    { nama: 'Deep Work',      kategori: 'Produktivitas', tipe: 'duration', target: 90, satuan: 'menit', keterangan: 'fokus' },
                    { nama: 'Rencana Harian', kategori: 'Produktivitas', tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'pagi' },
                    { nama: 'Bangun Awal',    kategori: 'Tidur',         tipe: 'count',    target: 1,  satuan: 'x',     keterangan: '05:00' },
                    { nama: 'Mindfulness',    kategori: 'Mental',        tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'sadar' },
                ],
            },
            {
                id: 'atlit', nama: 'Program Atlit', emoji: '🏋️', warna: '#c0a05f',
                deskripsi: 'Rutinitas untuk kamu yang ingin aktif seperti atlit — banyak gerak & nutrisi',
                aktivitas: [
                    { nama: 'Joging',        kategori: 'Sport',  tipe: 'duration', target: 45, satuan: 'menit', keterangan: 'pagi' },
                    { nama: 'Push Up',       kategori: 'Sport',  tipe: 'count',    target: 30, satuan: 'x',     keterangan: 'set 1' },
                    { nama: 'Sit Up',        kategori: 'Sport',  tipe: 'count',    target: 40, satuan: 'x',     keterangan: 'pagi' },
                    { nama: 'Stretching',    kategori: 'Sport',  tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'pemanasan' },
                    { nama: 'Menu Sehat',    kategori: 'Diet',   tipe: 'count',    target: 3,  satuan: 'x',     keterangan: 'harian' },
                    { nama: 'Minum Air',     kategori: 'Diet',   tipe: 'count',    target: 10, satuan: 'gelas', keterangan: 'hidrasi' },
                    { nama: 'Vitamin',       kategori: 'Diet',   tipe: 'count',    target: 1,  satuan: 'x',     keterangan: 'suplemen' },
                    { nama: 'Tidur Malam',   kategori: 'Tidur',  tipe: 'duration', target: 8,  satuan: 'jam',   keterangan: 'cukup' },
                    { nama: 'Meditasi Pagi', kategori: 'Mental', tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'tenang' },
                ],
            },
            {
                id: 'seimbang', nama: 'Sehat Seimbang', emoji: '⚖️', warna: '#186369',
                deskripsi: 'Program seimbang untuk tubuh & pikiran agar tetap bugar setiap hari',
                aktivitas: [
                    { nama: 'Joging',        kategori: 'Sport',  tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'pagi' },
                    { nama: 'Hatha Yoga',    kategori: 'Yoga',   tipe: 'duration', target: 20, satuan: 'menit', keterangan: 'pagi' },
                    { nama: 'Meditasi',      kategori: 'Yoga',   tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'malam' },
                    { nama: 'Menu Sehat',    kategori: 'Diet',   tipe: 'count',    target: 3,  satuan: 'x',     keterangan: 'harian' },
                    { nama: 'Minum Air',     kategori: 'Diet',   tipe: 'count',    target: 8,  satuan: 'gelas', keterangan: 'hidrasi' },
                    { nama: 'Konsumsi Buah', kategori: 'Diet',   tipe: 'count',    target: 2,  satuan: 'x',     keterangan: 'vitamin' },
                    { nama: 'Journaling',    kategori: 'Mental', tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'refleksi' },
                    { nama: 'Tidur Malam',   kategori: 'Tidur',  tipe: 'duration', target: 7,  satuan: 'jam',   keterangan: 'cukup' },
                ],
            },
            {
                id: 'spiritual', nama: 'Konsisten Spiritual', emoji: '🙏', warna: '#a855f7',
                deskripsi: 'Program untuk ketenangan batin, doa, dan refleksi harian',
                aktivitas: [
                    { nama: 'Doa Pagi',      kategori: 'Spiritual', tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'syukur' },
                    { nama: 'Doa Malam',     kategori: 'Spiritual', tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'introspeksi' },
                    { nama: 'Baca Kitab',    kategori: 'Spiritual', tipe: 'duration', target: 20, satuan: 'menit', keterangan: 'renungan' },
                    { nama: 'Refleksi Diri', kategori: 'Spiritual', tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'malam' },
                    { nama: 'Meditasi',      kategori: 'Yoga',      tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'tenang' },
                    { nama: 'Journaling',    kategori: 'Mental',    tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'perasaan' },
                ],
            },
            {
                id: 'pekerja', nama: 'Produktivitas Pekerja', emoji: '💼', warna: '#4090f2',
                deskripsi: 'Program untuk pekerja yang ingin tetap produktif & sehat',
                aktivitas: [
                    { nama: 'Rencana Harian',  kategori: 'Produktivitas', tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'pagi' },
                    { nama: 'Deep Work',       kategori: 'Produktivitas', tipe: 'duration', target: 90, satuan: 'menit', keterangan: 'fokus' },
                    { nama: 'Balas Email',     kategori: 'Produktivitas', tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'inbox' },
                    { nama: 'Evaluasi Harian', kategori: 'Produktivitas', tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'malam' },
                    { nama: 'Belajar Skill',   kategori: 'Produktivitas', tipe: 'duration', target: 45, satuan: 'menit', keterangan: 'baru' },
                    { nama: 'Minum Air',       kategori: 'Diet',          tipe: 'count',    target: 8,  satuan: 'gelas', keterangan: 'hidrasi' },
                    { nama: 'Meditasi Pagi',   kategori: 'Mental',        tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'tenang' },
                ],
            },
            {
                id: 'chill', nama: 'Santoruy', emoji: '😎', warna: '#14b8a6',
                deskripsi: 'Program santai buat recharge energi, gak harus produktif terus',
                aktivitas: [
                    { nama: 'Baca Komik',  kategori: 'Hobi',   tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'manga' },
                    { nama: 'Main Game',   kategori: 'Hobi',   tipe: 'duration', target: 60, satuan: 'menit', keterangan: 'santai' },
                    { nama: 'Mindfulness', kategori: 'Mental', tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'sadar' },
                    { nama: 'Me Time',     kategori: 'Mental', tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'santai' },
                    { nama: 'Chat Teman',  kategori: 'Sosial', tipe: 'count',    target: 3,  satuan: 'x',     keterangan: 'dekat' },
                ],
            },
        ],

        init() {
            const now = new Date();
            this.todayDate = now;
            this.currentMonth = now.getMonth();
            this.year = now.getFullYear();
            this.selectedDate = this.dateKey(now);

            this.loadCategories();
            this.loadTemplates();
            this.loadSchedules();
            this.loadConfirmed();
            this.loadHistory();

            const focus = new URLSearchParams(window.location.search).get('date');
            if (focus && /^\d{4}-\d{2}-\d{2}$/.test(focus)) {
                this.selectedDate = focus;
                const [y, m] = focus.split('-').map(Number);
                this.currentMonth = m - 1;
                this.year = y;
            }
        },

        loadTemplates() {
            this.templates = window.serverRecommendationTemplates || [];
        },

        loadCategories() {
            this.categories = window.serverCategories || [];
            return;

            const dummy = [
                {
                    id: 1, nama: 'Sport', warna: '#c0a05f', teksWarna: '#186369',
                    jenis: [
                        { nama: 'Joging',       tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'pagi' },
                        { nama: 'Push Up',      tipe: 'count',    target: 20, satuan: 'x',     keterangan: 'set 1' },
                        { nama: 'Sit Up',       tipe: 'count',    target: 30, satuan: 'x',     keterangan: 'pagi' },
                        { nama: 'Squat',        tipe: 'count',    target: 25, satuan: 'x',     keterangan: 'kaki' },
                        { nama: 'Plank',        tipe: 'duration', target: 2,  satuan: 'menit', keterangan: 'core' },
                        { nama: 'Badminton',    tipe: 'duration', target: 60, satuan: 'menit', keterangan: 'sore' },
                        { nama: 'Berenang',     tipe: 'duration', target: 45, satuan: 'menit', keterangan: 'kolam' },
                        { nama: 'Bersepeda',    tipe: 'duration', target: 40, satuan: 'menit', keterangan: 'sore' },
                        { nama: 'Stretching',   tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'pemanasan' },
                        { nama: 'Lompat Tali',  tipe: 'count',    target: 100, satuan: 'x',    keterangan: 'cardio' },
                    ],
                },
                {
                    id: 2, nama: 'Diet', warna: '#186369', teksWarna: '#ffffff',
                    jenis: [
                        { nama: 'Sarapan',        tipe: 'count', target: 1, satuan: 'x',     keterangan: 'oatmeal' },
                        { nama: 'Menu Sehat',     tipe: 'count', target: 3, satuan: 'x',     keterangan: 'harian' },
                        { nama: 'Snack Sehat',    tipe: 'count', target: 2, satuan: 'x',     keterangan: 'almond' },
                        { nama: 'Minum Air',      tipe: 'count', target: 8, satuan: 'gelas', keterangan: 'hidrasi' },
                        { nama: 'Hindari Gula',   tipe: 'count', target: 1, satuan: 'x',     keterangan: 'harian' },
                        { nama: 'Konsumsi Buah',  tipe: 'count', target: 2, satuan: 'x',     keterangan: 'vitamin' },
                        { nama: 'Konsumsi Sayur', tipe: 'count', target: 3, satuan: 'x',     keterangan: 'serat' },
                        { nama: 'Makan Malam',    tipe: 'count', target: 1, satuan: 'x',     keterangan: 'ringan' },
                        { nama: 'Vitamin',        tipe: 'count', target: 1, satuan: 'x',     keterangan: 'suplemen' },
                    ],
                },
                {
                    id: 3, nama: 'Yoga', warna: '#9fd6f8', teksWarna: '#186369',
                    jenis: [
                        { nama: 'Hatha Yoga',   tipe: 'duration', target: 20, satuan: 'menit', keterangan: 'pagi' },
                        { nama: 'Vinyasa Flow', tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'sore' },
                        { nama: 'Yin Yoga',     tipe: 'duration', target: 25, satuan: 'menit', keterangan: 'malam' },
                        { nama: 'Meditasi',     tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'malam' },
                        { nama: 'Pranayama',    tipe: 'duration', target: 5,  satuan: 'menit', keterangan: 'napas' },
                        { nama: 'Savasana',     tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'relaksasi' },
                        { nama: 'Mindfulness',  tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'sadar' },
                        { nama: 'Body Scan',    tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'relaksasi' },
                    ],
                },
                {
                    id: 4, nama: 'Musik', warna: '#8b7bd8', teksWarna: '#ffffff',
                    jenis: [
                        { nama: 'Gitar',         tipe: 'duration', target: 20, satuan: 'menit', keterangan: 'chord' },
                        { nama: 'Piano',         tipe: 'duration', target: 20, satuan: 'menit', keterangan: 'latihan' },
                        { nama: 'Vokal',         tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'pemanasan' },
                        { nama: 'Drum',          tipe: 'duration', target: 20, satuan: 'menit', keterangan: 'rudiment' },
                        { nama: 'Biola',         tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'bow' },
                        { nama: 'Teori Musik',   tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'baca not' },
                        { nama: 'Nyanyi Santai', tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'fun' },
                    ],
                },
                {
                    id: 5, nama: 'Belajar', warna: '#f59e0b', teksWarna: '#ffffff',
                    jenis: [
                        { nama: 'Baca Buku',      tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'novel' },
                        { nama: 'Coding',         tipe: 'duration', target: 60, satuan: 'menit', keterangan: 'project' },
                        { nama: 'Bahasa Inggris', tipe: 'duration', target: 20, satuan: 'menit', keterangan: 'vocab' },
                        { nama: 'Bahasa Jepang',  tipe: 'duration', target: 20, satuan: 'menit', keterangan: 'hiragana' },
                        { nama: 'Journaling',     tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'refleksi' },
                        { nama: 'Matematika',     tipe: 'duration', target: 45, satuan: 'menit', keterangan: 'latihan' },
                        { nama: 'Sejarah',        tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'baca' },
                        { nama: 'Fisika',         tipe: 'duration', target: 45, satuan: 'menit', keterangan: 'soal' },
                        { nama: 'Desain',         tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'latihan' },
                        { nama: 'Menulis',        tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'artikel' },
                    ],
                },
                {
                    id: 6, nama: 'Produktivitas', warna: '#4090f2', teksWarna: '#ffffff',
                    jenis: [
                        { nama: 'Deep Work',       tipe: 'duration', target: 90, satuan: 'menit', keterangan: 'fokus' },
                        { nama: 'Review Tugas',    tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'pagi' },
                        { nama: 'Balas Email',     tipe: 'duration', target: 20, satuan: 'menit', keterangan: 'inbox' },
                        { nama: 'Meeting',         tipe: 'duration', target: 60, satuan: 'menit', keterangan: 'tim' },
                        { nama: 'Rencana Harian',  tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'pagi' },
                        { nama: 'Evaluasi Harian', tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'malam' },
                        { nama: 'Belajar Skill',   tipe: 'duration', target: 45, satuan: 'menit', keterangan: 'baru' },
                        { nama: 'Declutter',       tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'meja' },
                    ],
                },
                {
                    id: 7, nama: 'Mental', warna: '#e879a4', teksWarna: '#ffffff',
                    jenis: [
                        { nama: 'Meditasi Pagi',    tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'tenang' },
                        { nama: 'Gratitude',        tipe: 'count',    target: 3,  satuan: 'x',     keterangan: 'syukur' },
                        { nama: 'Journaling',       tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'perasaan' },
                        { nama: 'Digital Detox',    tipe: 'duration', target: 60, satuan: 'menit', keterangan: 'no phone' },
                        { nama: 'Deep Breathing',   tipe: 'duration', target: 5,  satuan: 'menit', keterangan: 'rileks' },
                        { nama: 'Self-talk Positif',tipe: 'count',    target: 5,  satuan: 'x',     keterangan: 'afirmasi' },
                        { nama: 'Mindfulness',      tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'sadar' },
                        { nama: 'Me Time',          tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'santai' },
                    ],
                },
                {
                    id: 8, nama: 'Tidur', warna: '#6b7280', teksWarna: '#ffffff',
                    jenis: [
                        { nama: 'Tidur Malam',      tipe: 'duration', target: 7,  satuan: 'jam',   keterangan: 'cukup' },
                        { nama: 'Power Nap',        tipe: 'duration', target: 20, satuan: 'menit', keterangan: 'siang' },
                        { nama: 'No Screen',        tipe: 'duration', target: 60, satuan: 'menit', keterangan: 'sebelum tidur' },
                        { nama: 'Kurangi Kafein',   tipe: 'count',    target: 1,  satuan: 'x',     keterangan: 'sore' },
                        { nama: 'Tidur Tepat Waktu',tipe: 'count',    target: 1,  satuan: 'x',     keterangan: '22:00' },
                        { nama: 'Bangun Awal',      tipe: 'count',    target: 1,  satuan: 'x',     keterangan: '05:00' },
                    ],
                },
                {
                    id: 9, nama: 'Hobi', warna: '#14b8a6', teksWarna: '#ffffff',
                    jenis: [
                        { nama: 'Melukis',        tipe: 'duration', target: 45, satuan: 'menit', keterangan: 'akrilik' },
                        { nama: 'Fotografi',      tipe: 'duration', target: 60, satuan: 'menit', keterangan: 'street' },
                        { nama: 'Berkebun',       tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'tanaman' },
                        { nama: 'Masak',          tipe: 'duration', target: 60, satuan: 'menit', keterangan: 'resep baru' },
                        { nama: 'Baca Komik',     tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'manga' },
                        { nama: 'Main Game',      tipe: 'duration', target: 60, satuan: 'menit', keterangan: 'santai' },
                        { nama: 'Origami',        tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'lipat' },
                        { nama: 'Menulis Cerita', tipe: 'duration', target: 45, satuan: 'menit', keterangan: 'fiksi' },
                    ],
                },
                {
                    id: 10, nama: 'Sosial', warna: '#f97316', teksWarna: '#ffffff',
                    jenis: [
                        { nama: 'Chat Keluarga',  tipe: 'count',    target: 1,   satuan: 'x',     keterangan: 'harian' },
                        { nama: 'Chat Teman',     tipe: 'count',    target: 3,   satuan: 'x',     keterangan: 'dekat' },
                        { nama: 'Video Call',     tipe: 'duration', target: 30,  satuan: 'menit', keterangan: 'keluarga' },
                        { nama: 'Kunjungi Teman', tipe: 'duration', target: 120, satuan: 'menit', keterangan: 'offline' },
                        { nama: 'Nonton Bareng',  tipe: 'duration', target: 90,  satuan: 'menit', keterangan: 'film' },
                        { nama: 'Kencan',         tipe: 'duration', target: 120, satuan: 'menit', keterangan: 'romantis' },
                        { nama: 'Community',      tipe: 'duration', target: 60,  satuan: 'menit', keterangan: 'grup' },
                    ],
                },
                {
                    id: 11, nama: 'Keuangan', warna: '#22c55e', teksWarna: '#ffffff',
                    jenis: [
                        { nama: 'Catat Pengeluaran', tipe: 'count',    target: 1,  satuan: 'x',     keterangan: 'harian' },
                        { nama: 'Cek Saldo',         tipe: 'count',    target: 1,  satuan: 'x',     keterangan: 'pagi' },
                        { nama: 'Review Budget',     tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'mingguan' },
                        { nama: 'Belajar Investasi', tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'saham' },
                        { nama: 'Nabung',            tipe: 'count',    target: 1,  satuan: 'x',     keterangan: 'harian' },
                        { nama: 'Bayar Tagihan',     tipe: 'count',    target: 1,  satuan: 'x',     keterangan: 'bulanan' },
                    ],
                },
                {
                    id: 12, nama: 'Spiritual', warna: '#a855f7', teksWarna: '#ffffff',
                    jenis: [
                        { nama: 'Doa Pagi',      tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'syukur' },
                        { nama: 'Doa Malam',     tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'introspeksi' },
                        { nama: 'Baca Kitab',    tipe: 'duration', target: 20, satuan: 'menit', keterangan: 'renungan' },
                        { nama: 'Refleksi Diri', tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'malam' },
                        { nama: 'Sedekah',       tipe: 'count',    target: 1,  satuan: 'x',     keterangan: 'kebaikan' },
                    ],
                },
            ];

            let loaded = [];
            const s = null;
            if (s) { try { loaded = JSON.parse(s); } catch(e) {} }
            if (!Array.isArray(loaded) || !loaded.length) loaded = dummy;

            loaded = loaded.map(cat => ({
                ...cat,
                jenis: (cat.jenis || []).map(sub => {
                    if (typeof sub === 'string') {
                        return { nama: sub, tipe: 'count', target: 10, satuan: 'x', keterangan: '' };
                    }
                    return sub;
                }),
            }));

            this.categories = loaded;
        },

        loadSchedules() {
            this.schedules = window.serverSchedules || {};

            Object.entries(this.schedules).forEach(([dateKey, items]) => {
                if (!Array.isArray(items)) return;
                this.schedules[dateKey] = items.map(item => this.normalizeScheduleItem(item));
            });
        },
        normalizeScheduleItem(item) {
            const pickedCategory = this.categories.find(cat => String(cat.id) === String(item.category_id))
                || this.categories.find(cat => cat.nama === item.kategori)
                || null;

            const normalized = { ...item };
            normalized.kategori = normalized.kategori
                || normalized.category_name
                || (pickedCategory ? pickedCategory.nama : 'Lainnya');
            normalized.warna = normalized.warna || (pickedCategory ? pickedCategory.warna : '#186369');
            normalized.teksWarna = normalized.teksWarna || (pickedCategory ? pickedCategory.teksWarna : '#ffffff');

            if (!normalized.category_id && pickedCategory) {
                normalized.category_id = pickedCategory.id;
            }

            return normalized;
        },
        saveSchedules(dateKeys = null) {
            const todayKey = this.dateKey(this.todayDate);
            const requestedKeys = dateKeys || (this.selectedDate ? [this.selectedDate] : []);
            const keys = requestedKeys.filter(dateKey =>
                dateKey >= todayKey && !this.isDateConfirmed(dateKey)
            );
            if (!keys.length) return Promise.resolve();

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const requests = keys.map(dateKey => {
                const items = (this.schedules[dateKey] || []).map(item => ({
                    name: item.nama,
                    type: item.tipe || 'count',
                    target: Number(item.target) || 0,
                    unit: item.satuan || 'x',
                    description: item.keterangan || null,
                    category_id: item.category_id || null,
                    category_item_id: item.category_item_id || null,
                    done: item.done === true,
                }));

                return fetch('/jadwal', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ scheduled_date: dateKey, items }),
                }).then(async response => {
                    if (response.ok) return;

                    const body = await response.text();
                    throw new Error(body || 'Gagal menyimpan jadwal.');
                });
            });

            return Promise.all(requests).catch(error => {
                alert(error.message || 'Jadwal belum berhasil disimpan.');
                throw error;
            });
        },
        loadConfirmed() {
            this.confirmedDates = window.serverConfirmedDates || [];
        },
        saveConfirmed() {
            return true;
        },
        loadHistory() {
            this.history = window.serverHistory || [];
        },

        dateKey(d) {
            return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
        },
        monthName() { return this.monthNames[this.currentMonth]; },
        todayString() {
            if (!this.todayDate) return '';
            const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            return `${days[this.todayDate.getDay()]}, ${this.todayDate.getDate()} ${this.monthName()} ${this.year}`;
        },
        selectedDateLabel() {
            if (!this.selectedDate) return 'Pilih Tanggal';
            const [y, m, d] = this.selectedDate.split('-').map(Number);
            const date = new Date(y, m-1, d);
            const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            const isToday = this.selectedDate === this.dateKey(this.todayDate);
            return `${isToday ? 'Hari Ini · ' : ''}${days[date.getDay()]}, ${d} ${this.monthNames[m-1]}`;
        },
        calendarDays() {
            const firstDay = new Date(this.year, this.currentMonth, 1).getDay();
            const daysInMonth = new Date(this.year, this.currentMonth + 1, 0).getDate();
            const days = [];
            for (let i = 0; i < firstDay; i++) days.push('');
            for (let d = 1; d <= daysInMonth; d++) days.push(d);
            while (days.length % 7 !== 0) days.push('');
            return days;
        },
        isToday(day) {
            if (!day || !this.todayDate) return false;
            return day === this.todayDate.getDate() && this.currentMonth === this.todayDate.getMonth() && this.year === this.todayDate.getFullYear();
        },
        isSelectedDate(day) {
            if (!day || !this.selectedDate) return false;
            const [y, m, d] = this.selectedDate.split('-').map(Number);
            return day === d && this.currentMonth === m-1 && this.year === y;
        },
        hasScheduleOn(day) {
            if (!day) return false;
            const key = `${this.year}-${String(this.currentMonth+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
            return this.schedules[key] && this.schedules[key].length > 0;
        },
        selectDate(day) {
            const key = `${this.year}-${String(this.currentMonth+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
            this.selectedDate = key;
        },
        goToToday() {
            this.selectedDate = this.dateKey(this.todayDate);
            this.currentMonth = this.todayDate.getMonth();
            this.year = this.todayDate.getFullYear();
        },
        prevMonth() { if (this.currentMonth === 0) { this.currentMonth = 11; this.year--; } else { this.currentMonth--; } },
        nextMonth() { if (this.currentMonth === 11) { this.currentMonth = 0; this.year++; } else { this.currentMonth++; } },

        /* ═══ TASKS ═══ */
        selectedTodos() {
            if (!this.selectedDate) return [];
            return this.schedules[this.selectedDate] || [];
        },
        isDateConfirmed(dateKey) {
            return this.confirmedDates.includes(dateKey);
        },
        isFutureDate(dateKey) {
            return dateKey > this.dateKey(this.todayDate);
        },
        isPastDate(dateKey) {
            return dateKey < this.dateKey(this.todayDate);
        },
        canCompleteDate(dateKey) {
            return dateKey === this.dateKey(this.todayDate);
        },
        isTaskChecked(subName, catName) {
            return this.selectedTodos().some(t => t.nama === subName && t.kategori === catName);
        },
        countCheckedInCategory(catName) {
            return this.selectedTodos().filter(t => t.kategori === catName).length;
        },
        countDone() {
            return this.selectedTodos().filter(t => t.done).length;
        },

        toggleTaskFromTemplate(sub, cat) {
            if (!this.selectedDate) return;
            if (this.isDateConfirmed(this.selectedDate) || this.isPastDate(this.selectedDate)) return;

            const existing = this.selectedTodos().find(t => t.nama === sub.nama && t.kategori === cat.nama);
            if (!this.schedules[this.selectedDate]) this.schedules[this.selectedDate] = [];

            if (existing) {
                this.schedules[this.selectedDate] = this.selectedTodos().filter(t => t.id !== existing.id);
            } else {
                this.schedules[this.selectedDate].push({
                    id: Date.now() + Math.random(),
                    nama: sub.nama, kategori: cat.nama,
                    category_id: cat.id, category_item_id: sub.id,
                    warna: cat.warna, teksWarna: cat.teksWarna,
                    tipe: sub.tipe || 'count', target: sub.target ?? 0,
                    satuan: sub.satuan || 'x', keterangan: sub.keterangan || '',
                    date: this.selectedDate,
                    done: false,
                });
                this.triggerSweep(cat.warna);
            }
            this.saveSchedules();
        },

        toggleDone(item) {
            if (!this.selectedDate) return;
            if (this.isDateConfirmed(this.selectedDate) || !this.canCompleteDate(this.selectedDate)) return;
            item.done = !item.done;
            this.saveSchedules();
            if (item.done) this.triggerSweep('#10b981');
        },

        removeFromTracker(item) {
            if (!this.selectedDate) return;
            if (this.isDateConfirmed(this.selectedDate) || this.isPastDate(this.selectedDate)) return;
            this.schedules[this.selectedDate] = this.selectedTodos().filter(t => t.id !== item.id);
            this.saveSchedules();
        },

        changeTarget(item, delta) {
            if (this.isDateConfirmed(this.selectedDate) || this.isPastDate(this.selectedDate)) return;
            if (item.done || this.isPastDate(this.selectedDate)) return;
            const step = item.tipe === 'duration' ? 5 : 1;
            const next = (item.target || 0) + (delta * step);
            if (next < 0) return;
            item.target = next;
            this.saveSchedules();
        },
        sanitizeTarget(item) {
            if (item.done || this.isPastDate(this.selectedDate)) return;
            let v = parseInt(item.target) || 0;
            if (v < 0) v = 0;
            item.target = v;
            this.saveSchedules();
        },

        /* ═══ STYLE ═══ */
        dominantCategory() {
            const tasks = this.selectedTodos();
            if (!tasks.length) return null;
            const counts = {};
            tasks.forEach(t => { counts[t.kategori] = (counts[t.kategori] || 0) + 1; });
            let maxCat = null, maxCount = 0;
            for (const [nama, count] of Object.entries(counts)) {
                if (count > maxCount) { maxCount = count; maxCat = nama; }
            }
            return this.categories.find(c => c.nama === maxCat) || null;
        },
        trackerStyle() {
            const dom = this.dominantCategory();
            if (!dom) return 'background: linear-gradient(135deg, #186369 0%, #0e4348 100%);';
            return `background: linear-gradient(135deg, ${dom.warna} 0%, ${this.shade(dom.warna, -15)} 100%);`;
        },
        trackerTextStyle() {
            const dom = this.dominantCategory();
            if (!dom) return 'color: #ffffff;';
            return `color: ${dom.teksWarna};`;
        },
        trackerTextColorRaw() {
            const dom = this.dominantCategory();
            if (!dom) return '#ffffff';
            return dom.teksWarna;
        },
        trackerItemBg() {
            const dom = this.dominantCategory();
            if (!dom) return 'rgba(255,255,255,0.1)';
            return 'rgba(0,0,0,0.12)';
        },
        trackerItemAccent() {
            const dom = this.dominantCategory();
            if (!dom) return 'rgba(255,255,255,0.4)';
            return dom.teksWarna === '#ffffff' ? 'rgba(255,255,255,0.4)' : 'rgba(0,0,0,0.2)';
        },

        /* ═══ FILTER ═══ */
        filteredCategories() {
            const query = this.search.trim().toLowerCase();
            const scopedCategories = this.activeCategory
                ? this.categories.filter(c => c.id === this.activeCategory)
                : this.categories;

            if (!query) return scopedCategories;

            return scopedCategories.reduce((results, category) => {
                const categoryName = String(category.nama || '').toLowerCase();
                const categoryMatches = categoryName.includes(query);
                const matchingItems = categoryMatches
                    ? category.jenis || []
                    : (category.jenis || []).filter(item => {
                        const activityName = String(item.nama || '').toLowerCase();
                        const description = String(item.keterangan || '').toLowerCase();
                        return activityName.includes(query) || description.includes(query);
                    });

                if (matchingItems.length) {
                    results.push({ ...category, jenis: matchingItems });
                }

                return results;
            }, []);
        },
        searchChanged() {
            if (this.search.trim()) {
                this.popupOpen = true;
            }
        },
        activeCategoryName() {
            if (!this.activeCategory) return 'Semua';
            const cat = this.categories.find(c => c.id === this.activeCategory);
            return cat ? cat.nama : 'Semua';
        },
        get activeCategoryData() {
            if (!this.activeCategory) return null;
            return this.categories.find(c => c.id === this.activeCategory) || null;
        },

        /* ═══ POPUP CONTROL ═══ */
        openCategoryPopup(catId) {
            if (this.isDateConfirmed(this.selectedDate) || this.isPastDate(this.selectedDate)) {
                alert('Tanggal ini sudah dikonfirmasi. Pilih tanggal lain kalau mau menambah aktivitas.');
                return;
            }
            this.activeCategory = catId;
            this.popupOpen = true;
        },
        openAllActivitiesPopup() {
            if (this.isDateConfirmed(this.selectedDate) || this.isPastDate(this.selectedDate)) {
                alert('Tanggal ini sudah dikonfirmasi. Pilih tanggal lain kalau mau menambah aktivitas.');
                return;
            }
            this.activeCategory = null;
            this.popupOpen = true;
        },
        closeCategoryPopup() {
            this.popupOpen = false;
            this.activeCategory = null;
        },

        /* ═══ REKOMENDASI PROGRAM ═══ */
        openRekomendasi() {
            if (this.isDateConfirmed(this.selectedDate) || this.isPastDate(this.selectedDate)) {
                alert('Tanggal ini sudah dikonfirmasi.');
                return;
            }
            this.selectedTemplate = null;
            this.applyDays = 3;
            this.rekomendasiOpen = true;
        },

        closeRekomendasi() {
            this.rekomendasiOpen = false;
            this.selectedTemplate = null;
        },

        pilihTemplate(tplId) {
            this.selectedTemplate = this.selectedTemplate === tplId ? null : tplId;
        },

        applyTemplate() {
            if (!this.selectedTemplate) return;

            const tpl = this.templates.find(t => t.id === this.selectedTemplate);
            if (!tpl) return;

            const start = new Date(this.selectedDate);
            let totalAdded = 0;
            let skipped = 0;

            for (let i = 0; i < this.applyDays; i++) {
                const date = new Date(start);
                date.setDate(start.getDate() + i);
                const key = this.dateKey(date);

                if (this.isDateConfirmed(key) || this.isPastDate(key)) {
                    skipped++;
                    continue;
                }

                if (!this.schedules[key]) this.schedules[key] = [];

                tpl.aktivitas.forEach(akt => {
                    if (this.schedules[key].some(t => t.nama === akt.nama && t.kategori === akt.kategori)) return;

                    const cat = this.categories.find(c => c.nama === akt.kategori);
                    const warna = cat ? cat.warna : '#186369';
                    const teksWarna = cat ? cat.teksWarna : '#ffffff';

                    this.schedules[key].push({
                        id: Date.now() + Math.random() + Math.random(),
                        nama: akt.nama,
                        kategori: akt.kategori,
                        category_id: cat ? cat.id : null,
                        category_item_id: cat ? (cat.jenis.find(item => item.nama === akt.nama)?.id || null) : null,
                        warna: warna,
                        teksWarna: teksWarna,
                        tipe: akt.tipe,
                        target: akt.target,
                        satuan: akt.satuan,
                        keterangan: akt.keterangan,
                        date: key,
                        done: false,
                    });
                    totalAdded++;
                });
            }

            this.saveSchedules(Object.keys(this.schedules));
            this.rekomendasiOpen = false;
            this.selectedTemplate = null;
            this.triggerSweep('#186369');

            let msg = `✅ Program "${tpl.nama}" diterapkan!\n\n`;
            msg += `📅 ${this.applyDays} hari\n`;
            msg += `➕ ${totalAdded} aktivitas ditambahkan`;
            if (skipped > 0) msg += `\n⏭️ ${skipped} hari dilewati (sudah dikonfirmasi)`;

            setTimeout(() => alert(msg), 100);
        },

        /* ═══ AUTO GENERATE ═══ */
        openAutoGenerate() {
            this.autoGen = {
                startDate: this.selectedDate || this.dateKey(this.todayDate),
                days: 7, perDay: 3,
                categories: this.categories.map(c => c.nama),
            };
            this.autoGenOpen = true;
        },
        toggleAutoGenCategory(nama) {
            const idx = this.autoGen.categories.indexOf(nama);
            if (idx === -1) this.autoGen.categories.push(nama);
            else this.autoGen.categories.splice(idx, 1);
        },
        toggleAllAutoGen() {
            if (this.autoGen.categories.length === this.categories.length) {
                this.autoGen.categories = [];
            } else {
                this.autoGen.categories = this.categories.map(c => c.nama);
            }
        },
        executeAutoGenerate() {
            if (this.autoGen.categories.length === 0) return;

            const pool = [];
            this.categories
                .filter(c => this.autoGen.categories.includes(c.nama))
                .forEach(cat => cat.jenis.forEach(sub => pool.push({ sub, cat })));

            if (pool.length === 0) return alert('Gak ada aktivitas di kategori terpilih.');

            const start = new Date(this.autoGen.startDate);
            let count = 0;

            for (let i = 0; i < this.autoGen.days; i++) {
                const date = new Date(start);
                date.setDate(start.getDate() + i);
                const key = this.dateKey(date);

                if (this.isDateConfirmed(key) || this.isPastDate(key)) continue;
                if (!this.schedules[key]) this.schedules[key] = [];

                const existingCount = this.schedules[key].length;
                const toAdd = Math.max(0, this.autoGen.perDay - existingCount);
                const shuffled = [...pool].sort(() => Math.random() - 0.5);

                for (let j = 0; j < toAdd && j < shuffled.length; j++) {
                    const { sub, cat } = shuffled[j];
                    if (this.schedules[key].some(t => t.nama === sub.nama && t.kategori === cat.nama)) continue;

                    this.schedules[key].push({
                        id: Date.now() + Math.random() + j,
                        nama: sub.nama, kategori: cat.nama,
                        category_id: cat.id, category_item_id: sub.id,
                        warna: cat.warna, teksWarna: cat.teksWarna,
                        tipe: sub.tipe || 'count', target: sub.target ?? 0,
                        satuan: sub.satuan || 'x', keterangan: sub.keterangan || '',
                        date: key,
                        done: false,
                    });
                    count++;
                }
            }

            this.saveSchedules(Object.keys(this.schedules));
            this.autoGenOpen = false;
            setTimeout(() => alert(`✅ Berhasil generate ${count} aktivitas untuk ${this.autoGen.days} hari.`), 100);
        },

        /* ═══ KONFIRMASI ═══ */
        openConfirmModal() {
            if (this.selectedTodos().length === 0 || !this.canCompleteDate(this.selectedDate)) return;
            this.confirmModalOpen = true;
        },
        cancelConfirm() { this.confirmModalOpen = false; },
        submitConfirm() {
            if (!this.selectedDate || !this.canCompleteDate(this.selectedDate)) return;

            const doneItems = this.selectedTodos().filter(t => t.done);
            const items = doneItems.map(t => ({
                nama: t.nama, kategori: t.kategori,
                target: t.target, satuan: t.satuan,
            }));

            const [y, m, d] = this.selectedDate.split('-').map(Number);
            const date = new Date(y, m-1, d);
            const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            const label = `${days[date.getDay()]}, ${d} ${this.monthNames[m-1]} ${y}`;

            this.saveSchedules([this.selectedDate]).then(() => fetch('/jadwal/confirm', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ scheduled_date: this.selectedDate }),
            })).then(async response => {
                if (!response.ok) {
                    const body = await response.json().catch(() => ({}));
                    const firstError = Object.values(body.errors || {}).flat()[0];
                    throw new Error(firstError || body.message || 'Gagal mengonfirmasi jadwal.');
                }
                const data = await response.json();
                if (!this.confirmedDates.includes(this.selectedDate)) this.confirmedDates.push(this.selectedDate);
                this.history = [data.history, ...this.history.filter(item => item.tanggal !== data.history.tanggal)];
                this.confirmModalOpen = false;
                this.triggerSweep('#186369');
            }).catch(error => alert(error.message || 'Jadwal belum berhasil dikonfirmasi.'));
        },

        /* ═══ SWEEP ═══ */
        triggerSweep(color) {
            this.sweepColor = color || '#186369';
            this.sweepActive = false;
            this.$nextTick(() => {
                this.sweepActive = true;
                setTimeout(() => { this.sweepActive = false; }, 950);
            });
        },

        /* ═══ UTILITY ═══ */
        shade(hex, percent) {
            const num = parseInt(hex.replace('#', ''), 16);
            const amt = Math.round(2.55 * percent);
            const R = Math.max(0, Math.min(255, (num >> 16) + amt));
            const G = Math.max(0, Math.min(255, ((num >> 8) & 0x00FF) + amt));
            const B = Math.max(0, Math.min(255, (num & 0x0000FF) + amt));
            return '#' + (0x1000000 + R * 0x10000 + G * 0x100 + B).toString(16).slice(1);
        },
    };
}
</script>
@endpush