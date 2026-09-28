<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\ScheduleConfirmation;
use App\Models\Category;
use App\Models\RecommendationProgram;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $schedules = $request->user()->schedules()->with('items')->orderBy('scheduled_date')->get();
        $categories = Category::query()->where('is_global', true)->with('items')->orderBy('name')->get()->map(fn (Category $category) => [
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
        $scheduleData = [];
        foreach ($schedules as $schedule) {
            $scheduleData[$schedule->scheduled_date->format('Y-m-d')] = $schedule->items->map(function ($item) use ($schedule) {
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
                    'done' => $item->done,
                    'date' => $schedule->scheduled_date->format('Y-m-d'),
                ];
            })->values();
        }
        $confirmedDates = ScheduleConfirmation::query()
            ->where('user_id', $request->user()->id)
            ->pluck('scheduled_date')
            ->map(fn ($date) => date('Y-m-d', strtotime((string) $date)))
            ->values();
        $history = ScheduleConfirmation::query()
            ->where('user_id', $request->user()->id)
            ->latest('confirmed_at')
            ->get()
            ->map(function (ScheduleConfirmation $confirmation) use ($scheduleData) {
                $date = $confirmation->scheduled_date->format('Y-m-d');
                $items = $confirmation->items;

                if (empty($items)) {
                    $items = collect($scheduleData[$date] ?? [])
                        ->where('done', true)
                        ->map(fn (array $item) => [
                            'nama' => $item['nama'],
                            'kategori' => $item['kategori'],
                            'target' => $item['target'],
                            'satuan' => $item['satuan'],
                        ])
                        ->values()
                        ->all();
                }

                return [
                    'tanggal' => $date,
                    'tanggalLabel' => $confirmation->scheduled_date->translatedFormat('l, d F Y'),
                    'doneCount' => $confirmation->done_count,
                    'items' => $items,
                ];
            })
            ->values();
        $templates = RecommendationProgram::query()
            ->where('is_active', true)
            ->latest('id')
            ->get()
            ->map(fn (RecommendationProgram $program) => [
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
            ])->values();

        return view('pages.jadwal', compact('schedules', 'categories', 'scheduleData', 'confirmedDates', 'history', 'templates'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scheduled_date' => ['required', 'date'],
            'title' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.type' => ['required', 'in:count,duration'],
            'items.*.target' => ['required', 'integer', 'min:0'],
            'items.*.unit' => ['nullable', 'string', 'max:30'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'items.*.category_item_id' => ['nullable', 'integer', 'exists:category_items,id'],
            'items.*.done' => ['sometimes', 'boolean'],
        ]);

        if ($validated['scheduled_date'] > now()->toDateString()
            && collect($validated['items'] ?? [])->contains(fn (array $item) => ($item['done'] ?? false) === true)) {
            abort(422, 'Hari tersebut belum tiba. Aktivitas belum dapat diselesaikan.');
        }

        abort_if(
            $validated['scheduled_date'] < now()->toDateString(),
            422,
            'Hari sudah dilewati. Jadwal tidak dapat diubah.',
        );

        abort_if(
            ScheduleConfirmation::query()
                ->where('user_id', $request->user()->id)
                ->whereDate('scheduled_date', $validated['scheduled_date'])
                ->exists(),
            422,
            'Jadwal pada tanggal tersebut sudah dikonfirmasi dan tidak dapat diubah.',
        );

        $items = $validated['items'] ?? [];
        unset($validated['items']);

        $schedule = DB::transaction(function () use ($request, $validated, $items) {
            $schedule = $request->user()->schedules()->updateOrCreate(
                ['scheduled_date' => $validated['scheduled_date']],
                collect($validated)->except('items')->all(),
            );

            $schedule->items()->delete();
            $schedule->items()->createMany($items);

            return $schedule->load('items');
        });

        return response()->json($schedule, 201);
    }

    public function confirm(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scheduled_date' => ['required', 'date'],
        ]);

        abort_if(
            $validated['scheduled_date'] > now()->toDateString(),
            422,
            'Hari tersebut belum tiba. Aktivitas belum dapat dikonfirmasi.',
        );

        abort_if(
            $validated['scheduled_date'] < now()->toDateString(),
            422,
            'Hari sudah dilewati. Jadwal tidak dapat dikonfirmasi ulang.',
        );

        $schedule = $request->user()->schedules()
            ->whereDate('scheduled_date', $validated['scheduled_date'])
            ->with('items')
            ->firstOrFail();
        $allItems = $schedule->items->values();
        $doneItems = $allItems->where('done', true)->values();
        $doneCount = $doneItems->count();

        abort_if($allItems->isEmpty(), 422, 'Jadwal harus memiliki minimal satu aktivitas.');

        $historyItems = $allItems->map(fn ($item) => [
            'nama' => $item->name,
            'kategori' => $item->category?->name ?? $item->categoryItem?->category?->name ?? 'Lainnya',
            'target' => $item->target,
            'satuan' => $item->unit,
            'status' => $item->done ? 'done' : 'failed',
        ])->values()->all();

        $confirmation = DB::transaction(function () use ($request, $validated, $doneCount, $historyItems, $schedule) {
            $confirmation = ScheduleConfirmation::updateOrCreate(
                ['user_id' => $request->user()->id, 'scheduled_date' => $validated['scheduled_date']],
                ['done_count' => $doneCount, 'confirmed_at' => now(), 'items' => $historyItems],
            );

            $schedule->update(['is_confirmed' => true]);

            return $confirmation;
        });

        return response()->json([
            'confirmation' => $confirmation,
            'history' => [
                'tanggal' => $confirmation->scheduled_date->format('Y-m-d'),
                'tanggalLabel' => $confirmation->scheduled_date->translatedFormat('l, d F Y'),
                'doneCount' => $confirmation->done_count,
                'items' => $confirmation->items,
            ],
        ]);
    }

    public function update(Request $request, Schedule $schedule): JsonResponse
    {
        abort_unless($schedule->user_id === $request->user()->id, 403);
        abort_if($schedule->is_confirmed, 422, 'Jadwal yang sudah dikonfirmasi tidak dapat diubah.');
        abort_if($schedule->scheduled_date->isPast(), 422, 'Hari sudah dilewati. Jadwal tidak dapat diubah.');

        $validated = $request->validate([
            'scheduled_date' => ['sometimes', 'date'],
            'title' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'is_confirmed' => ['sometimes', 'boolean'],
        ]);

        $schedule->update($validated);

        return response()->json($schedule->fresh('items'));
    }

    public function destroy(Request $request, Schedule $schedule): JsonResponse
    {
        abort_unless($schedule->user_id === $request->user()->id, 403);
        abort_if($schedule->is_confirmed, 422, 'Jadwal yang sudah dikonfirmasi tidak dapat dihapus.');
        abort_if($schedule->scheduled_date->isPast(), 422, 'Hari sudah dilewati. Jadwal tidak dapat dihapus.');
        $schedule->delete();

        return response()->json(['message' => 'Jadwal berhasil dihapus.']);
    }
}
