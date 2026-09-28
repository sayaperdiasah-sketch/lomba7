<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminArticleController extends Controller
{
    public function index(): View
    {
        $articles = Article::query()
            ->latest('published_at')
            ->latest('id')
            ->get()
            ->map(fn (Article $article) => $this->toClient($article))
            ->values();

        return view('admin.artikel', compact('articles'));
    }

    public function store(Request $request): JsonResponse
    {
        return $this->persist($request);
    }

    public function update(Request $request, Article $article): JsonResponse
    {
        return $this->persist($request, $article);
    }

    public function destroy(Article $article): JsonResponse
    {
        $this->deleteFiles($article);
        $article->delete();

        return response()->json(['message' => 'Artikel berhasil dihapus.']);
    }

    private function persist(Request $request, ?Article $article = null): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'published_at' => ['required', 'date'],
            'content' => ['nullable', 'string'],
            'web_url' => ['nullable', 'url', 'max:2048'],
            'pdf_base64' => ['nullable', 'string'],
            'cover_base64' => ['nullable', 'string'],
            'keep_pdf' => ['sometimes', 'boolean'],
            'keep_cover' => ['sometimes', 'boolean'],
        ]);

        $article ??= new Article();
        $oldPdfPath = $article->pdf_path;
        $oldThumbnail = $article->thumbnail;

        $article->fill([
            'title' => $validated['title'],
            'slug' => $this->uniqueSlug($validated['title'], $article),
            'content' => $validated['content'] ?? null,
            'source_url' => $validated['web_url'] ?? null,
            'is_published' => true,
            'published_at' => $validated['published_at'],
        ]);

        if (!empty($validated['pdf_base64'])) {
            $article->pdf_path = $this->storeBase64($validated['pdf_base64'], 'articles/pdf', 'pdf');
        } elseif (!($validated['keep_pdf'] ?? false)) {
            $article->pdf_path = null;
        }

        if (!empty($validated['cover_base64'])) {
            $article->thumbnail = $this->storeBase64($validated['cover_base64'], 'articles/covers', 'jpg');
        } elseif (!($validated['keep_cover'] ?? false)) {
            $article->thumbnail = null;
        }

        $article->save();

        if ($article->pdf_path && $article->pdf_path !== $oldPdfPath) {
            $this->deletePath($oldPdfPath);
        }
        if ($article->thumbnail && $article->thumbnail !== $oldThumbnail) {
            $this->deletePath($oldThumbnail);
        }

        return response()->json($this->toClient($article->fresh()), $article->wasRecentlyCreated ? 201 : 200);
    }

    private function uniqueSlug(string $title, ?Article $article): string
    {
        $base = Str::slug($title) ?: 'artikel';
        $slug = $base;
        $suffix = 2;

        while (Article::query()->where('slug', $slug)->when($article?->exists, fn ($query) => $query->where('id', '!=', $article->id))->exists()) {
            $slug = $base . '-' . $suffix++;
        }

        return $slug;
    }

    private function storeBase64(string $value, string $directory, string $extension): string
    {
        if (!preg_match('/^data:[^;]+;base64,(.+)$/s', $value, $matches)) {
            abort(422, 'Format file tidak valid.');
        }

        $path = $directory . '/' . Str::uuid() . '.' . $extension;
        Storage::disk('public')->put($path, base64_decode($matches[1], true));

        return $path;
    }

    private function toClient(Article $article): array
    {
        return [
            'id' => $article->id,
            'judul' => $article->title,
            'tanggal' => optional($article->published_at)->format('Y-m-d'),
            'pdfBase64' => (bool) $article->pdf_path,
            'pdfUrl' => $article->pdf_path ? asset('storage/' . $article->pdf_path) : null,
            'webUrl' => $article->source_url,
            'cover' => $article->thumbnail ? asset('storage/' . $article->thumbnail) : null,
            'deskripsi' => $article->content,
        ];
    }

    private function deleteFiles(Article $article): void
    {
        $this->deletePath($article->pdf_path);
        $this->deletePath($article->thumbnail);
    }

    private function deletePath(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}