@extends('layouts.admin', ['activeNav' => 'kategori'])

@section('title', 'Kelola Kategori')

@section('content')

<div x-data="adminKategori()" x-init="load()" class="flex flex-col gap-5">

    <header class="flex-shrink-0">
        <h1 class="text-2xl sm:text-3xl font-bold text-[#186369]">Kelola Kategori</h1>
        <p class="text-sm text-[#186369]/70 mt-1">
            Atur kategori, sub-kategori, dan detail To Do masing-masing
        </p>
    </header>

    {{-- ═══ FORM KATEGORI ═══ --}}
    <div class="bg-white rounded-[32px] p-6 border border-gray-100 flex-shrink-0">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-[#186369]"
                x-text="editId ? 'Edit Kategori' : 'Tambah Kategori'"></h2>
            <button type="button" @click="reset()" x-show="editId"
                    class="text-[10px] font-bold text-red-500 hover:text-red-600 transition">
                Batal Edit
            </button>
        </div>

        <form @submit.prevent="save()" class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 mb-1">
                    Nama Kategori <span class="text-red-400">*</span>
                </label>
                <input type="text" x-model="form.nama" required
                       placeholder="Contoh: Sport"
                       class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl
                              focus:outline-none focus:border-[#186369]">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Warna Background</label>
                <div class="flex gap-2 items-center">
                    <input type="color" x-model="form.warna"
                           class="w-12 h-10 rounded-lg cursor-pointer border-0">
                    <input type="text" x-model="form.warna"
                           class="flex-1 px-4 py-2 border-2 border-gray-200 rounded-xl text-sm
                                  focus:outline-none focus:border-[#186369]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Warna Teks</label>
                <div class="flex gap-2 items-center">
                    <input type="color" x-model="form.teksWarna"
                           class="w-12 h-10 rounded-lg cursor-pointer border-0">
                    <input type="text" x-model="form.teksWarna"
                           class="flex-1 px-4 py-2 border-2 border-gray-200 rounded-xl text-sm
                                  focus:outline-none focus:border-[#186369]">
                </div>
            </div>

            {{-- SUB KATEGORI --}}
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 mb-2">
                    Sub Kategori & Detail Aktivitas <span class="text-red-400">*</span>
                </label>

                <div class="bg-[#f5f8f7] rounded-2xl p-3 mb-3 border-2 border-dashed border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">

                        <div class="md:col-span-4">
                            <label class="block text-[10px] font-bold text-gray-500 mb-1">Nama Sub</label>
                            <input type="text" x-model="formSub.nama"
                                   @keydown.enter.prevent="tambahSub()"
                                   placeholder="Contoh: Joging"
                                   class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl text-sm
                                          focus:outline-none focus:border-[#186369]">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold text-gray-500 mb-1">Tipe</label>
                            <select x-model="formSub.tipe"
                                    @change="formSub.satuan = formSub.tipe === 'duration' ? 'menit' : 'x'"
                                    class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl text-sm
                                           focus:outline-none focus:border-[#186369]">
                                <option value="count">Jumlah (x)</option>
                                <option value="duration">Durasi</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold text-gray-500 mb-1">Target</label>
                            <input type="number" x-model.number="formSub.target" min="0"
                                   placeholder="30"
                                   class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl text-sm
                                          focus:outline-none focus:border-[#186369]">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold text-gray-500 mb-1">Satuan</label>
                            <input type="text" x-model="formSub.satuan"
                                   class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl text-sm
                                          focus:outline-none focus:border-[#186369]">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold text-gray-500 mb-1">Keterangan</label>
                            <input type="text" x-model="formSub.keterangan"
                                   placeholder="pagi"
                                   class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl text-sm
                                          focus:outline-none focus:border-[#186369]">
                        </div>

                        <div class="md:col-span-12">
                            <button type="button" @click="tambahSub()"
                                    :disabled="!formSub.nama.trim()"
                                    class="w-full md:w-auto px-5 py-2 rounded-xl
                                           bg-[#186369] text-white text-sm font-bold
                                           hover:opacity-90 transition active:scale-95
                                           disabled:opacity-40 disabled:cursor-not-allowed
                                           flex items-center justify-center gap-1.5">
                                <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none"
                                     stroke="currentColor" stroke-width="2.5"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 5v14M5 12h14"/>
                                </svg>
                                Tambah Aktivitas
                            </button>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <template x-if="form.jenis.length === 0">
                        <p class="text-[11px] text-gray-400 italic py-4 text-center
                                  bg-[#f5f8f7] rounded-2xl border-2 border-dashed border-gray-200">
                            Belum ada aktivitas. Isi form di atas lalu klik Tambah.
                        </p>
                    </template>

                    <template x-for="(sub, i) in form.jenis" :key="i">
                        <div class="flex items-center gap-3 p-3 rounded-2xl bg-white border-2
                                    transition hover:shadow-md"
                             :style="`border-color: ${form.warna}40; background: ${form.warna}08;`">

                            <div class="w-10 h-10 rounded-xl flex items-center justify-center
                                        text-sm font-bold flex-shrink-0"
                                 :style="`background: ${form.warna}; color: ${form.teksWarna};`"
                                 x-text="sub.nama.charAt(0).toUpperCase()"></div>

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-[#186369] truncate"
                                   x-text="sub.nama"></p>
                                <p class="text-[10px] font-bold text-gray-500 mt-0.5">
                                    <span x-text="sub.tipe === 'duration' ? 'Durasi' : 'Jumlah'"></span>
                                    · <span x-text="sub.target + ' ' + sub.satuan"></span>
                                    <template x-if="sub.keterangan">
                                        <span> · <span x-text="sub.keterangan"></span></span>
                                    </template>
                                </p>
                            </div>

                            <button type="button" @click="hapusSub(i)"
                                    title="Hapus"
                                    class="w-7 h-7 rounded-lg bg-red-50 text-red-500
                                           hover:bg-red-500 hover:text-white
                                           flex items-center justify-center
                                           transition active:scale-90 flex-shrink-0">
                                <svg viewBox="0 0 16 16" class="w-3 h-3" fill="none"
                                     stroke="currentColor" stroke-width="2.5"
                                     stroke-linecap="round">
                                    <path d="M4 4l8 8M12 4l-8 8"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                <p class="text-[10px] text-gray-400 mt-2">
                    <span x-text="form.jenis.length"></span> aktivitas dalam kategori ini
                </p>
            </div>

            {{-- Preview --}}
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 mb-1">Preview</label>
                <div class="rounded-3xl p-4 sm:p-5 max-w-md"
                     :style="`background: ${form.warna}; color: ${form.teksWarna};`">
                    <div class="flex justify-between items-center mb-2">
                        <p class="text-sm font-bold" x-text="(form.nama || 'Kategori') + ' :'"></p>
                        <div class="text-right">
                            <p class="text-2xl font-bold leading-none"
                               x-text="form.jenis.length"></p>
                            <p class="text-[10px] font-bold mt-1 opacity-80">Aktivitas</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-1 mt-2" x-show="form.jenis.length > 0">
                        <template x-for="(j, ji) in form.jenis" :key="ji">
                            <span class="px-2 py-0.5 rounded-md text-[9px] font-bold"
                                  :style="`background: ${form.teksWarna}20;`"
                                  x-text="j.nama"></span>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="md:col-span-2 flex gap-3 pt-2">
                <button type="submit"
                        :disabled="form.jenis.length === 0"
                        class="px-6 py-2.5 bg-[#186369] text-white font-bold rounded-xl
                               hover:opacity-90 transition active:scale-95
                               disabled:opacity-40 disabled:cursor-not-allowed">
                    <span x-text="editId ? 'Simpan Perubahan' : 'Simpan Kategori'"></span>
                </button>
                <button type="button" @click="reset()"
                        x-show="editId || form.nama || form.jenis.length"
                        class="px-6 py-2.5 bg-gray-100 text-gray-600 font-bold rounded-xl
                               hover:bg-gray-200 transition active:scale-95">
                    Reset Form
                </button>
            </div>
        </form>
    </div>

    {{-- ═══ LIST KATEGORI ═══ --}}
    <div class="bg-white rounded-[32px] p-6 border border-gray-100">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-[#186369]">Daftar Kategori</h2>
            <span class="text-xs font-bold text-gray-500">
                <span x-text="kategoris.length"></span> kategori
                · <span x-text="totalAktivitas()"></span> aktivitas
            </span>
        </div>

        <template x-if="kategoris.length === 0">
            <div class="text-center py-12 text-gray-400">
                <p class="text-sm">Belum ada kategori.</p>
            </div>
        </template>

        <template x-for="(cat, i) in kategoris" :key="cat.id">
            <div class="py-4 border-b border-gray-100 last:border-0">

                <div class="flex items-start gap-4">
                    {{-- Preview warna --}}
                    <div class="w-20 h-16 rounded-2xl flex flex-col justify-center px-3 flex-shrink-0"
                         :style="`background: ${cat.warna}; color: ${cat.teksWarna};`">
                        <div class="text-[10px] font-bold truncate" x-text="cat.nama"></div>
                        <div class="text-xl font-bold leading-none"
                             x-text="(cat.jenis || []).length"></div>
                    </div>

                    {{-- Info + list aktivitas --}}
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-sm text-[#186369]" x-text="cat.nama"></p>
                        <p class="text-[10px] text-gray-500 mb-2">
                            <span x-text="(cat.jenis || []).length"></span> aktivitas
                        </p>

                        <div class="space-y-1 max-h-40 overflow-y-auto scroll-thin">
                            <template x-for="(sub, si) in (cat.jenis || [])" :key="si">
                                <div class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-[10px]"
                                     :style="`background: ${cat.warna}10;`">
                                    <div class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                         :style="`background: ${cat.warna};`"></div>
                                    <span class="font-bold text-[#186369]" x-text="sub.nama"></span>
                                    <span class="text-gray-400">·</span>
                                    <span class="text-gray-500" x-text="sub.tipe === 'duration' ? 'Durasi' : 'Jumlah'"></span>
                                    <span class="text-gray-400">·</span>
                                    <span class="text-gray-500 font-bold"
                                          x-text="sub.target + ' ' + sub.satuan"></span>
                                    <template x-if="sub.keterangan">
                                        <span class="text-gray-400">· <span x-text="sub.keterangan"></span></span>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-2 flex-shrink-0">
                        <button @click="edit(cat)"
                                class="px-3 py-1.5 bg-blue-500 text-white text-xs font-bold
                                       rounded-lg hover:bg-blue-600 transition">
                            Edit
                        </button>
                        <button @click="hapus(cat.id)"
                                class="px-3 py-1.5 bg-red-500 text-white text-xs font-bold
                                       rounded-lg hover:bg-red-600 transition">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

</div>


<script>
window.serverCategories = @json($categories);

function adminKategori() {
    return {
        kategoris: [],
        editId: null,

        form: {
            nama: '',
            warna: '#c0a05f',
            teksWarna: '#186369',
            jenis: [],
        },

        formSub: {
            nama: '',
            tipe: 'count',
            target: 10,
            satuan: 'x',
            keterangan: '',
        },

        categoriesFromServer: true,

        /* ═══ LOAD ═══ */
        load() {
            let loaded = window.serverCategories || [];

            /* Migrasi: kalau `jenis` masih array of strings */
            loaded = loaded.map(cat => ({
                ...cat,
                jenis: (cat.jenis || []).map(sub => {
                    if (typeof sub === 'string') {
                        return { nama: sub, tipe: 'count', target: 10, satuan: 'x', keterangan: '' };
                    }
                    return sub;
                }),
            }));

            this.kategoris = loaded;
        },

        /* ═══ SUB KATEGORI ═══ */
        tambahSub() {
            const val = this.formSub.nama.trim();
            if (!val) return;

            if (this.form.jenis.some(s => s.nama.toLowerCase() === val.toLowerCase())) {
                alert('Sub kategori "' + val + '" sudah ada');
                return;
            }

            this.form.jenis.push({
                nama: val,
                tipe: this.formSub.tipe,
                target: Number(this.formSub.target) || 0,
                satuan: this.formSub.satuan || (this.formSub.tipe === 'duration' ? 'menit' : 'x'),
                keterangan: this.formSub.keterangan.trim(),
            });

            this.formSub = {
                nama: '',
                tipe: this.formSub.tipe,
                target: this.formSub.tipe === 'duration' ? 15 : 10,
                satuan: this.formSub.tipe === 'duration' ? 'menit' : 'x',
                keterangan: '',
            };
        },

        hapusSub(index) {
            this.form.jenis.splice(index, 1);
        },

        /* ═══ SAVE KATEGORI ═══ */
        save() {
            if (this.form.jenis.length === 0) {
                alert('Tambahkan minimal 1 aktivitas');
                return;
            }

            const data = {
                nama: this.form.nama.trim(),
                warna: this.form.warna,
                teksWarna: this.form.teksWarna,
                jenis: this.form.jenis.map(s => ({ ...s })),
            };

            const method = this.editId ? 'PATCH' : 'POST';
            const url = this.editId ? `/admin/kategori/${this.editId}` : '/admin/kategori';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            if (!csrfToken) {
                alert('Token keamanan tidak ditemukan. Muat ulang halaman.');
                return;
            }

            fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(data),
            }).then(async response => {
                if (!response.ok) {
                    const body = await response.json().catch(() => ({}));
                    const message = Object.values(body.errors || {}).flat()[0]
                        || body.message
                        || `Gagal menyimpan kategori (HTTP ${response.status}).`;
                    throw new Error(message);
                }
                window.location.reload();
            }).catch(error => alert(error.message));
        },

        edit(cat) {
            this.editId = cat.id;
            this.form = {
                nama: cat.nama,
                warna: cat.warna,
                teksWarna: cat.teksWarna,
                jenis: (cat.jenis || []).map(s => ({ ...s })),
            };
            this.formSub = {
                nama: '', tipe: 'count', target: 10, satuan: 'x', keterangan: '',
            };
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        hapus(id) {
            if (!confirm('Yakin hapus kategori ini beserta semua aktivitasnya?')) return;
            fetch(`/admin/kategori/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            }).then(response => {
                if (!response.ok) throw new Error('Gagal menghapus kategori.');
                window.location.reload();
            }).catch(error => alert(error.message));
        },

        reset() {
            this.editId = null;
            this.form = {
                nama: '',
                warna: '#c0a05f',
                teksWarna: '#186369',
                jenis: [],
            };
            this.formSub = {
                nama: '', tipe: 'count', target: 10, satuan: 'x', keterangan: '',
            };
        },

        /* ═══ UTILITY ═══ */
        totalAktivitas() {
            return this.kategoris.reduce((sum, c) => sum + (c.jenis?.length || 0), 0);
        },
    };
}
</script>

@endsection