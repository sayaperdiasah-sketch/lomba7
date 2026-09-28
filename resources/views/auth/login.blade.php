<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mindo - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        html, body { margin: 0; padding: 0; height: 100%; overflow: hidden; }
        body { font-family: 'Inter', sans-serif; }

        /* ═══ NAVBAR OVERLAY ═══ */
        .login-overlay {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: 100px;
            z-index: 50;
            background: linear-gradient(180deg, #1e7378 0%, #186369 30%, #135256 70%, #0e4348 100%);
            box-shadow: 20px 0 60px -20px rgba(24, 99, 105, 0.5);
            transition: width 0.75s cubic-bezier(0.65, 0, 0.35, 1);
            overflow: hidden;
            border-top-right-radius: 60px;
            border-bottom-right-radius: 60px;
        }

        .login-overlay.expanded {
            width: 100vw;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            box-shadow: 0 0 80px rgba(24, 99, 105, 0.6);
        }

        .nav-mini {
            position: absolute;
            top: 0; left: 0; bottom: 0;
            width: 100px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            padding: 32px 0;
        }

        .login-form-wrap {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transform: scale(0.8);
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            transition-delay: 0.35s;
            pointer-events: none;
            padding: 24px;
        }

        .login-overlay.expanded .login-form-wrap {
            opacity: 1;
            transform: scale(1);
            pointer-events: auto;
        }

        /* ═══ MODE SWITCHER (Login / Register) ═══ */
        .mode-switch {
            position: relative;
            display: flex;
            background: #f5f8f7;
            border-radius: 16px;
            padding: 4px;
            margin-bottom: 24px;
        }
        .mode-switch-bg {
            position: absolute;
            top: 4px;
            left: 4px;
            width: calc(50% - 4px);
            height: calc(100% - 8px);
            background: linear-gradient(135deg, #186369 0%, #0e4348 100%);
            border-radius: 12px;
            transition: transform 0.4s cubic-bezier(0.65, 0, 0.35, 1);
            box-shadow: 0 4px 12px rgba(24, 99, 105, 0.25);
        }
        .mode-switch-bg.right {
            transform: translateX(100%);
        }
        .mode-btn {
            position: relative;
            z-index: 1;
            flex: 1;
            padding: 10px 0;
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            color: #18636980;
            transition: color 0.3s;
            cursor: pointer;
            background: transparent;
            border: none;
        }
        .mode-btn.active {
            color: #ffffff;
        }

        /* ═══ FORM SCROLL ═══ */
        .auth-form-scroll {
            max-height: calc(100vh - 48px);
            overflow-y: auto;
        }
        .auth-form-scroll::-webkit-scrollbar { width: 4px; }
        .auth-form-scroll::-webkit-scrollbar-track { background: transparent; }
        .auth-form-scroll::-webkit-scrollbar-thumb {
            background: rgba(24, 99, 105, 0.2);
            border-radius: 2px;
        }

        .main-content {
            position: fixed;
            inset: 0;
            z-index: 10;
            opacity: 0;
            transition: opacity 0.6s ease 0.2s;
            background: linear-gradient(135deg, #f5f8f7 0%, #eef3f2 100%);
        }

        .main-content.visible { opacity: 1; }

        @keyframes float-slow {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.4; }
            50% { transform: translate(20px, -20px) scale(1.1); opacity: 0.6; }
        }
        .float-decor { animation: float-slow 8s ease-in-out infinite; }

        @keyframes spin-dot {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }
        .loading-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: white; animation: spin-dot 1.4s ease-in-out infinite both;
        }
        .loading-dot:nth-child(1) { animation-delay: -0.32s; }
        .loading-dot:nth-child(2) { animation-delay: -0.16s; }

        @keyframes fade-slide {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-slide { animation: fade-slide 0.35s ease-out backwards; }
    </style>
</head>
<body>

<div x-data="authApp()" x-init="init()">

    {{-- ═══ NAVBAR OVERLAY ═══ --}}
    <aside class="login-overlay" :class="{ 'expanded': expanded }">

        <div class="absolute top-20 left-1/4 w-64 h-64 rounded-full
                    bg-white/5 blur-3xl pointer-events-none float-decor"></div>
        <div class="absolute bottom-20 right-1/4 w-80 h-80 rounded-full
                    bg-[#9fd6f8]/10 blur-3xl pointer-events-none float-decor"
             style="animation-delay: -4s;"></div>

        {{-- NAV MINI (collapsed) --}}
        <div class="nav-mini" x-show="!expanded">
            <div class="flex flex-col items-center gap-2">
                <div class="w-14 h-14 rounded-2xl bg-white/15
                            flex items-center justify-center backdrop-blur-sm">
                    <svg viewBox="0 0 32 32" class="w-8 h-8">
                        <circle cx="16" cy="16" r="13" fill="none" stroke="white" stroke-width="2" opacity="0.9"/>
                        <path d="M9 16 Q 12 11, 16 16 T 23 16" fill="none"
                              stroke="white" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="16" cy="7" r="1.5" fill="white"/>
                    </svg>
                </div>
            </div>

            <nav class="flex flex-col gap-3">
                <button class="w-11 h-11 rounded-2xl bg-white/10 hover:bg-white/20
                               flex items-center justify-center transition">
                    <svg viewBox="0 0 24 24" class="w-5 h-5 text-white/70" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 12l9-9 9 9M5 10v10h5v-5h4v5h5V10"/>
                    </svg>
                </button>
                <button class="w-11 h-11 rounded-2xl bg-white/10 hover:bg-white/20
                               flex items-center justify-center transition">
                    <svg viewBox="0 0 24 24" class="w-5 h-5 text-white/70" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 3v18M3 12h18"/>
                    </svg>
                </button>
                <button class="w-11 h-11 rounded-2xl bg-white/10 hover:bg-white/20
                               flex items-center justify-center transition">
                    <svg viewBox="0 0 24 24" class="w-5 h-5 text-white/70" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/>
                    </svg>
                </button>
            </nav>

            <button @click="expand()"
                    title="Login / Daftar"
                    class="w-12 h-12 rounded-2xl bg-white shadow-lg
                           flex items-center justify-center
                           hover:scale-110 active:scale-95 transition
                           ring-2 ring-white/30">
                <svg viewBox="0 0 24 24" class="w-5 h-5 text-[#186369]" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/>
                </svg>
            </button>
        </div>

        {{-- AUTH FORM (expanded) — Login / Register toggle --}}
        <div class="login-form-wrap">
            <div class="auth-form-scroll w-[400px] max-w-[92vw]">
                <div class="bg-white rounded-[32px] p-7 sm:p-8
                            shadow-2xl border border-white/20">

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
                            x-text="mode === 'login' ? 'Selamat Datang' : 'Buat Akun Baru'"></h2>
                        <p class="text-xs text-[#186369]/60 mt-1"
                           x-text="mode === 'login'
                               ? 'Login untuk melanjutkan ke Mindo'
                               : 'Daftar gratis, mulai perjalananmu'"></p>
                    </div>

                    {{-- ═══ MODE SWITCHER ═══ --}}
                    <div class="mode-switch">
                        <div class="mode-switch-bg" :class="{ 'right': mode === 'register' }"></div>
                        <button type="button"
                                class="mode-btn"
                                :class="{ 'active': mode === 'login' }"
                                @click="switchMode('login')">
                            Masuk
                        </button>
                        <button type="button"
                                class="mode-btn"
                                :class="{ 'active': mode === 'register' }"
                                @click="switchMode('register')">
                            Daftar
                        </button>
                    </div>

                    {{-- ═══════════════════════════════════════════════ --}}
                    {{-- FORM LOGIN                                       --}}
                    {{-- ═══════════════════════════════════════════════ --}}
                    <template x-if="mode === 'login'">
                        <form method="POST" action="{{ route('login.post') }}"
                              @submit.prevent="submitForm($event, 'login')"
                              class="space-y-4 fade-slide">
                            @csrf

                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1.5">Email</label>
                                <input type="email" name="email" required
                                       x-model="email"
                                       value="{{ old('email') }}"
                                       placeholder="nama@email.com"
                                       class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl
                                              focus:outline-none focus:border-[#186369] text-sm transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1.5">Password</label>
                                <input type="password" name="password" required
                                       x-model="password"
                                       placeholder="••••••••"
                                       class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl
                                              focus:outline-none focus:border-[#186369] text-sm transition">
                            </div>

                            {{-- Error Laravel --}}
                            @if ($errors->any() && old('_form') !== 'register')
                                <div class="text-[11px] text-red-500 bg-red-50 border border-red-200
                                            rounded-xl px-3 py-2 fade-slide">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <button type="submit"
                                    :disabled="loading"
                                    class="w-full py-3 bg-[#186369] text-white font-bold rounded-xl
                                           hover:opacity-90 transition active:scale-95
                                           disabled:opacity-70 disabled:cursor-not-allowed
                                           flex items-center justify-center gap-2">
                                <template x-if="!loading">
                                    <span>Masuk</span>
                                </template>
                                <template x-if="loading">
                                    <div class="flex items-center gap-1.5">
                                        <span class="loading-dot"></span>
                                        <span class="loading-dot"></span>
                                        <span class="loading-dot"></span>
                                    </div>
                                </template>
                            </button>
                        </form>
                    </template>

                    {{-- ═══════════════════════════════════════════════ --}}
                    {{-- FORM REGISTER                                    --}}
                    {{-- ═══════════════════════════════════════════════ --}}
                    <template x-if="mode === 'register'">
                        <form method="POST" action="{{ route('register.post') }}"
                              @submit.prevent="submitForm($event, 'register')"
                              class="space-y-3.5 fade-slide">
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

                            {{-- Error Laravel (khusus form register) --}}
                            @if ($errors->any() && old('_form') === 'register')
                                <div class="rounded-xl bg-red-50 border border-red-200 p-3
                                            text-[11px] text-red-600 space-y-0.5 fade-slide">
                                    @foreach ($errors->all() as $error)
                                        <p>{{ $error }}</p>
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
                                    :disabled="loading"
                                    class="w-full py-3 bg-[#186369] text-white font-bold rounded-xl
                                           hover:opacity-90 transition active:scale-95
                                           disabled:opacity-70 disabled:cursor-not-allowed
                                           flex items-center justify-center gap-2 mt-1">
                                <template x-if="!loading">
                                    <span class="flex items-center gap-1.5">
                                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none"
                                             stroke="currentColor" stroke-width="2.5"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                            <circle cx="8.5" cy="7" r="4"/>
                                            <path d="M20 8v6M23 11h-6"/>
                                        </svg>
                                        Daftar Sekarang
                                    </span>
                                </template>
                                <template x-if="loading">
                                    <div class="flex items-center gap-1.5">
                                        <span class="loading-dot"></span>
                                        <span class="loading-dot"></span>
                                        <span class="loading-dot"></span>
                                    </div>
                                </template>
                            </button>
                        </form>
                    </template>

                    {{-- ═══ BACK BUTTON ═══ --}}
                    <button type="button" @click="collapse()"
                            class="w-full text-xs font-bold text-gray-400
                                   hover:text-[#186369] transition py-2 mt-2">
                        ← Kembali
                    </button>
                </div>
            </div>
        </div>

    </aside>


    {{-- ═══ MAIN CONTENT (setelah login) ═══ --}}
    <main class="main-content flex items-center justify-center"
          :class="{ 'visible': loggedIn }">

        <div class="text-center px-6">
            <div class="w-20 h-20 rounded-3xl bg-[#186369]
                        flex items-center justify-center mx-auto mb-5 shadow-xl">
                <svg viewBox="0 0 32 32" class="w-12 h-12">
                    <circle cx="16" cy="16" r="13" fill="none" stroke="white" stroke-width="2"/>
                    <path d="M9 16 Q 12 11, 16 16 T 23 16" fill="none"
                          stroke="white" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="16" cy="7" r="1.5" fill="white"/>
                </svg>
            </div>

            <h1 class="text-4xl font-bold text-[#186369]">Dashboard</h1>
            <p class="text-sm text-[#186369]/60 mt-2">
                Selamat datang! 👋
            </p>

            <form method="POST" action="{{ route('logout') }}" class="mt-8">
                @csrf
                <button type="submit"
                        class="px-8 py-3 bg-[#186369] text-white rounded-xl
                               font-bold text-sm hover:opacity-90
                               transition active:scale-95
                               flex items-center gap-2 mx-auto">
                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>

    </main>

</div>


<script>
    function authApp() {
        return {
            expanded: false,
            loggedIn: {{ auth()->check() ? 'true' : 'false' }},
            loading: false,

            /* ⬇️ MODE: 'login' atau 'register' */
            mode: '{{ old("_form") === "register" ? "register" : "login" }}',

            email: '{{ old("email") }}',
            password: '',

            init() {
                /* Auto expand kalau belum login */
                @if(!auth()->check())
                    setTimeout(() => this.expand(), 600);
                @endif

                /* Auto expand + switch ke register kalau ada error dari register */
                @if($errors->any())
                    setTimeout(() => {
                        this.expand();
                        this.mode = '{{ old("_form") === "register" ? "register" : "login" }}';
                    }, 300);
                @endif
            },

            expand() {
                this.expanded = true;
            },

            collapse() {
                this.expanded = false;
            },

            switchMode(newMode) {
                if (this.mode === newMode) return;
                this.mode = newMode;
            },

            submitForm(e, formType) {
                /* Validasi cepat sebelum submit ke Laravel */
                if (formType === 'register') {
                    const form = e.target;
                    const password = form.querySelector('[name="password"]').value;
                    const confirm  = form.querySelector('[name="password_confirmation"]').value;

                    if (password.length < 6) {
                        alert('Password minimal 6 karakter.');
                        return;
                    }
                    if (password !== confirm) {
                        alert('Konfirmasi password tidak cocok.');
                        return;
                    }
                }

                this.loading = true;
                e.target.submit();
            },
        }
    }
</script>

</body>
</html>