@extends('layouts.admin', ['activeNav' => 'rekomendasi'])

@section('title', 'Kelola Rekomendasi')

@section('content')

<script>
    window.serverAdminRecommendationTemplates = @json($templates);
    window.serverAdminRecommendationCategories = @json($categories);
</script>

{{-- ⚠️ SCRIPT DI ATAS: dieksekusi sebelum <div x-data> --}}
<script>
    window.adminRekomendasi = function () {
        return {
            templates: [],
            categories: [],
            editId: null,
            aktSearch: '',
            filterKategori: null,

            form: {
                nama: '',
                emoji: '🎯',
                warna: '#186369',
                deskripsi: '',
                aktivitas: [],
            },

            defaults: [
                {
                    id: 'belajar', nama: 'Belajar Produktif', emoji: '📚', warna: '#f59e0b',
                    deskripsi: 'Program untuk pelajar & mahasiswa yang ingin fokus belajar tanpa lelah',
                    aktivitas: [
                        { nama: 'Baca Buku',      kategori: 'Belajar',       tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'novel' },
                        { nama: 'Coding',         kategori: 'Belajar',       tipe: 'duration', target: 60, satuan: 'menit', keterangan: 'project' },
                        { nama: 'Bahasa Inggris', kategori: 'Belajar',       tipe: 'duration', target: 20, satuan: 'menit', keterangan: 'vocab' },
                        { nama: 'Deep Work',      kategori: 'Produktivitas', tipe: 'duration', target: 90, satuan: 'menit', keterangan: 'fokus' },
                        { nama: 'Rencana Harian', kategori: 'Produktivitas', tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'pagi' },
                        { nama: 'Bangun Awal',    kategori: 'Tidur',         tipe: 'count',    target: 1,  satuan: 'x',     keterangan: '05:00' },
                        { nama: 'Mindfulness',    kategori: 'Mental',        tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'sadar' },
                    ],
                },
                {
                    id: 'atlit', nama: 'Program Atlit', emoji: '🏋️', warna: '#c0a05f',
                    deskripsi: 'Rutinitas untuk kamu yang ingin aktif seperti atlit — banyak gerak & nutrisi',
                    aktivitas: [
                        { nama: 'Joging',        kategori: 'Sport',  tipe: 'duration', target: 45, satuan: 'menit', keterangan: 'pagi' },
                        { nama: 'Push Up',       kategori: 'Sport',  tipe: 'count',    target: 30, satuan: 'x',     keterangan: 'set 1' },
                        { nama: 'Sit Up',        kategori: 'Sport',  tipe: 'count',    target: 40, satuan: 'x',     keterangan: 'pagi' },
                        { nama: 'Stretching',    kategori: 'Sport',  tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'pemanasan' },
                        { nama: 'Menu Sehat',    kategori: 'Diet',   tipe: 'count',    target: 3,  satuan: 'x',     keterangan: 'harian' },
                        { nama: 'Minum Air',     kategori: 'Diet',   tipe: 'count',    target: 10, satuan: 'gelas', keterangan: 'hidrasi' },
                        { nama: 'Vitamin',       kategori: 'Diet',   tipe: 'count',    target: 1,  satuan: 'x',     keterangan: 'suplemen' },
                        { nama: 'Tidur Malam',   kategori: 'Tidur',  tipe: 'duration', target: 8,  satuan: 'jam',   keterangan: 'cukup' },
                        { nama: 'Meditasi Pagi', kategori: 'Mental', tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'tenang' },
                    ],
                },
                {
                    id: 'seimbang', nama: 'Sehat Seimbang', emoji: '⚖️', warna: '#186369',
                    deskripsi: 'Program seimbang untuk tubuh & pikiran agar tetap bugar setiap hari',
                    aktivitas: [
                        { nama: 'Joging',        kategori: 'Sport',  tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'pagi' },
                        { nama: 'Hatha Yoga',    kategori: 'Yoga',   tipe: 'duration', target: 20, satuan: 'menit', keterangan: 'pagi' },
                        { nama: 'Meditasi',      kategori: 'Yoga',   tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'malam' },
                        { nama: 'Menu Sehat',    kategori: 'Diet',   tipe: 'count',    target: 3,  satuan: 'x',     keterangan: 'harian' },
                        { nama: 'Minum Air',     kategori: 'Diet',   tipe: 'count',    target: 8,  satuan: 'gelas', keterangan: 'hidrasi' },
                        { nama: 'Konsumsi Buah', kategori: 'Diet',   tipe: 'count',    target: 2,  satuan: 'x',     keterangan: 'vitamin' },
                        { nama: 'Journaling',    kategori: 'Mental', tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'refleksi' },
                        { nama: 'Tidur Malam',   kategori: 'Tidur',  tipe: 'duration', target: 7,  satuan: 'jam',   keterangan: 'cukup' },
                    ],
                },
                {
                    id: 'spiritual', nama: 'Konsisten Spiritual', emoji: '🙏', warna: '#a855f7',
                    deskripsi: 'Program untuk ketenangan batin, doa, dan refleksi harian',
                    aktivitas: [
                        { nama: 'Doa Pagi',      kategori: 'Spiritual', tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'syukur' },
                        { nama: 'Doa Malam',     kategori: 'Spiritual', tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'introspeksi' },
                        { nama: 'Baca Kitab',    kategori: 'Spiritual', tipe: 'duration', target: 20, satuan: 'menit', keterangan: 'renungan' },
                        { nama: 'Refleksi Diri', kategori: 'Spiritual', tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'malam' },
                        { nama: 'Meditasi',      kategori: 'Yoga',      tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'tenang' },
                        { nama: 'Journaling',    kategori: 'Mental',    tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'perasaan' },
                    ],
                },
                {
                    id: 'pekerja', nama: 'Produktivitas Pekerja', emoji: '💼', warna: '#4090f2',
                    deskripsi: 'Program untuk pekerja yang ingin tetap produktif & sehat',
                    aktivitas: [
                        { nama: 'Rencana Harian',  kategori: 'Produktivitas', tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'pagi' },
                        { nama: 'Deep Work',       kategori: 'Produktivitas', tipe: 'duration', target: 90, satuan: 'menit', keterangan: 'fokus' },
                        { nama: 'Balas Email',     kategori: 'Produktivitas', tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'inbox' },
                        { nama: 'Evaluasi Harian', kategori: 'Produktivitas', tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'malam' },
                        { nama: 'Belajar Skill',   kategori: 'Produktivitas', tipe: 'duration', target: 45, satuan: 'menit', keterangan: 'baru' },
                        { nama: 'Minum Air',       kategori: 'Diet',          tipe: 'count',    target: 8,  satuan: 'gelas', keterangan: 'hidrasi' },
                        { nama: 'Meditasi Pagi',   kategori: 'Mental',        tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'tenang' },
                    ],
                },
                {
                    id: 'chill', nama: 'Santoruy', emoji: '😎', warna: '#14b8a6',
                    deskripsi: 'Program santai buat recharge energi, gak harus produktif terus',
                    aktivitas: [
                        { nama: 'Baca Komik',  kategori: 'Hobi',   tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'manga' },
                        { nama: 'Main Game',   kategori: 'Hobi',   tipe: 'duration', target: 60, satuan: 'menit', keterangan: 'santai' },
                        { nama: 'Mindfulness', kategori: 'Mental', tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'sadar' },
                        { nama: 'Me Time',     kategori: 'Mental', tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'santai' },
                        { nama: 'Chat Teman',  kategori: 'Sosial', tipe: 'count',    target: 3,  satuan: 'x',     keterangan: 'dekat' },
                    ],
                },
            ],

            init() {
                this.load();

                window.addEventListener('storage', (e) => {
                    if (e.key === 'mindo_categories') this.load();
                });
            },

            load() {
                this.categories = window.serverAdminRecommendationCategories || [];
                this.templates = window.serverAdminRecommendationTemplates || [];
                window.clientProgramCount = this.templates.length;
            },

            defaultCategories() {
                return [
                    {
                        id: 1, nama: 'Sport', warna: '#c0a05f', teksWarna: '#186369',
                        jenis: [
                            { nama: 'Joging',       tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'pagi' },
                            { nama: 'Push Up',      tipe: 'count',    target: 20, satuan: 'x',     keterangan: 'set 1' },
                            { nama: 'Sit Up',       tipe: 'count',    target: 30, satuan: 'x',     keterangan: 'pagi' },
                            { nama: 'Squat',        tipe: 'count',    target: 25, satuan: 'x',     keterangan: 'kaki' },
                            { nama: 'Plank',        tipe: 'duration', target: 2,  satuan: 'menit', keterangan: 'core' },
                            { nama: 'Badminton',    tipe: 'duration', target: 60, satuan: 'menit', keterangan: 'sore' },
                            { nama: 'Berenang',     tipe: 'duration', target: 45, satuan: 'menit', keterangan: 'kolam' },
                            { nama: 'Bersepeda',    tipe: 'duration', target: 40, satuan: 'menit', keterangan: 'sore' },
                            { nama: 'Stretching',   tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'pemanasan' },
                            { nama: 'Lompat Tali',  tipe: 'count',    target: 100, satuan: 'x',    keterangan: 'cardio' },
                        ],
                    },
                    {
                        id: 2, nama: 'Diet', warna: '#186369', teksWarna: '#ffffff',
                        jenis: [
                            { nama: 'Sarapan',        tipe: 'count', target: 1, satuan: 'x',     keterangan: 'oatmeal' },
                            { nama: 'Menu Sehat',     tipe: 'count', target: 3, satuan: 'x',     keterangan: 'harian' },
                            { nama: 'Snack Sehat',    tipe: 'count', target: 2, satuan: 'x',     keterangan: 'almond' },
                            { nama: 'Minum Air',      tipe: 'count', target: 8, satuan: 'gelas', keterangan: 'hidrasi' },
                            { nama: 'Hindari Gula',   tipe: 'count', target: 1, satuan: 'x',     keterangan: 'harian' },
                            { nama: 'Konsumsi Buah',  tipe: 'count', target: 2, satuan: 'x',     keterangan: 'vitamin' },
                            { nama: 'Konsumsi Sayur', tipe: 'count', target: 3, satuan: 'x',     keterangan: 'serat' },
                            { nama: 'Makan Malam',    tipe: 'count', target: 1, satuan: 'x',     keterangan: 'ringan' },
                            { nama: 'Vitamin',        tipe: 'count', target: 1, satuan: 'x',     keterangan: 'suplemen' },
                        ],
                    },
                    {
                        id: 3, nama: 'Yoga', warna: '#9fd6f8', teksWarna: '#186369',
                        jenis: [
                            { nama: 'Hatha Yoga',   tipe: 'duration', target: 20, satuan: 'menit', keterangan: 'pagi' },
                            { nama: 'Vinyasa Flow', tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'sore' },
                            { nama: 'Yin Yoga',     tipe: 'duration', target: 25, satuan: 'menit', keterangan: 'malam' },
                            { nama: 'Meditasi',     tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'malam' },
                            { nama: 'Pranayama',    tipe: 'duration', target: 5,  satuan: 'menit', keterangan: 'napas' },
                            { nama: 'Savasana',     tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'relaksasi' },
                            { nama: 'Mindfulness',  tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'sadar' },
                            { nama: 'Body Scan',    tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'relaksasi' },
                        ],
                    },
                    {
                        id: 4, nama: 'Musik', warna: '#8b7bd8', teksWarna: '#ffffff',
                        jenis: [
                            { nama: 'Gitar',         tipe: 'duration', target: 20, satuan: 'menit', keterangan: 'chord' },
                            { nama: 'Piano',         tipe: 'duration', target: 20, satuan: 'menit', keterangan: 'latihan' },
                            { nama: 'Vokal',         tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'pemanasan' },
                            { nama: 'Drum',          tipe: 'duration', target: 20, satuan: 'menit', keterangan: 'rudiment' },
                            { nama: 'Biola',         tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'bow' },
                            { nama: 'Teori Musik',   tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'baca not' },
                            { nama: 'Nyanyi Santai', tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'fun' },
                        ],
                    },
                    {
                        id: 5, nama: 'Belajar', warna: '#f59e0b', teksWarna: '#ffffff',
                        jenis: [
                            { nama: 'Baca Buku',      tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'novel' },
                            { nama: 'Coding',         tipe: 'duration', target: 60, satuan: 'menit', keterangan: 'project' },
                            { nama: 'Bahasa Inggris', tipe: 'duration', target: 20, satuan: 'menit', keterangan: 'vocab' },
                            { nama: 'Bahasa Jepang',  tipe: 'duration', target: 20, satuan: 'menit', keterangan: 'hiragana' },
                            { nama: 'Journaling',     tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'refleksi' },
                            { nama: 'Matematika',     tipe: 'duration', target: 45, satuan: 'menit', keterangan: 'latihan' },
                            { nama: 'Sejarah',        tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'baca' },
                            { nama: 'Fisika',         tipe: 'duration', target: 45, satuan: 'menit', keterangan: 'soal' },
                            { nama: 'Desain',         tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'latihan' },
                            { nama: 'Menulis',        tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'artikel' },
                        ],
                    },
                    {
                        id: 6, nama: 'Produktivitas', warna: '#4090f2', teksWarna: '#ffffff',
                        jenis: [
                            { nama: 'Deep Work',       tipe: 'duration', target: 90, satuan: 'menit', keterangan: 'fokus' },
                            { nama: 'Review Tugas',    tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'pagi' },
                            { nama: 'Balas Email',     tipe: 'duration', target: 20, satuan: 'menit', keterangan: 'inbox' },
                            { nama: 'Meeting',         tipe: 'duration', target: 60, satuan: 'menit', keterangan: 'tim' },
                            { nama: 'Rencana Harian',  tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'pagi' },
                            { nama: 'Evaluasi Harian', tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'malam' },
                            { nama: 'Belajar Skill',   tipe: 'duration', target: 45, satuan: 'menit', keterangan: 'baru' },
                            { nama: 'Declutter',       tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'meja' },
                        ],
                    },
                    {
                        id: 7, nama: 'Mental', warna: '#e879a4', teksWarna: '#ffffff',
                        jenis: [
                            { nama: 'Meditasi Pagi',    tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'tenang' },
                            { nama: 'Gratitude',        tipe: 'count',    target: 3,  satuan: 'x',     keterangan: 'syukur' },
                            { nama: 'Journaling',       tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'perasaan' },
                            { nama: 'Digital Detox',    tipe: 'duration', target: 60, satuan: 'menit', keterangan: 'no phone' },
                            { nama: 'Deep Breathing',   tipe: 'duration', target: 5,  satuan: 'menit', keterangan: 'rileks' },
                            { nama: 'Self-talk Positif',tipe: 'count',    target: 5,  satuan: 'x',     keterangan: 'afirmasi' },
                            { nama: 'Mindfulness',      tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'sadar' },
                            { nama: 'Me Time',          tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'santai' },
                        ],
                    },
                    {
                        id: 8, nama: 'Tidur', warna: '#6b7280', teksWarna: '#ffffff',
                        jenis: [
                            { nama: 'Tidur Malam',      tipe: 'duration', target: 7,  satuan: 'jam',   keterangan: 'cukup' },
                            { nama: 'Power Nap',        tipe: 'duration', target: 20, satuan: 'menit', keterangan: 'siang' },
                            { nama: 'No Screen',        tipe: 'duration', target: 60, satuan: 'menit', keterangan: 'sebelum tidur' },
                            { nama: 'Kurangi Kafein',   tipe: 'count',    target: 1,  satuan: 'x',     keterangan: 'sore' },
                            { nama: 'Tidur Tepat Waktu',tipe: 'count',    target: 1,  satuan: 'x',     keterangan: '22:00' },
                            { nama: 'Bangun Awal',      tipe: 'count',    target: 1,  satuan: 'x',     keterangan: '05:00' },
                        ],
                    },
                    {
                        id: 9, nama: 'Hobi', warna: '#14b8a6', teksWarna: '#ffffff',
                        jenis: [
                            { nama: 'Melukis',        tipe: 'duration', target: 45, satuan: 'menit', keterangan: 'akrilik' },
                            { nama: 'Fotografi',      tipe: 'duration', target: 60, satuan: 'menit', keterangan: 'street' },
                            { nama: 'Berkebun',       tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'tanaman' },
                            { nama: 'Masak',          tipe: 'duration', target: 60, satuan: 'menit', keterangan: 'resep baru' },
                            { nama: 'Baca Komik',     tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'manga' },
                            { nama: 'Main Game',      tipe: 'duration', target: 60, satuan: 'menit', keterangan: 'santai' },
                            { nama: 'Origami',        tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'lipat' },
                            { nama: 'Menulis Cerita', tipe: 'duration', target: 45, satuan: 'menit', keterangan: 'fiksi' },
                        ],
                    },
                    {
                        id: 10, nama: 'Sosial', warna: '#f97316', teksWarna: '#ffffff',
                        jenis: [
                            { nama: 'Chat Keluarga',  tipe: 'count',    target: 1,   satuan: 'x',     keterangan: 'harian' },
                            { nama: 'Chat Teman',     tipe: 'count',    target: 3,   satuan: 'x',     keterangan: 'dekat' },
                            { nama: 'Video Call',     tipe: 'duration', target: 30,  satuan: 'menit', keterangan: 'keluarga' },
                            { nama: 'Kunjungi Teman', tipe: 'duration', target: 120, satuan: 'menit', keterangan: 'offline' },
                            { nama: 'Nonton Bareng',  tipe: 'duration', target: 90,  satuan: 'menit', keterangan: 'film' },
                            { nama: 'Kencan',         tipe: 'duration', target: 120, satuan: 'menit', keterangan: 'romantis' },
                            { nama: 'Community',      tipe: 'duration', target: 60,  satuan: 'menit', keterangan: 'grup' },
                        ],
                    },
                    {
                        id: 11, nama: 'Keuangan', warna: '#22c55e', teksWarna: '#ffffff',
                        jenis: [
                            { nama: 'Catat Pengeluaran', tipe: 'count',    target: 1,  satuan: 'x',     keterangan: 'harian' },
                            { nama: 'Cek Saldo',         tipe: 'count',    target: 1,  satuan: 'x',     keterangan: 'pagi' },
                            { nama: 'Review Budget',     tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'mingguan' },
                            { nama: 'Belajar Investasi', tipe: 'duration', target: 30, satuan: 'menit', keterangan: 'saham' },
                            { nama: 'Nabung',            tipe: 'count',    target: 1,  satuan: 'x',     keterangan: 'harian' },
                            { nama: 'Bayar Tagihan',     tipe: 'count',    target: 1,  satuan: 'x',     keterangan: 'bulanan' },
                        ],
                    },
                    {
                        id: 12, nama: 'Spiritual', warna: '#a855f7', teksWarna: '#ffffff',
                        jenis: [
                            { nama: 'Doa Pagi',      tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'syukur' },
                            { nama: 'Doa Malam',     tipe: 'duration', target: 10, satuan: 'menit', keterangan: 'introspeksi' },
                            { nama: 'Baca Kitab',    tipe: 'duration', target: 20, satuan: 'menit', keterangan: 'renungan' },
                            { nama: 'Refleksi Diri', tipe: 'duration', target: 15, satuan: 'menit', keterangan: 'malam' },
                            { nama: 'Sedekah',       tipe: 'count',    target: 1,  satuan: 'x',     keterangan: 'kebaikan' },
                        ],
                    },
                ];
            },

            filteredCategoriesWithSub() {
                const q = (this.aktSearch || '').trim().toLowerCase();
                let cats = this.categories;

                // ═══ FILTER BY CHIP KATEGORI ═══
                if (this.filterKategori) {
                    cats = cats.filter(c => c.nama === this.filterKategori);
                }

                // ═══ FILTER BY SEARCH ═══
                if (!q) return cats;

                return cats
                    .map(cat => {
                        const catMatch = cat.nama.toLowerCase().includes(q);
                        const matchingSubs = (cat.jenis || []).filter(sub =>
                            sub.nama.toLowerCase().includes(q)
                        );
                        if (catMatch) return cat;
                        if (matchingSubs.length > 0) {
                            return { ...cat, jenis: matchingSubs };
                        }
                        return null;
                    })
                    .filter(Boolean);
            },

            isSelected(nama, kategoriNama) {
                return this.form.aktivitas.some(a => a.nama === nama && a.kategori === kategoriNama);
            },

            toggleAkt(sub, cat) {
                const idx = this.form.aktivitas.findIndex(
                    a => a.nama === sub.nama && a.kategori === cat.nama
                );

                if (idx !== -1) {
                    this.form.aktivitas.splice(idx, 1);
                } else {
                    this.form.aktivitas.push({
                        nama: sub.nama,
                        kategori: cat.nama,
                        tipe: sub.tipe || 'count',
                        target: sub.target ?? 0,
                        satuan: sub.satuan || 'x',
                        keterangan: sub.keterangan || '',
                        warnaKategori: cat.warna,
                        teksWarnaKategori: cat.teksWarna,
                    });
                }
            },

            removeAkt(nama, kategoriNama) {
                this.form.aktivitas = this.form.aktivitas.filter(
                    a => !(a.nama === nama && a.kategori === kategoriNama)
                );
            },

            clearSelection() {
                if (!confirm('Hapus semua aktivitas terpilih?')) return;
                this.form.aktivitas = [];
            },

            countSelectedIn(cat) {
                return this.form.aktivitas.filter(a => a.kategori === cat.nama).length;
            },

            toggleAllInCategory(cat) {
                const allSelected = this.countSelectedIn(cat) === (cat.jenis || []).length;
                if (allSelected) {
                    this.form.aktivitas = this.form.aktivitas.filter(a => a.kategori !== cat.nama);
                } else {
                    (cat.jenis || []).forEach(sub => {
                        if (!this.isSelected(sub.nama, cat.nama)) {
                            this.toggleAkt(sub, cat);
                        }
                    });
                }
            },

            async save() {
                if (this.form.aktivitas.length === 0) {
                    alert('Pilih minimal 1 aktivitas');
                    return;
                }

                const payload = {
                    name: this.form.nama.trim(),
                    emoji: this.form.emoji || '🎯',
                    color: this.form.warna,
                    description: this.form.deskripsi.trim(),
                    activities: this.form.aktivitas.map(a => ({
                        name: a.nama,
                        category: a.kategori,
                        type: a.tipe,
                        target: Number(a.target) || 0,
                        unit: a.satuan || 'x',
                        description: a.keterangan || '',
                    })),
                };

                try {
                    const response = await fetch(this.editId ? `/admin/rekomendasi/${this.editId}` : '/admin/rekomendasi', {
                        method: this.editId ? 'PATCH' : 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify(payload),
                    });
                    const result = await response.json();
                    if (!response.ok) throw new Error(result.message || 'Gagal menyimpan program.');
                    if (this.editId) {
                        const index = this.templates.findIndex(t => t.id === this.editId);
                        if (index !== -1) this.templates[index] = result;
                    } else {
                        this.templates.unshift(result);
                    }
                    this.reset();
                    window.clientProgramCount = this.templates.length;
                    window.refreshAdminStats?.();
                } catch (error) {
                    alert(error.message || 'Gagal menyimpan program.');
                }
            },

            edit(tpl) {
                this.editId = tpl.id;
                this.form = {
                    nama: tpl.nama,
                    emoji: tpl.emoji || '🎯',
                    warna: tpl.warna,
                    deskripsi: tpl.deskripsi,
                    aktivitas: tpl.aktivitas.map(a => {
                        const cat = this.categories.find(c => c.nama === a.kategori);
                        return {
                            ...a,
                            warnaKategori: cat ? cat.warna : '#186369',
                            teksWarnaKategori: cat ? cat.teksWarna : '#ffffff',
                        };
                    }),
                };
                this.aktSearch = '';
                this.filterKategori = null;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            async hapus(id) {
                if (!confirm('Yakin hapus program ini?')) return;
                try {
                    const response = await fetch(`/admin/rekomendasi/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    });
                    if (!response.ok) throw new Error('Gagal menghapus program.');
                    this.templates = this.templates.filter(t => t.id !== id);
                    if (this.editId === id) this.reset();
                    window.clientProgramCount = this.templates.length;
                    window.refreshAdminStats?.();
                } catch (error) {
                    alert(error.message || 'Gagal menghapus program.');
                }
            },

            reset() {
                this.editId = null;
                this.form = {
                    nama: '',
                    emoji: '🎯',
                    warna: '#186369',
                    deskripsi: '',
                    aktivitas: [],
                };
                this.aktSearch = '';
                this.filterKategori = null;
            },
        }
    }
</script>


<div x-data="adminRekomendasi()" x-init="init()" class="flex flex-col gap-5 max-w-6xl mx-auto">

    <header class="flex-shrink-0">
        <h1 class="text-2xl sm:text-3xl font-bold text-[#186369]">Kelola Rekomendasi</h1>
        <p class="text-sm text-[#186369]/70 mt-1">
            Program rekomendasi yang tampil di halaman To Do List user
        </p>
    </header>

    {{-- FORM --}}
    <div class="bg-white rounded-[32px] p-6 border border-gray-100 flex-shrink-0">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-[#186369]"
                x-text="editId ? 'Edit Program' : 'Tambah Program'"></h2>
            <button type="button" @click="reset()" x-show="editId"
                    class="text-[10px] font-bold text-red-500 hover:text-red-600">
                Batal Edit
            </button>
        </div>

        <form @submit.prevent="save()" class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">
                    Nama Program <span class="text-red-400">*</span>
                </label>
                <input type="text" x-model="form.nama" required
                       placeholder="Contoh: Belajar Produktif"
                       class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl
                              focus:outline-none focus:border-[#186369]">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Emoji / Icon</label>
                <input type="text" x-model="form.emoji" maxlength="4"
                       placeholder="📚"
                       class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl text-center text-2xl
                              focus:outline-none focus:border-[#186369]">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Warna Tema</label>
                <div class="flex gap-2 items-center">
                    <input type="color" x-model="form.warna"
                           class="w-12 h-10 rounded-lg cursor-pointer border-0">
                    <input type="text" x-model="form.warna"
                           class="flex-1 px-4 py-2 border-2 border-gray-200 rounded-xl text-sm
                                  focus:outline-none focus:border-[#186369]">
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 mb-1">
                    Deskripsi Program <span class="text-red-400">*</span>
                </label>
                <textarea x-model="form.deskripsi" rows="2" required
                          placeholder="Program untuk pelajar & mahasiswa..."
                          class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl resize-none
                                 focus:outline-none focus:border-[#186369]"></textarea>
            </div>

            {{-- AKTIVITAS DARI KATEGORI --}}
            <div class="md:col-span-2">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold text-gray-500">
                        Aktivitas dalam Program <span class="text-red-400">*</span>
                    </label>
                    <span class="text-[10px] font-bold"
                          :class="form.aktivitas.length > 0 ? 'text-[#186369]' : 'text-gray-400'">
                        <span x-text="form.aktivitas.length"></span> aktivitas dipilih
                    </span>
                </div>

                <p class="text-[10px] text-gray-400 mb-2">
                    Pilih aktivitas dari kategori yang sudah ada. Klik untuk memilih.
                </p>

                {{-- SEARCH + RESET --}}
                <div class="flex gap-2 mb-3">
                    <div class="relative flex-1">
                        <svg viewBox="0 0 24 24"
                             class="w-3.5 h-3.5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"
                             fill="none" stroke="currentColor" stroke-width="2.5"
                             stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                        </svg>
                        <input type="text" x-model="aktSearch"
                               placeholder="Cari aktivitas atau kategori..."
                               class="w-full pl-9 pr-3 py-2 border-2 border-gray-200 rounded-xl text-sm
                                      focus:outline-none focus:border-[#186369]">
                    </div>
                    <button type="button" @click="clearSelection()"
                            x-show="form.aktivitas.length > 0"
                            class="px-3 py-2 rounded-xl bg-gray-100 text-gray-500 text-xs font-bold
                                   hover:bg-gray-200 transition active:scale-95">
                        Reset
                    </button>
                </div>

                {{-- ═══ FILTER KATEGORI (chips) ═══ --}}
                <div class="mb-3 -mx-1">
                    <div class="flex gap-1.5 overflow-x-auto scroll-thin-h pb-2 px-1">
                        {{-- Chip "Semua" --}}
                        <button type="button"
                                @click="filterKategori = null"
                                class="flex-shrink-0 px-3 py-1.5 rounded-full text-[11px] font-bold
                                       transition active:scale-95 border-2"
                                :class="filterKategori === null
                                    ? 'bg-[#186369] text-white border-[#186369] shadow-sm'
                                    : 'bg-white text-[#186369]/70 border-gray-200 hover:border-[#186369]/40'">
                            Semua
                        </button>

                        {{-- Chip per kategori --}}
                        <template x-for="cat in categories" :key="cat.id">
                            <button type="button"
                                    @click="filterKategori = (filterKategori === cat.nama ? null : cat.nama)"
                                    class="flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                           text-[11px] font-bold transition active:scale-95 border-2"
                                    :class="filterKategori === cat.nama
                                        ? 'text-white border-transparent shadow-sm'
                                        : 'bg-white text-[#186369]/70 border-gray-200 hover:border-[#186369]/40'"
                                    :style="filterKategori === cat.nama
                                        ? `background: ${cat.warna}; border-color: ${cat.warna};`
                                        : ''">
                                <span class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                      :style="filterKategori === cat.nama
                                          ? `background: ${cat.teksWarna};`
                                          : `background: ${cat.warna};`"></span>
                                <span x-text="cat.nama"></span>
                                <span class="opacity-70 text-[9px]"
                                      x-text="'(' + (cat.jenis?.length || 0) + ')'"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- SUMMARY AKTIVITAS TERPILIH --}}
                <template x-if="form.aktivitas.length > 0">
                    <div class="bg-[#f5f8f7] rounded-2xl p-3 mb-3 border-2 border-dashed border-[#186369]/20">
                        <p class="text-[10px] font-bold text-[#186369]/60 uppercase tracking-wider mb-2">
                            Terpilih
                        </p>
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="(akt, i) in form.aktivitas" :key="i">
                                <div class="flex items-center gap-1.5 pl-2 pr-1 py-1 rounded-lg bg-white border border-gray-200">
                                    <div class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                         :style="`background: ${akt.warnaKategori};`"></div>
                                    <span class="text-[10px] font-bold text-[#186369]" x-text="akt.nama"></span>
                                    <button type="button" @click="removeAkt(akt.nama, akt.kategori)"
                                            class="w-4 h-4 rounded flex items-center justify-center
                                                   text-gray-400 hover:text-red-500 hover:bg-red-50
                                                   transition">
                                        <svg viewBox="0 0 16 16" class="w-2.5 h-2.5" fill="none"
                                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                            <path d="M4 4l8 8M12 4l-8 8"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- LIST KATEGORI & AKTIVITAS --}}
                <div class="rounded-2xl border-2 border-gray-100 bg-[#f9fafb] max-h-[400px] overflow-y-auto scroll-thin">
                    <template x-if="categories.length === 0">
                        <div class="text-center py-8">
                            <p class="text-xs text-gray-400">
                                Belum ada kategori. Tambah dulu di
                                <a href="/admin/kategori" class="text-[#186369] font-bold hover:underline">
                                    Kelola Kategori
                                </a>.
                            </p>
                        </div>
                    </template>

                    <template x-if="categories.length > 0 && filteredCategoriesWithSub().length === 0">
                        <div class="text-center py-8">
                            <p class="text-xs text-gray-400">Tidak ada aktivitas yang cocok dengan pencarian.</p>
                        </div>
                    </template>

                    <template x-for="cat in filteredCategoriesWithSub()" :key="cat.id">
                        <div class="border-b border-gray-100 last:border-0">

                            {{-- HEADER KATEGORI --}}
                            <div class="flex items-center gap-2 px-4 py-2.5 bg-white/50 sticky top-0 z-10
                                        backdrop-blur-sm border-b border-gray-100">
                                <div class="w-3 h-3 rounded-full flex-shrink-0"
                                     :style="`background: ${cat.warna};`"></div>
                                <p class="text-xs font-bold text-[#186369] uppercase tracking-wider flex-1"
                                   x-text="cat.nama"></p>
                                <span class="text-[10px] font-bold text-[#186369]/50"
                                      x-text="countSelectedIn(cat) + '/' + (cat.jenis?.length || 0)"></span>
                                <button type="button" @click="toggleAllInCategory(cat)"
                                        class="text-[10px] font-bold text-[#186369] hover:underline ml-1">
                                    <span x-text="countSelectedIn(cat) === (cat.jenis?.length || 0) ? 'Kosongkan' : 'Pilih Semua'"></span>
                                </button>
                            </div>

                            {{-- SUB AKTIVITAS (pakai div @click, bukan label+input) --}}
                            <div class="p-2 space-y-1">
                                <template x-for="sub in cat.jenis" :key="sub.nama">
                                    <div @click="toggleAkt(sub, cat)"
                                         class="flex items-center gap-3 px-3 py-2 rounded-xl cursor-pointer
                                                hover:bg-white transition select-none"
                                         :class="isSelected(sub.nama, cat.nama)
                                             ? 'bg-white border-2 border-[#186369]/30 shadow-sm'
                                             : 'border-2 border-transparent'">

                                        {{-- Checkbox visual --}}
                                        <div class="w-4 h-4 rounded border-2 flex-shrink-0
                                                    flex items-center justify-center transition pointer-events-none"
                                             :style="isSelected(sub.nama, cat.nama)
                                                 ? `background: ${cat.warna}; border-color: ${cat.warna};`
                                                 : 'border-color: rgba(24,99,105,0.3);'">
                                            <svg x-show="isSelected(sub.nama, cat.nama)"
                                                 viewBox="0 0 16 16" class="w-2.5 h-2.5"
                                                 fill="none" stroke="white" stroke-width="3"
                                                 stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 8l4 4 7-7"/>
                                            </svg>
                                        </div>

                                        <div class="flex-1 min-w-0 pointer-events-none">
                                            <p class="text-xs font-bold text-[#186369] truncate"
                                               x-text="sub.nama"></p>
                                            <p class="text-[10px] text-gray-500 mt-0.5">
                                                <span x-text="sub.tipe === 'duration' ? 'Durasi' : 'Jumlah'"></span>
                                                · <span class="font-bold" x-text="sub.target + ' ' + sub.satuan"></span>
                                                <template x-if="sub.keterangan">
                                                    <span>
                                                        <span class="text-gray-400">·</span>
                                                        <span x-text="sub.keterangan"></span>
                                                    </span>
                                                </template>
                                            </p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="md:col-span-2 flex gap-3 pt-2">
                <button type="submit"
                        :disabled="form.aktivitas.length === 0"
                        class="px-6 py-2.5 bg-[#186369] text-white font-bold rounded-xl
                               hover:opacity-90 transition active:scale-95
                               disabled:opacity-40 disabled:cursor-not-allowed">
                    <span x-text="editId ? 'Simpan Perubahan' : 'Simpan Program'"></span>
                </button>
                <button type="button" @click="reset()"
                        x-show="editId || form.nama || form.aktivitas.length"
                        class="px-6 py-2.5 bg-gray-100 text-gray-600 font-bold rounded-xl
                               hover:bg-gray-200 transition active:scale-95">
                    Reset Form
                </button>
            </div>
        </form>
    </div>

    {{-- LIST PROGRAM --}}
    <div class="bg-white rounded-[32px] p-6 border border-gray-100">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-[#186369]">Daftar Program</h2>
            <span class="text-xs font-bold text-gray-500">
                <span x-text="templates.length"></span> program
            </span>
        </div>

        <template x-if="templates.length === 0">
            <div class="text-center py-12 text-gray-400">
                <p class="text-sm">Belum ada program rekomendasi.</p>
            </div>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="(tpl, i) in templates" :key="tpl.id">
                <div class="rounded-2xl p-4 border-2 transition hover:shadow-md"
                     :style="`background: ${tpl.warna}10; border-color: ${tpl.warna}40;`">

                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0"
                                 :style="`background: ${tpl.warna}25;`"
                                 x-text="tpl.emoji"></div>
                            <div>
                                <p class="text-sm font-bold text-[#186369]" x-text="tpl.nama"></p>
                                <p class="text-[9px] text-[#186369]/50" x-text="tpl.id"></p>
                            </div>
                        </div>
                    </div>

                    <p class="text-[10px] text-[#186369]/60 leading-relaxed mb-3 line-clamp-2"
                       x-text="tpl.deskripsi"></p>

                    <div class="space-y-1 mb-3 max-h-32 overflow-y-auto scroll-thin">
                        <template x-for="(akt, ai) in tpl.aktivitas" :key="ai">
                            <div class="flex items-center gap-2 px-2 py-1 rounded-lg text-[10px]"
                                 :style="`background: ${tpl.warna}10;`">
                                <div class="w-1 h-1 rounded-full flex-shrink-0"
                                     :style="`background: ${tpl.warna};`"></div>
                                <span class="font-bold text-[#186369] truncate flex-1" x-text="akt.nama"></span>
                                <span class="text-gray-400">·</span>
                                <span class="text-gray-500" x-text="akt.kategori"></span>
                                <span class="text-gray-400">·</span>
                                <span class="text-gray-500 font-bold flex-shrink-0"
                                      x-text="akt.target + ' ' + akt.satuan"></span>
                            </div>
                        </template>
                    </div>

                    <div class="text-[10px] font-bold text-[#186369]/60 mb-3 pb-3 border-b border-gray-200">
                        Total <span x-text="tpl.aktivitas.length"></span> aktivitas
                    </div>

                    <div class="flex gap-2">
                        <button type="button" @click="edit(tpl)"
                                class="flex-1 py-2 rounded-lg bg-blue-500 text-white text-xs font-bold
                                       hover:bg-blue-600 transition">
                            Edit
                        </button>
                        <button type="button" @click="hapus(tpl.id)"
                                class="flex-1 py-2 rounded-lg bg-red-500 text-white text-xs font-bold
                                       hover:bg-red-600 transition">
                            Hapus
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>

@endsection