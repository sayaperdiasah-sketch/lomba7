<aside class="navbar-user order-3 lg:order-1 flex-shrink-0 relative
              flex flex-row lg:flex-col
              items-center justify-around lg:justify-start
              px-3 py-2 lg:py-8
              w-full lg:w-[100px] h-[80px] lg:h-screen
              gap-1 lg:gap-4 z-50 lg:rounded-r-[60px]"
       :class="{ 'expanded': loginOpen }"
       style="
           background:
               radial-gradient(ellipse at top, rgba(255,255,255,0.12) 0%, transparent 60%),
               linear-gradient(180deg, #1e7378 0%, #186369 30%, #135256 70%, #0e4348 100%);
           box-shadow:
               inset 0 1px 0 rgba(255,255,255,0.15),
               inset -20px 0 40px -20px rgba(0,0,0,0.15),
               20px 0 40px -20px rgba(24,99,105,0.2);
           overflow: visible;
       ">

    {{-- ═══ DECORATION WRAPPER ═══ --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden lg:rounded-r-[60px]"
         :class="{ 'lg:rounded-r-none': loginOpen }">
        <div class="absolute top-20 left-1/4 w-64 h-64 rounded-full
                    bg-white/5 blur-3xl float-decor"></div>
        <div class="absolute bottom-20 right-1/4 w-80 h-80 rounded-full
                    bg-[#9fd6f8]/10 blur-3xl float-decor"
             style="animation-delay: -4s;"></div>
    </div>

    {{-- ═══ NAV CONTENT ═══ --}}
    <div class="nav-content contents lg:flex lg:flex-col lg:items-center lg:justify-between lg:w-full lg:h-full lg:min-h-0 lg:gap-4 relative z-10">

        {{-- Logo --}}
        <a href="/" class="hidden lg:flex items-center justify-center flex-shrink-0 relative group">
            <div class="nav-logo relative w-14 h-14 flex items-center justify-center">
                <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-white/25 to-white/5
                            backdrop-blur-sm border border-white/20 shadow-lg"></div>
                <svg viewBox="0 0 32 32" class="relative w-8 h-8">
                    <circle cx="16" cy="16" r="13" fill="none" stroke="white" stroke-width="2" opacity="0.9" />
                    <path d="M9 16 Q 12 11, 16 16 T 23 16" fill="none"
                          stroke="white" stroke-width="2" stroke-linecap="round" />
                    <circle cx="16" cy="7" r="1.5" fill="white" />
                </svg>
            </div>
        </a>

        <div class="hidden lg:block w-8 h-px bg-white/20 my-2"></div>

        {{-- Nav Menu --}}
            <nav class="flex min-w-0 flex-1 flex-row items-center justify-start
                    overflow-x-auto scroll-thin-h lg:min-h-0 lg:flex-col lg:justify-start
                    lg:overflow-y-auto lg:overflow-x-hidden lg:scroll-thin
                lg:w-auto gap-2 lg:gap-4">

            @php
                $navItems = [
                    ['label' => 'Home',       'active' => $activeNav === 'home',       'href' => '/',           'path' => 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10', 'guest_ok' => true],
                    ['label' => 'Artikel',   'active' => $activeNav === 'artikel',    'href' => '/artikel',    'path' => 'M4 4a2 2 0 012-2h8l6 6v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z M14 2v6h6 M9 13h6M9 17h6', 'guest_ok' => true],
                    ['label' => 'Test',       'active' => $activeNav === 'tes',        'href' => '/tes-mental', 'path' => 'M12 21C7 16 3 12.5 3 8.5a4.5 4.5 0 019-1.5 4.5 4.5 0 019 1.5c0 4-4 7.5-9 12.5z', 'guest_ok' => false],
                    ['label' => 'To Do List', 'active' => $activeNav === 'jadwal',     'href' => '/jadwal',     'path' => 'M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z', 'guest_ok' => false],
                    ['label' => 'Diary',      'active' => $activeNav === 'jurnal',     'href' => '/jurnal',     'path' => 'M4 4a2 2 0 012-2h12a2 2 0 012 2v18l-4-2-4 2-4-2-4 2V4z M8 7h8M8 11h8M8 15h5', 'guest_ok' => false],
                    ['label' => 'Konsultasi', 'active' => $activeNav === 'konsultasi', 'href' => '/konsultasi', 'path' => 'M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z', 'guest_ok' => false],
                ];
            @endphp

            @foreach($navItems as $nav)
                <a href="{{ $nav['href'] }}"
                   aria-label="{{ $nav['label'] }}"
                   @if(!$nav['guest_ok']) data-auth @endif
                   class="nav-icon-btn {{ $nav['active'] ? 'active' : '' }}
                          flex items-center justify-center w-12 h-12 lg:w-[56px] lg:h-[56px]
                          rounded-2xl group {{ $nav['active']
                              ? 'bg-white shadow-lg shadow-black/10'
                              : 'bg-white/10 hover:bg-white/20 border border-white/10' }}">
                    <svg viewBox="0 0 24 24" class="w-5 h-5 lg:w-6 lg:h-6 transition-colors
                                {{ $nav['active'] ? 'text-[#186369]' : 'text-white/80' }}"
                         fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="{{ $nav['path'] }}" />
                    </svg>
                    <span class="nav-tooltip">{{ $nav['label'] }}</span>
                    <span class="lg:hidden absolute -bottom-1 text-[8px] font-bold
                                 {{ $nav['active'] ? 'text-[#186369]' : 'text-white/60' }}">
                        {{ $nav['label'] }}
                    </span>
                </a>
            @endforeach
        </nav>

        {{-- BOTTOM: Login / Logout --}}
        <div class="flex flex-shrink-0 items-center gap-2 lg:flex-col lg:items-center lg:mt-auto">
            <div class="hidden w-8 h-px bg-white/20 mb-2 lg:block"></div>

            @guest
                <button type="button"
                        @click="openLogin()"
                        title="Login / Daftar"
                        class="nav-icon-btn
                               flex items-center justify-center w-12 h-12
                               rounded-2xl bg-white shadow-lg
                               hover:scale-110 active:scale-95 transition
                               ring-2 ring-white/30">
                    <svg viewBox="0 0 24 24" class="w-5 h-5 text-[#186369]" fill="none"
                         stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/>
                    </svg>
                    <span class="nav-tooltip">Login / Daftar</span>
                </button>
                <span class="hidden text-[8px] font-bold text-white/40 tracking-widest uppercase lg:block">
                    Guest
                </span>
            @endguest

            @auth
                <a href="/logout"
                   onclick="event.preventDefault(); if(confirm('Yakin mau logout?')) document.getElementById('logout-form-sidebar').submit();"
                   title="Logout"
                   class="nav-icon-btn
                          flex items-center justify-center w-12 h-12
                          rounded-2xl bg-red-500/20 hover:bg-red-500/40
                          border border-red-400/30 transition">
                    <svg viewBox="0 0 24 24" class="w-5 h-5 text-white" fill="none"
                         stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
                    </svg>
                    <span class="nav-tooltip">Logout</span>
                </a>

                <form id="logout-form-sidebar" method="POST" action="/logout" class="hidden">
                    @csrf
                </form>

                <span class="hidden text-[8px] font-bold text-white/40 tracking-widest uppercase lg:block">
                    Logout
                </span>
            @endauth
        </div>
    </div>


    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- LOGIN / REGISTER FORM — hanya guest                          --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @guest
        <div class="login-form-wrap">
            <button type="button"
                    @click="closeLogin()"
                    class="absolute top-6 right-6 z-20
                           w-11 h-11 rounded-full
                           bg-white/10 hover:bg-white/20
                           backdrop-blur-sm border border-white/20
                           flex items-center justify-center
                           transition active:scale-90">
                <svg viewBox="0 0 24 24" class="w-5 h-5 text-white" fill="none"
                     stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 6l12 12M18 6L6 18"/>
                </svg>
            </button>

            <div class="relative z-10
                        w-[400px] max-w-[calc(100vw-3rem)]
                        bg-white rounded-[32px] p-7 sm:p-8
                        shadow-2xl border border-white/20
                        max-h-[calc(100vh-3rem)] overflow-y-auto scroll-thin">

                {{-- ═══ HEADER ═══ --}}
                <div class="text-center mb-6">
                    <div class="w-16 h-16 rounded-2xl bg-[#186369]/10
                                flex items-center justify-center mx-auto mb-3">
                        <svg viewBox="0 0 32 32" class="w-9 h-9">
                            <circle cx="16" cy="16" r="13" fill="none" stroke="#186369" stroke-width="2"/>
                            <path d="M9 16 Q 12 11, 16 16 T 23 16" fill="none"
                                  stroke="#186369" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="16" cy="7" r="1.5" fill="#186369"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-[#186369]"
                        x-text="loginMode === 'login' ? 'Selamat Datang' : 'Buat Akun Baru'"></h2>
                    <p class="text-xs text-[#186369]/60 mt-1"
                       x-text="loginMode === 'login'
                           ? 'Login untuk melanjutkan ke Mindo'
                           : 'Daftar gratis, mulai perjalananmu'"></p>
                </div>

                {{-- ═══ MODE SWITCHER ═══ --}}
                <div class="relative flex bg-[#f5f8f7] rounded-2xl p-1 mb-6">
                    <div class="absolute top-1 left-1 w-[calc(50%-4px)] h-[calc(100%-8px)]
                                bg-gradient-to-br from-[#186369] to-[#0e4348]
                                rounded-xl shadow-md
                                transition-transform duration-300 ease-out"
                         :class="{ 'translate-x-full': loginMode === 'register' }"></div>

                    <button type="button"
                            @click="loginMode = 'login'"
                            class="relative z-10 flex-1 py-2.5 text-sm font-bold
                                   transition-colors rounded-xl"
                            :class="loginMode === 'login' ? 'text-white' : 'text-[#186369]/60'">
                        Masuk
                    </button>
                    <button type="button"
                            @click="loginMode = 'register'"
                            class="relative z-10 flex-1 py-2.5 text-sm font-bold
                                   transition-colors rounded-xl"
                            :class="loginMode === 'register' ? 'text-white' : 'text-[#186369]/60'">
                        Daftar
                    </button>
                </div>

                {{-- ═══════════════════════════════════════════════ --}}
                {{-- FORM LOGIN                                       --}}
                {{-- ═══════════════════════════════════════════════ --}}
                <template x-if="loginMode === 'login'">
                    <form method="POST"
                          action="{{ route('login.post') }}"
                          class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1.5">Email</label>
                            <input type="email" name="email" required value="{{ old('email') }}"
                                   placeholder="nama@email.com"
                                   class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl
                                          focus:outline-none focus:border-[#186369] text-sm transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1.5">Password</label>
                            <input type="password" name="password" required
                                   placeholder="••••••••"
                                   class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl
                                          focus:outline-none focus:border-[#186369] text-sm transition">
                        </div>

                        @if ($errors->any() && old('_form') !== 'register')
                            <div class="text-[11px] text-red-500 bg-red-50 border border-red-200
                                        rounded-xl px-3 py-2 flex items-start gap-2">
                                <span>❌</span>
                                <span>{{ $errors->first() }}</span>
                            </div>
                        @endif

                        <div class="bg-[#f5f8f7] rounded-xl px-3 py-2.5 border border-gray-200">
                            <p class="text-[10px] font-bold text-[#186369]/70 leading-relaxed">
                                <b>Demo Akun:</b><br>
                                Email: <code class="bg-white px-1 rounded">putranda@mindo.app</code><br>
                                Password: <code class="bg-white px-1 rounded">password123</code>
                            </p>
                        </div>

                        <button type="submit"
                                class="w-full py-3 bg-[#186369] text-white font-bold rounded-xl
                                       hover:opacity-90 transition active:scale-95">
                            Login
                        </button>
                    </form>
                </template>

                {{-- ═══════════════════════════════════════════════ --}}
                {{-- FORM REGISTER                                    --}}
                {{-- ═══════════════════════════════════════════════ --}}
                <template x-if="loginMode === 'register'">
                    <form method="POST"
                          action="{{ route('register.post') }}"
                          class="space-y-3.5">
                        @csrf
                        <input type="hidden" name="_form" value="register">

                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1.5">
                                Nama Lengkap
                            </label>
                            <div class="relative">
                                <svg viewBox="0 0 24 24"
                                     class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"
                                     fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                <input type="text" name="name" required
                                       value="{{ old('name') }}"
                                       placeholder="Nama kamu"
                                       autocomplete="name"
                                       class="w-full pl-10 pr-4 py-2.5 border-2 border-gray-200 rounded-xl
                                              focus:outline-none focus:border-[#186369] text-sm transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1.5">Email</label>
                            <div class="relative">
                                <svg viewBox="0 0 24 24"
                                     class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"
                                     fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                    <path d="M22 6l-10 7L2 6"/>
                                </svg>
                                <input type="email" name="email" required
                                       value="{{ old('email') }}"
                                       placeholder="nama@email.com"
                                       autocomplete="email"
                                       class="w-full pl-10 pr-4 py-2.5 border-2 border-gray-200 rounded-xl
                                              focus:outline-none focus:border-[#186369] text-sm transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1.5">Password</label>
                            <div class="relative">
                                <svg viewBox="0 0 24 24"
                                     class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"
                                     fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2"/>
                                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                                </svg>
                                <input type="password" name="password" required
                                       placeholder="Minimal 6 karakter"
                                       autocomplete="new-password"
                                       class="w-full pl-10 pr-4 py-2.5 border-2 border-gray-200 rounded-xl
                                              focus:outline-none focus:border-[#186369] text-sm transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1.5">
                                Konfirmasi Password
                            </label>
                            <div class="relative">
                                <svg viewBox="0 0 24 24"
                                     class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"
                                     fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2"/>
                                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                                    <path d="M12 16v-2"/>
                                </svg>
                                <input type="password" name="password_confirmation" required
                                       placeholder="Ulangi password"
                                       autocomplete="new-password"
                                       class="w-full pl-10 pr-4 py-2.5 border-2 border-gray-200 rounded-xl
                                              focus:outline-none focus:border-[#186369] text-sm transition">
                            </div>
                        </div>

                        @if ($errors->any() && old('_form') === 'register')
                            <div class="rounded-xl bg-red-50 border border-red-200 p-3
                                        text-[11px] text-red-600 space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <p>❌ {{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        <label class="flex items-start gap-2 cursor-pointer select-none pt-0.5">
                            <input type="checkbox" required class="peer sr-only">
                            <div class="w-4 h-4 rounded border-2 border-gray-300 mt-0.5
                                        peer-checked:bg-[#186369] peer-checked:border-[#186369]
                                        flex items-center justify-center flex-shrink-0 transition">
                                <svg viewBox="0 0 16 16" class="w-2.5 h-2.5 opacity-0 peer-checked:opacity-100"
                                     fill="none" stroke="white" stroke-width="3"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 8l4 4 7-7"/>
                                </svg>
                            </div>
                            <span class="text-[10px] text-gray-500 leading-relaxed">
                                Saya setuju dengan
                                <a href="#" class="font-bold text-[#186369] hover:underline">Syarat & Ketentuan</a>
                                dan
                                <a href="#" class="font-bold text-[#186369] hover:underline">Kebijakan Privasi</a>
                            </span>
                        </label>

                        <button type="submit"
                                class="w-full py-3 bg-[#186369] text-white font-bold rounded-xl
                                       hover:opacity-90 transition active:scale-95
                                       flex items-center justify-center gap-2">
                            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none"
                                 stroke="currentColor" stroke-width="2.5"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                <circle cx="8.5" cy="7" r="4"/>
                                <path d="M20 8v6M23 11h-6"/>
                            </svg>
                            Daftar Sekarang
                        </button>
                    </form>
                </template>
            </div>
        </div>
    @endguest

</aside>