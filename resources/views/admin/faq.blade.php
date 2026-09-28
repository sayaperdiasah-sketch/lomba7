@extends('layouts.admin', ['activeNav' => 'faq'])

@section('title', 'Kelola FAQ')

@section('content')

<script>
    window.serverAdminFaqs = @json($faqs);
</script>

<div x-data="adminFaq()" x-init="load()" class="flex flex-col gap-5">

    <header class="flex-shrink-0">
        <h1 class="text-2xl sm:text-3xl font-bold text-[#186369]">Kelola FAQ</h1>
        <p class="text-sm text-[#186369]/70 mt-1">
            Auto-reply chat dengan admin berdasarkan kata kunci
        </p>
    </header>

    <div class="bg-white rounded-[32px] p-6 border border-gray-100 flex-shrink-0">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-[#186369]"
                x-text="editId ? 'Edit FAQ' : 'Tambah FAQ'"></h2>
            <button type="button" @click="reset()" x-show="editId"
                    class="text-[10px] font-bold text-red-500">Batal Edit</button>
        </div>

        <form @submit.prevent="save()" class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">
                    Keyword (kata kunci, pisah dengan koma)
                </label>
                <input type="text" x-model="form.keyword" required
                       placeholder="harga, biaya, tarif"
                       class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl
                              focus:outline-none focus:border-[#186369]">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Kategori</label>
                <input type="text" x-model="form.kategori" placeholder="Umum, Pembayaran"
                       class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl
                              focus:outline-none focus:border-[#186369]">
            </div>

                 <div>
                  <label class="block text-xs font-bold text-gray-500 mb-1">Urutan</label>
                  <input type="number" min="0" x-model.number="form.order"
                      class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl
                          focus:outline-none focus:border-[#186369]">
                 </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 mb-1">Balasan Admin</label>
                <textarea x-model="form.balasan" rows="3" required
                          placeholder="Tulis balasan otomatis..."
                          class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl resize-none
                                 focus:outline-none focus:border-[#186369]"></textarea>
            </div>

            <div class="md:col-span-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-[#186369] text-white font-bold rounded-xl
                               hover:opacity-90 transition active:scale-95">
                    <span x-text="editId ? 'Simpan Perubahan' : 'Tambah FAQ'"></span>
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-[32px] p-6 border border-gray-100">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-[#186369]">Daftar FAQ</h2>
            <span class="text-xs font-bold text-gray-500">
                <span x-text="faqs.length"></span> FAQ
            </span>
        </div>

        <template x-if="faqs.length === 0">
            <div class="text-center py-12 text-gray-400">
                <p class="text-sm">Belum ada FAQ.</p>
            </div>
        </template>

        <template x-for="(f, i) in faqs" :key="f.id">
            <div class="flex items-start gap-4 py-3 border-b border-gray-100 last:border-0">
                <div class="w-10 h-10 rounded-xl bg-[#186369]/10
                            flex items-center justify-center flex-shrink-0">
                    <svg viewBox="0 0 24 24" class="w-5 h-5 text-[#186369]" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 2a6 6 0 016 6c0 2.5-2 3.5-3 4.5S9 15 9 16 M9 20h.01"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-[#186369]"
                       x-text="'Keyword: ' + f.keyword"></p>
                    <p class="text-[10px] text-gray-500 mt-1 line-clamp-2"
                       x-text="f.balasan"></p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <button @click="edit(f)"
                            class="px-3 py-1.5 bg-blue-500 text-white text-xs font-bold
                                   rounded-lg hover:bg-blue-600 transition">Edit</button>
                    <button @click="hapus(f.id)"
                            class="px-3 py-1.5 bg-red-500 text-white text-xs font-bold
                                   rounded-lg hover:bg-red-600 transition">Hapus</button>
                </div>
            </div>
        </template>
    </div>

</div>

<script>
    function adminFaq() {
        return {
            faqs: [],
            editId: null,
            form: { keyword: '', kategori: '', balasan: '', order: 0 },

            load() {
                this.faqs = Array.isArray(window.serverAdminFaqs) ? window.serverAdminFaqs : [];
            },

            async save() {
                const payload = {
                    keywords: this.form.keyword.split(',').map(keyword => keyword.trim()).filter(Boolean),
                    category: this.form.kategori.trim() || null,
                    answer: this.form.balasan.trim(),
                    order: Number(this.form.order) || 0,
                };

                try {
                    const response = await fetch(this.editId ? `/admin/faq/${this.editId}` : '/admin/faq', {
                        method: this.editId ? 'PATCH' : 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify(payload),
                    });
                    const result = await response.json();
                    if (!response.ok) {
                        const errors = result.errors ? Object.values(result.errors).flat().join('\n') : result.message;
                        throw new Error(errors || 'Gagal menyimpan FAQ.');
                    }
                    const index = this.faqs.findIndex(f => f.id === result.id);
                    if (index === -1) this.faqs.push(result);
                    else this.faqs[index] = result;
                    this.faqs.sort((a, b) => (a.order - b.order) || (a.id - b.id));
                    this.reset();
                } catch (error) {
                    alert(error.message || 'Gagal menyimpan FAQ.');
                }
            },

            edit(f) {
                this.editId = f.id;
                this.form = { keyword: f.keyword, kategori: f.kategori || '', balasan: f.balasan, order: f.order || 0 };
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            async hapus(id) {
                if (!confirm('Yakin hapus FAQ ini?')) return;
                try {
                    const response = await fetch(`/admin/faq/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    });
                    if (!response.ok) throw new Error('Gagal menghapus FAQ.');
                    this.faqs = this.faqs.filter(f => f.id !== id);
                    if (this.editId === id) this.reset();
                } catch (error) {
                    alert(error.message || 'Gagal menghapus FAQ.');
                }
            },

            reset() {
                this.editId = null;
                this.form = { keyword: '', kategori: '', balasan: '', order: 0 };
            },
        }
    }
</script>

@endsection