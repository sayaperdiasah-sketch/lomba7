<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\CategoryItem;
use App\Models\Doctor;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'artikel' => Article::query()->count(),
                'kategori' => Category::query()->where('is_global', true)->count(),
                'aktivitas' => CategoryItem::query()->whereHas('category', fn ($query) => $query->where('is_global', true))->count(),
                'dokter' => Doctor::query()->count(),
            ],
        ]);
    }
}
