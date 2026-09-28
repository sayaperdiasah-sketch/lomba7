{{-- ═══════════════════════════════════════════════════════════
     REGISTER MODAL — Form daftar akun
     Pakai scope Alpine dari parent (shellApp)
     ═══════════════════════════════════════════════════════════ --}}
<div x-show="registerOpen"
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click.self="closeRegister()"
     @keydown.escape.window="closeRegister()"
     class="fixed inset-0 z-[200] bg-[#0a3d42]/50 backdrop-blur-sm
            flex items-center justify-center p-4 overflow-y-auto">

    <div x-show="registerOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-90 translate-y-4"
         class="bg-white rounded-3xl shadow-2xl w-full max-w-md
                my-8 overflow-hidden">

        {{-- ═══ HEADER ═══ --}}
        <div class="relative p-6 pb-4
                    bg-gradient-to-br from-[#186369] to-[#0e4348]">
            <div class="absolute top-0 right-0 w-40 h-40
                        bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2
                        pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24
                        bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2
                        pointer-events-none"></div>

            <button type="button" @click="closeRegister()"
                    class="absolute top-4 right-4 w-8 h-8 rounded-full
                           bg-white/15 hover:bg-white/25
                           flex items-center justify-center
                           transition active:scale-90 z-10">
                <svg viewBox="0 0 16 16" class="w-3.5 h-3.5 text-white">
                    <path d="M4 4l8 8M12 4l-8 8" fill="none"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
            </button>

            <div class="relative flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur-sm
                            border border-white/20
                            flex items-center justify-center flex-shrink-0">
                    <svg viewBox="0 0 24 24" class="w-6 h-6 text-white" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <path d="M20 8v6M23 11h-6"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Daftar Akun</h2>
                    <p class="text-[11px] font-bold text-white/70 mt-0.5">
                        Buat akun Mindo gratis
                    </p>
                </div>
            </div>
        </div>

        {{-- ═══ FORM ═══ --}}
        <form method="POST" action="{{ route('register.post') }}"
              class="p-6 space-y-4">
            @csrf

            {{-- Error message --}}
            @if ($errors->any())
                <div class="rounded-2xl bg-red-50 border-2 border-red-100 p-3.5
                            flex items-start gap-2.5">
                    <svg viewBox="0 0 24 24" class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5"
                         fill="none" stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 8v4M12 16h.01"/>
                    </svg>
                    <div class="flex-1 text-[11px] font-bold text-red-600 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Nama --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">
                    Nama Lengkap <span class="text-red-400">*</span>
                </label>
                <div class="relative">
                    <svg viewBox="0 0 24 24"
                         class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2"
                         fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <input type="text" name="name" required
                           value="{{ old('name') }}"
                           placeholder="Nama kamu"
                           autocomplete="name"
                           class="w-full pl-11 pr-4 py-3
                                  border-2 border-gray-200 rounded-xl
                                  focus:outline-none focus:border-[#186369]
                                  text-sm font-bold text-[#186369]
                                  placeholder:text-gray-400 placeholder:font-normal">
                </div>
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">
                    Email <span class="text-red-400">*</span>
                </label>
                <div class="relative">
                    <svg viewBox="0 0 24 24"
                         class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2"
                         fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <path d="M22 6l-10 7L2 6"/>
                    </svg>
                    <input type="email" name="email" required
                           value="{{ old('email') }}"
                           placeholder="nama@email.com"
                           autocomplete="email"
                           class="w-full pl-11 pr-4 py-3
                                  border-2 border-gray-200 rounded-xl
                                  focus:outline-none focus:border-[#186369]
                                  text-sm font-bold text-[#186369]
                                  placeholder:text-gray-400 placeholder:font-normal">
                </div>
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">
                    Password <span class="text-red-400">*</span>
                </label>
                <div class="relative">
                    <svg viewBox="0 0 24 24"
                         class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2"
                         fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0110 0v4"/>
                    </svg>
                    <input type="password" name="password" required
                           placeholder="Minimal 6 karakter"
                           autocomplete="new-password"
                           class="w-full pl-11 pr-4 py-3
                                  border-2 border-gray-200 rounded-xl
                                  focus:outline-none focus:border-[#186369]
                                  text-sm font-bold text-[#186369]
                                  placeholder:text-gray-400 placeholder:font-normal">
                </div>
            </div>

            {{-- Konfirmasi Password --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">
                    Konfirmasi Password <span class="text-red-400">*</span>
                </label>
                <div class="relative">
                    <svg viewBox="0 0 24 24"
                         class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2"
                         fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0110 0v4"/>
                        <path d="M12 16v-2"/>
                    </svg>
                    <input type="password" name="password_confirmation" required
                           placeholder="Ulangi password"
                           autocomplete="new-password"
                           class="w-full pl-11 pr-4 py-3
                                  border-2 border-gray-200 rounded-xl
                                  focus:outline-none focus:border-[#186369]
                                  text-sm font-bold text-[#186369]
                                  placeholder:text-gray-400 placeholder:font-normal">
                </div>
            </div>

            {{-- Terms --}}
            <label class="flex items-start gap-2.5 cursor-pointer select-none pt-1">
                <input type="checkbox" required
                       class="peer sr-only">
                <div class="w-4 h-4 rounded border-2 border-gray-300 mt-0.5
                            peer-checked:bg-[#186369] peer-checked:border-[#186369]
                            flex items-center justify-center flex-shrink-0
                            transition">
                    <svg viewBox="0 0 16 16" class="w-2.5 h-2.5 hidden peer-checked:block"
                         fill="none" stroke="white" stroke-width="3"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 8l4 4 7-7"/>
                    </svg>
                </div>
                <span class="text-[11px] text-gray-500 leading-relaxed">
                    Saya setuju dengan
                    <a href="#" class="font-bold text-[#186369] hover:underline">Syarat & Ketentuan</a>
                    dan
                    <a href="#" class="font-bold text-[#186369] hover:underline">Kebijakan Privasi</a>
                </span>
            </label>

            {{-- Submit --}}
            <button type="submit"
                    class="w-full py-3 rounded-2xl
                           bg-gradient-to-br from-[#186369] to-[#0e4348]
                           text-white font-bold text-sm
                           hover:opacity-95 transition active:scale-[0.98]
                           flex items-center justify-center gap-2
                           shadow-lg shadow-[#186369]/20">
                <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none"
                     stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                    <circle cx="8.5" cy="7" r="4"/>
                    <path d="M20 8v6M23 11h-6"/>
                </svg>
                Daftar Sekarang
            </button>

            {{-- Divider --}}
            <div class="flex items-center gap-3 py-1">
                <div class="flex-1 h-px bg-gray-200"></div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                    Sudah punya akun?
                </span>
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>

            {{-- Switch ke Login --}}
            <button type="button" @click="switchToLogin()"
                    class="w-full py-3 rounded-2xl
                           bg-[#f5f8f7] hover:bg-[#eef3f2]
                           text-[#186369] font-bold text-sm
                           transition active:scale-[0.98]
                           flex items-center justify-center gap-2">
                <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none"
                     stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/>
                    <path d="M10 17l5-5-5-5M15 12H3"/>
                </svg>
                Masuk ke Akun
            </button>
        </form>
    </div>
</div>