@extends('layouts.app', ['activeNav' => 'konsultasi'])

@section('title', 'Konsultasi')

@push('styles')
<style>
    @keyframes msg-in { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    .msg-anim { animation: msg-in 0.3s ease-out backwards; }
    @keyframes card-in { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
    .card-anim { animation: card-in 0.4s ease-out backwards; }
    @keyframes chat-pulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(24, 99, 105, 0.4); } 50% { box-shadow: 0 0 0 12px rgba(24, 99, 105, 0); } }
    .chat-pulse { animation: chat-pulse 2s ease-in-out infinite; }
    @keyframes emergency-pulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.5); } 50% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); } }
    .emergency-pulse { animation: emergency-pulse 1.8s ease-in-out infinite; }
    @keyframes modal-in { from { opacity: 0; transform: scale(0.9) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
    .modal-anim { animation: modal-in 0.3s ease-out; }
</style>
@endpush

@section('content')
<script>
    window.serverDoctors = @json($doctors);
    window.serverBookings = @json($bookings);
</script>
<div x-data="konsultasiApp()" x-init="init()" class="contents">

    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 flex-shrink-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-[#186369]">Konsultasi</h1>
            <p class="text-sm text-[#186369]/70 mt-1">Dapatkan bimbingan langsung dari Profesional.</p>
        </div>
        <div class="flex items-center gap-3">
            <form role="search" class="relative flex-1 sm:w-64 lg:w-72">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 bg-gray-400 rounded"></div>
                <input type="search" x-model="search" placeholder="Cari dokter..."
                       class="w-full pl-11 pr-4 py-2.5 text-xs font-bold bg-white border-2 border-gray-300 rounded-full focus:outline-none focus:border-[#186369]">
            </form>
            <div class="relative flex-shrink-0">
                <span x-show="rippleActive" class="absolute inset-0 rounded-full bg-[#186369] pointer-events-none avatar-ripple"></span>
                <button type="button" @click="toggleProfile()"
                        class="relative flex-shrink-0 w-11 h-11 lg:w-12 lg:h-12 rounded-full overflow-hidden border-2 lg:border-[3px]"
                        :class="profileOpen ? 'border-[#186369]' : 'border-white shadow-md'">
                    <img src="{{ asset('images/avatar.png') }}" alt="Profil" class="w-full h-full object-cover">
                </button>
            </div>
        </div>
    </header>

    <div class="flex-shrink-0">
        <div class="flex gap-2 overflow-x-auto pb-2 scroll-thin-h">
            <button type="button" @click="filterSpes = null" data-auth
                    class="flex-shrink-0 px-4 py-2 rounded-full text-xs font-bold transition"
                    :class="filterSpes === null ? 'bg-[#186369] text-white' : 'bg-white text-[#186369] border border-gray-200'">
                Semua
            </button>
            <template x-for="(spes, i) in spesialisasiList()" :key="i">
                <button type="button" @click="filterSpes = spes" data-auth
                        class="flex-shrink-0 px-4 py-2 rounded-full text-xs font-bold transition"
                        :class="filterSpes === spes ? 'bg-[#186369] text-white' : 'bg-white text-[#186369] border border-gray-200'"
                        x-text="spes"></button>
            </template>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 flex-1 min-h-0 auto-rows-max">
        <template x-for="(doc, i) in filteredDoctors()" :key="doc.id">
            <article class="card-anim bg-white rounded-[32px] p-5 border border-gray-100 flex flex-col">
                <div class="flex items-start gap-4">
                    <div class="relative flex-shrink-0">
                        <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-100">
                            <template x-if="doc.fotoBase64 || doc.avatar">
                                <img :src="doc.fotoBase64 || doc.avatar" :alt="doc.nama" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!doc.fotoBase64 && !doc.avatar">
                                <div class="w-full h-full flex items-center justify-center bg-[#186369]/10 text-[#186369] text-2xl font-bold"
                                     x-text="doc.nama ? doc.nama.charAt(0) : '?'"></div>
                            </template>
                        </div>
                        <span class="absolute bottom-0 right-0 w-4 h-4 rounded-full border-2 border-white"
                              :class="doc.online ? 'bg-emerald-400' : 'bg-gray-300'"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-bold text-[#186369] truncate" x-text="doc.nama"></h3>
                        <p class="text-[11px] font-bold text-[#186369]/50 mt-0.5" x-text="doc.spesialisasi"></p>
                        <p class="text-[10px] font-bold text-[#186369]/40 mt-2">🕐 <span x-text="doc.jam_praktik"></span></p>
                    </div>
                </div>
                <p class="text-xs text-[#186369]/60 leading-relaxed mt-4 line-clamp-2" x-text="doc.bio"></p>
                <button type="button" @click="booking(doc)" data-auth
                        class="w-full mt-4 flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl
                               bg-[#186369] text-white text-xs font-bold hover:opacity-90 transition active:scale-95">
                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                    </svg>
                    Booking Konsultasi
                </button>
            </article>
        </template>
    </div>

    {{-- FLOATING CHAT --}}
    <div class="fixed bottom-6 right-6 z-[70] flex flex-col items-end gap-3">
        <div x-show="chatOpen" x-cloak x-transition
             class="w-[340px] sm:w-[380px] max-w-[calc(100vw-3rem)] h-[480px] max-h-[calc(100vh-8rem)]
                    bg-white rounded-3xl shadow-2xl border border-gray-100 flex flex-col overflow-hidden">
            <div class="p-3.5 flex-shrink-0 flex items-center gap-3 bg-[#186369] text-white">
                <div class="w-10 h-10 rounded-full overflow-hidden bg-white/20">
                    <img src="https://i.pravatar.cc/100?img=68" alt="Admin" class="w-full h-full object-cover">
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold">Admin Mindo</p>
                    <p class="text-[10px] font-bold text-white/60">Customer Service · Online</p>
                </div>
                <button type="button" @click="chatOpen = false" class="w-8 h-8 rounded-full hover:bg-white/10">
                    <svg viewBox="0 0 16 16" class="w-3.5 h-3.5 mx-auto" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4l8 8M12 4l-8 8" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 min-h-0 overflow-y-auto scroll-thin p-3.5 space-y-2.5" x-ref="chatArea">
                <template x-for="(msg, i) in messages" :key="msg.id">
                    <div class="msg-anim">
                        <div class="flex items-end gap-1.5" :class="msg.from === 'me' ? 'justify-end' : 'justify-start'">
                            <div class="max-w-[75%]">
                                <div class="px-3 py-2 rounded-2xl"
                                     :class="msg.from === 'me' ? 'bg-[#186369] text-white rounded-br-md' : 'bg-gray-100 text-[#186369] rounded-bl-md'">
                                    <p class="text-[11px] font-bold" x-text="msg.teks"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <div class="p-3 border-t border-gray-100">
                <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                    <input type="text" x-model="pesanBaru" placeholder="Ketik..."
                           class="flex-1 px-3.5 py-2 text-[11px] font-bold bg-gray-50 border border-gray-200 rounded-full">
                    <button type="submit" :disabled="!pesanBaru.trim()"
                            class="w-9 h-9 rounded-full bg-[#186369] disabled:opacity-40">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 text-white mx-auto" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <button type="button" @click="chatOpen = !chatOpen" data-auth
                class="relative group flex items-center gap-3 px-5 py-4 bg-[#186369] text-white
                       rounded-full shadow-2xl active:scale-95 chat-pulse">
            <span class="text-sm font-bold" x-text="chatOpen ? 'Tutup' : 'Hubungi Kami'"></span>
        </button>
    </div>

    {{-- MODAL BOOKING --}}
    <div x-show="bookingModalOpen" x-cloak x-transition
         @click.self="bookingModalOpen = false"
         class="fixed inset-0 z-[100] bg-[#0a3d42]/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="modal-anim bg-white rounded-3xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-hidden flex flex-col">
            <div class="p-6 pb-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-[#186369]"
                        x-text="bookingTarget ? 'Booking ' + bookingTarget.nama : 'Booking'"></h2>
                    <p class="text-[10px] font-bold text-[#186369]/50 mt-0.5"
                       x-text="bookingTarget ? bookingTarget.spesialisasi : ''"></p>
                </div>
                <button type="button" @click="bookingModalOpen = false" class="w-8 h-8 rounded-full bg-gray-100">
                    <svg viewBox="0 0 16 16" class="w-3.5 h-3.5 mx-auto text-[#186369]" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4l8 8M12 4l-8 8" />
                    </svg>
                </button>
            </div>
            <div class="p-6 space-y-4 flex-1 min-h-0 overflow-y-auto">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Pilih Tanggal <span class="text-red-400">*</span></label>
                    <input type="date" x-model="bookingForm.tanggal" :min="todayDate"
                           class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Pilih Jam <span class="text-red-400">*</span></label>
                    <select x-model="bookingForm.jam"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl text-sm">
                        <option value="">Pilih jam...</option>
                        <option value="09:00 - 10:00">09:00 - 10:00</option>
                        <option value="10:00 - 11:00">10:00 - 11:00</option>
                        <option value="13:00 - 14:00">13:00 - 14:00</option>
                        <option value="14:00 - 15:00">14:00 - 15:00</option>
                        <option value="15:00 - 16:00">15:00 - 16:00</option>
                        <option value="16:00 - 17:00">16:00 - 17:00</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Catatan (opsional)</label>
                    <textarea x-model="bookingForm.catatan" rows="3"
                              class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl text-sm resize-none"></textarea>
                </div>
            </div>
            <div class="p-6 pt-4 border-t border-gray-100 flex gap-2">
                <button type="button" @click="bookingModalOpen = false"
                        class="flex-1 py-2.5 rounded-2xl bg-gray-100 text-[#186369] font-bold text-sm">Batal</button>
                <button type="button" @click="submitBooking()"
                        :disabled="!bookingForm.tanggal || !bookingForm.jam"
                        class="flex-1 py-2.5 rounded-2xl bg-[#186369] text-white font-bold text-sm disabled:opacity-40">
                    Konfirmasi
                </button>
            </div>
        </div>
    </div>

</div>
@endsection


@push('sidebar-right')
<aside class="order-2 lg:order-3 hidden lg:flex flex-col w-[320px] xl:w-[360px] flex-shrink-0 h-screen overflow-y-auto scroll-thin p-5 gap-4"
       style="background: linear-gradient(180deg, #ffffff 0%, #f8faf9 100%);"
       x-data="jadwalPanel()" x-init="load()">

    <div class="flex-1 min-h-0 flex flex-col">
        <div class="flex items-center justify-between mb-3 flex-shrink-0">
            <h2 class="text-sm font-bold text-[#186369]">📅 Jadwal Saya</h2>
            <span class="text-[10px] font-bold text-[#186369]/40" x-text="jadwals.length + ' sesi'"></span>
        </div>
        <div class="flex-1 min-h-0 overflow-y-auto scroll-thin pr-1 space-y-2">
            <template x-if="jadwals.length === 0">
                <div class="bg-gray-50 border-2 border-dashed border-[#186369]/15 rounded-2xl p-6 text-center">
                    <p class="text-[10px] text-[#186369]/50">Belum ada jadwal konsultasi.</p>
                </div>
            </template>
            <template x-for="(j, i) in jadwals" :key="j.id">
                <div class="bg-gray-50 rounded-2xl p-3 hover:bg-gray-100 transition">
                    <div class="flex items-start gap-3">
                        <div class="w-1 self-stretch rounded-full"
                             :class="j.status === 'aktif' ? 'bg-emerald-500' : j.status === 'selesai' ? 'bg-gray-300' : 'bg-amber-400'"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[9px] font-bold uppercase"
                               :class="j.status === 'aktif' ? 'text-emerald-600' : j.status === 'selesai' ? 'text-gray-400' : 'text-amber-600'"
                               x-text="j.tanggalLabel"></p>
                            <p class="text-xs font-bold text-[#186369] truncate" x-text="j.dokter"></p>
                            <p class="text-[10px] text-[#186369]/50">
                                <span x-text="j.spesialisasi"></span> · <span x-text="j.jam"></span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 mt-2">
                        <template x-if="j.status === 'aktif'">
                            <a :href="'tel:' + (j.wa || '119')" data-auth
                               class="flex-1 py-1 rounded-lg bg-emerald-500 text-white text-[9px] font-bold text-center">
                                📞 Telepon
                            </a>
                        </template>
                        <template x-if="j.status === 'selesai'">
                            <div class="flex-1 py-1 rounded-lg bg-gray-100 text-gray-400 text-[9px] font-bold text-center">✓ Selesai</div>
                        </template>
                        <template x-if="j.status === 'dibatalkan'">
                            <div class="flex-1 py-1 rounded-lg bg-red-50 text-red-600 text-[9px] font-bold text-center">✕ Dibatalkan</div>
                        </template>
                        <template x-if="j.status === 'menunggu'">
                            <div class="flex-1 py-1 rounded-lg bg-amber-50 text-amber-600 text-[9px] font-bold text-center">⏳ Menunggu</div>
                        </template>
                        <button type="button"
                                x-show="['aktif','menunggu'].includes(j.status)"
                                @click="hapusJadwal(j.id)" data-auth
                                class="w-6 h-6 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center">
                            <svg viewBox="0 0 16 16" class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                <path d="M4 4l8 8M12 4l-8 8"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <div class="flex-shrink-0 mb-12 bg-gradient-to-br from-red-50 to-red-100/50 rounded-3xl p-4 border-2 border-red-200">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-8 h-8 rounded-xl bg-red-500 flex items-center justify-center emergency-pulse">
                <svg viewBox="0 0 24 24" class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M12 9v4M12 17h.01"/>
                </svg>
            </div>
            <div>
                <h3 class="text-xs font-bold text-red-700">Butuh Bantuan Segera?</h3>
                <p class="text-[9px] text-red-600/70">Hotline 24/7</p>
            </div>
        </div>
        <a href="tel:119" data-auth
           class="flex items-center gap-2 py-2 px-3 rounded-xl bg-white border border-red-200 mb-2">
            <p class="text-xs font-bold text-red-700">📞 119 ext 8</p>
        </a>
        <button type="button" @click="window.dispatchEvent(new CustomEvent('open-emergency-chat'))" data-auth
                class="w-full flex items-center gap-2 py-2 px-3 rounded-xl bg-white border border-red-200 text-left">
            <p class="text-xs font-bold text-red-700">💬 Chat Darurat</p>
        </button>
    </div>

</aside>
@endpush


@push('scripts')
<script>
    function konsultasiApp() {
        return {
            chatOpen: false, pesanBaru: '', adminTyping: false, unread: 0,
            search: '', filterSpes: null, doctors: [],
            bookingModalOpen: false, bookingTarget: null,
            bookingForm: { tanggal: '', jam: '', catatan: '' },
            todayDate: new Date().toISOString().slice(0, 10),
            messages: [
                { id: 1, from: 'admin', teks: 'Hai, saya Admin Mindo 👋', jam: '19.50' },
                { id: 2, from: 'admin', teks: 'Ada yang bisa saya bantu?', jam: '19.50' },
            ],
            init() {
                this.unread = 1;
                this.loadDoctors();
                window.addEventListener('open-emergency-chat', () => {
                    this.chatOpen = true; this.unread = 0;
                });
            },
            loadDoctors() {
                                this.doctors = window.serverDoctors || [];
            },
            spesialisasiList() { return [...new Set(this.doctors.map(d => d.spesialisasi))]; },
            filteredDoctors() {
                let list = this.doctors;
                if (this.filterSpes) list = list.filter(d => d.spesialisasi === this.filterSpes);
                if (this.search.trim()) {
                    const q = this.search.toLowerCase();
                    list = list.filter(d => d.nama.toLowerCase().includes(q) || d.spesialisasi.toLowerCase().includes(q));
                }
                return list;
            },
            async sendMessage() {
                if (!this.pesanBaru.trim()) return;
                const now = new Date();
                const jam = String(now.getHours()).padStart(2, '0') + '.' + String(now.getMinutes()).padStart(2, '0');
                const message = this.pesanBaru.trim();
                this.messages.push({ id: Date.now(), from: 'me', teks: message, jam });
                this.pesanBaru = '';
                this.$nextTick(() => {
                    const area = this.$refs.chatArea;
                    if (area) area.scrollTop = area.scrollHeight;
                });
                try {
                    const response = await fetch('/konsultasi/faq-reply', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ message }),
                    });
                    const result = await response.json();
                    if (!response.ok) throw new Error(result.message || 'Gagal mendapatkan balasan.');
                    this.messages.push({ id: Date.now() + 1, from: 'admin', teks: result.reply, jam });
                    this.$nextTick(() => {
                        const area = this.$refs.chatArea;
                        if (area) area.scrollTop = area.scrollHeight;
                    });
                } catch (error) {
                    this.messages.push({ id: Date.now() + 1, from: 'admin', teks: 'Maaf, balasan belum tersedia.', jam });
                }
            },
            booking(doc) {
                this.bookingTarget = doc;
                this.bookingForm = { tanggal: '', jam: '', catatan: '' };
                this.bookingModalOpen = true;
            },
            submitBooking() {
                if (!this.bookingForm.tanggal || !this.bookingForm.jam) return;
                const tgl = new Date(this.bookingForm.tanggal);
                const hariIni = new Date(); hariIni.setHours(0,0,0,0);
                const selisih = Math.floor((tgl - hariIni) / 86400000);
                let label = selisih === 0 ? 'Hari Ini' : selisih === 1 ? 'Besok' : selisih > 1 ? selisih + ' hari lagi' : Math.abs(selisih) + ' hari lalu';

                fetch('/konsultasi', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        doctor_id: this.bookingTarget.id,
                        booking_date: this.bookingForm.tanggal,
                        booking_time: this.bookingForm.jam,
                        notes: this.bookingForm.catatan,
                    }),
                }).then(async response => {
                    if (!response.ok) {
                        const result = await response.json().catch(() => ({}));
                        const errors = result.errors ? Object.values(result.errors).flat().join('\n') : result.message;
                        throw new Error(errors || 'Gagal membuat booking.');
                    }
                    window.location.reload();
                }).catch(error => alert(error.message || 'Booking belum berhasil disimpan.'));
            },
        };
    }

    function jadwalPanel() {
        return {
            jadwals: [],
            load() {
                this.jadwals = window.serverBookings || [];
            },
            hapusJadwal(id) {
                if (!confirm('Yakin hapus jadwal ini?')) return;
                fetch(`/konsultasi/${id}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ action: 'cancel' }),
                }).then(async response => {
                    const result = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(result.message || 'Gagal membatalkan booking.');
                    }
                    window.location.reload();
                }).catch((error) => alert(error.message || 'Booking belum berhasil dibatalkan.'));
            },
        };
    }
</script>
@endpush