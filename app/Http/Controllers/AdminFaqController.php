<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminFaqController extends Controller
{
    public function index(): View
    {
        $faqs = Faq::query()->orderBy('order')->orderBy('id')->get()->map(fn (Faq $faq) => $this->toClient($faq));

        return view('admin.faq', compact('faqs'));
    }

    public function store(Request $request): JsonResponse
    {
        return $this->persist($request);
    }

    public function update(Request $request, Faq $faq): JsonResponse
    {
        return $this->persist($request, $faq);
    }

    public function destroy(Faq $faq): JsonResponse
    {
        $faq->delete();

        return response()->json(['message' => 'FAQ berhasil dihapus.']);
    }

    private function persist(Request $request, ?Faq $faq = null): JsonResponse
    {
        $validated = $request->validate([
            'keywords' => ['required', 'array', 'min:1'],
            'keywords.*' => ['required', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'answer' => ['required', 'string'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $faq ??= new Faq();
        $keywords = collect($validated['keywords'])
            ->map(fn (string $keyword) => trim(mb_strtolower($keyword)))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $faq->fill([
            'question' => $keywords[0],
            'keywords' => $keywords,
            'category' => $validated['category'] ?? null,
            'answer' => $validated['answer'],
            'order' => $validated['order'] ?? 0,
            'is_active' => true,
        ]);
        $faq->save();

        return response()->json($this->toClient($faq->fresh()), $faq->wasRecentlyCreated ? 201 : 200);
    }

    private function toClient(Faq $faq): array
    {
        return [
            'id' => $faq->id,
            'keyword' => implode(', ', $faq->keywords ?: [$faq->question]),
            'kategori' => $faq->category,
            'balasan' => $faq->answer,
            'order' => $faq->order,
            'is_active' => $faq->is_active,
        ];
    }
}