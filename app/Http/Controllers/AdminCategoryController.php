<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminCategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->where('is_global', true)
            ->with('items')
            ->orderBy('id')
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

        return view('admin.kategori', compact('categories'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateCategory($request);

        $category = DB::transaction(function () use ($validated) {
            $category = Category::create([
                'name' => $validated['nama'],
                'warna' => $validated['warna'],
                'teks_warna' => $validated['teksWarna'],
                'is_global' => true,
            ]);

            $this->syncItems($category, $validated['jenis']);

            return $category->load('items');
        });

        return response()->json($category, 201);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        abort_unless($category->is_global, 404);
        $validated = $this->validateCategory($request);

        DB::transaction(function () use ($category, $validated) {
            $category->update([
                'name' => $validated['nama'],
                'warna' => $validated['warna'],
                'teks_warna' => $validated['teksWarna'],
            ]);
            $category->items()->delete();
            $this->syncItems($category, $validated['jenis']);
        });

        return response()->json($category->fresh('items'));
    }

    public function destroy(Category $category): JsonResponse
    {
        abort_unless($category->is_global, 404);
        $category->delete();

        return response()->json(['message' => 'Kategori berhasil dihapus.']);
    }

    private function validateCategory(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'warna' => ['required', 'string', 'max:20'],
            'teksWarna' => ['required', 'string', 'max:20'],
            'jenis' => ['required', 'array', 'min:1'],
            'jenis.*.nama' => ['required', 'string', 'max:255'],
            'jenis.*.tipe' => ['required', 'in:count,duration'],
            'jenis.*.target' => ['required', 'integer', 'min:0'],
            'jenis.*.satuan' => ['required', 'string', 'max:30'],
            'jenis.*.keterangan' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function syncItems(Category $category, array $items): void
    {
        foreach ($items as $order => $item) {
            $category->items()->create([
                'name' => $item['nama'],
                'type' => $item['tipe'],
                'target' => $item['target'],
                'unit' => $item['satuan'],
                'description' => $item['keterangan'] ?? null,
                'order' => $order,
            ]);
        }
    }
}
