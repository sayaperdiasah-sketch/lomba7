<?php

namespace App\Http\Controllers;

use App\Models\JourneyEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JourneyEntryController extends Controller
{
    public function index(Request $request): View
    {
        $entries = $request->user()->journeyEntries()->latest('entry_date')->get()->map(fn (JourneyEntry $entry) => [
            'id' => $entry->id,
            'judul' => $entry->title,
            'isi' => $entry->content,
            'tanggal' => date('d-m-Y', strtotime((string) $entry->entry_date)),
        ]);

        return view('pages.jurnal', compact('entries'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'entry_date' => ['required', 'date'],
        ]);

        return response()->json($request->user()->journeyEntries()->create($validated), 201);
    }

    public function update(Request $request, JourneyEntry $journeyEntry): JsonResponse
    {
        abort_unless($journeyEntry->user_id === $request->user()->id, 403);
        $journeyEntry->update($request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'content' => ['sometimes', 'string'],
            'entry_date' => ['sometimes', 'date'],
        ]));

        return response()->json($journeyEntry->fresh());
    }

    public function destroy(Request $request, JourneyEntry $journeyEntry): JsonResponse
    {
        abort_unless($journeyEntry->user_id === $request->user()->id, 403);
        $journeyEntry->delete();

        return response()->json(['message' => 'Jurnal berhasil dihapus.']);
    }
}
