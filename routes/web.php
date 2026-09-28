<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminArticleController;
use App\Http\Controllers\AdminDoctorController;
use App\Http\Controllers\AdminFaqController;
use App\Http\Controllers\AdminRecommendationController;
use App\Http\Controllers\ConsultationBookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JourneyEntryController;
use App\Http\Controllers\MentalTestController;
use App\Http\Controllers\ProfileController;   // ✅ DITAMBAHKAN
use App\Http\Controllers\ScheduleController;
use App\Models\Article;

/* ═══════════════════════════════════════════════════════════
   USER
   ═══════════════════════════════════════════════════════════ */
Route::get('/', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

Route::get('/login', fn() => view('auth.login'))->name('login');
Route::get('/register', fn() => view('auth.login'))->name('register');

Route::middleware('auth')->group(function () {
    // ✅ DIUBAH: PATCH → POST karena fetch pakai FormData (method spoofing tidak perlu)
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/tes-mental', [MentalTestController::class, 'index'])->name('tes-mental');
    Route::get('/tes-mental/{id}/soal', [MentalTestController::class, 'show'])->name('tes-mental.soal');
    Route::post('/tes-mental/{id}/submit', [MentalTestController::class, 'submit'])->name('tes-mental.submit');
    Route::get('/tes-mental/{id}/hasil/{resultId?}', [MentalTestController::class, 'result'])->name('tes-mental.hasil');

    Route::get('/jadwal', [ScheduleController::class, 'index'])->name('jadwal');
    Route::post('/jadwal', [ScheduleController::class, 'store'])->name('jadwal.store');
    Route::post('/jadwal/confirm', [ScheduleController::class, 'confirm'])->name('jadwal.confirm');
    Route::patch('/jadwal/{schedule}', [ScheduleController::class, 'update'])->name('jadwal.update');
    Route::delete('/jadwal/{schedule}', [ScheduleController::class, 'destroy'])->name('jadwal.destroy');

    Route::get('/jurnal', [JourneyEntryController::class, 'index'])->name('jurnal');
    Route::post('/jurnal', [JourneyEntryController::class, 'store'])->name('jurnal.store');
    Route::patch('/jurnal/{journeyEntry}', [JourneyEntryController::class, 'update'])->name('jurnal.update');
    Route::delete('/jurnal/{journeyEntry}', [JourneyEntryController::class, 'destroy'])->name('jurnal.destroy');

    Route::get('/konsultasi', [ConsultationBookingController::class, 'index'])->name('konsultasi');
    Route::post('/konsultasi', [ConsultationBookingController::class, 'store'])->name('konsultasi.store');
    Route::patch('/konsultasi/{booking}/cancel', [ConsultationBookingController::class, 'cancel'])->name('konsultasi.cancel');
    Route::post('/konsultasi/{booking}/cancel', [ConsultationBookingController::class, 'cancel'])->name('konsultasi.cancel.post');
    Route::delete('/konsultasi/{booking}/cancel', [ConsultationBookingController::class, 'cancel'])->name('konsultasi.cancel.delete');
    Route::post('/konsultasi/faq-reply', [ConsultationBookingController::class, 'faqReply'])->name('konsultasi.faq-reply');
});

Route::get('/artikel', function () {
    $articles = Article::query()
        ->where('is_published', true)
        ->latest('published_at')
        ->latest('id')
        ->get();

    return view('pages.artikel', compact('articles'));
})->name('artikel');

Route::get('/artikel/{article}', function (Article $article) {
    abort_unless($article->is_published, 404);

    return view('pages.artikel-detail', compact('article'));
})->name('artikel.detail');


/* ═══════════════════════════════════════════════════════════
   AUTH — LOGIN / REGISTER / LOGOUT
   ═══════════════════════════════════════════════════════════ */
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/* ═══════════════════════════════════════════════════════════
   ADMIN
   ═══════════════════════════════════════════════════════════ */
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',            [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/kategori',    [AdminCategoryController::class, 'index'])->name('kategori');
    Route::post('/kategori',   [AdminCategoryController::class, 'store'])->name('kategori.store');
    Route::patch('/kategori/{category}', [AdminCategoryController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{category}', [AdminCategoryController::class, 'destroy'])->name('kategori.destroy');
    Route::get('/artikel',     [AdminArticleController::class, 'index'])->name('artikel');
    Route::post('/artikel',    [AdminArticleController::class, 'store'])->name('artikel.store');
    Route::patch('/artikel/{article}', [AdminArticleController::class, 'update'])->name('artikel.update');
    Route::delete('/artikel/{article}', [AdminArticleController::class, 'destroy'])->name('artikel.destroy');
    Route::get('/rekomendasi', [AdminRecommendationController::class, 'index'])->name('rekomendasi');
    Route::post('/rekomendasi', [AdminRecommendationController::class, 'store'])->name('rekomendasi.store');
    Route::patch('/rekomendasi/{recommendation}', [AdminRecommendationController::class, 'update'])->name('rekomendasi.update');
    Route::delete('/rekomendasi/{recommendation}', [AdminRecommendationController::class, 'destroy'])->name('rekomendasi.destroy');
    Route::get('/tes',         fn() => view('admin.tes'))->name('tes');
    Route::get('/dokter',      [AdminDoctorController::class, 'index'])->name('dokter');
    Route::post('/dokter',     [AdminDoctorController::class, 'store'])->name('dokter.store');
    Route::patch('/dokter/{doctor}', [AdminDoctorController::class, 'update'])->name('dokter.update');
    Route::delete('/dokter/{doctor}', [AdminDoctorController::class, 'destroy'])->name('dokter.destroy');
    Route::get('/faq',         [AdminFaqController::class, 'index'])->name('faq');
    Route::post('/faq',        [AdminFaqController::class, 'store'])->name('faq.store');
    Route::patch('/faq/{faq}', [AdminFaqController::class, 'update'])->name('faq.update');
    Route::delete('/faq/{faq}', [AdminFaqController::class, 'destroy'])->name('faq.destroy');
});