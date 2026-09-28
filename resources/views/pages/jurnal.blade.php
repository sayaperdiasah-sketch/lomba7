@extends('layouts.app', ['activeNav' => 'jurnal'])

@section('title', 'Journey')

@push('styles')
<style>
    .journey-input {
        width: 100%;
        border: none;
        outline: none;
        background: transparent;
        font-family: 'Inter', sans-serif;
    }
    .journey-input::placeholder {
        color: rgba(24, 99, 105, 0.35);
        font-weight: 700;
    }

    @keyframes entry-slide-in {
        from { opacity: 0; transform: translateX(-12px); }
        to { opacity: 1; transform: translateX(0); }
    }
    .entry-item { animation: entry-slide-in 0.4s ease-out backwards; }

    @keyframes modal-in {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .modal-anim { animation: modal-in 0.3s ease-out; }

    @keyframes avatar-ripple {
        0%   { transform: scale(1);   opacity: 0.45; }
        100% { transform: scale(1.8); opacity: 0;    }
    }
    .avatar-ripple { animation: avatar-ripple 0.8s ease-out; }
</style>
@endpush


@section('content')

<div x-data="journeyApp()" x-init="init()" class="contents">
    <script>window.serverJourneyEntries = @json($entries);</script>

    {{-- ═══ HEADER — avatar memanggil drawer global ═══ --}}
    <header class="flex flex-col sm:flex-row sm:items-center
                   sm:justify-between gap-4 flex-shrink-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-[#186369]">
                Journey
            </h1>
            <p class="text-sm text-[#186369]/70 mt-1">
                Ceritakan apa yang kamu rasakan hari ini.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <form role="search" class="relative flex-1 sm:w-72 lg:w-80">
                <div class="absolute left-4 top-1/2 -translate-y-1/2
                            w-4 h-4 bg-gray-400 rounded"></div>
                <input type="search"
                       x-model="search"
                       placeholder="Cari catatan..."
                       class="w-full pl-11 pr-4 py-2.5 text-xs font-bold
                              bg-white border-2 border-gray-300 rounded-full
                              focus:outline-none focus:border-[#186369]">
            </form>

            {{-- Avatar — klik untuk buka drawer profil global --}}
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
                    <img src="{{ asset('images/avatar.png') }}" alt="Profil"
                         class="w-full h-full object-cover">
                    <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full
                                 border-2 border-white transition-colors duration-300"
                          :class="profileOpen ? 'bg-emerald-400' : 'bg-gray-300'"></span>
                </button>
            </div>
        </div>
    </header>

    {{-- ═══ KARTU EDITOR ═══ --}}
    <section class="card-animated bg-white rounded-[40px] p-5 sm:p-7 flex-1 flex flex-col min-h-[400px]">

        <div class="flex items-center justify-between mb-5 flex-shrink-0">
            <h2 class="text-xl sm:text-2xl font-bold text-[#186369]">
                <span x-text="editingId ? 'Edit Catatan' : 'Bagaimana Hari Ini ?'"></span>
            </h2>

            <div class="flex items-center gap-2">
                <button type="button"
                        x-show="editingId"
                        @click="cancelEdit()"
                        class="px-4 py-2.5 rounded-2xl
                               bg-gray-100 text-[#186369] text-sm font-bold
                               transition active:scale-95
                               hover:bg-gray-200">
                    Batal
                </button>

                <button type="button"
                        @click="saveEntry()"
                        :disabled="!judul.trim() || !isi.trim()"
                        class="px-5 py-2.5 rounded-2xl
                               bg-[#186369] text-white text-sm font-bold
                               transition active:scale-95
                               hover:opacity-90
                               disabled:opacity-40 disabled:cursor-not-allowed
                               flex items-center gap-2">
                    <svg viewBox="0 0 16 16" class="w-3.5 h-3.5"
                         fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 3h10v10H3zM5 3v4h6V3M5 10h6" />
                    </svg>
                    <span x-text="editingId ? 'Update' : 'Save'"></span>
                </button>
            </div>
        </div>

        <div class="flex-1 flex flex-col gap-4 min-h-0">
            <input type="text"
                   x-model="judul"
                   placeholder="Judul....."
                   maxlength="80"
                   class="journey-input
                          text-base sm:text-lg font-bold text-[#186369]
                          px-1 py-2 border-b-2 border-[#186369]/10
                          focus:border-[#186369] transition-colors">

            <textarea x-model="isi"
                      placeholder="Tulis di sini yah........."
                      class="journey-input flex-1 min-h-[200px]
                             text-sm text-[#186369]/80
                             leading-relaxed resize-none
                             px-1 py-2"></textarea>

            <div class="flex items-center justify-between
                        text-[10px] font-bold text-[#186369]/40 px-1">
                <span x-text="isi.length + ' karakter'"></span>
                <span x-text="todayString()"></span>
            </div>
        </div>
    </section>


    {{-- ═══ MODAL KONFIRMASI HAPUS ═══ --}}
    <div x-show="deleteModalOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="deleteModalOpen = false"
         @keydown.escape.window="deleteModalOpen = false"
         class="fixed inset-0 z-[100] bg-[#0a3d42]/40 backdrop-blur-sm
                flex items-center justify-center p-4">

        <div class="modal-anim bg-white rounded-3xl shadow-2xl w-full max-w-sm p-6">
            <div class="flex items-center justify-center mb-4">
                <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" class="w-7 h-7 text-red-500"
                         fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                    </svg>
                </div>
            </div>

            <h3 class="text-lg font-bold text-[#186369] text-center mb-2">
                Hapus Catatan?
            </h3>
            <p class="text-xs text-[#186369]/60 text-center leading-relaxed mb-5">
                Catatan "<span class="font-bold" x-text="deleteTarget?.judul"></span>"
                akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.
            </p>

            <div class="grid grid-cols-2 gap-2">
                <button type="button"
                        @click="deleteModalOpen = false"
                        class="py-2.5 rounded-2xl
                               bg-gray-100 text-[#186369] text-sm font-bold
                               hover:bg-gray-200 transition active:scale-95">
                    Batal
                </button>
                <button type="button"
                        @click="confirmDelete()"
                        class="py-2.5 rounded-2xl
                               bg-red-500 text-white text-sm font-bold
                               hover:bg-red-600 transition active:scale-95">
                    Hapus
                </button>
            </div>
        </div>
    </div>

</div>

@endsection


{{-- ═══ SIDEBAR KANAN — HISTORY JOURNEY saja ═══ --}}
@push('sidebar-right')
<aside class="order-2 lg:order-3 hidden lg:flex flex-col
              w-[300px] xl:w-[340px] flex-shrink-0
              h-screen overflow-y-auto scroll-thin
              p-6 gap-5"
       style="
           background: linear-gradient(180deg, #ffffff 0%, #f8faf9 100%);
           box-shadow: inset 20px 0 40px -20px rgba(24,99,105,0.06);
       ">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-shrink-0">
        <h3 class="text-sm font-bold text-[#186369]">History Journey</h3>
        <span class="text-[10px] font-bold text-[#186369]/40"
              x-data="journeyHistory()" x-init="load()"
              x-text="entries.length + ' catatan'"></span>
    </div>

    {{-- List --}}
    <div class="flex-1 min-h-0 flex flex-col"
         x-data="journeyHistory()"
         x-init="load()">
        <div class="flex-1 min-h-0 overflow-y-auto scroll-thin pr-1 space-y-2">

            <template x-if="entries.length === 0">
                <div class="bg-gray-50 border-2 border-dashed border-[#186369]/15
                            rounded-2xl p-6 text-center">
                    <p class="text-[10px] text-[#186369]/50 leading-relaxed">
                        Belum ada catatan.<br>
                        Tulis journey pertama kamu.
                    </p>
                </div>
            </template>

            <template x-for="(entry, i) in entries" :key="entry.id">
                <div class="entry-item
                            bg-gray-50 rounded-2xl p-3
                            hover:bg-gray-100 transition
                            group"
                     :style="`animation-delay: ${i * 30}ms;`">

                    <div class="cursor-pointer">
                        <p class="text-xs font-bold text-[#186369] truncate">
                            <span x-text="entry.judul"></span>
                        </p>
                        <p class="text-[10px] font-bold text-[#186369]/40 mt-0.5">
                            <span x-text="entry.tanggal"></span>
                        </p>
                    </div>

                    <div class="flex items-center gap-1 mt-2
                                opacity-0 group-hover:opacity-100 transition">
                        <button type="button"
                                @click.stop="$dispatch('load-journey', { id: entry.id })"
                                class="flex-1 py-1 rounded-lg
                                       bg-[#186369]/10 text-[#186369]
                                       text-[9px] font-bold
                                       hover:bg-[#186369]/20 transition">
                            Edit
                        </button>

                        <button type="button"
                                @click.stop="$dispatch('delete-journey', { id: entry.id })"
                                class="flex-1 py-1 rounded-lg
                                       bg-red-50 text-red-500
                                       text-[9px] font-bold
                                       hover:bg-red-100 transition">
                            Hapus
                        </button>
                    </div>
                </div>
            </template>

        </div>
    </div>

</aside>
@endpush


@push('scripts')
<script>
    /* ═══ JOURNEY APP — editor ═══ */
    function journeyApp() {
        return {
            judul: '',
            isi: '',
            editingId: null,
            search: '',
            deleteModalOpen: false,
            deleteTarget: null,

            init() {
                window.addEventListener('load-journey', (e) => {
                    const entry = this.getEntry(e.detail.id);
                    if (entry) this.editEntry(entry);
                });
                window.addEventListener('delete-journey', (e) => {
                    const entry = this.getEntry(e.detail.id);
                    if (entry) this.deleteEntry(entry);
                });
            },

            getEntry(id) {
                return (window.serverJourneyEntries || []).find(e => e.id === id) || null;
            },

            saveEntry() {
                if (!this.judul.trim() || !this.isi.trim()) return;

                const method = this.editingId !== null ? 'PATCH' : 'POST';
                const url = this.editingId !== null ? `/jurnal/${this.editingId}` : '/jurnal';
                fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        title: this.judul.trim(),
                        content: this.isi.trim(),
                        entry_date: new Date().toISOString().slice(0, 10),
                    }),
                }).then(response => {
                    if (!response.ok) throw new Error('Gagal menyimpan jurnal.');
                    window.location.reload();
                }).catch(() => alert('Jurnal belum berhasil disimpan.'));
            },

            editEntry(entry) {
                this.editingId = entry.id;
                this.judul = entry.judul;
                this.isi = entry.isi;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            deleteEntry(entry) {
                this.deleteTarget = entry;
                this.deleteModalOpen = true;
            },

            confirmDelete() {
                if (!this.deleteTarget) return;

                if (this.editingId === this.deleteTarget.id) {
                    this.resetEditor();
                }
                fetch(`/jurnal/${this.deleteTarget.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                }).then(response => {
                    if (!response.ok) throw new Error('Gagal menghapus jurnal.');
                    window.location.reload();
                }).catch(() => alert('Jurnal belum berhasil dihapus.'));
            },

            resetEditor() {
                this.judul = '';
                this.isi = '';
                this.editingId = null;
            },

            cancelEdit() { this.resetEditor(); },

            formatDate(date) {
                const d = String(date.getDate()).padStart(2, '0');
                const monthNames = [
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];
                return `${d}-${monthNames[date.getMonth()]}-${date.getFullYear()}`;
            },

            todayString() {
                const now = new Date();
                const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                return `${days[now.getDay()]}, ${this.formatDate(now)}`;
            },
        };
    }

    /* ═══ JOURNEY HISTORY ═══ */
    function journeyHistory() {
        return {
            entries: [],

            load() {
                this.entries = window.serverJourneyEntries || [];
            },
        };
    }
</script>
@endpush