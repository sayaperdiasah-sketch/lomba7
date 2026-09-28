@extends('layouts.app', ['activeNav' => 'tes'])

@section('title', 'Tes Kesehatan Mental')

@section('content')

{{-- ⚠️ SCRIPT DI ATAS — biar tidak bergantung pada @stack('scripts') --}}
<script>
    window.serverTests = @json($tests);
    window.serverProfileComplete = @json($profileComplete);
    window.serverSearchArticles = @json($searchArticles);
    window.serverSearchCategories = @json($searchCategories);
    window.serverSearchDoctors = @json($searchDoctors);
    window.serverSearchSchedules = @json($searchSchedules);

    window.testList = function () {
        return {
            tests: window.serverTests || [],
            isProfileComplete: window.serverProfileComplete === true,
            searchQuery: '',
            searchOpen: false,

            init() {
                this.tests = window.serverTests || [];
            },

            get searchResults() {
                const query = this.searchQuery.trim().toLowerCase();
                if (!query) return [];

                const tests = this.tests
                    .filter(test => [test.judul, test.jenis, test.gejala_singkat]
                        .some(value => String(value || '').toLowerCase().includes(query)))
                    .slice(0, 5)
                    .map(test => ({
                        key: `test-${test.id}`,
                        label: test.judul,
                        type: `Tes mental · ${test.jenis}`,
                        href: `/tes-mental/${test.id}/soal`,
                    }));

                const matches = (values) => values.some(value =>
                    String(value || '').toLowerCase().includes(query)
                );
                const articles = (window.serverSearchArticles || [])
                    .filter(article => matches([article.title, article.content]))
                    .slice(0, 5)
                    .map(article => ({
                        key: `article-${article.id}`,
                        label: article.title,
                        type: 'Artikel kesehatan',
                        href: `/artikel/${article.id}`,
                    }));
                const doctors = (window.serverSearchDoctors || [])
                    .filter(doctor => matches([doctor.name, doctor.spesialisasi, doctor.bio]))
                    .slice(0, 5)
                    .map(doctor => ({
                        key: `doctor-${doctor.id}`,
                        label: doctor.name,
                        type: `Dokter · ${doctor.spesialisasi || 'Konsultasi'}`,
                        href: '/konsultasi',
                    }));
                const categories = (window.serverSearchCategories || [])
                    .filter(category => matches([
                        category.nama,
                        ...(category.jenis || []).map(item => item.nama),
                    ]))
                    .slice(0, 5)
                    .map(category => ({
                        key: `category-${category.id}`,
                        label: category.nama,
                        type: 'Kategori aktivitas',
                        href: '/jadwal',
                    }));
                const activities = Object.entries(window.serverSearchSchedules || {})
                    .flatMap(([date, items]) => (Array.isArray(items) ? items : []).map(item => ({ ...item, date })))
                    .filter(item => matches([item.nama, item.kategori, item.keterangan]))
                    .slice(0, 5)
                    .map(item => ({
                        key: `activity-${item.date}-${item.id || item.nama}`,
                        label: item.nama,
                        type: `Aktivitas · ${item.kategori || 'Lainnya'}`,
                        href: '/jadwal',
                    }));

                const features = [
                    { key: 'feature-jadwal', label: 'Jadwal Aktivitas', type: 'Fitur', href: '/jadwal' },
                    { key: 'feature-jurnal', label: 'Jurnal Harian', type: 'Fitur', href: '/jurnal' },
                    { key: 'feature-konsultasi', label: 'Konsultasi Dokter', type: 'Fitur', href: '/konsultasi' },
                ].filter(feature => feature.label.toLowerCase().includes(query));

                return [...tests, ...activities, ...categories, ...doctors, ...articles, ...features].slice(0, 8);
            },

            seedDefault() {
                return [
                    {
                        id: 1,
                        jenis: 'Depresi',
                        judul: 'Skrining Depresi',
                        gejala_singkat: 'Deteksi gejala depresi melalui 20 pertanyaan berdasarkan kondisi 2 minggu terakhir',
                        warna: '#186369',
                        detail: 'Kuesioner ini menilai tingkat depresi melalui 20 butir pernyataan terkait suasana hati, energi, tidur, nafsu makan, konsentrasi, dan perasaan berharga.',
                        gejala_list: [
                            'Perasaan sedih',
                            'Kesepian',
                            'Putus asa',
                            'Kecemasan',
                            'Kehilangan minat',
                            'Kelelahan',
                            'Gangguan tidur',
                            'Nafsu makan berubah',
                            'Sulit konsentrasi',
                            'Rasa bersalah',
                            'Pikiran negatif',
                        ],
                        deskripsi_tes: 'Silakan baca setiap pernyataan dengan cermat. Berikan jawaban yang paling menggambarkan kondisi atau perasaan Anda selama 2 minggu terakhir.',
                        intro: 'Selama 2 minggu terakhir, seberapa sering Anda mengalami hal berikut?',
                        soal: [
                            'Saya merasa sedih atau murung tanpa alasan yang jelas.',
                            'Saya merasa sangat kesepian meskipun sedang berada di keramaian.',
                            'Saya merasa ingin menangis atau menangis lebih sering dari biasanya.',
                            'Saya merasa putus asa secara mendalam tentang masa depan saya.',
                            'Saya merasa cemas, tegang, atau tidak bisa tenang.',
                            'Saya kehilangan minat pada hobi atau aktivitas yang biasanya saya sukai.',
                            'Saya merasa tidak memiliki energi atau kekuatan untuk beraktivitas.',
                            'Saya merasa sulit untuk memulai atau menyelesaikan pekerjaan sehari-hari.',
                            'Saya merasa tidak ada hal yang bisa membuat saya merasa bahagia lagi.',
                            'Saya merasa enggan atau malas berinteraksi dengan orang-orang di sekitar saya.',
                            'Saya mengalami gangguan tidur (sulit tidur, sering terbangun, atau tidur berlebihan).',
                            'Nafsu makan saya berubah drastis (sangat menurun atau justru makan berlebihan).',
                            'Saya merasa tubuh saya sangat berat, lelah, dan lemas sepanjang hari.',
                            'Saya mengalami keluhan fisik (seperti sakit kepala atau nyeri lambung) akibat stres.',
                            'Saya merasa sulit berkonsentrasi saat membaca, bekerja, atau menonton.',
                            'Saya membutuhkan waktu yang sangat lama hanya untuk mengambil keputusan kecil.',
                            'Saya merasa ingatan saya melemah atau sering melupakan hal-hal penting.',
                            'Pikiran saya dipenuhi oleh rasa bersalah atas hal-hal yang bukan sepenuhnya salah saya.',
                            'Saya merasa diri saya tidak berguna, tidak berharga, atau gagal sebagai manusia.',
                            'Muncul pikiran bahwa akan lebih baik jika saya tidak bangun lagi atau mati saja.',
                        ],
                        options: [
                            { value: 0, label: 'Tidak Pernah',  warna: '#10b981' },
                            { value: 1, label: 'Jarang',        warna: '#3a8a8c' },
                            { value: 2, label: 'Kadang-kadang', warna: '#7ec3ec' },
                            { value: 3, label: 'Sering',        warna: '#f97316' },
                            { value: 4, label: 'Selalu',        warna: '#e63946' },
                        ],
                        diagnosis: {
                            minimal:     { levelLabel: 'Normal (0-15)',    label: 'Kondisi Normal',          sublabel: 'Fluktuasi stres masih dalam batas wajar',       detail: 'Tidak ada indikasi depresi. Pertahankan pola hidup sehat.',                          warna: '#10b981' },
                            ringan:      { levelLabel: 'Ringan (16-30)',   label: 'Indikasi Depresi Ringan', sublabel: 'Ada gejala ringan yang perlu diperhatikan',     detail: 'Terdapat indikasi depresi ringan. Perhatikan kesehatan mental Anda.',               warna: '#3a8a8c' },
                            sedang:      { levelLabel: 'Sedang (31-50)',   label: 'Indikasi Depresi Sedang', sublabel: 'Gejala sedang terdeteksi',                       detail: 'Sangat disarankan untuk konseling dengan psikolog.',                               warna: '#f59e0b' },
                            cukup_berat: { levelLabel: 'Berat (51-80)',    label: 'Indikasi Depresi Berat',  sublabel: 'Segera cari bantuan profesional',               detail: 'Segera hubungi psikiater atau profesional kesehatan mental.',                       warna: '#e63946' },
                            berat:       { levelLabel: 'Risiko Tinggi ⚠️', label: 'Perhatian Khusus',        sublabel: 'Nomor 20 dijawab Sering/Selalu',                detail: 'Indikasi risiko tinggi terdeteksi. Segera cari bantuan profesional.',               warna: '#991b1b' },
                        },
                        impacts: [
                            { icon: '🌙', title: 'Tidur',                       description: 'Kualitas tidur menurun',                    level: 'Cukup Terdampak', score: 3, color: '#a855f7' },
                            { icon: '🍽️', title: 'Nafsu Makan',                 description: 'Perubahan drastis nafsu makan',              level: 'Cukup Terdampak', score: 3, color: '#f59e0b' },
                            { icon: '📚', title: 'Konsentrasi & Produktivitas', description: 'Sulit fokus dan menyelesaikan tugas',        level: 'Cukup Terdampak', score: 3, color: '#4090f2' },
                            { icon: '👥', title: 'Hubungan Sosial',             description: 'Enggan berinteraksi dengan orang sekitar',  level: 'Ringan',          score: 2, color: '#10b981' },
                            { icon: '💪', title: 'Energi Harian',               description: 'Merasa lemas dan tidak bersemangat',         level: 'Cukup Terdampak', score: 3, color: '#e63946' },
                        ],
                        recommendations: [
                            { icon: '🫁', title: 'Latihan Pernapasan',          description: 'Tarik napas dalam 4 detik, tahan 4 detik, hembuskan 4 detik', action: 'Mulai Latihan',  link: '/konsultasi', bg: '#eff6ff', buttonColor: '#4090f2' },
                            { icon: '📝', title: 'Tulis Jurnal Harian',          description: 'Tuangkan perasaan Anda setiap hari',                          action: 'Buka Jurnal',    link: '/jurnal',     bg: '#eff6ff', buttonColor: '#4090f2' },
                            { icon: '🚶', title: 'Jalan Pagi 15 Menit',          description: 'Aktivitas fisik ringan membantu meningkatkan mood',           action: 'Lihat Aktivitas', link: '/jadwal',     bg: '#eff6ff', buttonColor: '#4090f2' },
                            { icon: '💬', title: 'Bercerita ke Orang Dipercaya', description: 'Bicarakan yang mengganggu pikiran Anda',                      action: 'Tips Berbicara', link: '/konsultasi', bg: '#fef3c7', buttonColor: '#f59e0b' },
                        ],
                        whenToSeekHelp: [
                            'Perasaan sedih atau putus asa yang berlangsung lebih dari 2 minggu',
                            'Gejala mengganggu pekerjaan, sekolah, atau hubungan',
                            'Muncul pikiran untuk menyakiti diri sendiri atau orang lain',
                            'Nomor 20 (pikiran tentang kematian) dijawab Sering atau Selalu',
                            'Sulit menjalankan aktivitas dasar seperti makan, mandi, atau keluar rumah',
                            'Merasa tidak ada harapan atau tidak berdaya',
                        ],
                    },
                ];
            },

            checkProfile() {
                this.isProfileComplete = window.serverProfileComplete === true;
            },

            startTest(test) {
                if (!this.isProfileComplete) return;
                window.location.href = `/tes-mental/${test.id}/soal`;
            },

            shade(hex, percent) {
                const num = parseInt(hex.replace('#', ''), 16);
                const amt = Math.round(2.55 * percent);
                const R = Math.max(0, Math.min(255, (num >> 16) + amt));
                const G = Math.max(0, Math.min(255, ((num >> 8) & 0x00FF) + amt));
                const B = Math.max(0, Math.min(255, (num & 0x0000FF) + amt));
                return '#' + (0x1000000 + R * 0x10000 + G * 0x100 + B).toString(16).slice(1);
            },
        };
    };
