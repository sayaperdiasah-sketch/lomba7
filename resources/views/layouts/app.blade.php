<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mindo - @yield('title', 'Beranda')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/mindo.css') }}">

    @stack('styles')
</head>
<body>

<div class="h-screen flex flex-col lg:flex-row overflow-hidden relative"
     x-data="{ ...shellApp() }"
     @click.capture="handleAuthClick($event)">

    @include('partials.sidebar-user')

    <main class="main-content order-1 lg:order-2 flex-1 min-w-0 min-h-0
                 overflow-y-auto scroll-thin
                 p-4 sm:p-6 lg:p-8
                 flex flex-col gap-5"
          :class="{ 'blurred': loginOpen }"
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

    @stack('sidebar-right')

    @include('partials.profile-drawer')

</div>

<script>
    function shellApp() {
        return {
            isLoggedIn: {{ auth()->check() ? 'true' : 'false' }},

            /* ═══════════════════════════════════════════════
               LOGIN / REGISTER STATE
               ═══════════════════════════════════════════════ */
            loginOpen: false,
            loginMode: '{{ old("_form") === "register" ? "register" : "login" }}',

            openLogin() {
                this.loginOpen = true;
                // Auto set mode register kalau ada error dari form register
                @if($errors->any() && old('_form') === 'register')
                    this.loginMode = 'register';
                @endif
            },

            closeLogin() {
                this.loginOpen = false;
                this.loginMode = 'login';
            },

            handleAuthClick(e) {
                if (this.isLoggedIn) return;
                const el = e.target.closest('[data-auth]');
                if (!el) return;
                e.preventDefault();
                e.stopPropagation();
                this.openLogin();
            },

            /* ═══════════════════════════════════════════════
               PROFILE DRAWER
               ═══════════════════════════════════════════════ */
            profileOpen: false,
            rippleActive: false,
            breatheActive: false,

            toggleProfile() {
                this.rippleActive = true;
                setTimeout(() => this.rippleActive = false, 600);
                this.profileOpen = !this.profileOpen;

                if (this.profileOpen) {
                    setTimeout(() => {
                        this.breatheActive = true;
                        setTimeout(() => this.breatheActive = false, 4200);
                    }, 500);

                    // Auto-load reminders setiap buka drawer
                    this.refreshReminders();
                } else {
                    this.breatheActive = false;
                }
            },

            /* ═══════════════════════════════════════════════
               REMINDERS — Quest yang belum selesai
               ═══════════════════════════════════════════════ */
            reminders: [],
            remindersTotal: 0,

            refreshReminders() {
                const MAX = 6;
                const monthNames = [
                    'Jan','Feb','Mar','Apr','Mei','Jun',
                    'Jul','Agu','Sep','Okt','Nov','Des'
                ];
                const dayNames = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];

                let schedules = {};
                let categories = [];

                try {
                    schedules  = JSON.parse(localStorage.getItem('mindo_schedules') || '{}');
                    categories = JSON.parse(localStorage.getItem('mindo_categories') || '[]');
                } catch(e) {
                    console.warn('Gagal baca localStorage untuk reminders', e);
                }

                if (!schedules || typeof schedules !== 'object') schedules = {};
                if (!Array.isArray(categories)) categories = [];

                // Bangun map warna dari kategori
                const catMap = {};
                categories.forEach(c => {
                    catMap[c.nama] = {
                        warna: c.warna || '#186369',
                        teksWarna: c.teksWarna || '#ffffff',
                    };
                });

                // Tanggal hari ini
                const now = new Date();
                const todayKey = `${now.getFullYear()}-${String(now.getMonth()+1).padStart(2,'0')}-${String(now.getDate()).padStart(2,'0')}`;

                // Kumpulkan semua quest yang BELUM selesai (done !== true)
                const list = [];

                Object.keys(schedules).forEach(dateKey => {
                    const items = schedules[dateKey] || [];
                    if (!Array.isArray(items) || !items.length) return;

                    const [y, m, d] = dateKey.split('-').map(Number);
                    const date = new Date(y, m-1, d);
                    const isToday = dateKey === todayKey;
                    const isPast  = dateKey < todayKey;

                    // Format label tanggal
                    let label;
                    if (isToday) {
                        label = 'Hari ini';
                    } else {
                        label = `${dayNames[date.getDay()]}, ${d} ${monthNames[m-1]}`;
                    }

                    items.forEach(item => {
                        // HANYA tampilkan yang BELUM selesai
                        if (item.done === true) return;

                        const catInfo = catMap[item.kategori] || {
                            warna: item.warna || '#186369',
                            teksWarna: item.teksWarna || '#ffffff',
                        };

                        list.push({
                            id: `${dateKey}-${item.id || item.nama}`,
                            nama: item.nama,
                            kategori: item.kategori,
                            target: item.target,
                            satuan: item.satuan,
                            tanggalKey: dateKey,
                            tanggalLabel: label,
                            isToday,
                            isPast,
                            warna: catInfo.warna,
                            teksWarna: catInfo.teksWarna,
                        });
                    });
                });

                // Sort: hari ini dulu → yang paling lewat → terdekat
                list.sort((a, b) => {
                    if (a.isToday !== b.isToday) return a.isToday ? -1 : 1;
                    return a.tanggalKey.localeCompare(b.tanggalKey);
                });

                this.remindersTotal = list.length;
                this.reminders = list.slice(0, MAX);
            },

            goToSchedule(dateKey) {
                if (!dateKey) return;
                try {
                    localStorage.setItem('mindo_focus_date', dateKey);
                } catch(e) {}
                window.location.href = '/jadwal';
            },

            /* ═══════════════════════════════════════════════
               USER PROFILE DATA
               ═══════════════════════════════════════════════ */
            user: {
                nama:   '{{ auth()->check() ? auth()->user()->name : "Putranda" }}',
                email:  '{{ auth()->check() ? auth()->user()->email : "Putranda456@gmail.com" }}',
                goldar: 'AB+',
                tinggi: '178 cm',
                berat:  '65 kg',
                bio:    'Sebenarnya saya adalah spiderman, i am spiderman. " cyu-cyu-cyu "',
                avatar: '{{ asset('images/avatar.png') }}',
            },
        };
    }
</script>

@include('partials.daily-quote')

@stack('scripts')

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                const el = document.querySelector('[x-data]');
                if (el && el._x_dataStack) {
                    const data = el._x_dataStack[0];
                    data.loginOpen = true;

                    // Kalau error dari form register → auto switch ke mode register
                    @if(old('_form') === 'register')
                        data.loginMode = 'register';
                    @endif
                }
            }, 100);
        });
    </script>
@endif

</body>
</html>