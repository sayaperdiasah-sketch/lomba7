<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - @yield('title', 'Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        html, body { margin: 0; padding: 0; height: 100%; overflow: hidden; }
        body {
            font-family: 'Inter', sans-serif;
            background:
                radial-gradient(ellipse at top left, #f7fbfa 0%, transparent 50%),
                linear-gradient(135deg, #eef4f3 0%, #e5eae9 100%);
        }
        [x-cloak] { display: none !important; }

        .scroll-thin::-webkit-scrollbar { width: 6px; }
        .scroll-thin::-webkit-scrollbar-track { background: transparent; }
        .scroll-thin::-webkit-scrollbar-thumb {
            background: rgba(24, 99, 105, 0.3);
            border-radius: 3px;
        }
        .scroll-thin::-webkit-scrollbar-thumb:hover {
            background: rgba(24, 99, 105, 0.5);
        }
        .scroll-thin-h::-webkit-scrollbar { height: 4px; }
        .scroll-thin-h::-webkit-scrollbar-track { background: transparent; }
        .scroll-thin-h::-webkit-scrollbar-thumb {
            background: rgba(24, 99, 105, 0.25);
            border-radius: 2px;
        }

        /* ═══ SCROLLBAR SIDEBAR NAV (background hijau) ═══ */
        aside.nav-sidebar nav.scroll-thin::-webkit-scrollbar {
            width: 4px;
        }
        aside.nav-sidebar nav.scroll-thin::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.25);
            border-radius: 4px;
        }
        aside.nav-sidebar nav.scroll-thin::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.45);
        }
        aside.nav-sidebar nav.scroll-thin::-webkit-scrollbar-track {
            background: transparent;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        @keyframes nav-breathing {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.06); opacity: 0.9; }
        }
        @keyframes nav-glow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4); }
            50% { box-shadow: 0 0 0 8px rgba(255, 255, 255, 0); }
        }
        .nav-logo { animation: nav-breathing 4s ease-in-out infinite; }
        .nav-icon-btn { position: relative; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .nav-icon-btn:hover { transform: translateY(-2px) scale(1.05); }
        .nav-icon-btn.active { animation: nav-glow 2.5s ease-in-out infinite; }

        /* ═══ TOOLTIP FIXED (bebas dari overflow clipping) ═══ */
        #admin-nav-tooltip {
            position: fixed;
            z-index: 9999;
            padding: 6px 12px;
            background: #186369;
            color: #fff;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            transform: translateY(-50%) translateX(-6px);
            transition: opacity 0.15s, transform 0.15s;
            box-shadow: 0 4px 12px rgba(24, 99, 105, 0.25);
        }
        #admin-nav-tooltip.visible {
            opacity: 1;
            transform: translateY(-50%) translateX(0);
        }
        /* Panah kecil ke kiri */
        #admin-nav-tooltip::before {
            content: '';
            position: absolute;
            right: 100%;
            top: 50%;
            transform: translateY(-50%);
            border: 5px solid transparent;
            border-right-color: #186369;
        }
        /* Sembunyikan di mobile */
        @media (max-width: 1023px) {
            #admin-nav-tooltip { display: none !important; }
        }

        @keyframes item-in {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .item-anim { animation: item-in 0.3s ease-out backwards; }

        @keyframes stat-pop {
            0% { transform: scale(1); }
            50% { transform: scale(1.15); color: #c0a05f; }
            100% { transform: scale(1); }
        }
        .stat-pop { animation: stat-pop 0.4s ease-out; }
    </style>

    @stack('styles')
</head>
<body>

<div class="h-screen flex flex-col lg:flex-row overflow-hidden relative">

    {{-- ═══ SIDEBAR NAV KIRI ═══ --}}
    <aside class="nav-sidebar order-3 lg:order-1 flex-shrink-0 relative
                  flex flex-row lg:flex-col
                  items-center justify-around lg:justify-start
                  px-3 py-2 lg:py-6
                  w-full lg:w-[100px] h-[80px] lg:h-screen
                  gap-1 lg:gap-3 z-10 lg:rounded-r-[60px]"
           style="
               background:
                   radial-gradient(ellipse at top, rgba(255,255,255,0.12) 0%, transparent 60%),
                   linear-gradient(180deg, #1e7378 0%, #186369 30%, #135256 70%, #0e4348 100%);
               box-shadow:
                   inset 0 1px 0 rgba(255,255,255,0.15),
                   inset -20px 0 40px -20px rgba(0,0,0,0.15),
                   20px 0 40px -20px rgba(24,99,105,0.2);
           ">

        <div class="absolute top-20 left-1/2 -translate-x-1/2 w-32 h-32
                    rounded-full bg-white/5 blur-2xl pointer-events-none"></div>
        <div class="absolute bottom-20 left-1/2 -translate-x-1/2 w-24 h-24
                    rounded-full bg-[#9fd6f8]/10 blur-2xl pointer-events-none"></div>

        {{-- LOGO --}}
        <a href="/admin" class="hidden lg:flex flex-col items-center gap-1 flex-shrink-0 relative group">
            <div class="nav-logo relative w-14 h-14 flex items-center justify-center">
                <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-white/25 to-white/5
                            backdrop-blur-sm border border-white/20 shadow-lg"></div>
                <svg viewBox="0 0 24 24" class="relative w-7 h-7" fill="none"
                     stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            <span class="text-[8px] font-bold text-white/70 tracking-widest uppercase">
                Admin
            </span>
        </a>

        <div class="hidden lg:block w-8 h-px bg-white/20 my-1 flex-shrink-0"></div>

        {{-- NAV ITEMS — dengan data-tooltip --}}
        <nav class="flex flex-row lg:flex-col items-center lg:items-stretch justify-around lg:justify-start
                    w-full lg:w-full lg:flex-1 lg:min-h-0
                    gap-1 lg:gap-2
                    overflow-x-auto lg:overflow-x-hidden lg:overflow-y-auto
                    scroll-thin-h lg:scroll-thin
                    py-1 lg:py-2
                    lg:pr-1">

            @php
                $activeNav = $activeNav ?? 'dashboard';

                $navItems = [
                    ['label' => 'Dashboard',   'active' => $activeNav === 'dashboard',   'href' => '/admin',              'path' => 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10'],
                    ['label' => 'Kategori',    'active' => $activeNav === 'kategori',    'href' => '/admin/kategori',     'path' => 'M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z'],
                    ['label' => 'Artikel',     'active' => $activeNav === 'artikel',     'href' => '/admin/artikel',      'path' => 'M4 4a2 2 0 012-2h8l6 6v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z M14 2v6h6 M9 13h6M9 17h6'],
                    ['label' => 'Rekomendasi', 'active' => $activeNav === 'rekomendasi', 'href' => '/admin/rekomendasi',  'path' => 'M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83'],
                    ['label' => 'Tes Mental',  'active' => $activeNav === 'tes',         'href' => '/admin/tes',          'path' => 'M9.5 2A6.5 6.5 0 003 8.5c0 4 4 8 9 13 5-5 9-9 9-13A6.5 6.5 0 009.5 2z'],
                    ['label' => 'Dokter',      'active' => $activeNav === 'dokter',      'href' => '/admin/dokter',       'path' => 'M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2 M12 11a4 4 0 100-8 4 4 0 000 8z'],
                    ['label' => 'FAQ',         'active' => $activeNav === 'faq',         'href' => '/admin/faq',          'path' => 'M8 2a6 6 0 016 6c0 2.5-2 3.5-3 4.5S9 15 9 16 M9 20h.01'],
                ];
            @endphp

            @foreach($navItems as $nav)
                <a href="{{ $nav['href'] }}"
                   aria-label="{{ $nav['label'] }}"
                   data-tooltip="{{ $nav['label'] }}"
                   class="nav-icon-btn {{ $nav['active'] ? 'active' : '' }}
                          flex items-center justify-center flex-shrink-0
                          w-11 h-11 lg:w-[52px] lg:h-[52px]
                          lg:mx-auto
                          rounded-2xl group {{ $nav['active']
                              ? 'bg-white shadow-lg shadow-black/10'
                              : 'bg-white/10 hover:bg-white/20 border border-white/10' }}">
                    <svg viewBox="0 0 24 24" class="w-5 h-5 transition-colors
                                {{ $nav['active'] ? 'text-[#186369]' : 'text-white/80' }}"
                         fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="{{ $nav['path'] }}" />
                    </svg>
                </a>
            @endforeach

        </nav>

        {{-- FOOTER — tombol keluar dengan tooltip --}}
        <div class="hidden lg:flex flex-col items-center gap-2 mt-auto flex-shrink-0">
            <div class="w-8 h-px bg-white/20 mb-1"></div>
            <a href="/"
               aria-label="Keluar Admin"
               data-tooltip="Keluar Admin"
               class="nav-icon-btn
                      flex items-center justify-center w-11 h-11
                      rounded-2xl bg-red-500/20 hover:bg-red-500/40
                      border border-red-400/30 transition">
                <svg viewBox="0 0 24 24" class="w-5 h-5 text-white/80" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
                </svg>
            </a>
            <span class="text-[8px] font-bold text-white/40 tracking-widest uppercase">
                Logout
            </span>
        </div>
    </aside>


    {{-- ═══ MAIN CONTENT ═══ --}}
    <main class="order-1 lg:order-2 flex-1 min-w-0 min-h-0
                 overflow-y-auto scroll-thin
                 p-4 sm:p-6 lg:p-8
                 flex flex-col gap-5"
          style="
              background:
                  radial-gradient(ellipse at top left, #fdfefe 0%, transparent 60%),
                  linear-gradient(135deg, #f5f8f7 0%, #eef3f2 100%);
              box-shadow:
                  inset 1px 0 0 rgba(255,255,255,0.8),
                  inset 0 1px 0 rgba(255,255,255,0.6);
          ">

        @yield('content')

    </main>


    {{-- ═══ SIDEBAR KANAN (statistik) ═══ --}}
    <aside class="order-2 lg:order-3 hidden lg:flex flex-col
                  w-[300px] xl:w-[340px] flex-shrink-0
                  h-screen overflow-y-auto scroll-thin
                  p-6 gap-5"
           style="
               background: linear-gradient(180deg, #ffffff 0%, #f8faf9 100%);
               box-shadow: inset 20px 0 40px -20px rgba(24,99,105,0.06);
           ">

        <div class="flex flex-col items-center gap-3 flex-shrink-0">
            <img src="{{ asset('images/avatar.png') }}"
                 alt="Admin"
                 class="w-20 h-20 rounded-full object-cover
                        border-4 border-[#186369]/20 shadow">

            <div class="text-center">
                <h2 class="text-xl font-bold text-[#186369]">
                    Admin Mindo
                </h2>
                <p class="text-[10px] font-bold text-[#186369]/50 mt-0.5">
                    Administrator
                </p>
            </div>

            <span class="px-3 py-1 rounded-full
                         bg-[#186369] text-white
                         text-[9px] font-bold uppercase tracking-wider">
                Super Admin
            </span>
        </div>

        <div class="h-px bg-gray-100"></div>

        <div class="grid grid-cols-2 gap-2 flex-shrink-0">
            <div class="bg-[#186369]/5 rounded-xl py-2.5 text-center">
                <p class="text-[9px] font-bold text-[#186369]/50">Artikel</p>
                <p class="text-lg font-bold text-[#186369] transition-colors"
                   data-stat="artikel">0</p>
            </div>
            <div class="bg-[#186369]/5 rounded-xl py-2.5 text-center">
                <p class="text-[9px] font-bold text-[#186369]/50">Kategori</p>
                <p class="text-lg font-bold text-[#186369] transition-colors"
                   data-stat="kategori">0</p>
            </div>
            <div class="bg-[#186369]/5 rounded-xl py-2.5 text-center">
                <p class="text-[9px] font-bold text-[#186369]/50">Aktivitas Rekomendasi</p>
                <p class="text-lg font-bold text-[#186369] transition-colors"
                   data-stat="program">0</p>
            </div>
            <div class="bg-[#186369]/5 rounded-xl py-2.5 text-center">
                <p class="text-[9px] font-bold text-[#186369]/50">Dokter</p>
                <p class="text-lg font-bold text-[#186369] transition-colors"
                   data-stat="dokter">0</p>
            </div>
        </div>

        <div class="bg-gray-50 rounded-2xl p-3.5 flex-shrink-0">
            <p class="text-[10px] font-bold text-[#186369] mb-2">🚀 Menu Cepat</p>
            <div class="space-y-1.5">
                <a href="/admin/artikel"
                   class="flex items-center gap-2 py-1.5 px-2 rounded-lg
                          hover:bg-white transition text-[11px] font-bold text-[#186369]">
                    <div class="w-1.5 h-1.5 rounded-full bg-[#186369]"></div>
                    Tambah Artikel
                </a>
                <a href="/admin/kategori"
                   class="flex items-center gap-2 py-1.5 px-2 rounded-lg
                          hover:bg-white transition text-[11px] font-bold text-[#186369]">
                    <div class="w-1.5 h-1.5 rounded-full bg-[#c0a05f]"></div>
                    Kelola Kategori
                </a>
                <a href="/admin/rekomendasi"
                   class="flex items-center gap-2 py-1.5 px-2 rounded-lg
                          hover:bg-white transition text-[11px] font-bold text-[#186369]">
                    <div class="w-1.5 h-1.5 rounded-full bg-[#4090f2]"></div>
                    Kelola Rekomendasi
                </a>
                <a href="/admin/dokter"
                   class="flex items-center gap-2 py-1.5 px-2 rounded-lg
                          hover:bg-white transition text-[11px] font-bold text-[#186369]">
                    <div class="w-1.5 h-1.5 rounded-full bg-[#8b7bd8]"></div>
                    Input Dokter
                </a>
            </div>
        </div>

        <div class="flex-1 min-h-0 flex flex-col">
            <h3 class="text-sm font-bold text-[#186369] mb-3 flex-shrink-0">
                Info Sistem
            </h3>
            <div class="flex-1 min-h-0 overflow-y-auto scroll-thin pr-1 space-y-2">
                <div class="bg-gray-50 rounded-xl px-3.5 py-2.5">
                    <p class="text-[9px] font-bold text-[#186369]/50">Versi</p>
                    <p class="text-xs font-bold text-[#186369]">Mindo v1.0</p>
                </div>
                <div class="bg-gray-50 rounded-xl px-3.5 py-2.5">
                    <p class="text-[9px] font-bold text-[#186369]/50">Framework</p>
                    <p class="text-xs font-bold text-[#186369]">Laravel 12</p>
                </div>
                <div class="bg-gray-50 rounded-xl px-3.5 py-2.5">
                    <p class="text-[9px] font-bold text-[#186369]/50">Storage</p>
                    <p class="text-xs font-bold text-[#186369]">localStorage</p>
                </div>
                <div class="bg-green-50 rounded-xl px-3.5 py-2.5 border border-green-200">
                    <p class="text-[9px] font-bold text-green-600">Status</p>
                    <p class="text-xs font-bold text-green-700 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                        Online
                    </p>
                </div>
            </div>
        </div>

        <a href="/"
           class="flex-shrink-0 flex items-center justify-center gap-2
                  py-3 rounded-2xl
                  bg-red-50 hover:bg-red-100
                  text-red-600 text-xs font-bold
                  transition active:scale-95">
            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
            </svg>
            Keluar dari Admin
        </a>

    </aside>

</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- TOOLTIP FIXED (satu elemen untuk semua, di luar overflow)   --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div id="admin-nav-tooltip"></div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- SCRIPT: Auto-update statistik + Tooltip handler             --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<script>
    (function() {
        /* ═══════════════════════════════════════════════
           TOOLTIP FIXED — bebas dari overflow clipping
           ═══════════════════════════════════════════════ */
        const tooltip = document.getElementById('admin-nav-tooltip');

        function attachTooltips() {
            if (!tooltip) return;

            document.querySelectorAll('.nav-sidebar [data-tooltip]').forEach(el => {
                el.addEventListener('mouseenter', () => {
                    const rect = el.getBoundingClientRect();
                    tooltip.textContent = el.getAttribute('data-tooltip');
                    tooltip.style.left = (rect.right + 14) + 'px';
                    tooltip.style.top  = (rect.top + rect.height / 2) + 'px';
                    tooltip.classList.add('visible');
                });

                el.addEventListener('mouseleave', () => {
                    tooltip.classList.remove('visible');
                });

                el.addEventListener('click', () => {
                    tooltip.classList.remove('visible');
                });
            });

            window.addEventListener('scroll', () => tooltip.classList.remove('visible'), true);
            window.addEventListener('resize', () => tooltip.classList.remove('visible'));
        }

        /* ═══════════════════════════════════════════════
           STATISTIK SIDEBAR
           ═══════════════════════════════════════════════ */
        function updateStats() {
            const stats = {
                artikel:  {{ \App\Models\Article::query()->count() }},
                kategori: {{ \App\Models\Category::query()->where('is_global', true)->count() }},
                program:  {{ \App\Models\RecommendationProgram::query()->count() }},
                dokter:   {{ \App\Models\Doctor::query()->count() }},
            };

            document.querySelectorAll('[data-stat]').forEach(el => {
                const key = el.getAttribute('data-stat');
                const val = stats[key] ?? 0;
                const old = parseInt(el.textContent) || 0;

                el.textContent = val;

                if (old !== 0 && old !== val) {
                    el.classList.remove('stat-pop');
                    void el.offsetWidth;
                    el.classList.add('stat-pop');
                    setTimeout(() => el.classList.remove('stat-pop'), 500);
                }
            });
        }

        /* ═══ INIT ═══ */
        function init() {
            attachTooltips();
            updateStats();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }

        window.refreshAdminStats = updateStats;
    })();
</script>

{{-- ⬇️ WAJIB: render @push('scripts') dari view anak ⬇️ --}}
@stack('scripts')

</body>
</html>