@extends('layouts.admin', ['activeNav' => 'artikel'])

@section('title', 'Kelola Artikel')

@section('content')

<script>
    window.serverAdminArticles = @json($articles);
</script>

<div x-data="adminJurnal()" x-init="load()" class="flex flex-col gap-5">

    {{-- ═══ HEADER ═══ --}}
    <header class="flex-shrink-0">
        <h1 class="text-2xl sm:text-3xl font-bold text-[#186369]">Kelola Artikel</h1>
        <p class="text-sm text-[#186369]/70 mt-1">
            Upload PDF langsung atau isi URL ke laman eksternal
        </p>
    </header>

    {{-- ═══ FORM ═══ --}}
    <div class="bg-white rounded-[32px] p-6 border border-gray-100 flex-shrink-0">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-[#186369]"
                x-text="editId ? 'Edit Jurnal' : 'Tambah Jurnal'"></h2>
            <button type="button" @click="reset()" x-show="editId"
                    class="text-[10px] font-bold text-red-500 hover:text-red-600">
                Batal Edit
            </button>
        </div>

        <form @submit.prevent="save()" class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Judul --}}
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 mb-1">
                    Judul Jurnal <span class="text-red-400">*</span>
                </label>
                <input type="text" x-model="form.judul" required
                       placeholder="Contoh: Jurnal Kesehatan Mental Vol. 1"
                       class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl
                              focus:outline-none focus:border-[#186369]">
            </div>

            {{-- Tanggal --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">
                    Tanggal <span class="text-red-400">*</span>
                </label>
                <input type="date" x-model="form.tanggal" required
                       class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl
                              focus:outline-none focus:border-[#186369]">
            </div>

            {{-- Sumber (badge) --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">
                    Sumber <span class="text-red-400">*</span>
                </label>
                <div class="flex items-center gap-2 p-2 bg-[#f5f8f7] rounded-xl
                            border-2 border-gray-200 h-[42px]">
                    <span class="px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider"
                          :class="sumberJurnal() === 'PDF'
                              ? 'bg-red-100 text-red-600'
                              : sumberJurnal() === 'Laman'
                                  ? 'bg-blue-100 text-blue-600'
                                  : 'bg-gray-200 text-gray-500'"
                          x-text="sumberJurnal()"></span>
                    <span class="text-[10px] text-gray-500 flex-1"
                          x-show="sumberJurnal() === 'Kosong'">
                        Upload PDF atau isi URL laman
                    </span>
                    <span class="text-[10px] text-gray-500 flex-1"
                          x-show="sumberJurnal() !== 'Kosong'">
                        Otomatis dari input yang diisi
                    </span>
                </div>
            </div>

            {{-- ═══ DROP ZONE PDF ═══ --}}
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 mb-1">
                    🔴 File PDF <span class="text-gray-400">(opsional)</span>
                </label>

                {{-- Kalau belum ada PDF --}}
                <div x-show="!form.pdfBase64"
                     x-on:dragover.prevent="dragOverPdf = true"
                     x-on:dragleave.prevent="dragOverPdf = false"
                     x-on:drop.prevent="handleDropPdf($event)"
                     x-on:click="$refs.pdfInput.click()"
                     :class="dragOverPdf
                        ? 'border-[#186369] bg-[#186369]/5'
                        : 'border-gray-300 bg-[#f5f8f7] hover:border-[#186369]/50'"
                     class="border-2 border-dashed rounded-2xl p-6
                            text-center cursor-pointer transition-all">

                    <input type="file" x-ref="pdfInput"
                           accept="application/pdf"
                           x-on:change="handleFileSelect($event)"
                           class="hidden">

                    <div class="flex flex-col items-center gap-2">
                        <div class="w-12 h-12 rounded-2xl bg-red-50
                                    flex items-center justify-center">
                            <svg viewBox="0 0 24 24" class="w-6 h-6 text-red-500"
                                 fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z M14 2v6h6"/>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-[#186369]">
                            Drop PDF di sini atau klik untuk pilih
                        </p>
                        <p class="text-[10px] text-gray-500">
                            Maksimal 2 MB · Format PDF
                        </p>
                    </div>
                </div>

                {{-- Kalau sudah ada PDF --}}
                <div x-show="form.pdfBase64" x-transition
                     class="flex items-center gap-3 p-3 rounded-2xl bg-red-50 border-2 border-red-200">

                    <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center flex-shrink-0">
                        <svg viewBox="0 0 24 24" class="w-6 h-6 text-red-500"
                             fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z M14 2v6h6"/>
                        </svg>
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-red-700 truncate"
                           x-text="form.pdfName"></p>
                        <p class="text-[10px] text-red-500/80 mt-0.5">
                            <span x-text="formatSize(form.pdfSize)"></span>
                            · Siap disimpan
                        </p>
                    </div>

                    <button type="button" @click="clearPdf()"
                            title="Hapus PDF"
                            class="w-8 h-8 rounded-lg bg-white hover:bg-red-500 text-red-500 hover:text-white
                                   flex items-center justify-center transition active:scale-90 flex-shrink-0">
                        <svg viewBox="0 0 16 16" class="w-3.5 h-3.5" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                            <path d="M4 4l8 8M12 4l-8 8"/>
                        </svg>
                    </button>
                </div>

                {{-- Error message --}}
                <p x-show="pdfError" x-text="pdfError"
                   class="text-[10px] text-red-600 mt-1.5"></p>
            </div>

            {{-- ═══ DIVIDER "ATAU" ═══ --}}
            <div class="md:col-span-2 flex items-center gap-3">
                <div class="flex-1 h-px bg-gray-200"></div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                    atau
                </span>
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>

            {{-- ═══ URL LAMAN ═══ --}}
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 mb-1">
                    🔵 URL Laman <span class="text-gray-400">(opsional kalau ada PDF)</span>
                </label>
                <input type="url" x-model="form.webUrl"
                       placeholder="https://notion.so/... atau https://medium.com/..."
                       :disabled="!!form.pdfBase64"
                       class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl text-sm
                              focus:outline-none focus:border-[#186369]
                              disabled:opacity-50 disabled:cursor-not-allowed
                              disabled:bg-gray-50">
                <p class="text-[10px] text-gray-400 mt-1"
                   x-show="form.pdfBase64">
                    🚫 Dinonaktifkan karena kamu sudah upload PDF
                </p>
            </div>

            {{-- ═══════════════════════════════════════════════════════ --}}
            {{-- 🔧 COVER — UBAH JADI DRAG & DROP GAMBAR            --}}
            {{-- ═══════════════════════════════════════════════════════ --}}
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 mb-1">
                    🖼️ Cover Jurnal <span class="text-gray-400">(opsional)</span>
                </label>

                {{-- Kalau belum ada cover --}}
                <div x-show="!form.cover"
                     x-on:dragover.prevent="dragOverCover = true"
                     x-on:dragleave.prevent="dragOverCover = false"
                     x-on:drop.prevent="handleDropCover($event)"
                     x-on:click="$refs.coverInput.click()"
                     :class="dragOverCover
                        ? 'border-[#186369] bg-[#186369]/5'
                        : 'border-gray-300 bg-[#f5f8f7] hover:border-[#186369]/50'"
                     class="border-2 border-dashed rounded-2xl p-6
                            text-center cursor-pointer transition-all">

                    <input type="file" x-ref="coverInput"
                           accept="image/*"
                           x-on:change="handleCoverSelect($event)"
                           class="hidden">

                    <div class="flex flex-col items-center gap-2">
                        <div class="w-12 h-12 rounded-2xl bg-purple-50
                                    flex items-center justify-center">
                            <svg viewBox="0 0 24 24" class="w-6 h-6 text-purple-500"
                                 fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 16l5-5 4 4 4-4 5 5M3 4h18v16H3z"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-[#186369]">
                            Drop gambar di sini atau klik untuk pilih
                        </p>
                        <p class="text-[10px] text-gray-500">
                            Maksimal 2 MB · JPG / PNG / WEBP
                        </p>
                    </div>
                </div>

                {{-- Kalau sudah ada cover --}}
                <div x-show="form.cover" x-transition
                     class="flex items-center gap-3 p-3 rounded-2xl bg-purple-50 border-2 border-purple-200">

                    <div class="w-20 h-20 rounded-xl overflow-hidden bg-white flex-shrink-0
                                border border-purple-200">
                        <img :src="form.cover" alt="Cover"
                             class="w-full h-full object-cover">
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-purple-700 truncate"
                           x-text="form.coverName || 'cover.jpg'"></p>
                        <p class="text-[10px] text-purple-500/80 mt-0.5">
                            <span x-text="formatSize(form.coverSize)"></span>
                            · Siap disimpan
                        </p>
                    </div>

                    <button type="button" @click="clearCover()"
                            title="Hapus Cover"
                            class="w-8 h-8 rounded-lg bg-white hover:bg-purple-500 text-purple-500 hover:text-white
                                   flex items-center justify-center transition active:scale-90 flex-shrink-0">
                        <svg viewBox="0 0 16 16" class="w-3.5 h-3.5" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                            <path d="M4 4l8 8M12 4l-8 8"/>
                        </svg>
                    </button>
                </div>

                {{-- Error message --}}
                <p x-show="coverError" x-text="coverError"
                   class="text-[10px] text-red-600 mt-1.5"></p>
            </div>

            {{-- Deskripsi --}}
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 mb-1">
                    Deskripsi <span class="text-gray-400">(opsional)</span>
                </label>
                <textarea x-model="form.deskripsi" rows="3"
                          placeholder="Ringkasan singkat tentang jurnal ini..."
                          class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl resize-none
                                 focus:outline-none focus:border-[#186369]"></textarea>
            </div>

            {{-- Tombol --}}
            <div class="md:col-span-2 flex gap-3 pt-2">
                <button type="submit"
                        :disabled="!bisaSimpan()"
                        class="px-6 py-2.5 bg-[#186369] text-white font-bold rounded-xl
                               hover:opacity-90 transition active:scale-95
                               disabled:opacity-40 disabled:cursor-not-allowed">
                    <span x-text="editId ? 'Simpan Perubahan' : 'Tambah Jurnal'"></span>
                </button>
                <button type="button" @click="reset()"
                        x-show="editId || form.judul || form.pdfBase64 || form.webUrl || form.cover"
                        class="px-6 py-2.5 bg-gray-100 text-gray-600 font-bold rounded-xl
                               hover:bg-gray-200 transition active:scale-95">
                    Reset Form
                </button>
            </div>

            {{-- Warning --}}
            <div class="md:col-span-2" x-show="form.judul && !bisaSimpan()">
                <p class="text-[11px] text-amber-600 bg-amber-50 border border-amber-200
                          rounded-xl px-4 py-2.5">
                    ⚠ Isi minimal salah satu: <b>Upload PDF</b> atau <b>URL Laman</b>
                </p>
            </div>
        </form>
    </div>

    {{-- ═══ LIST JURNAL ═══ --}}
    <div class="bg-white rounded-[32px] p-6 border border-gray-100">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-[#186369]">Daftar Jurnal</h2>
            <span class="text-xs font-bold text-gray-500">
                <span x-text="jurnals.length"></span> jurnal
            </span>
        </div>

        <template x-if="jurnals.length === 0">
            <div class="text-center py-12 text-gray-400">
                <p class="text-sm">Belum ada jurnal.</p>
            </div>
        </template>

        <template x-for="(j, i) in jurnals" :key="j.id">
            <div class="flex items-center gap-4 py-3 border-b border-gray-100 last:border-0">

                {{-- Cover / Icon --}}
                <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0
                            flex items-center justify-center">
                    <template x-if="j.cover">
                        <img :src="j.cover" alt="Cover" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!j.cover">
                        <div class="w-full h-full flex items-center justify-center"
                             :class="j.pdfBase64 ? 'bg-red-50' : 'bg-blue-50'">
                            <svg x-show="j.pdfBase64" viewBox="0 0 24 24" class="w-6 h-6 text-red-500"
                                 fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z M14 2v6h6"/>
                            </svg>
                            <svg x-show="!j.pdfBase64" viewBox="0 0 24 24" class="w-6 h-6 text-blue-500"
                                 fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71 M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/>
                            </svg>
                        </div>
                    </template>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span x-show="j.pdfBase64"
                              class="px-2 py-0.5 rounded-md text-[10px] font-bold
                                     bg-red-100 text-red-600 uppercase tracking-wider">
                            PDF File
                        </span>
                        <span x-show="!j.pdfBase64 && j.webUrl"
                              class="px-2 py-0.5 rounded-md text-[10px] font-bold
                                     bg-blue-100 text-blue-600 uppercase tracking-wider">
                            Laman
                        </span>
                        <span class="text-[10px] font-bold text-gray-400"
                              x-text="j.tanggal"></span>
                    </div>
                    <p class="font-bold text-sm text-[#186369] line-clamp-2"
                       x-text="j.judul"></p>
                    <p x-show="j.deskripsi"
                       class="text-[10px] text-gray-500 mt-0.5 line-clamp-1"
                       x-text="j.deskripsi"></p>
                </div>

                {{-- Actions --}}
                <div class="flex gap-2 flex-shrink-0">
                    <button type="button"
                            x-show="j.pdfBase64"
                            @click="bukaPdf(j)"
                            class="px-3 py-1.5 bg-[#186369] text-white text-xs font-bold
                                   rounded-lg hover:opacity-90 transition
                                   flex items-center gap-1">
                        <svg viewBox="0 0 16 16" class="w-3 h-3" fill="none"
                             stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10 3h3v3M13 3L7 9M12 10v3H3V6h3"/>
                        </svg>
                        Buka
                    </button>
                    <a x-show="!j.pdfBase64 && j.webUrl"
                       :href="j.webUrl"
                       target="_blank"
                       class="px-3 py-1.5 bg-[#186369] text-white text-xs font-bold
                              rounded-lg hover:opacity-90 transition
                              flex items-center gap-1">
                        <svg viewBox="0 0 16 16" class="w-3 h-3" fill="none"
                             stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10 3h3v3M13 3L7 9M12 10v3H3V6h3"/>
                        </svg>
                        Buka
                    </a>

                    <button @click="edit(j)"
                            class="px-3 py-1.5 bg-blue-500 text-white text-xs font-bold
                                   rounded-lg hover:bg-blue-600 transition">
                        Edit
                    </button>
                    <button @click="hapus(j.id)"
                            class="px-3 py-1.5 bg-red-500 text-white text-xs font-bold
                                   rounded-lg hover:bg-red-600 transition">
                        Hapus
                    </button>
                </div>
            </div>
        </template>
    </div>

</div>


<script>
    function adminJurnal() {
        return {
            jurnals: [],
            editId: null,
            dragOverPdf: false,
            dragOverCover: false,
            pdfError: '',
            coverError: '',

            form: {
                judul: '',
                tanggal: new Date().toISOString().slice(0, 10),
                pdfBase64: '',
                pdfExisting: false,
                pdfName: '',
                pdfSize: 0,
                webUrl: '',
                cover: '',          /* base64 gambar */
                coverExisting: false,
                coverName: '',      /* nama file asli */
                coverSize: 0,       /* ukuran file (bytes) */
                deskripsi: '',
            },

            /* ═══ LOAD ═══ */
            load() {
                this.jurnals = Array.isArray(window.serverAdminArticles)
                    ? window.serverAdminArticles
                    : [];
            },

            /* ═══ SUMBER ═══ */
            sumberJurnal() {
                if (this.form.pdfBase64 || this.form.pdfExisting) return 'PDF';
                if (this.form.webUrl.trim()) return 'Laman';
                return 'Kosong';
            },

            /* ═══ VALIDASI ═══ */
            bisaSimpan() {
                return this.form.judul.trim()
                    && (this.form.pdfBase64 || this.form.pdfExisting || this.form.webUrl.trim());
            },

            /* ═══════════════════════════════════════════════
               PDF: DRAG & DROP
               ═══════════════════════════════════════════════ */
            handleDropPdf(e) {
                this.dragOverPdf = false;
                const file = e.dataTransfer.files[0];
                if (file) this.processPdf(file);
            },

            handleFileSelect(e) {
                const file = e.target.files[0];
                if (file) this.processPdf(file);
                e.target.value = '';
            },

            processPdf(file) {
                this.pdfError = '';

                if (file.type !== 'application/pdf') {
                    this.pdfError = '❌ File harus PDF';
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    this.pdfError = `❌ File terlalu besar (${this.formatSize(file.size)}). Max 2 MB.`;
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    this.form.pdfBase64 = e.target.result;
                    this.form.pdfExisting = false;
                    this.form.pdfName   = file.name;
                    this.form.pdfSize   = file.size;
                    this.form.webUrl    = '';   /* auto clear karena PDF udah diisi */
                };
                reader.onerror = () => {
                    this.pdfError = '❌ Gagal baca file';
                };
                reader.readAsDataURL(file);
            },

            clearPdf() {
                this.form.pdfBase64 = '';
                this.form.pdfExisting = false;
                this.form.pdfName   = '';
                this.form.pdfSize   = 0;
                this.pdfError        = '';
            },

            /* ═══════════════════════════════════════════════
               COVER: DRAG & DROP GAMBAR
               ═══════════════════════════════════════════════ */
            handleDropCover(e) {
                this.dragOverCover = false;
                const file = e.dataTransfer.files[0];
                if (file) this.processCover(file);
            },

            handleCoverSelect(e) {
                const file = e.target.files[0];
                if (file) this.processCover(file);
                e.target.value = '';
            },

            processCover(file) {
                this.coverError = '';

                /* Validasi tipe */
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    this.coverError = '❌ File harus JPG, PNG, WEBP, atau GIF';
                    return;
                }

                /* Validasi ukuran (max 2MB) */
                if (file.size > 2 * 1024 * 1024) {
                    this.coverError = `❌ File terlalu besar (${this.formatSize(file.size)}). Max 2 MB.`;
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    this.form.cover     = e.target.result;
                    this.form.coverExisting = false;
                    this.form.coverName = file.name;
                    this.form.coverSize = file.size;
                };
                reader.onerror = () => {
                    this.coverError = '❌ Gagal baca file';
                };
                reader.readAsDataURL(file);
            },

            clearCover() {
                this.form.cover     = '';
                this.form.coverExisting = false;
                this.form.coverName = '';
                this.form.coverSize = 0;
                this.coverError      = '';
            },

            /* ═══ FORMAT SIZE ═══ */
            formatSize(bytes) {
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
                return (bytes / 1024 / 1024).toFixed(2) + ' MB';
            },

            /* ═══ BUKA PDF (base64 → blob → new tab) ═══ */
            bukaPdf(j) {
                if (j.pdfUrl) window.open(j.pdfUrl, '_blank');
            },

            /* ═══ SAVE ═══ */
            async save() {
                if (!this.bisaSimpan()) {
                    alert('Isi judul dan minimal salah satu (PDF / URL Laman)');
                    return;
                }

                const payload = {
                    title: this.form.judul.trim(),
                    published_at: this.form.tanggal,
                    content: this.form.deskripsi.trim(),
                    web_url: this.form.webUrl.trim() || null,
                    pdf_base64: this.form.pdfBase64.startsWith('data:') ? this.form.pdfBase64 : null,
                    cover_base64: this.form.cover.startsWith('data:') ? this.form.cover : null,
                    keep_pdf: this.form.pdfExisting,
                    keep_cover: this.form.coverExisting,
                };

                try {
                    const response = await fetch(this.editId ? `/admin/artikel/${this.editId}` : '/admin/artikel', {
                        method: this.editId ? 'PATCH' : 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify(payload),
                    });
                    const result = await response.json();
                    if (!response.ok) throw new Error(result.message || 'Gagal menyimpan artikel.');

                    if (this.editId) {
                        const index = this.jurnals.findIndex(j => j.id === this.editId);
                        if (index !== -1) this.jurnals[index] = result;
                    } else {
                        this.jurnals.unshift(result);
                    }
                    window.refreshAdminStats?.();
                    this.reset();
                } catch (error) {
                    alert(error.message || 'Gagal menyimpan artikel.');
                }
            },

            /* ═══ EDIT ═══ */
            edit(j) {
                this.editId = j.id;
                this.form = {
                    judul:      j.judul,
                    tanggal:    j.tanggal,
                    pdfBase64:  '',
                    pdfExisting: !!j.pdfBase64,
                    pdfName:    j.pdfName || '',
                    pdfSize:    j.pdfSize || 0,
                    webUrl:     j.webUrl || '',
                    cover:      '',
                    coverExisting: !!j.cover,
                    coverName:  j.coverName || '',
                    coverSize:  j.coverSize || 0,
                    deskripsi:  j.deskripsi || '',
                };
                this.pdfError = '';
                this.coverError = '';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            /* ═══ HAPUS ═══ */
            async hapus(id) {
                if (!confirm('Yakin hapus jurnal ini?')) return;

                try {
                    const response = await fetch(`/admin/artikel/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    });
                    if (!response.ok) throw new Error('Gagal menghapus artikel.');
                    this.jurnals = this.jurnals.filter(j => j.id !== id);
                    window.refreshAdminStats?.();
                    if (this.editId === id) this.reset();
                } catch (error) {
                    alert(error.message || 'Gagal menghapus artikel.');
                }
            },

            /* ═══ RESET ═══ */
            reset() {
                this.editId = null;
                this.pdfError = '';
                this.coverError = '';
                this.form = {
                    judul: '',
                    tanggal: new Date().toISOString().slice(0, 10),
                    pdfBase64: '',
                    pdfExisting: false,
                    pdfName: '',
                    pdfSize: 0,
                    webUrl: '',
                    cover: '',
                    coverExisting: false,
                    coverName: '',
                    coverSize: 0,
                    deskripsi: '',
                };
            },
        }
    }
</script>

@endsection