@extends('layouts.app', ['activeNav' => 'tes'])

@section('title', 'Tes Kesehatan Mental')

@push('styles')
<style>
    .opt-circle {
        width: 38px; height: 38px;
        border-radius: 50%;
        border-width: 2px;
        border-style: solid;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        flex-shrink: 0;
    }
    .opt-circle:hover { transform: scale(1.15); }
    .opt-circle.selected {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .opt-circle.selected::after {
        content: '';
        position: absolute;
        inset: 4px;
        border-radius: 50%;
        background: white;
        opacity: 0.35;
    }

    @keyframes fade-in-up {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .fade-in-up { animation: fade-in-up 0.4s ease-out backwards; }

    @keyframes selected-pop {
        0% { transform: scale(1); }
        50% { transform: scale(1.3); }
        100% { transform: scale(1.1); }
    }
    .selected-pop { animation: selected-pop 0.35s ease-out; }
</style>
@endpush

@section('content')

<div x-data="testPage({{ $testId ?? 1 }})" x-init="init()" class="contents">

    {{-- ═══ HEADER ═══ --}}
    <header class="flex flex-col sm:flex-row sm:items-start
                   sm:justify-between gap-4 flex-shrink-0">

        <div class="flex items-start gap-3">
            <a href="/tes-mental"
               class="flex-shrink-0 w-10 h-10 rounded-full bg-white shadow-md
                      flex items-center justify-center
                      text-[#186369] hover:scale-105 transition active:scale-95 mt-1">
                <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none"
                     stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
            </a>

            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-[#186369]">
                    Tes Kesehatan Mental
                </h1>
                <p class="text-sm text-[#186369]/70 mt-1"
                   x-text="testData?.judul || 'Sedang memuat...'"></p>
            </div>
        </div>

        {{-- Counter halaman + tombol reset --}}
        <div class="flex-shrink-0 self-end sm:self-auto flex items-center gap-2">
            <button type="button"
                    x-show="hasAnyAnswer"
                    @click="resetProgress()"
                    title="Mulai ulang dari awal"
                    class="h-10 px-3 rounded-2xl bg-white shadow-sm
                           border border-gray-100
                           text-[10px] font-bold text-red-500
                           hover:bg-red-50 transition active:scale-95
                           flex items-center gap-1.5">
                <svg viewBox="0 0 16 16" class="w-3 h-3" fill="none"
                     stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 8a7 7 0 0113-3.5M15 8a7 7 0 01-13 3.5M14 1v4h-4M2 15v-4h4"/>
                </svg>
                Mulai Ulang
            </button>

            <div class="bg-white rounded-2xl px-4 py-2.5 shadow-sm border border-gray-100">
                <p class="text-[9px] font-bold text-[#186369]/50 uppercase tracking-wider text-center">
                    Halaman
                </p>
                <p class="text-lg font-bold text-[#186369] text-center leading-none mt-0.5">
                    <span x-text="currentPage + 1"></span>
                    <span class="text-gray-300">/</span>
                    <span class="text-gray-400" x-text="totalPages"></span>
                </p>
            </div>
        </div>
    </header>


    {{-- ═══ MAIN CARD ═══ --}}
    <div class="bg-white rounded-[40px] p-6 sm:p-8 lg:p-10
                shadow-xl shadow-black/5 border border-gray-100 flex-shrink-0">

        {{-- Progress bar --}}
        <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden mb-6">
            <div class="h-full rounded-full
                        bg-gradient-to-r from-[#186369] to-[#7ec3ec]
                        transition-all duration-500 ease-out"
                 :style="`width: ${progressPercent}%;`"></div>
        </div>

        {{-- Loading state --}}
        <template x-if="questions.length === 0">
            <div class="text-center py-12">
                <div class="inline-flex items-center gap-2 text-[#186369]/60">
                    <svg class="animate-spin w-5 h-5" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" opacity="0.25"/>
                        <path d="M12 2a10 10 0 0110 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span class="text-sm font-bold">Memuat soal...</span>
                </div>
            </div>
        </template>

        {{-- Deskripsi --}}
        <template x-if="deskripsiTes && questions.length > 0">
            <div class="bg-[#f5f8f7] rounded-2xl p-4 sm:p-5 mb-6 border-l-4 border-[#186369]">
                <p class="text-xs sm:text-sm text-[#186369]/80 leading-relaxed"
                   x-text="deskripsiTes"></p>
            </div>
        </template>

        {{-- Intro --}}
        <template x-if="questions.length > 0">
            <p class="text-sm sm:text-base font-bold text-[#186369]
                      leading-relaxed mb-6"
               x-text="intro"></p>
        </template>

        {{-- SOAL --}}
        <div class="space-y-6">
            <template x-for="(q, qi) in currentQuestions" :key="q.id">
                <div class="fade-in-up"
                     :style="`animation-delay: ${qi * 80}ms;`">

                    <div class="flex items-start gap-2 mb-3">
                        <span class="flex-shrink-0 font-bold text-[#186369] text-sm sm:text-base"
                              x-text="(q.id) + '.'"></span>
                        <p class="text-sm sm:text-base font-bold text-[#186369] leading-snug"
                           x-text="q.text"></p>
                    </div>

                    <div class="flex items-center gap-3 sm:gap-5 pl-5 sm:pl-7
                                overflow-x-auto scroll-thin pb-1">
                        <template x-for="opt in options" :key="opt.value">
                            <button type="button"
                                    @click="selectAnswer(q.id, opt.value)"
                                    :title="opt.label"
                                    class="opt-circle"
                                    :class="{ 'selected selected-pop': answers[q.id] === opt.value }"
                                    :style="answers[q.id] === opt.value
                                        ? `background: ${opt.warna}; border-color: ${opt.warna};`
                                        : `background: transparent; border-color: ${opt.warna};`">
                            </button>
                        </template>
                    </div>

                    <p x-show="answers[q.id] !== undefined"
                       x-transition
                       class="mt-2 pl-5 sm:pl-7 text-[10px] font-bold"
                       :style="`color: ${selectedOptionLabel(q.id).warna};`">
                        <span x-text="'→ ' + selectedOptionLabel(q.id).label"></span>
                    </p>
                </div>
            </template>
        </div>

        {{-- Footer Buttons --}}
        <template x-if="questions.length > 0">
            <div class="mt-8 pt-6 border-t border-gray-100
                        flex items-center justify-between gap-3">

                <button type="button" @click="prev()"
                        x-show="currentPage > 0"
                        class="px-5 py-3 rounded-2xl
                               bg-gray-100 text-[#186369] text-sm font-bold
                               hover:bg-gray-200 transition active:scale-95
                               flex items-center gap-2">
                    <svg viewBox="0 0 16 16" class="w-3.5 h-3.5" fill="none"
                         stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 12L6 8l4-4"/>
                    </svg>
                    Kembali
                </button>

                <button type="button" @click="next()"
                        :disabled="!canProceed"
                        class="px-7 py-3 rounded-2xl
                               bg-[#186369] text-white text-sm font-bold
                               hover:opacity-90 transition active:scale-95
                               disabled:opacity-40 disabled:cursor-not-allowed
                               flex items-center gap-2
                               shadow-lg shadow-[#186369]/20">
                    <span x-text="currentPage < totalPages - 1 ? 'Next' : 'Selesai'"></span>
                    <svg x-show="currentPage < totalPages - 1"
                         viewBox="0 0 16 16" class="w-3.5 h-3.5" fill="none"
                         stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 4l4 4-4 4"/>
                    </svg>
                    <svg x-show="currentPage >= totalPages - 1"
                         viewBox="0 0 16 16" class="w-3.5 h-3.5" fill="none"
                         stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 8l4 4 7-7"/>
                    </svg>
                </button>

            </div>
        </template>

        {{-- Legend --}}
        <template x-if="questions.length > 0">
            <div class="mt-8 pt-5 border-t border-gray-100">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">
                        Skala Jawaban
                    </p>
                    <p class="text-[10px] font-bold text-gray-400">
                        <span x-text="answeredOnCurrentPage"></span>
                        / <span x-text="currentQuestions.length"></span> dijawab
                    </p>
                </div>
                <div class="flex flex-wrap gap-x-4 gap-y-2">
                    <template x-for="opt in options" :key="opt.value">
                        <div class="flex items-center gap-1.5">
                            <div class="w-3 h-3 rounded-full flex-shrink-0"
                                 :style="`background: ${opt.warna};`"></div>
                            <span class="text-[10px] font-bold text-gray-600"
                                  x-text="opt.label"></span>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>

    <p class="text-[10px] text-gray-400 text-center mt-4 px-4">
        Hasil tes ini bukan diagnosis medis. Untuk bantuan lebih lanjut, konsultasikan dengan profesional.
    </p>

</div>

@endsection


@push('scripts')
<script>
    window.serverTest = @json($test);

    window.testPage = function (testId) {
        return {
            testId: testId,
            currentPage: 0,
            questionsPerPage: 4,
            answers: {},
            testData: null,

            intro: 'Selama 2 minggu terakhir, seberapa sering Anda mengalami hal berikut?',
            deskripsiTes: '',
            questions: [],
            options: [
                { value: 0, label: 'Tidak Pernah',  warna: '#10b981' },
                { value: 1, label: 'Jarang',        warna: '#3a8a8c' },
                { value: 2, label: 'Kadang-kadang', warna: '#7ec3ec' },
                { value: 3, label: 'Sering',        warna: '#f97316' },
                { value: 4, label: 'Selalu',        warna: '#e63946' },
            ],

            init() {
                this.loadTest();
            },

            loadTest() {
                const t = window.serverTest;
                if (!t) return;

                this.testData = t;

                if (t.lie_soal && t.lie_soal.length > 0) {
                    this.questions = this.buildCombinedQuestions(t);
                } else {
                    this.questions = (t.soal || []).map((text, i) => ({
                        id: i + 1, text: text,
                        isLie: false, weight: 1.0, originalNumber: i + 1,
                    }));
                }

                if (t.options && t.options.length) this.options = t.options;
                if (t.intro) this.intro = t.intro;
                if (t.deskripsi_tes) this.deskripsiTes = t.deskripsi_tes;
            },

            buildCombinedQuestions(t) {
                const soal = t.soal || [];
                const lie = t.lie_soal || [];
                const weights = t.critical_weights || { 19: 1.5, 20: 2.0 };

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
                        combined.push({
                            id: pos, text: lie[lieIdx],
                            isLie: true, weight: 0, originalNumber: null,
                        });
                        lieIdx++;
                    } else if (soalIdx < soal.length) {
                        const origNum = soalIdx + 1;
                        combined.push({
                            id: pos, text: soal[soalIdx],
                            isLie: false,
                            weight: weights[origNum] || 1.0,
                            originalNumber: origNum,
                        });
                        soalIdx++;
                    } else break;
                    pos++;
                }
                return combined;
            },

            /* ═══ RESET MANUAL — tombol "Mulai Ulang" ═══ */
            resetProgress() {
                if (!confirm('Hapus semua jawaban dan mulai dari awal?')) return;

                this.answers = {};
                this.currentPage = 0;

                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            seedDefault() {
                return [{
                    id: 1, jenis: 'Depresi', judul: 'Skrining Depresi',
                    gejala_singkat: 'Deteksi gejala depresi', warna: '#186369',
                    detail: 'Kuesioner 20 butir untuk skrining depresi.',
                    gejala_list: ['Perasaan sedih', 'Kesepian', 'Putus asa'],
                    deskripsi_tes: 'Silakan baca setiap pernyataan dengan cermat.',
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
                        'Saya mengalami gangguan tidur.',
                        'Nafsu makan saya berubah drastis.',
                        'Saya merasa tubuh saya sangat berat, lelah, dan lemas sepanjang hari.',
                        'Saya mengalami keluhan fisik akibat stres.',
                        'Saya merasa sulit berkonsentrasi.',
                        'Saya butuh waktu lama mengambil keputusan kecil.',
                        'Saya merasa ingatan saya melemah.',
                        'Saya merasa bersalah berlebihan.',
                        'Saya merasa tidak berguna atau gagal.',
                        'Muncul pikiran bahwa akan lebih baik jika saya tidak bangun lagi.',
                    ],
                    options: [
                        { value: 0, label: 'Tidak Pernah',  warna: '#10b981' },
                        { value: 1, label: 'Jarang',        warna: '#3a8a8c' },
                        { value: 2, label: 'Kadang-kadang', warna: '#7ec3ec' },
                        { value: 3, label: 'Sering',        warna: '#f97316' },
                        { value: 4, label: 'Selalu',        warna: '#e63946' },
                    ],
                    critical_weights: { 19: 1.5, 20: 2.0 },
                    lie_soal: [
                        'Saya tidak pernah marah kepada siapa pun sepanjang hidup saya.',
                        'Saya tidak pernah mengatakan hal yang tidak benar.',
                        'Saya selalu menepati janji saya, tidak peduli apapun.',
                    ],
                    lie_threshold: 2,
                    diagnosis: {
                        minimal:     { levelLabel: 'Normal',    label: 'Kondisi Normal',          sublabel: 'Fluktuasi stres wajar',       detail: 'Tidak ada indikasi depresi.',           warna: '#10b981' },
                        ringan:      { levelLabel: 'Ringan',    label: 'Indikasi Depresi Ringan', sublabel: 'Ada gejala ringan',           detail: 'Perhatikan kesehatan mental Anda.',      warna: '#3a8a8c' },
                        sedang:      { levelLabel: 'Sedang',    label: 'Indikasi Depresi Sedang', sublabel: 'Gejala sedang terdeteksi',     detail: 'Disarankan konseling dengan psikolog.',  warna: '#f59e0b' },
                        cukup_berat: { levelLabel: 'Berat',     label: 'Indikasi Depresi Berat',  sublabel: 'Segera cari bantuan',         detail: 'Hubungi psikiater atau profesional.',     warna: '#e63946' },
                        berat:       { levelLabel: 'Risiko',    label: 'Perhatian Khusus',        sublabel: 'Perlu bantuan segera',        detail: 'Segera cari bantuan profesional.',        warna: '#991b1b' },
                    },
                    impacts: [
                        { icon: '🌙', title: 'Tidur', description: 'Kualitas tidur menurun', level: 'Cukup Terdampak', score: 3, color: '#a855f7' },
                        { icon: '📚', title: 'Konsentrasi', description: 'Sulit fokus', level: 'Cukup Terdampak', score: 3, color: '#4090f2' },
                    ],
                    recommendations: [
                        { icon: '🫁', title: 'Latihan Pernapasan', description: 'Tarik napas dalam', action: 'Mulai', link: '/konsultasi', bg: '#eff6ff', buttonColor: '#4090f2' },
                    ],
                    whenToSeekHelp: [
                        'Perasaan sedih lebih dari 2 minggu',
                        'Gejala mengganggu aktivitas',
                        'Muncul pikiran menyakiti diri sendiri',
                    ],
                }];
            },

            get totalPages() {
                return Math.max(1, Math.ceil(this.questions.length / this.questionsPerPage));
            },

            get currentQuestions() {
                const start = this.currentPage * this.questionsPerPage;
                return this.questions.slice(start, start + this.questionsPerPage);
            },

            get progressPercent() {
                return ((this.currentPage + 1) / this.totalPages) * 100;
            },

            /* ═══ Ada jawaban minimal 1? — untuk tombol "Mulai Ulang" ═══ */
            get hasAnyAnswer() {
                return Object.keys(this.answers).length > 0;
            },

            hasAnswer(qid) {
                return this.answers[qid] !== undefined;
            },

            selectAnswer(qid, value) {
                this.answers[qid] = value;
            },

            selectedOptionLabel(qid) {
                const val = this.answers[qid];
                return this.options.find(o => o.value === val) || { label: '', warna: '#186369' };
            },

            get answeredOnCurrentPage() {
                return this.currentQuestions.filter(q => this.hasAnswer(q.id)).length;
            },

            get canProceed() {
                return this.currentQuestions.every(q => this.hasAnswer(q.id));
            },

            next() {
                if (!this.canProceed) return;

                if (this.currentPage < this.totalPages - 1) {
                    this.currentPage++;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    fetch('{{ route('tes-mental.submit', ['id' => $testId]) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ answers: this.answers }),
                    }).then(response => {
                        if (!response.ok) throw new Error('Gagal menyimpan jawaban.');
                        window.location.href = response.url;
                    }).catch(() => alert('Jawaban belum berhasil disimpan. Silakan coba lagi.'));
                }
            },

            prev() {
                if (this.currentPage > 0) {
                    this.currentPage--;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },
        };
    };
</script>
@endpush