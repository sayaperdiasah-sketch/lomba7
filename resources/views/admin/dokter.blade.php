@extends('layouts.admin', ['activeNav' => 'dokter'])

@section('title', 'Kelola Dokter')

@section('content')

<script>
    window.serverAdminDoctors = @json($doctors);
</script>

<div x-data="adminDokter()" x-init="load()" class="flex flex-col gap-5">

    {{-- ═══ HEADER ═══ --}}
    <header class="flex-shrink-0">
        <h1 class="text-2xl sm:text-3xl font-bold text-[#186369]">Kelola Dokter</h1>
        <p class="text-sm text-[#186369]/70 mt-1">
            Dokter & profesional untuk halaman konsultasi
        </p>
    </header>

    {{-- ═══ FORM ═══ --}}
    <div class="bg-white rounded-[32px] p-6 border border-gray-100 flex-shrink-0">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-[#186369]"
                x-text="editId ? 'Edit Dokter' : 'Tambah Dokter'"></h2>
            <button type="button" @click="reset()" x-show="editId"
                    class="text-[10px] font-bold text-red-500 hover:text-red-600">
                Batal Edit
            </button>
        </div>

        <form @submit.prevent="save()" class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Nama --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">
                    Nama Dokter <span class="text-red-400">*</span>
                </label>
                <input type="text" x-model="form.nama" required placeholder="Dr. Kholik"
                       class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl
                              focus:outline-none focus:border-[#186369]">
            </div>

            {{-- Spesialisasi --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">
                    Spesialisasi <span class="text-red-400">*</span>
                </label>
                <input type="text" x-model="form.spesialisasi" required
                       placeholder="Psikolog Klinis"
                       class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl
                              focus:outline-none focus:border-[#186369]">
            </div>

            {{-- Bio --}}
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 mb-1">Bio</label>
                <textarea x-model="form.bio" rows="2" placeholder="Deskripsi singkat..."
                          class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl resize-none
                                 focus:outline-none focus:border-[#186369]"></textarea>
            </div>

            {{-- ═══ DROP ZONE FOTO ═══ --}}
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 mb-1">
                    Foto Dokter <span class="text-gray-400">(opsional)</span>
                </label>

                {{-- Kalau belum ada foto --}}
                <div x-show="!form.fotoBase64"
                     x-on:dragover.prevent="dragOver = true"
                     x-on:dragleave.prevent="dragOver = false"
                     x-on:drop.prevent="handleDrop($event)"
                     x-on:click="$refs.fotoInput.click()"
                     :class="dragOver
                        ? 'border-[#186369] bg-[#186369]/5'
                        : 'border-gray-300 bg-[#f5f8f7] hover:border-[#186369]/50'"
                     class="border-2 border-dashed rounded-2xl p-6
                            text-center cursor-pointer transition-all">

                    <input type="file" x-ref="fotoInput"
                           accept="image/png, image/jpeg, image/jpg, image/webp"
                           x-on:change="handleFileSelect($event)"
                           class="hidden">

                    <div class="flex flex-col items-center gap-2">
                        <div class="w-14 h-14 rounded-full bg-[#186369]/10
                                    flex items-center justify-center">
                            <svg viewBox="0 0 24 24" class="w-6 h-6 text-[#186369]"
                                 fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 4v16M4 12h16"/>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-[#186369]">
                            Drop foto di sini atau klik untuk pilih
                        </p>
                        <p class="text-[10px] text-gray-500">
                            JPG / PNG / WebP · Maksimal 1 MB
                        </p>
                    </div>
                </div>

                {{-- Kalau sudah ada foto --}}
                <div x-show="form.fotoBase64" x-transition
                     class="flex items-center gap-4 p-3 rounded-2xl bg-[#f5f8f7] border-2 border-[#186369]/20">

                    {{-- Preview bulat --}}
                    <div class="relative flex-shrink-0">
                        <img :src="form.fotoBase64" alt="Foto"
                             class="w-16 h-16 rounded-full object-cover
                                    border-3 border-white shadow-md">
                        <span class="absolute bottom-0 right-0 w-4 h-4 rounded-full
                                     bg-emerald-400 border-2 border-white"
                              x-show="form.online"></span>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-[#186369] truncate"
                           x-text="form.fotoName || 'Foto Dokter'"></p>
                        <p class="text-[10px] text-gray-500 mt-0.5">
                            <span x-text="formatSize(form.fotoSize)"></span>
                            · Siap disimpan
                        </p>
                    </div>

                    {{-- Ganti Foto --}}
                    <button type="button" @click="$refs.fotoInput.click()"
                            class="px-3 py-1.5 rounded-lg bg-white border border-[#186369]/20
                                   text-[#186369] text-[10px] font-bold
                                   hover:bg-[#186369]/5 transition active:scale-95 flex-shrink-0">
                        Ganti
                    </button>

                    {{-- Hapus --}}
                    <button type="button" @click="clearFoto()"
                            title="Hapus Foto"
                            class="w-8 h-8 rounded-lg bg-white hover:bg-red-500
                                   text-red-500 hover:text-white
                                   flex items-center justify-center
                                   transition active:scale-90 flex-shrink-0">
                        <svg viewBox="0 0 16 16" class="w-3.5 h-3.5" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                            <path d="M4 4l8 8M12 4l-8 8"/>
                        </svg>
                    </button>
                </div>

                {{-- Error message --}}
                <p x-show="fotoError" x-text="fotoError"
                   class="text-[10px] text-red-600 mt-1.5"></p>
            </div>

            {{-- WhatsApp --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">
                    WhatsApp (62xxx) <span class="text-red-400">*</span>
                </label>
                <input type="text" x-model="form.wa" required placeholder="6281234567890"
                       class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl
                              focus:outline-none focus:border-[#186369]">
            </div>

            {{-- Website --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Website</label>
                <input type="text" x-model="form.web" placeholder="https://..."
                       class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl text-sm
                              focus:outline-none focus:border-[#186369]">
            </div>

            {{-- Jam Praktik --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Jam Praktik</label>
                <input type="text" x-model="form.jam_praktik" placeholder="09:00 - 17:00"
                       class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl
                              focus:outline-none focus:border-[#186369]">
            </div>

            {{-- Online Checkbox --}}
            <div class="flex items-center gap-3 pt-6">
                <input type="checkbox" x-model="form.online" id="online"
                       class="w-5 h-5 rounded accent-[#186369]">
                <label for="online" class="text-xs font-bold text-gray-600 cursor-pointer">
                    Sedang Online
                </label>
            </div>

            {{-- Tombol --}}
            <div class="md:col-span-2 flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-[#186369] text-white font-bold rounded-xl
                               hover:opacity-90 transition active:scale-95">
                    <span x-text="editId ? 'Simpan Perubahan' : 'Tambah Dokter'"></span>
                </button>
                <button type="button" @click="reset()"
                        x-show="editId || form.nama || form.fotoBase64"
                        class="px-6 py-2.5 bg-gray-100 text-gray-600 font-bold rounded-xl
                               hover:bg-gray-200 transition active:scale-95">
                    Reset Form
                </button>
            </div>
        </form>
    </div>

    {{-- ═══ LIST DOKTER ═══ --}}
    <div class="bg-white rounded-[32px] p-6 border border-gray-100">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-[#186369]">Daftar Dokter</h2>
            <span class="text-xs font-bold text-gray-500">
                <span x-text="dokters.length"></span> dokter
            </span>
        </div>

        <template x-if="dokters.length === 0">
            <div class="text-center py-12 text-gray-400">
                <p class="text-sm">Belum ada dokter.</p>
            </div>
        </template>

        <template x-for="(d, i) in dokters" :key="d.id">
            <div class="flex items-center gap-4 py-3 border-b border-gray-100 last:border-0">

                {{-- Foto --}}
                <div class="relative flex-shrink-0">
                    <div class="w-14 h-14 rounded-full overflow-hidden bg-gray-100">
                        <template x-if="d.fotoBase64 || d.avatar">
                            <img :src="d.fotoBase64 || d.avatar" :alt="d.nama"
                                 class="w-full h-full object-cover">
                        </template>
                        <template x-if="!d.fotoBase64 && !d.avatar">
                            <div class="w-full h-full flex items-center justify-center
                                        bg-[#186369]/10 text-[#186369] text-lg font-bold"
                                 x-text="d.nama ? d.nama.charAt(0) : '?'"></div>
                        </template>
                    </div>
                    <span class="absolute bottom-0 right-0 w-3.5 h-3.5 rounded-full
                                 border-2 border-white"
                          :class="d.online ? 'bg-emerald-400' : 'bg-gray-300'"></span>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="font-bold text-sm text-[#186369]" x-text="d.nama"></p>
                    </div>
                    <p class="text-[10px] text-gray-500 mt-0.5">
                        <span class="px-1.5 py-0.5 rounded bg-[#186369]/10 text-[#186369] font-bold"
                              x-text="d.spesialisasi"></span>
                        · <span x-text="d.jam_praktik"></span>
                    </p>
                    <p class="text-[10px] text-gray-400 mt-0.5">
                        WA: <span x-text="d.wa"></span>
                    </p>
                </div>

                {{-- Actions --}}
                <div class="flex gap-2 flex-shrink-0">
                    <button @click="edit(d)"
                            class="px-3 py-1.5 bg-blue-500 text-white text-xs font-bold
                                   rounded-lg hover:bg-blue-600 transition">
                        Edit
                    </button>
                    <button @click="hapus(d.id)"
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
    function adminDokter() {
        return {
            dokters: [],
            editId: null,
            dragOver: false,
            fotoError: '',

            form: {
                nama:         '',
                spesialisasi: '',
                bio:          '',
                fotoBase64:   '',       /* Foto sebagai base64 */
                fotoName:     '',       /* Nama file asli */
                fotoSize:     0,        /* Ukuran (bytes) */
                fotoRemoved:  false,
                avatar:       '',       /* Legacy: URL foto (kalau ada) */
                wa:           '',
                web:          '',
                jam_praktik:  '09:00 - 17:00',
                online:       true,
            },

            /* ═══ Default contoh ═══ */
            defaults: [
                {
                    id: 1, nama: 'Dr. Kholik', spesialisasi: 'Psikolog Klinis',
                    bio: 'Spesialis kecemasan dan depresi.',
                    fotoBase64: '', avatar: 'https://i.pravatar.cc/150?img=12',
                    wa: '6281234567890', web: 'https://example.com/kholik',
                    jam_praktik: '09:00 - 17:00', online: true,
                },
                {
                    id: 2, nama: 'Dr. Sarah Wijaya', spesialisasi: 'Psikiater',
                    bio: 'Gangguan tidur dan stres berat.',
                    fotoBase64: '', avatar: 'https://i.pravatar.cc/150?img=45',
                    wa: '6281234567891', web: 'https://example.com/sarah',
                    jam_praktik: '10:00 - 19:00', online: true,
                },
                {
                    id: 3, nama: 'Dr. Budi Santoso', spesialisasi: 'Konselor',
                    bio: 'Konseling hubungan & karir.',
                    fotoBase64: '', avatar: 'https://i.pravatar.cc/150?img=33',
                    wa: '6281234567892', web: '',
                    jam_praktik: '08:00 - 16:00', online: false,
                },
            ],

            /* ═══ LOAD ═══ */
            load() {
                this.dokters = Array.isArray(window.serverAdminDoctors)
                    ? window.serverAdminDoctors
                    : [];
            },

            /* ═══ DRAG & DROP FOTO ═══ */
            handleDrop(e) {
                this.dragOver = false;
                const file = e.dataTransfer.files[0];
                if (file) this.processFile(file);
            },

            handleFileSelect(e) {
                const file = e.target.files[0];
                if (file) this.processFile(file);
                e.target.value = '';  /* reset biar bisa pilih file yang sama */
            },

            processFile(file) {
                this.fotoError = '';

                /* Validasi tipe — harus image */
                if (!file.type.startsWith('image/')) {
                    this.fotoError = '❌ File harus gambar (JPG, PNG, WebP)';
                    return;
                }

                /* Validasi ukuran — max 1 MB */
                if (file.size > 1 * 1024 * 1024) {
                    this.fotoError = `❌ File terlalu besar (${this.formatSize(file.size)}). Max 1 MB.`;
                    return;
                }

                /* Baca sebagai base64 */
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.form.fotoBase64 = e.target.result;
                    this.form.fotoRemoved = false;
                    this.form.fotoName   = file.name;
                    this.form.fotoSize   = file.size;
                };
                reader.onerror = () => {
                    this.fotoError = '❌ Gagal baca file';
                };
                reader.readAsDataURL(file);
            },

            clearFoto() {
                this.form.fotoBase64 = '';
                this.form.fotoRemoved = true;
                this.form.fotoName   = '';
                this.form.fotoSize   = 0;
                this.fotoError        = '';
            },

            formatSize(bytes) {
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
                return (bytes / 1024 / 1024).toFixed(2) + ' MB';
            },

            /* ═══ SAVE ═══ */
            async save() {
                const payload = {
                    name: this.form.nama.trim(),
                    specialization: this.form.spesialisasi.trim(),
                    bio: this.form.bio.trim(),
                    photo: this.form.fotoBase64.startsWith('data:image/') ? this.form.fotoBase64 : null,
                    photo_removed: this.form.fotoRemoved,
                    avatar: this.form.avatar || null,
                    whatsapp: this.form.wa.trim(),
                    website: this.form.web.trim() || null,
                    jam_praktik: this.form.jam_praktik.trim(),
                    online: this.form.online,
                };

                try {
                    const response = await fetch(this.editId ? `/admin/dokter/${this.editId}` : '/admin/dokter', {
                        method: this.editId ? 'PATCH' : 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify(payload),
                    });
                    const raw = await response.text();
                    let result = {};
                    try { result = raw ? JSON.parse(raw) : {}; } catch (parseError) {
                        throw new Error(`Server mengembalikan respons tidak valid (HTTP ${response.status}).`);
                    }
                    if (!response.ok) {
                        const errors = result.errors ? Object.values(result.errors).flat().join('\n') : result.message;
                        throw new Error(errors || 'Gagal menyimpan dokter.');
                    }
                    const index = this.dokters.findIndex(d => d.id === result.id);
                    if (index === -1) this.dokters.unshift(result);
                    else this.dokters[index] = result;
                    window.refreshAdminStats?.();
                    this.reset();
                } catch (error) {
                    alert(error.message || 'Gagal menyimpan dokter.');
                }
            },

            /* ═══ EDIT ═══ */
            edit(d) {
                this.editId = d.id;
                this.form = {
                    nama:         d.nama || '',
                    spesialisasi: d.spesialisasi || '',
                    bio:          d.bio || '',
                    fotoBase64:   d.fotoBase64 || '',
                    fotoName:     d.fotoName || '',
                    fotoSize:     d.fotoSize || 0,
                    fotoRemoved:  false,
                    avatar:       d.avatar || '',
                    wa:           d.wa || '',
                    web:          d.web || '',
                    jam_praktik:  d.jam_praktik || '09:00 - 17:00',
                    online:       d.online ?? true,
                };
                this.fotoError = '';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            /* ═══ HAPUS ═══ */
            async hapus(id) {
                if (!confirm('Yakin hapus dokter ini?')) return;
                try {
                    const response = await fetch(`/admin/dokter/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    });
                    if (!response.ok) throw new Error('Gagal menghapus dokter.');
                    this.dokters = this.dokters.filter(d => d.id !== id);
                    window.refreshAdminStats?.();
                    if (this.editId === id) this.reset();
                } catch (error) {
                    alert(error.message || 'Gagal menghapus dokter.');
                }
            },

            /* ═══ RESET ═══ */
            reset() {
                this.editId = null;
                this.fotoError = '';
                this.form = {
                    nama:         '',
                    spesialisasi: '',
                    bio:          '',
                    fotoBase64:   '',
                    fotoName:     '',
                    fotoSize:     0,
                    fotoRemoved:  false,
                    avatar:       '',
                    wa:           '',
                    web:          '',
                    jam_praktik:  '09:00 - 17:00',
                    online:       true,
                };
            },
        }
    }
</script>

@endsection