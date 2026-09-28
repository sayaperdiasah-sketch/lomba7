{{-- ═══════════════════════════════════════════════════════════
     PROFILE DRAWER — dengan mode edit + riwayat tes (popup)
     ═══════════════════════════════════════════════════════════ --}}
<div x-show="profileOpen"
     x-cloak
     x-transition:enter="transition ease-out duration-500"
     x-transition:enter-start="opacity-0 scale-50 -translate-y-12"
     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
     x-transition:leave-end="opacity-0 scale-50 -translate-y-12"
     @click.away="profileOpen = false; closeEdit()"
     class="profile-panel fixed z-50 top-[80px] right-4 sm:right-6 lg:right-8
            w-[340px] max-w-[calc(100vw-2rem)] max-h-[calc(100vh-100px)]
            rounded-3xl bg-white border border-gray-100
            flex flex-col overflow-hidden"
     :class="{ 'animate-breathe': breatheActive }"
     style="box-shadow: 0 20px 60px rgba(24,99,105,0.15);"
     x-data="profileEditor()"
     x-init="init()">

    <div class="flex-1 min-h-0 overflow-y-auto scroll-thin p-6 space-y-5">

        {{-- HEADER ACTION BUTTONS --}}
        <div class="absolute top-4 right-4 flex items-center gap-1.5 z-10">
            <button type="button"
                    @click="isEditing ? save() : startEdit()"
                    :disabled="saving"
                    :title="isEditing ? 'Simpan' : 'Edit profil'"
                    class="w-8 h-8 rounded-full flex items-center justify-center
                           transition active:scale-90 disabled:opacity-50 disabled:cursor-not-allowed"
                    :class="isEditing
                        ? 'bg-emerald-500 hover:bg-emerald-600 text-white'
                        : 'bg-gray-100 hover:bg-gray-200 text-[#186369]'">
                <svg x-show="!isEditing" viewBox="0 0 16 16" class="w-3.5 h-3.5"
                     fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11.5 2.5l2 2L5 13l-3 1 1-3 8.5-8.5z"/>
                </svg>
                <svg x-show="isEditing && !saving" viewBox="0 0 16 16" class="w-3.5 h-3.5"
                     fill="none" stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 8l4 4 7-7"/>
                </svg>
                <svg x-show="isEditing && saving" viewBox="0 0 16 16" class="w-3.5 h-3.5 animate-spin"
                     fill="none" stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round">
                    <circle cx="8" cy="8" r="6" stroke-dasharray="20 10"/>
                </svg>
            </button>

            <button type="button"
                    x-show="isEditing"
                    @click="cancelEdit()"
                    title="Batal"
                    class="w-8 h-8 rounded-full bg-red-100 hover:bg-red-200
                           flex items-center justify-center
                           text-red-600 transition active:scale-90">
                <svg viewBox="0 0 16 16" class="w-3.5 h-3.5"
                     fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round">
                    <path d="M4 4l8 8M12 4l-8 8"/>
                </svg>
            </button>

            <button type="button"
                    @click="profileOpen = false; closeEdit()"
                    title="Tutup"
                    class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200
                           flex items-center justify-center
                           transition active:scale-90">
                <svg viewBox="0 0 16 16" class="w-3.5 h-3.5 text-[#186369]"
                     fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round">
                    <path d="M4 4l8 8M12 4l-8 8"/>
                </svg>
            </button>
        </div>

        {{-- AVATAR --}}
        <div class="flex justify-center pt-6">
            <img :src="user.avatar" :alt="user.nama"
                 class="w-24 h-24 rounded-full object-cover
                        border-4 border-gray-100 shadow">
        </div>

        <template x-if="isEditing">
            <div class="flex justify-center -mt-2">
                <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                              bg-[#f5f8f7] text-[#186369] text-[10px] font-bold
                              hover:bg-[#e8f0ef] transition">
                    Pilih foto profil
                    <input type="file" accept="image/jpeg,image/png,image/webp"
                           class="hidden" @change="selectAvatar($event)">
                </label>
            </div>
        </template>

        {{-- MODE TAMPIL --}}
        <template x-if="!isEditing">
            <div class="space-y-5">

                <div class="text-center">
                    <h2 class="text-2xl font-bold text-[#186369]"
                        x-text="user.nama || 'Belum diisi'"></h2>
                    <p class="text-xs font-bold text-[#186369]/50 mt-1 break-all"
                       x-text="user.email"></p>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div class="bg-gray-50 rounded-xl py-2.5 text-center">
                        <p class="text-[10px] font-bold text-[#186369]/50">Goldar</p>
                        <p class="text-base font-bold text-[#186369] mt-0.5"
                           x-text="user.goldar || '—'"></p>
                    </div>
                    <div class="bg-gray-50 rounded-xl py-2.5 text-center">
                        <p class="text-[10px] font-bold text-[#186369]/50">Tinggi</p>
                        <p class="text-base font-bold text-[#186369] mt-0.5"
                           x-text="user.tinggi || '—'"></p>
                    </div>
                    <div class="bg-gray-50 rounded-xl py-2.5 text-center">
                        <p class="text-[10px] font-bold text-[#186369]/50">Berat</p>
                        <p class="text-base font-bold text-[#186369] mt-0.5"
                           x-text="user.berat || '—'"></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-gray-50 rounded-xl py-2.5 text-center">
                        <p class="text-[10px] font-bold text-[#186369]/50">Usia</p>
                        <p class="text-base font-bold text-[#186369] mt-0.5">
                            <span x-text="user.usia || '—'"></span>
                            <span x-show="user.usia" class="text-xs font-normal text-[#186369]/50">thn</span>
                        </p>
                    </div>
                    <div class="bg-gray-50 rounded-xl py-2.5 text-center">
                        <p class="text-[10px] font-bold text-[#186369]/50">Gender</p>
                        <p class="text-base font-bold text-[#186369] mt-0.5 capitalize"
                           x-text="genderLabel || '—'"></p>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-2xl p-4">
                    <p class="text-xs font-bold text-[#186369] mb-1.5">⭐ Biografi :</p>
                    <p class="text-[11px] font-bold text-[#186369]/60 leading-relaxed"
                       x-text="user.bio || 'Belum ada bio. Klik ikon edit untuk menambahkan.'"></p>
                </div>

                {{-- Warning profil belum lengkap --}}
                <template x-if="!isComplete">
                    <div class="bg-amber-50 border-2 border-amber-200
                                rounded-2xl p-3.5
                                flex items-start gap-2.5">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5"
                             fill="none" stroke="currentColor" stroke-width="2.5"
                             stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 8v4M12 16h.01"/>
                        </svg>
                        <div class="flex-1">
                            <p class="text-[11px] font-bold text-amber-700">Profil belum lengkap</p>
                            <p class="text-[10px] text-amber-600/80 mt-0.5 leading-relaxed">
                                Isi nama, usia, dan gender dulu untuk pengalaman yang lebih personal.
                            </p>
                            <button type="button" @click="startEdit()"
                                    class="mt-2 text-[10px] font-bold text-amber-700
                                           bg-amber-100 hover:bg-amber-200
                                           px-3 py-1.5 rounded-lg transition active:scale-95">
                                Lengkapi Sekarang →
                            </button>
                        </div>
                    </div>
                </template>

                {{-- RIWAYAT TES --}}
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-bold text-[#186369] flex items-center gap-1.5">
                            <span>📋 Riwayat Tes</span>
                            <span x-show="testHistory.length > 0"
                                  class="px-1.5 py-0.5 rounded-full bg-[#186369] text-white
                                         text-[9px] font-bold min-w-[18px] text-center"
                                  x-text="testHistory.length"></span>
                        </h3>
                        <button type="button"
                                x-show="testHistory.length > 0"
                                @click="clearHistory()"
                                class="text-[10px] font-bold text-red-500 hover:text-red-600 transition">
                            Hapus Semua
                        </button>
                    </div>

                    <template x-if="testHistory.length === 0">
                        <div class="bg-gray-50 rounded-2xl p-5 text-center">
                            <div class="w-12 h-12 rounded-full bg-white mx-auto mb-2
                                        flex items-center justify-center">
                                <svg viewBox="0 0 24 24" class="w-6 h-6 text-[#186369]/30"
                                     fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9.5 2A6.5 6.5 0 003 8.5c0 4 4 8 9 13 5-5 9-9 9-13A6.5 6.5 0 009.5 2z"/>
                                </svg>
                            </div>
                            <p class="text-[11px] font-bold text-[#186369]/60">Belum ada riwayat tes</p>
                            <a href="/tes-mental"
                               class="mt-2 inline-flex items-center gap-1
                                      px-3 py-1.5 rounded-lg bg-[#186369]/10
                                      text-[10px] font-bold text-[#186369]
                                      hover:bg-[#186369]/20 transition">
                                Mulai Tes →
                            </a>
                        </div>
                    </template>

                    <div class="space-y-2">
                        <template x-for="(h, hi) in testHistory.slice(0, 10)" :key="h.id">
                            <button type="button"
                                    @click="openHistoryPopup(h)"
                                    class="w-full text-left p-3 flex items-start gap-3
                                           bg-gray-50 rounded-2xl
                                           hover:bg-gray-100 transition active:scale-[0.98]">

                                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                                     :style="`background: ${h.testWarna}20; color: ${h.testWarna};`">
                                    <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none"
                                         stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9.5 2A6.5 6.5 0 003 8.5c0 4 4 8 9 13 5-5 9-9 9-13A6.5 6.5 0 009.5 2z"/>
                                    </svg>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5 mb-0.5 flex-wrap">
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider"
                                              :style="`background: ${h.testWarna}20; color: ${h.testWarna};`"
                                              x-text="h.testJenis"></span>
                                        <template x-if="!h.isValid">
                                            <span class="px-1.5 py-0.5 rounded text-[8px] font-bold bg-red-100 text-red-600">
                                                INVALID
                                            </span>
                                        </template>
                                        <template x-if="h.isHighRisk && h.isValid">
                                            <span class="px-1.5 py-0.5 rounded text-[8px] font-bold bg-red-100 text-red-600">
                                                HIGH RISK
                                            </span>
                                        </template>
                                    </div>
                                    <p class="text-xs font-bold text-[#186369] truncate"
                                       x-text="h.testJudul"></p>
                                    <p class="text-[9px] font-bold text-gray-400 mt-0.5"
                                       x-text="h.tanggalLabel"></p>
                                </div>

                                <div class="text-right flex-shrink-0 flex flex-col items-end">
                                    <p class="text-lg font-bold leading-none"
                                       :style="`color: ${h.severityWarna};`"
                                       x-text="h.totalScore + '%'"></p>
                                    <span class="text-[8px] font-bold text-[#186369]/40 mt-1">
                                        Lihat →
                                    </span>
                                </div>
                            </button>
                        </template>
                    </div>

                    <template x-if="testHistory.length > 10">
                        <p class="text-[9px] text-center text-gray-400 mt-2">
                            Menampilkan 10 dari <span x-text="testHistory.length"></span> riwayat
                        </p>
                    </template>
                </div>

                {{-- REMINDERS --}}
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-bold text-[#186369] flex items-center gap-1.5">
                            <span>🔔 Reminders</span>
                            <span x-show="reminders.length > 0"
                                  class="px-1.5 py-0.5 rounded-full bg-[#e63946] text-white
                                         text-[9px] font-bold min-w-[18px] text-center"
                                  x-text="reminders.length"></span>
                        </h3>
                        <button type="button"
                                @click="refreshReminders()"
                                x-show="reminders.length > 0"
                                class="text-[10px] font-bold text-[#186369]/50 hover:text-[#186369]
                                       transition flex items-center gap-1">
                            <svg viewBox="0 0 16 16" class="w-3 h-3" fill="none"
                                 stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 8a7 7 0 0113-3.5M15 8a7 7 0 01-13 3.5M14 1v4h-4M2 15v-4h4"/>
                            </svg>
                            Refresh
                        </button>
                    </div>

                    <template x-if="reminders.length === 0">
                        <div class="bg-emerald-50 border-2 border-dashed border-emerald-200
                                    rounded-2xl p-6 text-center">
                            <div class="w-12 h-12 rounded-full bg-white mx-auto mb-2
                                        flex items-center justify-center">
                                <svg viewBox="0 0 24 24" class="w-6 h-6 text-emerald-500"
                                     fill="none" stroke="currentColor" stroke-width="2.5"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <p class="text-xs font-bold text-emerald-700">
                                Semua quest sudah selesai! 🎉
                            </p>
                            <p class="text-[10px] text-emerald-600/70 mt-1">
                                Tidak ada pekerjaan yang tertunda
                            </p>
                        </div>
                    </template>

                    <div class="space-y-2">
                        <template x-for="(rem, i) in reminders" :key="rem.id">
                            <button type="button"
                                    @click="goToSchedule(rem.tanggalKey)"
                                    class="w-full rounded-xl px-3.5 py-3 text-left
                                           hover:shadow-md transition active:scale-[0.98]
                                           border-l-4 flex items-start gap-3"
                                    :style="`background: ${rem.warna}08;
                                             border-left-color: ${rem.warna};`">

                                <div class="w-9 h-9 rounded-lg flex items-center justify-center
                                            flex-shrink-0 mt-0.5"
                                     :style="`background: ${rem.warna}20;`">
                                    <svg viewBox="0 0 24 24" class="w-4 h-4"
                                         :style="`color: ${rem.teksWarna};`"
                                         fill="none" stroke="currentColor" stroke-width="2.5"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 11l3 3L22 4"/>
                                        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                                    </svg>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-[#186369] truncate"
                                       x-text="rem.nama"></p>
                                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold"
                                              :style="`background: ${rem.warna}20; color: ${rem.warna};`"
                                              x-text="rem.kategori"></span>
                                        <span class="text-[9px] font-bold text-gray-400">·</span>
                                        <span class="text-[9px] font-bold flex items-center gap-1"
                                              :class="rem.isPast
                                                  ? 'text-red-500'
                                                  : rem.isToday
                                                      ? 'text-[#c0a05f]'
                                                      : 'text-gray-500'">
                                            <svg viewBox="0 0 16 16" class="w-2.5 h-2.5" fill="none"
                                                 stroke="currentColor" stroke-width="2"
                                                 stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="8" cy="8" r="6"/>
                                                <path d="M8 4v4l2.5 1.5"/>
                                            </svg>
                                            <span x-text="rem.tanggalLabel"></span>
                                        </span>
                                    </div>
                                    <p class="text-[9px] font-bold text-gray-400 mt-1">
                                        Target: <span x-text="rem.target + ' ' + rem.satuan"></span>
                                    </p>
                                </div>

                                <template x-if="rem.isPast">
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-bold flex-shrink-0
                                                 bg-red-100 text-red-600 uppercase tracking-wider">
                                        Lewat
                                    </span>
                                </template>
                                <template x-if="rem.isToday">
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-bold flex-shrink-0
                                                 bg-amber-100 text-amber-600 uppercase tracking-wider">
                                        Hari ini
                                    </span>
                                </template>
                            </button>
                        </template>
                    </div>

                    <p x-show="remindersTotal > reminders.length"
                       class="text-[10px] text-center text-gray-400 mt-3">
                        Menampilkan <span x-text="reminders.length"></span>
                        dari <span x-text="remindersTotal"></span> quest tertunda
                    </p>
                </div>
            </div>
        </template>

        {{-- MODE EDIT --}}
        <template x-if="isEditing">
            <div class="space-y-4 pt-2">
                <div>
                    <label class="block text-[10px] font-bold text-[#186369]/60 uppercase tracking-wider mb-1.5">
                        Nama <span class="text-red-400">*</span>
                    </label>
                    <input type="text" x-model="draft.nama"
                           placeholder="Nama lengkap atau panggilan"
                           maxlength="50"
                           class="w-full px-3 py-2.5 rounded-xl
                                  bg-[#f5f8f7] border-2 border-transparent
                                  text-sm font-bold text-[#186369]
                                  placeholder:text-[#186369]/30 placeholder:font-normal
                                  focus:outline-none focus:border-[#186369]
                                  transition">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] font-bold text-[#186369]/60 uppercase tracking-wider mb-1.5">
                            Usia <span class="text-red-400">*</span>
                        </label>
                        <input type="number" x-model.number="draft.usia"
                               min="5" max="120" placeholder="20"
                               class="w-full px-3 py-2.5 rounded-xl
                                      bg-[#f5f8f7] border-2 border-transparent
                                      text-sm font-bold text-[#186369]
                                      placeholder:text-[#186369]/30 placeholder:font-normal
                                      focus:outline-none focus:border-[#186369]
                                      transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-[#186369]/60 uppercase tracking-wider mb-1.5">
                            Gender <span class="text-red-400">*</span>
                        </label>
                        <select x-model="draft.gender"
                                class="w-full px-3 py-2.5 rounded-xl
                                       bg-[#f5f8f7] border-2 border-transparent
                                       text-sm font-bold text-[#186369]
                                       focus:outline-none focus:border-[#186369]
                                       transition cursor-pointer">
                            <option value="">Pilih</option>
                            <option value="pria">Pria</option>
                            <option value="wanita">Wanita</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-[10px] font-bold text-[#186369]/60 uppercase tracking-wider mb-1.5">
                            Goldar
                        </label>
                        <select x-model="draft.goldar"
                                class="w-full px-2 py-2.5 rounded-xl
                                       bg-[#f5f8f7] border-2 border-transparent
                                       text-sm font-bold text-[#186369]
                                       focus:outline-none focus:border-[#186369]
                                       transition cursor-pointer">
                            <option value="">—</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-[#186369]/60 uppercase tracking-wider mb-1.5">
                            Tinggi
                        </label>
                        <input type="number" x-model.number="draft.tinggi" placeholder="178" min="30" max="300" step="1"
                               class="w-full px-2 py-2.5 rounded-xl
                                      bg-[#f5f8f7] border-2 border-transparent
                                      text-sm font-bold text-[#186369]
                                      placeholder:text-[#186369]/30 placeholder:font-normal
                                      focus:outline-none focus:border-[#186369]
                                      transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-[#186369]/60 uppercase tracking-wider mb-1.5">
                            Berat
                        </label>
                        <input type="number" x-model.number="draft.berat" placeholder="65" min="2" max="500" step="1"
                               class="w-full px-2 py-2.5 rounded-xl
                                      bg-[#f5f8f7] border-2 border-transparent
                                      text-sm font-bold text-[#186369]
                                      placeholder:text-[#186369]/30 placeholder:font-normal
                                      focus:outline-none focus:border-[#186369]
                                      transition">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-[#186369]/60 uppercase tracking-wider mb-1.5">
                        Biografi
                    </label>
                    <textarea x-model="draft.bio" rows="3" maxlength="200"
                              placeholder="Ceritakan sedikit tentang dirimu..."
                              class="w-full px-3 py-2.5 rounded-xl
                                     bg-[#f5f8f7] border-2 border-transparent
                                     text-sm font-bold text-[#186369]
                                     placeholder:text-[#186369]/30 placeholder:font-normal
                                     focus:outline-none focus:border-[#186369]
                                     resize-none transition"></textarea>
                    <p class="text-[9px] text-[#186369]/40 text-right mt-1">
                        <span x-text="draft.bio ? draft.bio.length : 0"></span>/200
                    </p>
                </div>

                <template x-if="error">
                    <p class="text-[10px] text-red-500 font-bold
                              bg-red-50 border border-red-200
                              rounded-xl px-3 py-2"
                       x-text="error"></p>
                </template>

                <template x-if="successMessage">
                    <p class="text-[10px] text-emerald-600 font-bold
                              bg-emerald-50 border border-emerald-200
                              rounded-xl px-3 py-2"
                       x-text="successMessage"></p>
                </template>

                <p class="text-[10px] text-[#186369]/50 leading-relaxed">
                    💡 Data ini dipakai untuk personalisasi tes & rekomendasi.
                </p>

                <div class="flex gap-2 pt-2">
                    <button type="button" @click="cancelEdit()"
                            class="flex-1 py-2.5 rounded-2xl
                                   bg-gray-100 text-[#186369] text-sm font-bold
                                   hover:bg-gray-200 transition active:scale-95">
                        Batal
                    </button>
                    <button type="button" @click="save()" :disabled="saving"
                            class="flex-1 py-2.5 rounded-2xl
                                   bg-[#186369] text-white text-sm font-bold
                                   hover:opacity-90 transition active:scale-95
                                   disabled:opacity-60 disabled:cursor-not-allowed
                                   flex items-center justify-center gap-1.5">
                        <svg x-show="!saving" viewBox="0 0 16 16" class="w-3.5 h-3.5"
                             fill="none" stroke="currentColor" stroke-width="2.5"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 8l4 4 7-7"/>
                        </svg>
                        <svg x-show="saving" viewBox="0 0 16 16" class="w-3.5 h-3.5 animate-spin"
                             fill="none" stroke="currentColor" stroke-width="2.5"
                             stroke-linecap="round">
                            <circle cx="8" cy="8" r="6" stroke-dasharray="20 10"/>
                        </svg>
                        <span x-text="saving ? 'Menyimpan...' : 'Simpan'"></span>
                    </button>
                </div>
            </div>
        </template>

        <div class="h-2"></div>
    </div>


    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- POPUP RIWAYAT — SINGKAT                                       --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <template x-if="historyPopupOpen">
        <div x-show="historyPopupOpen" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click.self="closeHistoryPopup()"
             class="fixed inset-0 z-[300] bg-[#0a3d42]/60 backdrop-blur-md
                    flex items-center justify-center p-4">

            <div x-show="historyPopupOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-90 translate-y-4"
                 class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden">

                <div class="relative p-5 pb-4"
                     :style="`background: linear-gradient(135deg,
                                 ${selectedHistory?.testWarna || '#186369'} 0%,
                                 ${selectedHistory?.testWarna || '#186369'}CC 100%);`">

                    <button type="button" @click="closeHistoryPopup()"
                            class="absolute top-3 right-3 w-8 h-8 rounded-full
                                   bg-white/20 hover:bg-white/30 backdrop-blur-sm
                                   flex items-center justify-center
                                   text-white transition active:scale-90">
                        <svg viewBox="0 0 16 16" class="w-3.5 h-3.5" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M4 4l8 8M12 4l-8 8"/>
                        </svg>
                    </button>

                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-widest
                                     bg-white/25 text-white"
                              x-text="selectedHistory?.testJenis"></span>
                        <template x-if="selectedHistory && !selectedHistory.isValid">
                            <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-widest
                                         bg-red-100 text-red-700">
                                INVALID
                            </span>
                        </template>
                    </div>

                    <h3 class="text-white font-bold text-lg leading-tight mb-0.5"
                        x-text="selectedHistory?.testJudul"></h3>
                    <p class="text-[10px] font-bold text-white/70"
                       x-text="selectedHistory?.tanggalLabel"></p>
                </div>

                <div class="p-5 space-y-4">

                    <div class="flex items-center gap-4">
                        <div class="relative w-20 h-20 flex-shrink-0">
                            <svg viewBox="0 0 120 120" class="w-full h-full -rotate-90">
                                <circle cx="60" cy="60" r="50" fill="none"
                                        stroke="#e5e7eb" stroke-width="12"/>
                                <circle cx="60" cy="60" r="50" fill="none"
                                        :stroke="selectedHistory?.severityWarna || '#186369'"
                                        stroke-width="12" stroke-linecap="round"
                                        :stroke-dasharray="`${((selectedHistory?.totalScore || 0) / 100) * 314} 314`"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <p class="text-xl font-bold text-[#186369]"
                                   x-text="(selectedHistory?.totalScore || 0) + '%'"></p>
                            </div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1">
                                Hasil Diagnosis
                            </p>
                            <p class="text-sm font-bold leading-tight"
                               :style="`color: ${selectedHistory?.severityWarna};`"
                               x-text="selectedHistory?.diagnosisLabel || selectedHistory?.severityLabel"></p>
                            <p class="text-[10px] text-gray-500 mt-1 leading-snug"
                               x-text="selectedHistory?.diagnosisSublabel"></p>
                        </div>
                    </div>

                    <template x-if="selectedHistory?.clusters && selectedHistory.clusters.length > 0">
                        <div>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-2">
                                Area yang Paling Terasa
                            </p>
                            <div class="space-y-1.5">
                                <template x-for="(c, ci) in selectedHistory.clusters.slice(0, 3)" :key="ci">
                                    <div class="flex items-center gap-2 bg-gray-50 rounded-xl px-2.5 py-2">
                                        <div class="w-6 h-6 rounded-lg flex items-center justify-center flex-shrink-0 text-sm"
                                             :style="`background: ${c.warna}20;`">
                                            <span x-text="c.icon"></span>
                                        </div>
                                        <p class="text-[10px] font-bold text-[#186369] flex-1 truncate"
                                           x-text="c.nama"></p>
                                        <span class="text-[9px] font-bold flex-shrink-0 px-1.5 py-0.5 rounded"
                                              :style="`background: ${c.levelWarna}15; color: ${c.levelWarna};`"
                                              x-text="c.level"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <div class="bg-[#f5f8f7] rounded-2xl p-3">
                        <p class="text-[10px] text-gray-500 leading-relaxed">
                            💡 Buka halaman lengkap untuk melihat detail gejala, dampak, dan rekomendasi.
                        </p>
                    </div>
                </div>

                <div class="p-4 border-t border-gray-100 flex gap-2">
                    <button type="button" @click="deleteSelectedHistory()"
                            class="px-4 py-2.5 rounded-2xl
                                   bg-red-50 text-red-500 text-xs font-bold
                                   hover:bg-red-100 transition active:scale-95
                                   flex items-center gap-1.5">
                        <svg viewBox="0 0 16 16" class="w-3.5 h-3.5" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                            <path d="M3 5h10M6 5V3h4v2M5 5l1 8h4l1-8"/>
                        </svg>
                        Hapus
                    </button>

                    <a :href="`/tes-mental/${selectedHistory?.testId}/hasil?history=${selectedHistory?.id}`"
                       @click="closeHistoryPopup()"
                       class="flex-1 py-2.5 rounded-2xl
                              bg-[#186369] text-white text-xs font-bold
                              hover:opacity-90 transition active:scale-95
                              flex items-center justify-center gap-1.5">
                        <span>Lihat Hasil Lengkap</span>
                        <svg viewBox="0 0 16 16" class="w-3.5 h-3.5" fill="none"
                             stroke="currentColor" stroke-width="2.5"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 4l4 4-4 4"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </template>
</div>


@push('scripts')
<script>
    window.serverProfile = @json(auth()->user()->profile);
    window.serverUser = {
        name:  @json(auth()->user()->name),
        email: @json(auth()->user()->email),
    };

    function profileEditor() {
        return {
            isEditing: false,
            saving: false,
            error: '',
            successMessage: '',

            user: {
                nama:   window.serverUser?.name  || 'Putranda',
                email:  window.serverUser?.email || 'Putranda456@gmail.com',
                goldar: '',
                tinggi: '',
                berat:  '',
                usia:   '',
                gender: '',
                bio:    '',
                avatar: '{{ asset('images/avatar.png') }}',
            },

            draft: {},
            avatarFile: null,

            testHistory: [],
            historyPopupOpen: false,
            selectedHistory: null,

            init() {
                // Hydrate dari server
                if (window.serverProfile) {
                    this.user = { ...this.user, ...window.serverProfile };
                }

                // Pastikan avatar path lengkap
                if (this.user.avatar
                    && !this.user.avatar.startsWith('http')
                    && !this.user.avatar.startsWith('/')) {
                    this.user.avatar = `/storage/${this.user.avatar}`;
                }

                this.loadTestHistory();

                window.addEventListener('storage', (e) => {
                    if (e.key === 'mindo_test_history') this.loadTestHistory();
                });
            },

            get genderLabel() {
                return {
                    pria: 'Pria',
                    wanita: 'Wanita',
                    lainnya: 'Lainnya',
                }[this.user.gender] || '';
            },

            get isComplete() {
                return this.user.nama && this.user.usia && this.user.gender;
            },

            /* ═══ RIWAYAT ═══ */
            loadTestHistory() {
                try {
                    const s = localStorage.getItem('mindo_test_history');
                    this.testHistory = s ? (JSON.parse(s) || []) : [];
                    if (!Array.isArray(this.testHistory)) this.testHistory = [];
                } catch(e) {
                    this.testHistory = [];
                }
            },

            openHistoryPopup(h) {
                this.selectedHistory = h;
                this.historyPopupOpen = true;
            },

            closeHistoryPopup() {
                this.historyPopupOpen = false;
                this.selectedHistory = null;
            },

            deleteSelectedHistory() {
                if (!this.selectedHistory) return;
                if (!confirm('Hapus riwayat ini?')) return;

                const id = this.selectedHistory.id;
                this.testHistory = this.testHistory.filter(h => h.id !== id);

                try {
                    localStorage.setItem('mindo_test_history', JSON.stringify(this.testHistory));
                } catch(e) {}

                this.closeHistoryPopup();
            },

            clearHistory() {
                if (!confirm('Hapus SEMUA riwayat tes? Tindakan ini tidak bisa dibatalkan.')) return;
                this.testHistory = [];
                try { localStorage.removeItem('mindo_test_history'); } catch(e) {}
            },

            /* ═══ EDIT PROFILE ═══ */
            startEdit() {
                this.draft = {
                    nama:   this.user.nama   || '',
                    usia:   this.user.usia   || '',
                    gender: this.user.gender || '',
                    goldar: this.user.goldar || '',
                    tinggi: this.user.tinggi || '',
                    berat:  this.user.berat  || '',
                    bio:    this.user.bio    || '',
                };
                this.error = '';
                this.successMessage = '';
                this.avatarFile = null;
                this.isEditing = true;
            },

            selectAvatar(event) {
                const file = event.target.files?.[0];
                if (!file) return;

                if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                    this.error = 'Foto harus berformat JPG, PNG, atau WebP.';
                    event.target.value = '';
                    return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    this.error = 'Ukuran foto maksimal 2 MB.';
                    event.target.value = '';
                    return;
                }

                this.avatarFile = file;
                this.user.avatar = URL.createObjectURL(file);
                this.error = '';
            },

            cancelEdit() {
                this.isEditing = false;
                this.error = '';
                this.successMessage = '';
                this.draft = {};
                this.avatarFile = null;
            },

            closeEdit() {
                this.isEditing = false;
                this.error = '';
                this.successMessage = '';
                this.avatarFile = null;
            },

            save() {
                this.error = '';
                this.successMessage = '';

                // ─── Validasi ──────────────────────────────────
                if (!this.draft.nama || this.draft.nama.trim().length < 2) {
                    this.error = 'Nama minimal 2 karakter.';
                    return;
                }

                const usia = Number(this.draft.usia);
                if (!usia || usia < 5 || usia > 120) {
                    this.error = 'Usia harus antara 5-120 tahun.';
                    return;
                }

                if (!this.draft.gender) {
                    this.error = 'Pilih gender.';
                    return;
                }

                // Tinggi (opsional)
                let tinggi = this.draft.tinggi;
                if (tinggi !== '' && tinggi != null) {
                    tinggi = Number(tinggi);
                    if (!Number.isInteger(tinggi) || tinggi < 30 || tinggi > 300) {
                        this.error = 'Tinggi harus bilangan bulat 30-300 cm.';
                        return;
                    }
                } else {
                    tinggi = '';
                }

                // Berat (opsional)
                let berat = this.draft.berat;
                if (berat !== '' && berat != null) {
                    berat = Number(berat);
                    if (!Number.isInteger(berat) || berat < 2 || berat > 500) {
                        this.error = 'Berat harus bilangan bulat 2-500 kg.';
                        return;
                    }
                } else {
                    berat = '';
                }

                // ─── Bangun FormData ──────────────────────────
                const formData = new FormData();
                formData.append('nama',   this.draft.nama.trim());
                formData.append('usia',   usia);
                formData.append('gender', this.draft.gender);
                formData.append('goldar', this.draft.goldar || '');
                formData.append('tinggi', tinggi);
                formData.append('berat',  berat);
                formData.append('bio',    (this.draft.bio || '').trim());
                if (this.avatarFile) formData.append('avatar', this.avatarFile);

                // ─── CSRF ─────────────────────────────────────
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                if (!csrfMeta) {
                    this.error = 'CSRF token tidak ditemukan. Tambahkan <meta name="csrf-token"> di layout.';
                    return;
                }

                // ─── Fetch ────────────────────────────────────
                this.saving = true;

                fetch('{{ route('profile.update') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfMeta.content,
                    },
                    body: formData,
                })
                .then(async response => {
                    const data = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        const firstErr = Object.values(data.errors || {}).flat()[0];
                        throw new Error(firstErr || data.message || 'Gagal menyimpan profil.');
                    }

                    // Update user HANYA setelah sukses
                    if (data.profile) {
                        this.user = { ...this.user, ...data.profile };
                        window.serverProfile = data.profile;
                        window.serverUser = {
                            name:  data.profile.nama,
                            email: data.profile.email,
                        };
                    } else {
                        // Fallback: pakai draft
                        this.user = {
                            ...this.user,
                            nama:   this.draft.nama.trim(),
                            usia:   usia,
                            gender: this.draft.gender,
                            goldar: this.draft.goldar || '',
                            tinggi: tinggi,
                            berat:  berat,
                            bio:    (this.draft.bio || '').trim(),
                        };
                    }

                    this.saving = false;
                    this.isEditing = false;
                    this.avatarFile = null;

                    this.successMessage = 'Profil berhasil disimpan.';
                    setTimeout(() => { this.successMessage = ''; }, 2500);
                })
                .catch(err => {
                    this.saving = false;
                    this.error = err.message || 'Terjadi kesalahan.';
                });
            },
        }
    }
</script>
@endpush