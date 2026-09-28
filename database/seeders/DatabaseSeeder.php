<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\CategoryItem;
use App\Models\Doctor;
use App\Models\Faq;
use App\Models\MentalTest;
use App\Models\RecommendationProgram;
use App\Models\ConsultationBooking;
use App\Models\JourneyEntry;
use App\Models\Schedule;
use App\Models\ScheduleConfirmation;
use App\Models\TestResult;
use App\Models\UserProfile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@mindo.test'],
            [
                'name' => 'Admin Mindo',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ],
        );

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'role' => 'user',
                'password' => Hash::make('password'),
            ],
        );

        User::updateOrCreate(
            ['email' => 'afaryanda18874@gmail.com'],
            [
                'name' => '008 Ahmad Fahrur Aryanda',
                'role' => 'user',
                'password' => Hash::make('password'),
            ],
        );

        $this->seedCategories();
        $this->seedRecommendationPrograms();
        $this->seedDoctors();
        $this->seedFaqs();
        $this->seedArticles();
        $this->seedMentalTests();
        $this->seedUserData();
    }

    private function seedCategories(): void
    {
        $categories = [
            [
                'name' => 'Sport',
                'warna' => '#c0a05f',
                'teks_warna' => '#186369',
                'items' => [
                    ['name' => 'Joging', 'type' => 'duration', 'target' => 30, 'unit' => 'menit', 'description' => 'pagi'],
                    ['name' => 'Push Up', 'type' => 'count', 'target' => 20, 'unit' => 'x', 'description' => 'set 1'],
                    ['name' => 'Sit Up', 'type' => 'count', 'target' => 30, 'unit' => 'x', 'description' => 'pagi'],
                    ['name' => 'Squat', 'type' => 'count', 'target' => 25, 'unit' => 'x', 'description' => 'kaki'],
                    ['name' => 'Plank', 'type' => 'duration', 'target' => 2, 'unit' => 'menit', 'description' => 'core'],
                    ['name' => 'Badminton', 'type' => 'duration', 'target' => 60, 'unit' => 'menit', 'description' => 'sore'],
                    ['name' => 'Berenang', 'type' => 'duration', 'target' => 45, 'unit' => 'menit', 'description' => 'kolam'],
                    ['name' => 'Bersepeda', 'type' => 'duration', 'target' => 40, 'unit' => 'menit', 'description' => 'sore'],
                    ['name' => 'Stretching', 'type' => 'duration', 'target' => 10, 'unit' => 'menit', 'description' => 'pemanasan'],
                    ['name' => 'Lompat Tali', 'type' => 'count', 'target' => 100, 'unit' => 'x', 'description' => 'cardio'],
                ],
            ],
            [
                'name' => 'Diet',
                'warna' => '#186369',
                'teks_warna' => '#ffffff',
                'items' => [
                    ['name' => 'Sarapan', 'type' => 'count', 'target' => 1, 'unit' => 'x', 'description' => 'oatmeal'],
                    ['name' => 'Menu Sehat', 'type' => 'count', 'target' => 3, 'unit' => 'x', 'description' => 'harian'],
                    ['name' => 'Snack Sehat', 'type' => 'count', 'target' => 2, 'unit' => 'x', 'description' => 'almond'],
                    ['name' => 'Minum Air', 'type' => 'count', 'target' => 8, 'unit' => 'gelas', 'description' => 'hidrasi'],
                    ['name' => 'Hindari Gula', 'type' => 'count', 'target' => 1, 'unit' => 'x', 'description' => 'harian'],
                    ['name' => 'Konsumsi Buah', 'type' => 'count', 'target' => 2, 'unit' => 'x', 'description' => 'vitamin'],
                    ['name' => 'Konsumsi Sayur', 'type' => 'count', 'target' => 3, 'unit' => 'x', 'description' => 'serat'],
                    ['name' => 'Makan Malam', 'type' => 'count', 'target' => 1, 'unit' => 'x', 'description' => 'ringan'],
                    ['name' => 'Vitamin', 'type' => 'count', 'target' => 1, 'unit' => 'x', 'description' => 'suplemen'],
                ],
            ],
            [
                'name' => 'Yoga', 'warna' => '#9fd6f8', 'teks_warna' => '#186369',
                'items' => [
                    ['name' => 'Hatha Yoga', 'type' => 'duration', 'target' => 20, 'unit' => 'menit', 'description' => 'pagi'],
                    ['name' => 'Vinyasa Flow', 'type' => 'duration', 'target' => 30, 'unit' => 'menit', 'description' => 'sore'],
                    ['name' => 'Yin Yoga', 'type' => 'duration', 'target' => 25, 'unit' => 'menit', 'description' => 'malam'],
                    ['name' => 'Meditasi', 'type' => 'duration', 'target' => 10, 'unit' => 'menit', 'description' => 'malam'],
                    ['name' => 'Pranayama', 'type' => 'duration', 'target' => 5, 'unit' => 'menit', 'description' => 'napas'],
                    ['name' => 'Savasana', 'type' => 'duration', 'target' => 10, 'unit' => 'menit', 'description' => 'relaksasi'],
                    ['name' => 'Mindfulness', 'type' => 'duration', 'target' => 15, 'unit' => 'menit', 'description' => 'sadar'],
                    ['name' => 'Body Scan', 'type' => 'duration', 'target' => 10, 'unit' => 'menit', 'description' => 'relaksasi'],
                ],
            ],
            [
                'name' => 'Musik', 'warna' => '#8b7bd8', 'teks_warna' => '#ffffff',
                'items' => [
                    ['name' => 'Gitar', 'type' => 'duration', 'target' => 20, 'unit' => 'menit', 'description' => 'chord'],
                    ['name' => 'Piano', 'type' => 'duration', 'target' => 20, 'unit' => 'menit', 'description' => 'latihan'],
                    ['name' => 'Vokal', 'type' => 'duration', 'target' => 15, 'unit' => 'menit', 'description' => 'pemanasan'],
                    ['name' => 'Drum', 'type' => 'duration', 'target' => 20, 'unit' => 'menit', 'description' => 'rudiment'],
                    ['name' => 'Biola', 'type' => 'duration', 'target' => 15, 'unit' => 'menit', 'description' => 'bow'],
                    ['name' => 'Teori Musik', 'type' => 'duration', 'target' => 15, 'unit' => 'menit', 'description' => 'baca not'],
                    ['name' => 'Nyanyi Santai', 'type' => 'duration', 'target' => 10, 'unit' => 'menit', 'description' => 'fun'],
                ],
            ],
            [
                'name' => 'Belajar', 'warna' => '#f59e0b', 'teks_warna' => '#ffffff',
                'items' => [
                    ['name' => 'Baca Buku', 'type' => 'duration', 'target' => 30, 'unit' => 'menit', 'description' => 'novel'],
                    ['name' => 'Coding', 'type' => 'duration', 'target' => 60, 'unit' => 'menit', 'description' => 'project'],
                    ['name' => 'Bahasa Inggris', 'type' => 'duration', 'target' => 20, 'unit' => 'menit', 'description' => 'vocab'],
                    ['name' => 'Bahasa Jepang', 'type' => 'duration', 'target' => 20, 'unit' => 'menit', 'description' => 'hiragana'],
                    ['name' => 'Journaling', 'type' => 'duration', 'target' => 15, 'unit' => 'menit', 'description' => 'refleksi'],
                    ['name' => 'Matematika', 'type' => 'duration', 'target' => 45, 'unit' => 'menit', 'description' => 'latihan'],
                    ['name' => 'Sejarah', 'type' => 'duration', 'target' => 30, 'unit' => 'menit', 'description' => 'baca'],
                    ['name' => 'Fisika', 'type' => 'duration', 'target' => 45, 'unit' => 'menit', 'description' => 'soal'],
                    ['name' => 'Desain', 'type' => 'duration', 'target' => 30, 'unit' => 'menit', 'description' => 'latihan'],
                    ['name' => 'Menulis', 'type' => 'duration', 'target' => 30, 'unit' => 'menit', 'description' => 'artikel'],
                ],
            ],
            [
                'name' => 'Produktivitas', 'warna' => '#4090f2', 'teks_warna' => '#ffffff',
                'items' => [
                    ['name' => 'Deep Work', 'type' => 'duration', 'target' => 90, 'unit' => 'menit', 'description' => 'fokus'],
                    ['name' => 'Review Tugas', 'type' => 'duration', 'target' => 30, 'unit' => 'menit', 'description' => 'pagi'],
                    ['name' => 'Balas Email', 'type' => 'duration', 'target' => 20, 'unit' => 'menit', 'description' => 'inbox'],
                    ['name' => 'Meeting', 'type' => 'duration', 'target' => 60, 'unit' => 'menit', 'description' => 'tim'],
                    ['name' => 'Rencana Harian', 'type' => 'duration', 'target' => 10, 'unit' => 'menit', 'description' => 'pagi'],
                    ['name' => 'Evaluasi Harian', 'type' => 'duration', 'target' => 15, 'unit' => 'menit', 'description' => 'malam'],
                    ['name' => 'Belajar Skill', 'type' => 'duration', 'target' => 45, 'unit' => 'menit', 'description' => 'baru'],
                    ['name' => 'Declutter', 'type' => 'duration', 'target' => 15, 'unit' => 'menit', 'description' => 'meja'],
                ],
            ],
            [
                'name' => 'Mental', 'warna' => '#e879a4', 'teks_warna' => '#ffffff',
                'items' => [
                    ['name' => 'Meditasi Pagi', 'type' => 'duration', 'target' => 10, 'unit' => 'menit', 'description' => 'tenang'],
                    ['name' => 'Gratitude', 'type' => 'count', 'target' => 3, 'unit' => 'x', 'description' => 'syukur'],
                    ['name' => 'Journaling', 'type' => 'duration', 'target' => 15, 'unit' => 'menit', 'description' => 'perasaan'],
                    ['name' => 'Digital Detox', 'type' => 'duration', 'target' => 60, 'unit' => 'menit', 'description' => 'no phone'],
                    ['name' => 'Deep Breathing', 'type' => 'duration', 'target' => 5, 'unit' => 'menit', 'description' => 'rileks'],
                    ['name' => 'Self-talk Positif', 'type' => 'count', 'target' => 5, 'unit' => 'x', 'description' => 'afirmasi'],
                    ['name' => 'Mindfulness', 'type' => 'duration', 'target' => 10, 'unit' => 'menit', 'description' => 'sadar'],
                    ['name' => 'Me Time', 'type' => 'duration', 'target' => 30, 'unit' => 'menit', 'description' => 'santai'],
                ],
            ],
            [
                'name' => 'Tidur', 'warna' => '#6b7280', 'teks_warna' => '#ffffff',
                'items' => [
                    ['name' => 'Tidur Malam', 'type' => 'duration', 'target' => 7, 'unit' => 'jam', 'description' => 'cukup'],
                    ['name' => 'Power Nap', 'type' => 'duration', 'target' => 20, 'unit' => 'menit', 'description' => 'siang'],
                    ['name' => 'No Screen', 'type' => 'duration', 'target' => 60, 'unit' => 'menit', 'description' => 'sebelum tidur'],
                    ['name' => 'Kurangi Kafein', 'type' => 'count', 'target' => 1, 'unit' => 'x', 'description' => 'sore'],
                    ['name' => 'Tidur Tepat Waktu', 'type' => 'count', 'target' => 1, 'unit' => 'x', 'description' => '22:00'],
                    ['name' => 'Bangun Awal', 'type' => 'count', 'target' => 1, 'unit' => 'x', 'description' => '05:00'],
                ],
            ],
            [
                'name' => 'Hobi', 'warna' => '#14b8a6', 'teks_warna' => '#ffffff',
                'items' => [
                    ['name' => 'Melukis', 'type' => 'duration', 'target' => 45, 'unit' => 'menit', 'description' => 'akrilik'],
                    ['name' => 'Fotografi', 'type' => 'duration', 'target' => 60, 'unit' => 'menit', 'description' => 'street'],
                    ['name' => 'Berkebun', 'type' => 'duration', 'target' => 30, 'unit' => 'menit', 'description' => 'tanaman'],
                    ['name' => 'Masak', 'type' => 'duration', 'target' => 60, 'unit' => 'menit', 'description' => 'resep baru'],
                    ['name' => 'Baca Komik', 'type' => 'duration', 'target' => 30, 'unit' => 'menit', 'description' => 'manga'],
                    ['name' => 'Main Game', 'type' => 'duration', 'target' => 60, 'unit' => 'menit', 'description' => 'santai'],
                    ['name' => 'Origami', 'type' => 'duration', 'target' => 30, 'unit' => 'menit', 'description' => 'lipat'],
                    ['name' => 'Menulis Cerita', 'type' => 'duration', 'target' => 45, 'unit' => 'menit', 'description' => 'fiksi'],
                ],
            ],
            [
                'name' => 'Sosial', 'warna' => '#f97316', 'teks_warna' => '#ffffff',
                'items' => [
                    ['name' => 'Chat Keluarga', 'type' => 'count', 'target' => 1, 'unit' => 'x', 'description' => 'harian'],
                    ['name' => 'Chat Teman', 'type' => 'count', 'target' => 3, 'unit' => 'x', 'description' => 'dekat'],
                    ['name' => 'Video Call', 'type' => 'duration', 'target' => 30, 'unit' => 'menit', 'description' => 'keluarga'],
                    ['name' => 'Kunjungi Teman', 'type' => 'duration', 'target' => 120, 'unit' => 'menit', 'description' => 'offline'],
                    ['name' => 'Nonton Bareng', 'type' => 'duration', 'target' => 90, 'unit' => 'menit', 'description' => 'film'],
                    ['name' => 'Kencan', 'type' => 'duration', 'target' => 120, 'unit' => 'menit', 'description' => 'romantis'],
                    ['name' => 'Community', 'type' => 'duration', 'target' => 60, 'unit' => 'menit', 'description' => 'grup'],
                ],
            ],
            [
                'name' => 'Keuangan', 'warna' => '#22c55e', 'teks_warna' => '#ffffff',
                'items' => [
                    ['name' => 'Catat Pengeluaran', 'type' => 'count', 'target' => 1, 'unit' => 'x', 'description' => 'harian'],
                    ['name' => 'Cek Saldo', 'type' => 'count', 'target' => 1, 'unit' => 'x', 'description' => 'pagi'],
                    ['name' => 'Review Budget', 'type' => 'duration', 'target' => 15, 'unit' => 'menit', 'description' => 'mingguan'],
                    ['name' => 'Belajar Investasi', 'type' => 'duration', 'target' => 30, 'unit' => 'menit', 'description' => 'saham'],
                    ['name' => 'Nabung', 'type' => 'count', 'target' => 1, 'unit' => 'x', 'description' => 'harian'],
                    ['name' => 'Bayar Tagihan', 'type' => 'count', 'target' => 1, 'unit' => 'x', 'description' => 'bulanan'],
                ],
            ],
            [
                'name' => 'Spiritual', 'warna' => '#a855f7', 'teks_warna' => '#ffffff',
                'items' => [
                    ['name' => 'Doa Pagi', 'type' => 'duration', 'target' => 10, 'unit' => 'menit', 'description' => 'syukur'],
                    ['name' => 'Doa Malam', 'type' => 'duration', 'target' => 10, 'unit' => 'menit', 'description' => 'introspeksi'],
                    ['name' => 'Baca Kitab', 'type' => 'duration', 'target' => 20, 'unit' => 'menit', 'description' => 'renungan'],
                    ['name' => 'Refleksi Diri', 'type' => 'duration', 'target' => 15, 'unit' => 'menit', 'description' => 'malam'],
                    ['name' => 'Sedekah', 'type' => 'count', 'target' => 1, 'unit' => 'x', 'description' => 'kebaikan'],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $items = $categoryData['items'];
            unset($categoryData['items']);

            $category = Category::updateOrCreate(
                ['name' => $categoryData['name'], 'is_global' => true],
                $categoryData + ['user_id' => null],
            );

            foreach ($items as $order => $item) {
                $category->items()->updateOrCreate(
                    ['name' => $item['name']],
                    $item + ['order' => $order],
                );
            }
        }

        $testCategory = Category::updateOrCreate(
            ['name' => 'tes', 'is_global' => true],
            ['user_id' => null, 'warna' => '#c0a05f', 'teks_warna' => '#186369'],
        );

        $testCategory->items()->updateOrCreate(
            ['name' => 'tes'],
            ['type' => 'count', 'target' => 10, 'unit' => 'x', 'description' => null, 'order' => 0],
        );

        $spiritual = Category::where('name', 'Spiritual')->where('is_global', true)->firstOrFail();
        $spiritual->items()->updateOrCreate(
            ['name' => 'Kultivasi'],
            ['type' => 'duration', 'target' => 10, 'unit' => 'tahun', 'description' => null, 'order' => 5],
        );
    }

    private function seedRecommendationPrograms(): void
    {
        $programs = [
            [
                'name' => 'Belajar Produktif', 'slug' => 'belajar-produktif', 'emoji' => '📚', 'color' => '#f59e0b',
                'description' => 'Program untuk pelajar dan mahasiswa yang ingin fokus belajar tanpa lelah.',
                'activities' => [
                    ['name' => 'Baca Buku', 'category' => 'Belajar', 'type' => 'duration', 'target' => 30, 'unit' => 'menit', 'description' => 'novel'],
                    ['name' => 'Coding', 'category' => 'Belajar', 'type' => 'duration', 'target' => 60, 'unit' => 'menit', 'description' => 'project'],
                    ['name' => 'Bahasa Inggris', 'category' => 'Belajar', 'type' => 'duration', 'target' => 20, 'unit' => 'menit', 'description' => 'vocab'],
                    ['name' => 'Deep Work', 'category' => 'Produktivitas', 'type' => 'duration', 'target' => 90, 'unit' => 'menit', 'description' => 'fokus'],
                    ['name' => 'Rencana Harian', 'category' => 'Produktivitas', 'type' => 'duration', 'target' => 10, 'unit' => 'menit', 'description' => 'pagi'],
                    ['name' => 'Bangun Awal', 'category' => 'Tidur', 'type' => 'count', 'target' => 1, 'unit' => 'x', 'description' => '05:00'],
                    ['name' => 'Mindfulness', 'category' => 'Mental', 'type' => 'duration', 'target' => 10, 'unit' => 'menit', 'description' => 'sadar'],
                ],
            ],
            [
                'name' => 'Program Atlit', 'slug' => 'program-atlit', 'emoji' => '🏋️', 'color' => '#c0a05f',
                'description' => 'Rutinitas untuk kamu yang ingin aktif seperti atlet, dengan banyak gerak dan nutrisi.',
                'activities' => [
                    ['name' => 'Joging', 'category' => 'Sport', 'type' => 'duration', 'target' => 45, 'unit' => 'menit', 'description' => 'pagi'],
                    ['name' => 'Push Up', 'category' => 'Sport', 'type' => 'count', 'target' => 30, 'unit' => 'x', 'description' => 'set 1'],
                    ['name' => 'Sit Up', 'category' => 'Sport', 'type' => 'count', 'target' => 40, 'unit' => 'x', 'description' => 'pagi'],
                    ['name' => 'Stretching', 'category' => 'Sport', 'type' => 'duration', 'target' => 15, 'unit' => 'menit', 'description' => 'pemanasan'],
                    ['name' => 'Menu Sehat', 'category' => 'Diet', 'type' => 'count', 'target' => 3, 'unit' => 'x', 'description' => 'harian'],
                    ['name' => 'Minum Air', 'category' => 'Diet', 'type' => 'count', 'target' => 10, 'unit' => 'gelas', 'description' => 'hidrasi'],
                    ['name' => 'Vitamin', 'category' => 'Diet', 'type' => 'count', 'target' => 1, 'unit' => 'x', 'description' => 'suplemen'],
                    ['name' => 'Tidur Malam', 'category' => 'Tidur', 'type' => 'duration', 'target' => 8, 'unit' => 'jam', 'description' => 'cukup'],
                    ['name' => 'Meditasi Pagi', 'category' => 'Mental', 'type' => 'duration', 'target' => 10, 'unit' => 'menit', 'description' => 'tenang'],
                ],
            ],
            [
                'name' => 'Sehat Seimbang', 'slug' => 'sehat-seimbang', 'emoji' => '⚖️', 'color' => '#186369',
                'description' => 'Program seimbang untuk tubuh dan pikiran agar tetap bugar setiap hari.',
                'activities' => [
                    ['name' => 'Joging', 'category' => 'Sport', 'type' => 'duration', 'target' => 30, 'unit' => 'menit', 'description' => 'pagi'],
                    ['name' => 'Hatha Yoga', 'category' => 'Yoga', 'type' => 'duration', 'target' => 20, 'unit' => 'menit', 'description' => 'pagi'],
                    ['name' => 'Meditasi', 'category' => 'Yoga', 'type' => 'duration', 'target' => 10, 'unit' => 'menit', 'description' => 'malam'],
                    ['name' => 'Menu Sehat', 'category' => 'Diet', 'type' => 'count', 'target' => 3, 'unit' => 'x', 'description' => 'harian'],
                    ['name' => 'Minum Air', 'category' => 'Diet', 'type' => 'count', 'target' => 8, 'unit' => 'gelas', 'description' => 'hidrasi'],
                    ['name' => 'Konsumsi Buah', 'category' => 'Diet', 'type' => 'count', 'target' => 2, 'unit' => 'x', 'description' => 'vitamin'],
                    ['name' => 'Journaling', 'category' => 'Mental', 'type' => 'duration', 'target' => 15, 'unit' => 'menit', 'description' => 'refleksi'],
                    ['name' => 'Tidur Malam', 'category' => 'Tidur', 'type' => 'duration', 'target' => 7, 'unit' => 'jam', 'description' => 'cukup'],
                ],
            ],
            [
                'name' => 'Konsisten Spiritual', 'slug' => 'konsisten-spiritual', 'emoji' => '🙏', 'color' => '#a855f7',
                'description' => 'Program untuk ketenangan batin, doa, dan refleksi harian.',
                'activities' => [
                    ['name' => 'Doa Pagi', 'category' => 'Spiritual', 'type' => 'duration', 'target' => 15, 'unit' => 'menit', 'description' => 'syukur'],
                    ['name' => 'Doa Malam', 'category' => 'Spiritual', 'type' => 'duration', 'target' => 15, 'unit' => 'menit', 'description' => 'introspeksi'],
                    ['name' => 'Baca Kitab', 'category' => 'Spiritual', 'type' => 'duration', 'target' => 20, 'unit' => 'menit', 'description' => 'renungan'],
                    ['name' => 'Refleksi Diri', 'category' => 'Spiritual', 'type' => 'duration', 'target' => 15, 'unit' => 'menit', 'description' => 'malam'],
                    ['name' => 'Meditasi', 'category' => 'Yoga', 'type' => 'duration', 'target' => 15, 'unit' => 'menit', 'description' => 'tenang'],
                    ['name' => 'Journaling', 'category' => 'Mental', 'type' => 'duration', 'target' => 15, 'unit' => 'menit', 'description' => 'perasaan'],
                ],
            ],
            [
                'name' => 'Produktivitas Pekerja', 'slug' => 'produktivitas-pekerja', 'emoji' => '💼', 'color' => '#4090f2',
                'description' => 'Program untuk pekerja yang ingin tetap produktif dan sehat.',
                'activities' => [
                    ['name' => 'Rencana Harian', 'category' => 'Produktivitas', 'type' => 'duration', 'target' => 10, 'unit' => 'menit', 'description' => 'pagi'],
                    ['name' => 'Deep Work', 'category' => 'Produktivitas', 'type' => 'duration', 'target' => 90, 'unit' => 'menit', 'description' => 'fokus'],
                    ['name' => 'Balas Email', 'category' => 'Produktivitas', 'type' => 'duration', 'target' => 30, 'unit' => 'menit', 'description' => 'inbox'],
                    ['name' => 'Evaluasi Harian', 'category' => 'Produktivitas', 'type' => 'duration', 'target' => 15, 'unit' => 'menit', 'description' => 'malam'],
                    ['name' => 'Belajar Skill', 'category' => 'Produktivitas', 'type' => 'duration', 'target' => 45, 'unit' => 'menit', 'description' => 'baru'],
                    ['name' => 'Minum Air', 'category' => 'Diet', 'type' => 'count', 'target' => 8, 'unit' => 'gelas', 'description' => 'hidrasi'],
                    ['name' => 'Meditasi Pagi', 'category' => 'Mental', 'type' => 'duration', 'target' => 10, 'unit' => 'menit', 'description' => 'tenang'],
                ],
            ],
            [
                'name' => 'Santoruy', 'slug' => 'santoruy', 'emoji' => '😎', 'color' => '#14b8a6',
                'description' => 'Program santai untuk mengisi ulang energi tanpa harus produktif terus.',
                'activities' => [
                    ['name' => 'Baca Komik', 'category' => 'Hobi', 'type' => 'duration', 'target' => 30, 'unit' => 'menit', 'description' => 'manga'],
                    ['name' => 'Main Game', 'category' => 'Hobi', 'type' => 'duration', 'target' => 60, 'unit' => 'menit', 'description' => 'santai'],
                    ['name' => 'Mindfulness', 'category' => 'Mental', 'type' => 'duration', 'target' => 15, 'unit' => 'menit', 'description' => 'sadar'],
                    ['name' => 'Me Time', 'category' => 'Mental', 'type' => 'duration', 'target' => 30, 'unit' => 'menit', 'description' => 'santai'],
                    ['name' => 'Chat Teman', 'category' => 'Sosial', 'type' => 'count', 'target' => 3, 'unit' => 'x', 'description' => 'dekat'],
                ],
            ],
        ];

        foreach ($programs as $program) {
            RecommendationProgram::firstOrCreate(
                ['slug' => $program['slug']],
                $program + ['is_active' => true],
            );
        }
    }

    private function seedDoctors(): void
    {
        $doctors = [
            ['name' => 'Dr. Kholik', 'spesialisasi' => 'Psikolog Klinis', 'bio' => 'Spesialis kecemasan dan depresi.', 'whatsapp' => '6281234567890', 'website' => 'https://example.com/kholik', 'avatar' => 'https://i.pravatar.cc/150?img=12', 'jam_praktik' => '09:00 - 17:00', 'online' => true],
            ['name' => 'Dr. Sarah Wijaya', 'spesialisasi' => 'Psikiater', 'bio' => 'Menangani gangguan tidur dan stres berat.', 'whatsapp' => '6281234567891', 'website' => 'https://example.com/sarah', 'avatar' => 'https://i.pravatar.cc/150?img=45', 'jam_praktik' => '10:00 - 19:00', 'online' => true],
            ['name' => 'Dr. Budi Santoso', 'spesialisasi' => 'Konselor', 'bio' => 'Konseling hubungan dan karier.', 'whatsapp' => '6281234567892', 'website' => null, 'avatar' => 'https://i.pravatar.cc/150?img=33', 'jam_praktik' => '08:00 - 16:00', 'online' => false],
            ['name' => 'Dr. Strange', 'spesialisasi' => 'Spesialis Dunia Paralel', 'bio' => 'dia aneh', 'whatsapp' => '08123456789', 'website' => null, 'avatar' => null, 'jam_praktik' => '09:00 - 17:00', 'online' => true, 'photo' => null],
        ];

        foreach ($doctors as $doctor) {
            Doctor::updateOrCreate(['name' => $doctor['name']], $doctor);
        }
    }

    private function seedFaqs(): void
    {
        $faqs = [
            ['question' => 'Berapa biaya konsultasi?', 'keywords' => ['harga', 'biaya', 'tarif', 'konsultasi'], 'category' => 'Pembayaran', 'answer' => 'Untuk informasi biaya, silakan hubungi tim Mindo.', 'order' => 1, 'is_active' => true],
            ['question' => 'Bagaimana cara berkonsultasi dengan dokter?', 'keywords' => ['dokter', 'konsul', 'booking', 'konsultasi'], 'category' => 'Konsultasi', 'answer' => 'Pilih dokter pada halaman konsultasi, lalu buat booking sesuai jadwal yang tersedia.', 'order' => 2, 'is_active' => true],
            ['question' => 'Apakah hasil tes mental merupakan diagnosis medis?', 'keywords' => ['tes mental', 'diagnosis', 'hasil tes'], 'category' => 'Tes Mental', 'answer' => 'Tidak. Hasil tes adalah skrining awal dan bukan diagnosis medis.', 'order' => 3, 'is_active' => true],
            ['question' => 'Sapaan umum', 'keywords' => ['halo', 'hai', 'pagi', 'siang', 'malam'], 'category' => 'Sapaan', 'answer' => 'Hai! Ada yang bisa saya bantu?', 'order' => 4, 'is_active' => true],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(['question' => $faq['question']], $faq);
        }
    }

    private function seedArticles(): void
    {
        $articles = [
            ['title' => 'Pengaruh Waktu Tidur pada Kesehatan Mental', 'slug' => 'pengaruh-waktu-tidur-pada-kesehatan-mental-2', 'content' => 'Pengaruh Waktu Tidur pada Kesehatan Mental', 'source_url' => null, 'thumbnail' => 'articles/covers/0e1457cf-a0dc-4678-8cfa-d1262c149541.jpg', 'pdf_path' => 'articles/pdf/b4eb5f0b-76ae-4542-adf3-177d342a5600.pdf', 'is_published' => true, 'published_at' => '2026-09-20 00:00:00'],
            ['title' => 'Pandemi COVID-19 dan Tantangan Kebijakan Kesehatan Mental di Indonesia', 'slug' => 'pandemi-covid-19-dan-tantangan-kebijakan-kesehatan-mental-di-indonesia', 'content' => 'Pandemi COVID-19 dan Tantangan Kebijakan Kesehatan Mental di Indonesia', 'source_url' => null, 'thumbnail' => 'articles/covers/f5bd8c47-f1d1-4254-8833-1fe8c011c92d.jpg', 'pdf_path' => 'articles/pdf/bc50e4eb-9e06-4272-9a36-52337afc135e.pdf', 'is_published' => true, 'published_at' => '2026-09-20 00:00:00'],
            ['title' => 'ANALISIS SITUASI KESEHATAN MENTAL PADA MASYARAKAT DI INDONESIA DAN STRATEGI PENANGGULANGANNYA', 'slug' => 'analisis-situasi-kesehatan-mental-pada-masyarakat-di-indonesia-dan-strategi-penanggulangannya', 'content' => "ANALISIS SITUASI KESEHATAN MENTAL PADA MASYARAKAT\nDI INDONESIA DAN STRATEGI PENANGGULANGANNYA", 'source_url' => null, 'thumbnail' => 'articles/covers/29e09f18-273b-4d4f-9dd5-cba2bfbb4934.jpg', 'pdf_path' => 'articles/pdf/d00961b8-8b27-4ee1-8d20-446e0747199c.pdf', 'is_published' => true, 'published_at' => '2026-09-20 00:00:00'],
        ];

        Article::whereNotIn('slug', array_column($articles, 'slug'))->delete();

        foreach ($articles as $article) {
            Article::updateOrCreate(['slug' => $article['slug']], $article);
        }
    }

    private function seedUserData(): void
    {
        $user = User::where('email', 'afaryanda18874@gmail.com')->firstOrFail();
        $testUser = User::where('email', 'test@example.com')->firstOrFail();
        $doctor = Doctor::where('name', 'Dr. Strange')->firstOrFail();
        $mentalTest = MentalTest::where('jenis', 'Depresi')->firstOrFail();
        $today = now()->toDateString();

        $this->clearFutureDemoSchedules([$user->id, $testUser->id], $today);

        UserProfile::updateOrCreate(
            ['user_id' => $testUser->id],
            ['nama' => 'Test User', 'usia' => 20, 'gender' => 'pria', 'goldar' => 'B+', 'tinggi' => 222, 'berat' => 22, 'avatar' => null, 'bio' => '2222', 'profile_completed_at' => '2026-09-16 11:44:34'],
        );

        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            ['nama' => '008 Ahmad Fahrur Aryanda', 'usia' => 22, 'gender' => 'pria', 'goldar' => 'A+', 'tinggi' => 150, 'berat' => 75, 'avatar' => null, 'bio' => null, 'profile_completed_at' => '2026-09-17 08:11:49'],
        );

        JourneyEntry::updateOrCreate(
            ['user_id' => $user->id, 'entry_date' => '2026-09-17'],
            ['title' => 'Aku?', 'content' => 'Hai... Perkenalkan namaku Arya. Nggak kerasa sekarang aku sudah memasuki semester 7 pada perkuliahanku. Ya... tidak terasa waktu berjalan begitu cepat! Kurang dari setahun lagi aku akan menyandang gelar sarjana S.Kom ku (aamiin wkwkwk) yang mayoritas orang pikir gelar ini akan sulit untuk didapatkan (?). Padahal kenyataannya tidak terlalu sulit, ya walau bisa dibilang aku sudah jadi setengah gila hehehe. Tapi kalau dibandingkan dengan sarjana S.Kom jaman dulu, bisa dibilang lulusan sekarang memiliki bobot yang kurang. Mengapa? Mahasiswa jaman sekarang lebih memilih menggarap tugasnya dengan AI tanpa memahami konteks tugasnya secara menyeluruh. Mungkin yang mereka pikirkan adalah "Ah yang penting tugasku selesai". Padahal kalau mereka punya mindset yang berkembang AI bisa menjadi sebuah alat pembelajaran yang sangat powerful.', 'created_at' => '2026-09-17 08:41:59', 'updated_at' => '2026-09-17 08:42:12'],
        );

        $categoryIds = Category::where('is_global', true)->pluck('id', 'name');
        $categoryItemIds = CategoryItem::query()->get()->keyBy(fn ($item) => $item->category_id . ':' . $item->name);
        $scheduleData = [
            '2026-09-17' => [
                ['name' => 'Baca Buku', 'type' => 'duration', 'target' => 30, 'unit' => 'menit', 'description' => 'novel', 'done' => true],
                ['name' => 'Coding', 'type' => 'duration', 'target' => 60, 'unit' => 'menit', 'description' => 'project', 'done' => true],
            ],
            '2026-09-18' => [
                ['category' => 'Diet', 'item' => 'Sarapan', 'name' => 'Sarapan', 'type' => 'count', 'target' => 1, 'unit' => 'x', 'description' => 'oatmeal'],
                ['category' => 'Mental', 'item' => 'Digital Detox', 'name' => 'Digital Detox', 'type' => 'duration', 'target' => 60, 'unit' => 'menit', 'description' => 'no phone'],
                ['category' => 'Diet', 'item' => 'Menu Sehat', 'name' => 'Menu Sehat', 'type' => 'count', 'target' => 3, 'unit' => 'x', 'description' => 'harian'],
            ],
            '2026-09-19' => [
                ['category' => 'Mental', 'item' => 'Digital Detox', 'name' => 'Digital Detox', 'type' => 'duration', 'target' => 60, 'unit' => 'menit', 'description' => 'no phone'],
                ['category' => 'Diet', 'item' => 'Minum Air', 'name' => 'Minum Air', 'type' => 'count', 'target' => 8, 'unit' => 'gelas', 'description' => 'hidrasi'],
                ['category' => 'Mental', 'item' => 'Meditasi Pagi', 'name' => 'Meditasi Pagi', 'type' => 'duration', 'target' => 10, 'unit' => 'menit', 'description' => 'tenang'],
            ],
            '2026-09-20' => [
                ['category' => 'Diet', 'item' => 'Konsumsi Buah', 'name' => 'Konsumsi Buah', 'type' => 'count', 'target' => 2, 'unit' => 'x', 'description' => 'vitamin'],
                ['category' => 'Sosial', 'item' => 'Nonton Bareng', 'name' => 'Nonton Bareng', 'type' => 'duration', 'target' => 90, 'unit' => 'menit', 'description' => 'film'],
                ['category' => 'Keuangan', 'item' => 'Cek Saldo', 'name' => 'Cek Saldo', 'type' => 'count', 'target' => 1, 'unit' => 'x', 'description' => 'pagi'],
            ],
            '2026-09-21' => [
                ['category' => 'Belajar', 'item' => 'Bahasa Inggris', 'name' => 'Bahasa Inggris', 'type' => 'duration', 'target' => 20, 'unit' => 'menit', 'description' => 'vocab'],
                ['category' => 'Hobi', 'item' => 'Fotografi', 'name' => 'Fotografi', 'type' => 'duration', 'target' => 60, 'unit' => 'menit', 'description' => 'street'],
                ['category' => 'Belajar', 'item' => 'Desain', 'name' => 'Desain', 'type' => 'duration', 'target' => 30, 'unit' => 'menit', 'description' => 'latihan'],
            ],
            '2026-09-22' => [
                ['category' => 'Sport', 'item' => 'Bersepeda', 'name' => 'Bersepeda', 'type' => 'duration', 'target' => 40, 'unit' => 'menit', 'description' => 'sore'],
                ['category' => 'Belajar', 'item' => 'Journaling', 'name' => 'Journaling', 'type' => 'duration', 'target' => 15, 'unit' => 'menit', 'description' => 'refleksi'],
                ['category' => 'Sport', 'item' => 'Plank', 'name' => 'Plank', 'type' => 'duration', 'target' => 2, 'unit' => 'menit', 'description' => 'core'],
            ],
            '2026-09-23' => [
                ['category' => 'Musik', 'item' => 'Teori Musik', 'name' => 'Teori Musik', 'type' => 'duration', 'target' => 15, 'unit' => 'menit', 'description' => 'baca not'],
                ['category' => 'Sport', 'item' => 'Badminton', 'name' => 'Badminton', 'type' => 'duration', 'target' => 60, 'unit' => 'menit', 'description' => 'sore'],
                ['category' => 'Hobi', 'item' => 'Fotografi', 'name' => 'Fotografi', 'type' => 'duration', 'target' => 60, 'unit' => 'menit', 'description' => 'street'],
            ],
            '2026-09-24' => [
                ['category' => 'Hobi', 'item' => 'Berkebun', 'name' => 'Berkebun', 'type' => 'duration', 'target' => 30, 'unit' => 'menit', 'description' => 'tanaman'],
                ['category' => 'Diet', 'item' => 'Hindari Gula', 'name' => 'Hindari Gula', 'type' => 'count', 'target' => 1, 'unit' => 'x', 'description' => 'harian'],
                ['category' => 'Musik', 'item' => 'Piano', 'name' => 'Piano', 'type' => 'duration', 'target' => 20, 'unit' => 'menit', 'description' => 'latihan'],
            ],
        ];

        foreach ($scheduleData as $date => $items) {
            if ($date > $today) {
                continue;
            }

            $schedule = Schedule::updateOrCreate(
                ['user_id' => $user->id, 'scheduled_date' => $date],
                ['title' => 'Jadwal Harian', 'notes' => null, 'is_confirmed' => $date === '2026-09-17'],
            );

            foreach ($items as $item) {
                $categoryId = isset($item['category']) ? $categoryIds[$item['category']] : null;
                $categoryItem = $categoryId && isset($item['item']) ? $categoryItemIds[$categoryId . ':' . $item['item']] : null;
                unset($item['category'], $item['item']);
                $schedule->items()->updateOrCreate(
                    ['name' => $item['name']],
                    $item + ['category_id' => $categoryId, 'category_item_id' => $categoryItem ? $categoryItem->id : null, 'done' => isset($item['done']) ? $item['done'] : false],
                );
            }
        }

        ScheduleConfirmation::updateOrCreate(
            ['user_id' => $user->id, 'scheduled_date' => '2026-09-17'],
            [
                'done_count' => 2,
                'confirmed_at' => '2026-09-17 06:13:21',
                'items' => [
                    ['nama' => 'Baca Buku', 'kategori' => 'Belajar', 'target' => 30, 'satuan' => 'menit', 'status' => 'done'],
                    ['nama' => 'Coding', 'kategori' => 'Belajar', 'target' => 60, 'satuan' => 'menit', 'status' => 'done'],
                ],
            ],
        );

        $this->seedSeptemberSchedules($user, $categoryIds, $categoryItemIds);
        $this->seedSeptemberSchedules($testUser, $categoryIds, $categoryItemIds);

        ConsultationBooking::updateOrCreate(
            ['user_id' => $user->id, 'doctor_id' => $doctor->id, 'booking_date' => '2026-09-19', 'booking_time' => '09:00 - 10:00'],
            ['notes' => null, 'status' => 'rejected'],
        );

        $answers = [];
        foreach (range(1, 20) as $question) {
            $answers[(string) $question] = [0, 1, 3, 4][($question - 1) % 4];
        }
        TestResult::updateOrCreate(
            ['user_id' => $testUser->id, 'mental_test_id' => $mentalTest->id, 'score' => 40],
            ['answers' => $answers, 'level_label' => 'Sedang (31-50)', 'label' => 'Indikasi Depresi Sedang', 'sublabel' => 'Gejala sedang terdeteksi', 'detail' => 'Sangat disarankan untuk berkonsultasi dengan psikolog.', 'started_at' => '2026-09-16 11:45:08', 'finished_at' => '2026-09-16 11:45:08'],
        );
        TestResult::updateOrCreate(
            ['user_id' => $testUser->id, 'mental_test_id' => $mentalTest->id, 'score' => 80],
            ['answers' => array_fill_keys(array_map('strval', range(1, 20)), 4), 'level_label' => 'Berat (51-80)', 'label' => 'Indikasi Depresi Berat', 'sublabel' => 'Segera cari bantuan profesional', 'detail' => 'Segera hubungi psikiater atau profesional kesehatan mental.', 'started_at' => '2026-09-16 11:45:54', 'finished_at' => '2026-09-16 11:45:54'],
        );
        TestResult::updateOrCreate(
            ['user_id' => $testUser->id, 'mental_test_id' => $mentalTest->id, 'score' => 0],
            ['answers' => array_fill_keys(array_map('strval', range(1, 20)), 0), 'level_label' => 'Normal (0-15)', 'label' => 'Kondisi Normal', 'sublabel' => 'Fluktuasi stres masih dalam batas wajar', 'detail' => 'Tidak ada indikasi depresi. Pertahankan pola hidup sehat.', 'started_at' => '2026-09-16 11:46:47', 'finished_at' => '2026-09-16 11:46:47'],
        );
    }

    private function seedSeptemberSchedules(User $user, $categoryIds, $categoryItemIds): void
    {
        $activityPool = [
            ['category' => 'Sport', 'item' => 'Joging', 'type' => 'duration', 'target' => 30, 'unit' => 'menit', 'description' => 'pagi'],
            ['category' => 'Diet', 'item' => 'Minum Air', 'type' => 'count', 'target' => 8, 'unit' => 'gelas', 'description' => 'hidrasi'],
            ['category' => 'Mental', 'item' => 'Meditasi Pagi', 'type' => 'duration', 'target' => 10, 'unit' => 'menit', 'description' => 'tenang'],
            ['category' => 'Belajar', 'item' => 'Baca Buku', 'type' => 'duration', 'target' => 30, 'unit' => 'menit', 'description' => 'novel'],
            ['category' => 'Produktivitas', 'item' => 'Rencana Harian', 'type' => 'duration', 'target' => 10, 'unit' => 'menit', 'description' => 'pagi'],
            ['category' => 'Hobi', 'item' => 'Berkebun', 'type' => 'duration', 'target' => 30, 'unit' => 'menit', 'description' => 'tanaman'],
            ['category' => 'Tidur', 'item' => 'Tidur Malam', 'type' => 'duration', 'target' => 7, 'unit' => 'jam', 'description' => 'cukup'],
            ['category' => 'Spiritual', 'item' => 'Doa Pagi', 'type' => 'duration', 'target' => 10, 'unit' => 'menit', 'description' => 'syukur'],
        ];

        foreach (range(1, 30) as $day) {
            $date = sprintf('2026-09-%02d', $day);
            if ($date > now()->toDateString()) {
                continue;
            }

            $selectedActivities = [];

            foreach (range(0, 2) as $offset) {
                $activity = $activityPool[($day - 1 + $offset) % count($activityPool)];
                $categoryId = $categoryIds[$activity['category']];
                $categoryItem = $categoryItemIds[$categoryId . ':' . $activity['item']];
                $done = (($day + $offset) % 3) !== 0;

                $selectedActivities[] = [
                    'category_id' => $categoryId,
                    'category_item_id' => $categoryItem->id,
                    'name' => $activity['item'],
                    'type' => $activity['type'],
                    'target' => $activity['target'],
                    'unit' => $activity['unit'],
                    'description' => $activity['description'],
                    'done' => $done,
                ];
            }

            $schedule = Schedule::updateOrCreate(
                ['user_id' => $user->id, 'scheduled_date' => $date],
                ['title' => 'Aktivitas September', 'notes' => 'Data dummy aktivitas harian', 'is_confirmed' => true],
            );
            $schedule->items()->delete();
            $schedule->items()->createMany($selectedActivities);

            $historyItems = collect($selectedActivities)->map(fn (array $item) => [
                'nama' => $item['name'],
                'kategori' => $activityPool[array_search($item['name'], array_column($activityPool, 'item'))]['category'],
                'target' => $item['target'],
                'satuan' => $item['unit'],
                'status' => $item['done'] ? 'done' : 'failed',
            ])->values()->all();

            ScheduleConfirmation::updateOrCreate(
                ['user_id' => $user->id, 'scheduled_date' => $date],
                [
                    'done_count' => collect($selectedActivities)->where('done', true)->count(),
                    'confirmed_at' => $date . ' 21:00:00',
                    'items' => $historyItems,
                ],
            );
        }
    }

    private function clearFutureDemoSchedules(array $userIds, string $today): void
    {
        ScheduleConfirmation::query()
            ->whereIn('user_id', $userIds)
            ->whereDate('scheduled_date', '>', $today)
            ->delete();

        Schedule::query()
            ->whereIn('user_id', $userIds)
            ->whereDate('scheduled_date', '>', $today)
            ->delete();
    }

    private function seedMentalTests(): void
    {
        MentalTest::updateOrCreate(
            ['jenis' => 'Depresi'],
            [
                'judul' => 'Skrining Depresi',
                'gejala_singkat' => 'Deteksi gejala depresi melalui 20 pertanyaan berdasarkan kondisi 2 minggu terakhir.',
                'warna' => '#186369',
                'detail' => 'Kuesioner ini menilai suasana hati, energi, tidur, nafsu makan, konsentrasi, dan perasaan berharga.',
                'deskripsi_tes' => 'Baca setiap pernyataan dengan cermat dan pilih jawaban yang paling menggambarkan kondisi Anda selama 2 minggu terakhir.',
                'intro' => 'Selama 2 minggu terakhir, seberapa sering Anda mengalami hal berikut?',
                'soal' => [
                    'Saya merasa sedih atau murung tanpa alasan yang jelas.',
                    'Saya merasa sangat kesepian meskipun sedang berada di keramaian.',
                    'Saya merasa ingin menangis atau menangis lebih sering dari biasanya.',
                    'Saya merasa putus asa secara mendalam tentang masa depan saya.',
                    'Saya merasa cemas, tegang, atau tidak bisa tenang.',
                    'Saya kehilangan minat pada hobi atau aktivitas yang biasanya saya sukai.',
                    'Saya merasa tidak memiliki energi atau kekuatan untuk beraktivitas.',
                    'Saya merasa sulit untuk memulai atau menyelesaikan pekerjaan sehari-hari.',
                    'Saya merasa tidak ada hal yang bisa membuat saya merasa bahagia lagi.',
                    'Saya enggan atau malas berinteraksi dengan orang-orang di sekitar saya.',
                    'Saya mengalami gangguan tidur atau tidur berlebihan.',
                    'Nafsu makan saya berubah drastis.',
                    'Saya merasa tubuh saya sangat berat, lelah, dan lemas sepanjang hari.',
                    'Saya mengalami keluhan fisik akibat stres.',
                    'Saya merasa sulit berkonsentrasi saat membaca atau bekerja.',
                    'Saya membutuhkan waktu lama untuk mengambil keputusan kecil.',
                    'Saya merasa ingatan saya melemah.',
                    'Pikiran saya dipenuhi rasa bersalah.',
                    'Saya merasa diri saya tidak berguna atau tidak berharga.',
                    'Muncul pikiran bahwa akan lebih baik jika saya tidak bangun lagi.',
                ],
                'options' => [
                    ['value' => 0, 'label' => 'Tidak Pernah', 'warna' => '#10b981'],
                    ['value' => 1, 'label' => 'Jarang', 'warna' => '#3a8a8c'],
                    ['value' => 2, 'label' => 'Kadang-kadang', 'warna' => '#7ec3ec'],
                    ['value' => 3, 'label' => 'Sering', 'warna' => '#f97316'],
                    ['value' => 4, 'label' => 'Selalu', 'warna' => '#e63946'],
                ],
                'diagnosis' => [
                    'minimal' => ['levelLabel' => 'Normal (0-15)', 'label' => 'Kondisi Normal', 'sublabel' => 'Fluktuasi stres masih dalam batas wajar', 'detail' => 'Tidak ada indikasi depresi. Pertahankan pola hidup sehat.', 'warna' => '#10b981'],
                    'ringan' => ['levelLabel' => 'Ringan (16-30)', 'label' => 'Indikasi Depresi Ringan', 'sublabel' => 'Ada gejala ringan yang perlu diperhatikan', 'detail' => 'Perhatikan kesehatan mental Anda.', 'warna' => '#3a8a8c'],
                    'sedang' => ['levelLabel' => 'Sedang (31-50)', 'label' => 'Indikasi Depresi Sedang', 'sublabel' => 'Gejala sedang terdeteksi', 'detail' => 'Sangat disarankan untuk berkonsultasi dengan psikolog.', 'warna' => '#f59e0b'],
                    'berat' => ['levelLabel' => 'Berat (51-80)', 'label' => 'Indikasi Depresi Berat', 'sublabel' => 'Segera cari bantuan profesional', 'detail' => 'Segera hubungi psikiater atau profesional kesehatan mental.', 'warna' => '#e63946'],
                ],
                'impacts' => [],
                'recommendations' => [
                    ['icon' => '📝', 'title' => 'Tulis Jurnal Harian', 'description' => 'Tuangkan perasaan Anda setiap hari', 'action' => 'Buka Jurnal', 'link' => '/jurnal'],
                    ['icon' => '💬', 'title' => 'Bercerita ke Orang Dipercaya', 'description' => 'Bicarakan hal yang mengganggu pikiran Anda', 'action' => 'Konsultasi', 'link' => '/konsultasi'],
                ],
                'when_to_seek_help' => [
                    'Perasaan sedih atau putus asa berlangsung lebih dari 2 minggu.',
                    'Gejala mengganggu pekerjaan, sekolah, atau hubungan.',
                    'Muncul pikiran untuk menyakiti diri sendiri atau orang lain.',
                ],
                'is_active' => true,
            ],
        );
    }
}
