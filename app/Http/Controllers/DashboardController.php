<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::query()->where('is_global', true)->with('items')->orderBy('name')->get()->map(fn (Category $category) => [
            'id' => $category->id,
            'nama' => $category->name,
            'warna' => $category->warna,
            'teksWarna' => $category->teks_warna,
            'jenis' => $category->items->map(fn ($item) => [
                'nama' => $item->name,
                'tipe' => $item->type,
                'target' => $item->target,
                'satuan' => $item->unit,
                'keterangan' => $item->description,
            ])->values(),
        ])->values();

        $schedules = [];
        foreach ($request->user()->schedules()->with(['items.category', 'items.categoryItem.category'])->get() as $schedule) {
            $schedules[$schedule->scheduled_date->format('Y-m-d')] = $schedule->items->map(function ($item) use ($schedule) {
                $category = $item->category;
                $categoryItem = $item->categoryItem;

                return [
                    'id' => $item->id,
                    'nama' => $item->name,
                    'kategori' => $category?->name ?? $categoryItem?->category?->name ?? 'Lainnya',
                    'category_id' => $item->category_id,
                    'category_item_id' => $item->category_item_id,
                    'warna' => $category?->warna ?? $categoryItem?->category?->warna ?? '#186369',
                    'teksWarna' => $category?->teks_warna ?? $categoryItem?->category?->teks_warna ?? '#ffffff',
                    'tipe' => $item->type,
                    'target' => $item->target,
                    'satuan' => $item->unit,
                    'keterangan' => $item->description,
                    'done' => (bool) $item->done,
                    'date' => $schedule->scheduled_date->format('Y-m-d'),
                ];
            })->values();
        }

        $articles = Article::query()
            ->where('is_published', true)
            ->latest('published_at')
            ->take(6)
            ->get()
            ->map(function (Article $article) {
                $article->thumbnail = $article->thumbnail_url;
                return $article;
            });

        $searchArticles = Article::query()
            ->where('is_published', true)
            ->get(['id', 'title', 'content'])
            ->map(fn (Article $article) => [
                'id' => $article->id,
                'title' => $article->title,
                'content' => $article->content,
            ])
            ->values();

        $doctors = Doctor::query()
            ->orderBy('name')
            ->get(['id', 'name', 'spesialisasi', 'bio', 'online'])
            ->map(fn (Doctor $doctor) => [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'spesialisasi' => $doctor->spesialisasi,
                'bio' => $doctor->bio,
                'online' => $doctor->online,
            ])
            ->values();

        $profileAvatar = $request->user()->profile?->avatar;
        $avatarUrl = $profileAvatar
            ? asset('storage/' . ltrim($profileAvatar, '/'))
            : asset('images/avatar.png');

        return view('pages.dashboard', compact('categories', 'schedules', 'articles', 'searchArticles', 'doctors', 'avatarUrl'));
    }
}
