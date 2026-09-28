<?php

namespace App\Http\Controllers;

use App\Models\MentalTest;
use App\Models\TestResult;
use App\Models\Article;
use App\Models\Category;
use App\Models\Doctor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MentalTestController extends Controller
{
    public function index(Request $request): View
    {
        $tests = MentalTest::query()->where('is_active', true)->latest()->get();
        $profileComplete = (bool) optional($request->user()->profile)->profile_completed_at;

        $searchArticles = Article::query()
            ->where('is_published', true)
            ->get(['id', 'title', 'content'])
            ->map(fn (Article $article) => [
                'id' => $article->id,
                'title' => $article->title,
                'content' => $article->content,
            ])
            ->values();

        $searchCategories = Category::query()
            ->where('is_global', true)
            ->with('items:id,category_id,name')
            ->get(['id', 'name'])
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'nama' => $category->name,
                'jenis' => $category->items->map(fn ($item) => ['nama' => $item->name])->values(),
            ])
            ->values();

        $searchDoctors = Doctor::query()
            ->orderBy('name')
            ->get(['id', 'name', 'spesialisasi', 'bio'])
            ->map(fn (Doctor $doctor) => [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'spesialisasi' => $doctor->spesialisasi,
                'bio' => $doctor->bio,
            ])
            ->values();

        $searchSchedules = $request->user()->schedules()
            ->with(['items:id,schedule_id,name,category_id,description', 'items.category:id,name'])
            ->get()
            ->mapWithKeys(fn ($schedule) => [
                $schedule->scheduled_date->format('Y-m-d') => $schedule->items->map(fn ($item) => [
                    'id' => $item->id,
                    'nama' => $item->name,
                    'kategori' => $item->category?->name ?? 'Lainnya',
                    'keterangan' => $item->description,
                ])->values(),
            ]);

        return view('pages.tes-mental', compact(
            'tests',
            'profileComplete',
            'searchArticles',
            'searchCategories',
            'searchDoctors',
            'searchSchedules',
        ));
    }

    public function show(int $id): View
    {
        $test = MentalTest::query()->where('is_active', true)->findOrFail($id);

        return view('pages.tes-mental-soal', [
            'test' => $test,
            'testId' => $test->id,
        ]);
    }

    public function submit(Request $request, int $id): RedirectResponse
    {
        $test = MentalTest::query()->where('is_active', true)->findOrFail($id);
        $questions = is_array($test->soal) ? $test->soal : [];

        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'integer', 'min:0', 'max:4'],
        ]);

        $answers = $validated['answers'];
        $score = array_sum(array_map('intval', $answers));
        $diagnosis = $this->resolveDiagnosis($test, $score);

        $result = TestResult::create([
            'user_id' => $request->user()->id,
            'mental_test_id' => $test->id,
            'answers' => $answers,
            'score' => $score,
            'level_label' => $diagnosis['levelLabel'] ?? null,
            'label' => $diagnosis['label'] ?? null,
            'sublabel' => $diagnosis['sublabel'] ?? null,
            'detail' => $diagnosis['detail'] ?? null,
            'started_at' => now(),
            'finished_at' => now(),
        ]);

        return redirect()->route('tes-mental.hasil', [
            'id' => $test->id,
            'resultId' => $result->id,
        ]);
    }

    public function result(Request $request, int $id, ?int $resultId = null): View
    {
        $test = MentalTest::query()->where('is_active', true)->findOrFail($id);
        $result = TestResult::query()
            ->where('user_id', $request->user()->id)
            ->where('mental_test_id', $test->id)
            ->when($resultId, fn ($query) => $query->whereKey($resultId))
            ->latest('finished_at')
            ->firstOrFail();

        return view('pages.tes-mental-hasil', [
            'test' => $test,
            'result' => $result,
            'testId' => $test->id,
        ]);
    }

    public function history(Request $request): View
    {
        $results = $request->user()->testResults()->with('mentalTest')->latest()->get();

        return view('pages.tes-mental-hasil', compact('results'));
    }

    private function resolveDiagnosis(MentalTest $test, int $score): array
    {
        $diagnosis = is_array($test->diagnosis) ? $test->diagnosis : [];

        foreach ($diagnosis as $item) {
            if (! is_array($item)) {
                continue;
            }

            $range = preg_replace('/[^0-9-]/', '', (string) ($item['levelLabel'] ?? ''));
            if ($range && str_contains($range, '-')) {
                [$minimum, $maximum] = array_map('intval', explode('-', $range, 2));
                if ($score >= $minimum && $score <= $maximum) {
                    return $item;
                }
            }
        }

        return [];
    }
}