</script>


<div x-data="testList()" x-init="init()" class="contents">

    <header class="flex flex-col sm:flex-row sm:items-start
                   sm:justify-between gap-4 flex-shrink-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-[#186369]">
                Tes Kesehatan Mental
            </h1>
            <p class="text-sm text-[#186369]/70 mt-1">
                Pilih tes untuk mengenali kondisi mentalmu
            </p>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto">
            <form role="search" class="relative flex-1 sm:w-72"
                  @submit.prevent="searchOpen = searchResults.length > 0">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-[#186369]/40 pointer-events-none">
                    <svg viewBox="0 0 20 20" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <circle cx="8.5" cy="8.5" r="5.5"/><path d="M13 13l4 4"/>
                    </svg>
                </div>
                <input type="search" x-model="searchQuery"
                       @input="searchOpen = searchQuery.trim().length > 0"
                       @focus="searchOpen = searchQuery.trim().length > 0"
                       placeholder="Cari aktivitas, dokter, artikel, atau fitur"
                       class="w-full pl-11 pr-4 py-2.5 text-xs font-bold bg-white
                              border-2 border-gray-200 rounded-full focus:outline-none
                              focus:border-[#186369]">
                <div x-show="searchOpen && searchQuery.trim()" x-cloak
                     @click.outside="searchOpen = false"
                     class="absolute left-0 right-0 top-full mt-2 z-40 bg-white rounded-2xl
                            border border-gray-100 shadow-xl p-2">
                    <template x-if="searchResults.length > 0">
                        <div class="space-y-1">
                            <template x-for="result in searchResults" :key="result.key">
                                <a :href="result.href" @click="searchOpen = false"
                                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-[#f5f8f7] transition">
                                    <span class="w-8 h-8 rounded-lg bg-[#186369]/10 text-[#186369]
                                                 flex items-center justify-center text-xs font-bold">⌕</span>
                                    <span class="flex-1 min-w-0">
                                        <span class="block text-xs font-bold text-[#186369] truncate" x-text="result.label"></span>
                                        <span class="block text-[10px] text-[#186369]/50" x-text="result.type"></span>
                                    </span>
                                    <span class="text-[#186369]/30">→</span>
                                </a>
                            </template>
                        </div>
                    </template>
                    <p x-show="searchResults.length === 0"
                       class="px-3 py-3 text-center text-xs font-bold text-[#186369]/50">
                        Tidak ada hasil
                    </p>
                </div>
            </form>

            <button type="button" @click="toggleProfile()"
                    :title="profileOpen ? 'Tutup profil' : 'Buka profil'"
                    class="relative flex-shrink-0 w-11 h-11 rounded-full overflow-hidden
                           border-2 border-white shadow-md hover:scale-105 active:scale-95 transition">
                <img src="{{ asset('images/avatar.png') }}" alt="Profil" class="w-full h-full object-cover">
                <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white"
                      :class="profileOpen ? 'bg-emerald-400' : 'bg-gray-300'"></span>
            </button>
        </div>
    </header>

    {{-- ═══ INFO PROFILE — kalau belum lengkap ═══ --}}
    <template x-if="!isProfileComplete">
        <div class="bg-amber-50 border-2 border-amber-200
                    rounded-2xl p-4 flex-shrink-0
                    flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-100
                        flex items-center justify-center flex-shrink-0">
                <svg viewBox="0 0 24 24" class="w-4 h-4 text-amber-600"
                     fill="none" stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 8v4M12 16h.01"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-xs font-bold text-amber-800">
                    Lengkapi profil dulu
                </p>
                <p class="text-[11px] text-amber-700/80 mt-0.5 leading-relaxed">
                    Isi nama, usia, dan gender di profil supaya hasil tes
                    bisa dipersonalisasi untukmu.
                </p>
            </div>
            <button type="button"
                    @click="profileOpen = true"
                    class="flex-shrink-0 text-[10px] font-bold
                           text-amber-700 bg-amber-100 hover:bg-amber-200
                           px-3 py-2 rounded-lg transition active:scale-95">
                Buka Profil →
            </button>
        </div>
    </template>

    {{-- ═══ LIST CARD TES ═══ --}}
    <div class="space-y-4 flex-1">

        <template x-for="(test, i) in tests" :key="test.id">
            <article class="card-animated
                            bg-white rounded-[32px]
                            p-3 sm:p-4
                            flex flex-col lg:flex-row gap-3 lg:gap-4
                            transition-all duration-300
                            hover:shadow-xl hover:shadow-[#186369]/8"
                     :style="`animation-delay: ${i * 80}ms;`">

                {{-- KIRI: warna card --}}
                <div class="relative lg:flex-[1.2] min-h-[200px] sm:min-h-[220px]
                            rounded-[24px]
                            p-5 sm:p-6
                            overflow-hidden text-white
                            flex flex-col justify-center"
                     :style="`background: linear-gradient(135deg,
                                 ${test.warna || '#186369'} 0%,
                                 ${shade(test.warna || '#186369', -15)} 100%);`">

                    <span class="self-start px-2.5 py-1 mb-3
                                 bg-white/20 backdrop-blur-sm
                                 text-[9px] font-bold uppercase tracking-widest
                                 rounded-md">
                        <span x-text="test.jenis"></span>
                    </span>

                    <h3 class="text-xl sm:text-2xl font-bold leading-tight"
                        x-text="test.judul"></h3>

                    <p class="text-sm sm:text-base mt-2 opacity-75
                              max-w-[58%] leading-relaxed"
                       x-text="test.gejala_singkat"></p>

                    <p class="text-[11px] sm:text-xs mt-3 opacity-60 font-bold">
                        <span x-text="(test.soal || []).length"></span> soal
                    </p>

                    <button type="button"
                            @click="startTest(test)"
                            :disabled="!isProfileComplete"
                            class="mt-4 self-start
                                   px-6 py-2.5
                                   bg-white/25 hover:bg-white/35
                                   backdrop-blur-sm
                                   rounded-2xl
                                   text-white font-bold text-sm
                                   transition active:scale-95
                                   disabled:opacity-40 disabled:cursor-not-allowed
                                   flex items-center gap-2">
                        <span x-text="isProfileComplete ? 'Mulai' : 'Lengkapi Profil'"></span>
                        <svg viewBox="0 0 16 16" class="w-3.5 h-3.5"
                             fill="none" stroke="currentColor"
                             stroke-width="2.5" stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="M3 8h10M9 4l4 4-4 4" />
                        </svg>
                    </button>

                    <img src="{{ asset('images/dashboard/cek-mental.png') }}"
                         alt="Ilustrasi"
                         class="absolute right-3 bottom-3
                                w-28 h-24 sm:w-36 sm:h-32
                                object-contain pointer-events-none select-none
                                drop-shadow-2xl">
                </div>

                {{-- KANAN: detail dari admin --}}
                <div class="flex-1 flex flex-col justify-center
                            px-4 sm:px-6 py-4 lg:py-2">

                    <span class="self-start
                                 px-3 py-1
                                 bg-[#186369]/10
                                 text-[#186369]
                                 text-[10px] font-bold uppercase tracking-wider
                                 rounded-md">
                        Detail
                    </span>

                    <p class="mt-3
                              text-xs sm:text-sm
                              text-[#186369]/70 leading-relaxed
                              line-clamp-3"
                       x-text="test.detail"></p>

                    <template x-if="test.gejala_list && test.gejala_list.length > 0">
                        <div class="mt-3">
                            <p class="text-[10px] font-bold text-[#186369]/50 uppercase tracking-wider mb-2">
                                Gejala yang Diperiksa
                            </p>
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="(g, gi) in test.gejala_list.slice(0, 4)" :key="gi">
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-bold border"
                                          :style="`background: ${test.warna || '#186369'}10;
                                                   color: ${test.warna || '#186369'};
                                                   border-color: ${test.warna || '#186369'}30;`"
                                          x-text="g"></span>
                                </template>
                                <span x-show="test.gejala_list.length > 4"
                                      class="px-2 py-1 rounded-lg text-[10px] font-bold
                                             bg-gray-100 text-[#186369]/50">
                                    +<span x-text="test.gejala_list.length - 4"></span>
                                </span>
                            </div>
                        </div>
                    </template>
                </div>
            </article>
        </template>

        <template x-if="tests.length === 0">
            <div class="text-center py-16">
                <p class="text-sm text-[#186369]/60">
                    Belum ada tes yang tersedia.
                </p>
            </div>
        </template>

    </div>

</div>
@endsection