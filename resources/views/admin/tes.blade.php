@extends('layouts.admin', ['activeNav' => 'tes'])

@section('title', 'Kelola Tes Mental')

@section('content')

<div x-data="adminTes()" x-init="load()" class="flex flex-col gap-5 max-w-6xl mx-auto">

    <header class="flex-shrink-0">
        <h1 class="text-2xl sm:text-3xl font-bold text-[#186369]">Kelola Tes Mental</h1>
        <p class="text-sm text-[#186369]/70 mt-1">
            Konfigurasi tes kesehatan mental yang tampil di halaman user
        </p>
    </header>

    {{-- ═══ FORM ═══ --}}
    <div class="bg-white rounded-[32px] p-6 border border-gray-100 flex-shrink-0">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-[#186369]"
                x-text="editId ? 'Edit Tes' : 'Tambah Tes'"></h2>
            <button type="button" @click="reset()" x-show="editId"
                    class="text-[10px] font-bold text-red-500 hover:text-red-600">
                Batal Edit
            </button>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-1 overflow-x-auto pb-2 mb-5 border-b border-gray-200 scroll-thin-h">
            <template x-for="tab in tabs" :key="tab.id">
                <button type="button" @click="currentTab = tab.id"
                        :class="currentTab === tab.id
                            ? 'text-[#186369] border-[#186369]'
                            : 'text-gray-400 border-transparent hover:text-[#186369]'"
                        class="px-4 py-2 text-xs font-bold border-b-2 transition
                               whitespace-nowrap flex-shrink-0">
                    <span x-text="tab.label"></span>
                </button>
            </template>
        </div>

        <form @submit.prevent="save()" class="space-y-4">

            {{-- ═══ TAB: INFO DASAR ═══ --}}
            <div x-show="currentTab === 'info'" class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">
                        Jenis (badge) <span class="text-red-400">*</span>
                    </label>
                    <input type="text" x-model="form.jenis" required
                           placeholder="Contoh: Depresi"
                           class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl
                                  focus:outline-none focus:border-[#186369]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Warna Tema</label>
                    <div class="flex gap-2 items-center">
                        <input type="color" x-model="form.warna"
                               class="w-12 h-10 rounded-lg cursor-pointer border-0">
                        <input type="text" x-model="form.warna"
                               class="flex-1 px-4 py-2 border-2 border-gray-200 rounded-xl text-sm">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 mb-1">
                        Judul Tes <span class="text-red-400">*</span>
                    </label>
                    <input type="text" x-model="form.judul" required
                           placeholder="Contoh: Skrining Depresi"
                           class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl
                                  focus:outline-none focus:border-[#186369]">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 mb-1">
                        Gejala Singkat <span class="text-gray-400 font-normal">(kiri card)</span>
                    </label>
                    <textarea x-model="form.gejala_singkat" rows="2"
                              placeholder="Deteksi gejala depresi melalui 20 pertanyaan..."
                              class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl resize-none
                                     focus:outline-none focus:border-[#186369]"></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 mb-1">
                        Detail <span class="text-gray-400 font-normal">(kanan card)</span>
                    </label>
                    <textarea x-model="form.detail" rows="3"
                              placeholder="Kuesioner ini menilai tingkat depresi..."
                              class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl resize-none
                                     focus:outline-none focus:border-[#186369]"></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 mb-1">
                        Gejala yang Diperiksa
                        <span class="text-gray-400 font-normal">(1 baris = 1 gejala)</span>
                    </label>
                    <textarea x-model="form.gejalaListText" rows="4"
                              placeholder="Perasaan sedih&#10;Kesepian&#10;Putus asa"
                              class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl resize-none
                                     focus:outline-none focus:border-[#186369]"></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 mb-1">
                        Deskripsi Tes <span class="text-gray-400 font-normal">(halaman soal)</span>
                    </label>
                    <textarea x-model="form.deskripsi_tes" rows="2"
                              placeholder="Silakan baca setiap pernyataan dengan cermat..."
                              class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl resize-none
                                     focus:outline-none focus:border-[#186369]"></textarea>
                </div>
            </div>

            {{-- ═══ TAB: SOAL & OPSI ═══ --}}
            <div x-show="currentTab === 'soal'" class="space-y-4">

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">
                        Intro <span class="text-gray-400 font-normal">(di atas pertanyaan)</span>
                    </label>
                    <input type="text" x-model="form.intro"
                           placeholder="Selama 2 minggu terakhir..."
                           class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl
                                  focus:outline-none focus:border-[#186369]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1 flex items-center justify-between">
                        <span>Daftar Soal (1 baris = 1 soal)</span>
                        <span class="text-[#186369]">
                            <span x-text="soalCount"></span> soal
                        </span>
                    </label>
                    <textarea x-model="form.soalText" rows="16"
                              placeholder="Saya merasa sedih atau murung tanpa alasan yang jelas.&#10;Saya merasa sangat kesepian..."
                              class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl resize-none
                                     focus:outline-none focus:border-[#186369] font-mono text-xs"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2">
                        Opsi Jawaban <span class="text-gray-400 font-normal">(5 opsi)</span>
                    </label>
                    <div class="space-y-2">
                        <template x-for="(opt, i) in form.options" :key="i">
                            <div class="flex gap-2 items-center">
                                <input type="color" x-model="opt.warna"
                                       class="w-10 h-9 rounded cursor-pointer border-0 flex-shrink-0">
                                <span class="text-xs font-bold text-gray-400 w-8 text-center"
                                      x-text="'#' + i"></span>
                                <input type="number" x-model.number="opt.value" min="0"
                                       class="w-14 px-2 py-1.5 border-2 border-gray-200 rounded-lg
                                              text-xs text-center font-bold">
                                <input type="text" x-model="opt.label"
                                       class="flex-1 px-3 py-1.5 border-2 border-gray-200 rounded-lg
                                              text-xs font-bold">
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- ═══ TAB: VALIDITAS ═══ --}}
            <div x-show="currentTab === 'validitas'" class="space-y-4">

                <div class="bg-purple-50 border border-purple-200 rounded-xl px-4 py-3">
                    <p class="text-[11px] text-purple-700 leading-relaxed">
                        🧪 <b>Validitas Skala (Anti-Manipulasi)</b><br>
                        Sisipkan soal jebakan untuk mendeteksi user yang menjawab tidak jujur.
                        Soal jebakan tidak dihitung dalam skor depresi, tapi jadi indikator validitas.
                    </p>
                </div>

                {{-- Bobot Soal Kritis --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">
                        Bobot Soal Kritis <span class="text-gray-400 font-normal">(default: 19 &amp; 20 → 1.5x)</span>
                    </label>
                    <div class="flex gap-2 mb-2">
                        <input type="text"
                               x-model="form.criticalWeightsText"
                               placeholder="19:1.5, 20:2.0"
                               class="flex-1 px-4 py-2 border-2 border-gray-200 rounded-xl text-sm
                                      font-mono focus:outline-none focus:border-[#186369]">
                        <button type="button" @click="form.criticalWeightsText = '19:1.5, 20:2.0'"
                                class="px-3 py-2 rounded-xl bg-[#186369]/10 text-[#186369] text-xs font-bold
                                       hover:bg-[#186369]/20 transition whitespace-nowrap">
                            Reset Default
                        </button>
                    </div>
                    <p class="text-[10px] text-gray-400">
                        Format: <code class="bg-gray-100 px-1 rounded">nomorSoal:bobot, nomorSoal:bobot</code>
                    </p>
                </div>

                {{-- Lie Questions --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">
                        Soal Jebakan (Lie Scale)
                        <span class="text-gray-400 font-normal">(1 baris = 1 soal)</span>
                    </label>
                    <textarea x-model="form.lieText" rows="4"
                              placeholder="Saya tidak pernah marah atau kesal kepada siapa pun sepanjang hidup saya.&#10;Saya tidak pernah mengatakan hal yang tidak benar, bahkan sekecil apapun.&#10;Saya selalu menepati janji saya, tidak peduli apapun yang terjadi."
                              class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl resize-none
                                     focus:outline-none focus:border-[#186369] font-mono text-xs"></textarea>
                    <p class="text-[10px] text-gray-400 mt-1">
                        <span x-text="lieCount"></span> soal jebakan
                    </p>
                </div>

                {{-- Threshold --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">
                        Threshold Invalid
                        <span class="text-gray-400 font-normal">(min. jawaban "Sering/Selalu" untuk dianggap tidak valid)</span>
                    </label>
                    <input type="number" min="1" max="10" x-model.number="form.lieThreshold"
                           class="w-32 px-4 py-2 border-2 border-gray-200 rounded-xl text-sm
                                  focus:outline-none focus:border-[#186369]">
                    <p class="text-[10px] text-gray-400 mt-1">
                        Contoh: threshold = 2 → user dianggap <b>INVALID</b> kalau jawab "Sering/Selalu"
                        di 2 soal jebakan atau lebih.
                    </p>
                </div>

                {{-- Info Misfit --}}
                <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
                    <p class="text-[11px] text-amber-700 leading-relaxed">
                        🔍 <b>Misfit Detection</b> (otomatis): Kalau user jawab "Selalu" pada soal
                        ringan (No. 1-10) tapi "Tidak Pernah" pada soal berat (No. 19-20),
                        sistem akan menandai pola jawaban sebagai <b>tidak konsisten</b>.
                    </p>
                </div>
            </div>

            {{-- ═══ TAB: DIAGNOSIS ═══ --}}
            <div x-show="currentTab === 'diagnosis'" class="space-y-4">

                <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3">
                    <p class="text-[11px] text-blue-700 leading-relaxed">
                        💡 Range skor untuk 20 soal (0-80):
                        <b>0-15 Normal</b> · <b>16-30 Ringan</b> ·
                        <b>31-50 Sedang</b> · <b>51-80 Berat</b><br>
                        ⚠️ <b>Risiko Tinggi</b> jika soal No. 20 dijawab Sering/Selalu.
                    </p>
                </div>

                <template x-for="(lvl, key) in form.diagnosis" :key="key">
                    <div class="border-2 border-gray-100 rounded-2xl p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-4 h-4 rounded-full" :style="`background: ${lvl.warna};`"></div>
                            <p class="text-xs font-bold text-[#186369] uppercase tracking-wider"
                               x-text="lvl.levelLabel"></p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 mb-1">
                                    Label Diagnosis
                                </label>
                                <input type="text" x-model="lvl.label"
                                       class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 mb-1">Warna</label>
                                <div class="flex gap-2">
                                    <input type="color" x-model="lvl.warna"
                                           class="w-12 h-9 rounded cursor-pointer border-0">
                                    <input type="text" x-model="lvl.warna"
                                           class="flex-1 px-3 py-2 border-2 border-gray-200 rounded-xl text-xs">
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-bold text-gray-500 mb-1">
                                    Sub-label
                                </label>
                                <input type="text" x-model="lvl.sublabel"
                                       class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl text-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-bold text-gray-500 mb-1">
                                    Detail Paragraf
                                </label>
                                <textarea x-model="lvl.detail" rows="3"
                                          class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl
                                                 text-sm resize-none"></textarea>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- ═══ TAB: DAMPAK ═══ --}}
            <div x-show="currentTab === 'dampak'" class="space-y-3">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold text-gray-500">Dampak pada Kehidupan</p>
                    <button type="button" @click="addImpact()"
                            class="text-[10px] font-bold text-[#186369] bg-[#186369]/10
                                   hover:bg-[#186369]/20 px-3 py-1.5 rounded-lg transition">
                        + Tambah
                    </button>
                </div>

                <template x-if="form.impacts.length === 0">
                    <p class="text-[11px] text-gray-400 italic text-center py-6
                              bg-[#f5f8f7] rounded-2xl border-2 border-dashed border-gray-200">
                        Belum ada dampak.
                    </p>
                </template>

                <template x-for="(imp, i) in form.impacts" :key="i">
                    <div class="border-2 border-gray-100 rounded-2xl p-4">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                            <div class="md:col-span-1">
                                <label class="block text-[10px] font-bold text-gray-500 mb-1">Icon</label>
                                <input type="text" x-model="imp.icon" maxlength="4"
                                       class="w-full px-2 py-2 border-2 border-gray-200 rounded-xl
                                              text-center text-xl">
                            </div>
                            <div class="md:col-span-5">
                                <label class="block text-[10px] font-bold text-gray-500 mb-1">Judul</label>
                                <input type="text" x-model="imp.title"
                                       class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl text-sm">
                            </div>
                            <div class="md:col-span-4">
                                <label class="block text-[10px] font-bold text-gray-500 mb-1">Level</label>
                                <input type="text" x-model="imp.level"
                                       class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl text-sm">
                            </div>
                            <div class="md:col-span-2 flex items-end gap-2">
                                <div class="flex-1">
                                    <label class="block text-[10px] font-bold text-gray-500 mb-1">Skor</label>
                                    <input type="number" min="0" max="4" x-model.number="imp.score"
                                           class="w-full px-2 py-2 border-2 border-gray-200 rounded-xl text-sm text-center">
                                </div>
                                <input type="color" x-model="imp.color"
                                       class="w-10 h-10 rounded cursor-pointer border-0 flex-shrink-0">
                            </div>
                            <div class="md:col-span-11">
                                <label class="block text-[10px] font-bold text-gray-500 mb-1">Deskripsi</label>
                                <input type="text" x-model="imp.description"
                                       class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl text-sm">
                            </div>
                            <div class="md:col-span-1 flex items-end">
                                <button type="button" @click="form.impacts.splice(i, 1)"
                                        class="w-10 h-10 rounded-lg bg-red-50 text-red-500
                                               hover:bg-red-500 hover:text-white
                                               flex items-center justify-center transition">
                                    <svg viewBox="0 0 16 16" class="w-3 h-3" fill="none"
                                         stroke="currentColor" stroke-width="2.5">
                                        <path d="M4 4l8 8M12 4l-8 8"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- ═══ TAB: REKOMENDASI ═══ --}}
            <div x-show="currentTab === 'rekomendasi'" class="space-y-3">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold text-gray-500">Rekomendasi untuk User</p>
                    <button type="button" @click="addRecommendation()"
                            class="text-[10px] font-bold text-[#186369] bg-[#186369]/10
                                   hover:bg-[#186369]/20 px-3 py-1.5 rounded-lg transition">
                        + Tambah
                    </button>
                </div>

                <template x-if="form.recommendations.length === 0">
                    <p class="text-[11px] text-gray-400 italic text-center py-6
                              bg-[#f5f8f7] rounded-2xl border-2 border-dashed border-gray-200">
                        Belum ada rekomendasi.
                    </p>
                </template>

                <template x-for="(rec, i) in form.recommendations" :key="i">
                    <div class="border-2 border-gray-100 rounded-2xl p-4">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                            <div class="md:col-span-1">
                                <label class="block text-[10px] font-bold text-gray-500 mb-1">Icon</label>
                                <input type="text" x-model="rec.icon" maxlength="4"
                                       class="w-full px-2 py-2 border-2 border-gray-200 rounded-xl
                                              text-center text-xl">
                            </div>
                            <div class="md:col-span-6">
                                <label class="block text-[10px] font-bold text-gray-500 mb-1">Judul</label>
                                <input type="text" x-model="rec.title"
                                       class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl text-sm">
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-[10px] font-bold text-gray-500 mb-1">Action</label>
                                <input type="text" x-model="rec.action"
                                       class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl text-sm">
                            </div>
                            <div class="md:col-span-2 flex items-end">
                                <button type="button" @click="form.recommendations.splice(i, 1)"
                                        class="w-full h-10 rounded-lg bg-red-50 text-red-500
                                               hover:bg-red-500 hover:text-white
                                               flex items-center justify-center transition">
                                    <svg viewBox="0 0 16 16" class="w-3 h-3" fill="none"
                                         stroke="currentColor" stroke-width="2.5">
                                        <path d="M4 4l8 8M12 4l-8 8"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="md:col-span-8">
                                <label class="block text-[10px] font-bold text-gray-500 mb-1">Deskripsi</label>
                                <input type="text" x-model="rec.description"
                                       class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl text-sm">
                            </div>
                            <div class="md:col-span-4">
                                <label class="block text-[10px] font-bold text-gray-500 mb-1">Link</label>
                                <input type="text" x-model="rec.link"
                                       class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl text-sm">
                            </div>
                            <div class="md:col-span-6">
                                <label class="block text-[10px] font-bold text-gray-500 mb-1">BG Color</label>
                                <div class="flex gap-2">
                                    <input type="color" x-model="rec.bg"
                                           class="w-12 h-9 rounded cursor-pointer border-0">
                                    <input type="text" x-model="rec.bg"
                                           class="flex-1 px-3 py-2 border-2 border-gray-200 rounded-xl text-xs">
                                </div>
                            </div>
                            <div class="md:col-span-6">
                                <label class="block text-[10px] font-bold text-gray-500 mb-1">Button Color</label>
                                <div class="flex gap-2">
                                    <input type="color" x-model="rec.buttonColor"
                                           class="w-12 h-9 rounded cursor-pointer border-0">
                                    <input type="text" x-model="rec.buttonColor"
                                           class="flex-1 px-3 py-2 border-2 border-gray-200 rounded-xl text-xs">
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- ═══ TAB: KAPAN BANTUAN ═══ --}}
            <div x-show="currentTab === 'bantuan'" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">
                        Kapan Perlu Mencari Bantuan
                        <span class="text-gray-400 font-normal">(1 baris = 1 poin)</span>
                    </label>
                    <textarea x-model="form.whenHelpText" rows="8"
                              placeholder="Perasaan sedih yang berlangsung lebih dari 2 minggu&#10;Nomor 20 dijawab Sering atau Selalu"
                              class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl resize-none
                                     focus:outline-none focus:border-[#186369] font-mono text-xs"></textarea>
                </div>
            </div>

            {{-- BUTTONS --}}
            <div class="pt-4 border-t border-gray-100 flex flex-wrap gap-3">
                <button type="submit"
                        class="px-6 py-2.5 bg-[#186369] text-white font-bold rounded-xl
                               hover:opacity-90 transition active:scale-95">
                    <span x-text="editId ? 'Simpan Perubahan' : 'Tambah Tes'"></span>
                </button>
                <button type="button" @click="reset()" x-show="editId"
                        class="px-6 py-2.5 bg-gray-100 text-gray-600 font-bold rounded-xl
                               hover:bg-gray-200 transition active:scale-95">
                    Reset Form
                </button>
            </div>
        </form>
    </div>

    {{-- ═══ LIST TES ═══ --}}
    <div class="bg-white rounded-[32px] p-6 border border-gray-100">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-[#186369]">Daftar Tes</h2>
            <span class="text-xs font-bold text-gray-500">
                <span x-text="tesList.length"></span> tes
            </span>
        </div>

        <template x-if="tesList.length === 0">
            <div class="text-center py-12 text-gray-400">
                <p class="text-sm">Belum ada tes.</p>
            </div>
        </template>

        <div class="space-y-3">
            <template x-for="(t, i) in tesList" :key="t.id">
                <div class="rounded-2xl border-2 border-gray-100 p-4
                            hover:shadow-md transition-all
                            flex items-start gap-4"
                     :style="`border-left: 4px solid ${t.warna || '#186369'};`">

                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0"
                         :style="`background: ${t.warna || '#186369'}20;
                                  color: ${t.warna || '#186369'};`">
                        <svg viewBox="0 0 24 24" class="w-6 h-6" fill="none"
                             stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9.5 2A6.5 6.5 0 003 8.5c0 4 4 8 9 13 5-5 9-9 9-13A6.5 6.5 0 009.5 2z"/>
                        </svg>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider"
                                  :style="`background: ${t.warna || '#186369'}20;
                                           color: ${t.warna || '#186369'};`"
                                  x-text="t.jenis"></span>
                            <p class="font-bold text-sm text-[#186369]" x-text="t.judul"></p>
                        </div>

                        <p class="text-[10px] text-gray-500 mt-0.5">
                            <span x-text="(t.soal || []).length"></span> soal
                            <template x-if="t.lie_soal && t.lie_soal.length">
                                <span> + <span x-text="t.lie_soal.length"></span> jebakan</span>
                            </template>
                            · <span x-text="(t.impacts || []).length"></span> dampak
                            · <span x-text="(t.recommendations || []).length"></span> rekomendasi
                        </p>

                        <p class="text-[10px] text-gray-400 mt-1 line-clamp-2"
                           x-text="t.gejala_singkat || t.detail"></p>
                    </div>

                    <div class="flex gap-2 flex-shrink-0">
                        <button @click="edit(t)"
                                class="px-3 py-1.5 bg-blue-500 text-white text-xs font-bold
                                       rounded-lg hover:bg-blue-600 transition">
                            Edit
                        </button>
                        <button @click="hapus(t.id)"
                                class="px-3 py-1.5 bg-red-500 text-white text-xs font-bold
                                       rounded-lg hover:bg-red-600 transition">
                            Hapus
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>


@push('scripts')
<script>
    function adminTes() {
        return {
            tesList: [],
            editId: null,
            currentTab: 'info',

            tabs: [
                { id: 'info',        label: 'Info Dasar' },
                { id: 'soal',        label: 'Soal & Opsi' },
                { id: 'validitas',   label: 'Validitas' },
                { id: 'diagnosis',   label: 'Diagnosis' },
                { id: 'dampak',      label: 'Dampak' },
                { id: 'rekomendasi', label: 'Rekomendasi' },
                { id: 'bantuan',     label: 'Kapan Bantuan' },
            ],

            form: null,

            getDefaultForm() {
                return {
                    jenis: '',
                    judul: '',
                    gejala_singkat: '',
                    warna: '#186369',
                    detail: '',
                    gejalaListText: '',

                    deskripsi_tes: '',
                    intro: 'Selama 2 minggu terakhir, seberapa sering Anda mengalami hal berikut?',
                    soalText: '',

                    // Validitas
                    criticalWeightsText: '19:1.5, 20:2.0',
                    lieText: 'Saya tidak pernah marah atau kesal kepada siapa pun sepanjang hidup saya.\nSaya tidak pernah mengatakan hal yang tidak benar, bahkan sekecil apapun.\nSaya selalu menepati janji saya, tidak peduli apapun yang terjadi.',
                    lieThreshold: 2,

                    options: [
                        { value: 0, label: 'Tidak Pernah',  warna: '#10b981' },
                        { value: 1, label: 'Jarang',        warna: '#3a8a8c' },
                        { value: 2, label: 'Kadang-kadang', warna: '#7ec3ec' },
                        { value: 3, label: 'Sering',        warna: '#f97316' },
                        { value: 4, label: 'Selalu',        warna: '#e63946' },
                    ],

                    diagnosis: {
                        minimal:     { levelLabel: 'Normal (0-15)',    label: '', sublabel: '', detail: '', warna: '#10b981' },
                        ringan:      { levelLabel: 'Ringan (16-30)',   label: '', sublabel: '', detail: '', warna: '#3a8a8c' },
                        sedang:      { levelLabel: 'Sedang (31-50)',   label: '', sublabel: '', detail: '', warna: '#f59e0b' },
                        cukup_berat: { levelLabel: 'Berat (51-80)',    label: '', sublabel: '', detail: '', warna: '#e63946' },
                        berat:       { levelLabel: 'Risiko Tinggi ⚠️', label: '', sublabel: '', detail: '', warna: '#991b1b' },
                    },

                    impacts: [],
                    recommendations: [],
                    whenHelpText: '',
                };
            },

            defaults: [
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
                    // ⬇️ Validitas
                    critical_weights: { 19: 1.5, 20: 2.0 },
                    lie_soal: [
                        'Saya tidak pernah marah atau kesal kepada siapa pun sepanjang hidup saya.',
                        'Saya tidak pernah mengatakan hal yang tidak benar, bahkan sekecil apapun.',
                        'Saya selalu menepati janji saya, tidak peduli apapun yang terjadi.',
                    ],
                    lie_threshold: 2,
                    // Diagnosis
                    diagnosis: {
                        minimal:     { levelLabel: 'Normal (0-15)',    label: 'Kondisi Normal',               sublabel: 'Fluktuasi stres masih dalam batas wajar',        detail: 'Tidak ada indikasi depresi. Pertahankan pola hidup sehat.',                          warna: '#10b981' },
                        ringan:      { levelLabel: 'Ringan (16-30)',   label: 'Indikasi Depresi Ringan',      sublabel: 'Ada gejala ringan yang perlu diperhatikan',      detail: 'Terdapat indikasi depresi ringan. Perhatikan kesehatan mental Anda.',               warna: '#3a8a8c' },
                        sedang:      { levelLabel: 'Sedang (31-50)',   label: 'Indikasi Depresi Sedang',      sublabel: 'Gejala sedang terdeteksi',                        detail: 'Sangat disarankan untuk konseling dengan psikolog.',                               warna: '#f59e0b' },
                        cukup_berat: { levelLabel: 'Berat (51-80)',    label: 'Indikasi Depresi Berat',       sublabel: 'Segera cari bantuan profesional',                 detail: 'Segera hubungi psikiater atau profesional kesehatan mental.',                       warna: '#e63946' },
                        berat:       { levelLabel: 'Risiko Tinggi ⚠️', label: 'Perhatian Khusus',             sublabel: 'Nomor 20 dijawab Sering/Selalu',                  detail: 'Indikasi risiko tinggi terdeteksi. Segera cari bantuan profesional.',               warna: '#991b1b' },
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
            ],

            load() {
                try {
                    const s = localStorage.getItem('mindo_tes');
                    if (s) {
                        const parsed = JSON.parse(s);
                        this.tesList = Array.isArray(parsed) && parsed.length ? parsed : [...this.defaults];
                    } else {
                        this.tesList = [...this.defaults];
                        localStorage.setItem('mindo_tes', JSON.stringify(this.defaults));
                    }
                } catch(e) {
                    console.error('Load error:', e);
                    this.tesList = [...this.defaults];
                }
                this.reset();
            },

            saveToStorage() {
                localStorage.setItem('mindo_tes', JSON.stringify(this.tesList));
            },

            get soalCount() {
                if (!this.form || !this.form.soalText) return 0;
                return this.form.soalText.split('\n').filter(s => s.trim()).length;
            },

            get lieCount() {
                if (!this.form || !this.form.lieText) return 0;
                return this.form.lieText.split('\n').filter(s => s.trim()).length;
            },

            save() {
                if (!this.form.judul || !this.form.jenis) {
                    alert('Isi minimal: Jenis dan Judul');
                    return;
                }

                const soalArray = (this.form.soalText || '')
                    .split('\n').map(s => s.trim()).filter(s => s);
                const gejalaArray = (this.form.gejalaListText || '')
                    .split('\n').map(s => s.trim()).filter(s => s);
                const whenHelpArray = (this.form.whenHelpText || '')
                    .split('\n').map(s => s.trim()).filter(s => s);
                const lieArray = (this.form.lieText || '')
                    .split('\n').map(s => s.trim()).filter(s => s);

                // Parse bobot soal kritis
                const criticalWeights = {};
                (this.form.criticalWeightsText || '').split(',').forEach(pair => {
                    const [num, w] = pair.split(':').map(s => s.trim());
                    if (num && w) criticalWeights[Number(num)] = parseFloat(w) || 1.0;
                });

                const data = {
                    jenis: this.form.jenis,
                    judul: this.form.judul,
                    gejala_singkat: this.form.gejala_singkat,
                    warna: this.form.warna,
                    detail: this.form.detail,
                    gejala_list: gejalaArray,

                    deskripsi_tes: this.form.deskripsi_tes,
                    intro: this.form.intro,
                    soal: soalArray,
                    options: JSON.parse(JSON.stringify(this.form.options)),

                    // Validitas
                    critical_weights: criticalWeights,
                    lie_soal: lieArray,
                    lie_threshold: this.form.lieThreshold || 2,

                    diagnosis: JSON.parse(JSON.stringify(this.form.diagnosis)),
                    impacts: JSON.parse(JSON.stringify(this.form.impacts)),
                    recommendations: JSON.parse(JSON.stringify(this.form.recommendations)),
                    whenToSeekHelp: whenHelpArray,
                };

                if (this.editId) {
                    const idx = this.tesList.findIndex(t => t.id === this.editId);
                    if (idx !== -1) this.tesList[idx] = { ...data, id: this.editId };
                } else {
                    const newId = this.tesList.length
                        ? Math.max(...this.tesList.map(t => t.id)) + 1 : 1;
                    this.tesList.push({ ...data, id: newId });
                }

                this.saveToStorage();
                this.reset();
                alert('✅ Tes berhasil disimpan');
            },

            edit(t) {
                this.editId = t.id;
                this.currentTab = 'info';

                this.form = {
                    jenis: t.jenis || '',
                    judul: t.judul || '',
                    gejala_singkat: t.gejala_singkat || '',
                    warna: t.warna || '#186369',
                    detail: t.detail || '',
                    gejalaListText: (t.gejala_list || []).join('\n'),

                    deskripsi_tes: t.deskripsi_tes || '',
                    intro: t.intro || this.getDefaultForm().intro,
                    soalText: (t.soal || []).join('\n'),

                    // Validitas
                    criticalWeightsText: t.critical_weights
                        ? Object.entries(t.critical_weights).map(([k, v]) => `${k}:${v}`).join(', ')
                        : '19:1.5, 20:2.0',
                    lieText: (t.lie_soal || []).join('\n') || this.getDefaultForm().lieText,
                    lieThreshold: t.lie_threshold || 2,

                    options: t.options && t.options.length
                        ? JSON.parse(JSON.stringify(t.options))
                        : this.getDefaultForm().options,

                    diagnosis: t.diagnosis
                        ? JSON.parse(JSON.stringify(t.diagnosis))
                        : this.getDefaultForm().diagnosis,

                    impacts: t.impacts
                        ? JSON.parse(JSON.stringify(t.impacts))
                        : [],
                    recommendations: t.recommendations
                        ? JSON.parse(JSON.stringify(t.recommendations))
                        : [],
                    whenHelpText: (t.whenToSeekHelp || []).join('\n'),
                };

                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            hapus(id) {
                if (!confirm('Yakin hapus tes ini?')) return;
                this.tesList = this.tesList.filter(t => t.id !== id);
                this.saveToStorage();
                if (this.editId === id) this.reset();
            },

            reset() {
                this.editId = null;
                this.currentTab = 'info';
                this.form = this.getDefaultForm();
            },

            addImpact() {
                this.form.impacts.push({
                    icon: '✨',
                    title: '',
                    description: '',
                    level: 'Ringan',
                    score: 2,
                    color: '#186369',
                });
            },

            addRecommendation() {
                this.form.recommendations.push({
                    icon: '💡',
                    title: '',
                    description: '',
                    action: 'Lihat',
                    link: '/',
                    bg: '#eff6ff',
                    buttonColor: '#4090f2',
                });
            },
        }
    }
</script>
@endpush

@endsection