<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\RecommendationProgram;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminRecommendationController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->where('is_global', true)
            ->with('items')
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'nama' => $category->name,
                'warna' => $category->warna,
                'teksWarna' => $category->teks_warna,
                'jenis' => $category->items->map(fn ($item) => [
                    'id' => $item->id,
                    'nama' => $item->name,
                    'tipe' => $item->type,
                    'target' => $item->target,
                    'satuan' => $item->unit,
                    'keterangan' => $item->description,
                ])->values(),
            ])->values();

        $templates = RecommendationProgram::query()->latest('id')->get()->map(fn (RecommendationProgram $program) => $this->toClient($program));

        return view('admin.rekomendasi', compact('categories', 'templates'));
    }

    public function store(Request $request): JsonResponse
    {
        return $this->persist($request);
    }

    public function update(Request $request, RecommendationProgram $recommendation): JsonResponse
    {
        return $this->persist($request, $recommendation);
    }

    public function destroy(RecommendationProgram $recommendation): JsonResponse
    {
        $recommendation->delete();

        return response()->json(['message' => 'Program berhasil dihapus.']);
    }

    private function persist(Request $request, ?RecommendationProgram $program = null): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'emoji' => ['nullable', 'string', 'max:20'],
            'color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'description' => ['nullable', 'string'],
            'activities' => ['required', 'array', 'min:1'],
            'activities.*.name' => ['required', 'string', 'max:255'],
            'activities.*.category' => ['required', 'string', 'max:255'],
            'activities.*.type' => ['required', 'in:count,duration'],
            'activities.*.target' => ['required', 'integer', 'min:0'],
            'activities.*.unit' => ['required', 'string', 'max:30'],
            'activities.*.description' => ['nullable', 'string', 'max:255'],
        ]);

        $program ??= new RecommendationProgram();
        $program->fill([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name'], $program),
            'emoji' => $validated['emoji'] ?: '🎯',
            'color' => $validated['color'],
            'description' => $validated['description'] ?? null,
            'activities' => $validated['activities'],
            'is_active' => true,
        ]);
        $program->save();

        return response()->json($this->toClient($program->fresh()), $program->wasRecentlyCreated ? 201 : 200);
    }

    private function uniqueSlug(string $name, ?RecommendationProgram $program): string
    {
        $base = Str::slug($name) ?: 'program';
        $slug = $base;
        $suffix = 2;

        while (RecommendationProgram::query()->where('slug', $slug)->when($program?->exists, fn ($query) => $query->where('id', '!=', $program->id))->exists()) {
            $slug = $base . '-' . $suffix++;
        }

        return $slug;
    }

    private function toClient(RecommendationProgram $program): array
    {
        return [
            'id' => $program->id,
            'nama' => $program->name,
            'emoji' => $program->emoji,
            'warna' => $program->color,
            'deskripsi' => $program->description,
            'aktivitas' => collect($program->activities)->map(fn (array $activity) => [
                'nama' => $activity['name'],
                'kategori' => $activity['category'],
                'tipe' => $activity['type'],
                'target' => $activity['target'],
                'satuan' => $activity['unit'],
                'keterangan' => $activity['description'] ?? '',
            ])->values(),
        ];
    }
}