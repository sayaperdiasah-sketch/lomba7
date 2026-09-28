<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDoctorController extends Controller
{
    public function index(): View
    {
        $doctors = Doctor::query()->latest('id')->get()->map(fn (Doctor $doctor) => $this->toClient($doctor));

        return view('admin.dokter', compact('doctors'));
    }

    public function store(Request $request): JsonResponse
    {
        return $this->persist($request);
    }

    public function update(Request $request, Doctor $doctor): JsonResponse
    {
        return $this->persist($request, $doctor);
    }

    public function destroy(Doctor $doctor): JsonResponse
    {
        $doctor->delete();

        return response()->json(['message' => 'Dokter berhasil dihapus.']);
    }

    private function persist(Request $request, ?Doctor $doctor = null): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'specialization' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'whatsapp' => ['required', 'string', 'max:30'],
            'website' => ['nullable', 'url', 'max:2048'],
            'jam_praktik' => ['nullable', 'string', 'max:255'],
            'online' => ['sometimes', 'boolean'],
            'photo' => ['nullable', 'string'],
            'photo_removed' => ['sometimes', 'boolean'],
            'avatar' => ['nullable', 'string', 'max:2048'],
        ]);

        $doctor ??= new Doctor();
        $doctor->fill([
            'name' => $validated['name'],
            'spesialisasi' => $validated['specialization'],
            'bio' => $validated['bio'] ?? null,
            'whatsapp' => $validated['whatsapp'],
            'website' => $validated['website'] ?? null,
            'jam_praktik' => $validated['jam_praktik'] ?? null,
            'online' => $validated['online'] ?? false,
            'avatar' => $validated['avatar'] ?? null,
        ]);

        if (!empty($validated['photo'])) {
            abort_unless(str_starts_with($validated['photo'], 'data:image/'), 422, 'Format foto tidak valid.');
            $doctor->photo = $validated['photo'];
        } elseif ($validated['photo_removed'] ?? false) {
            $doctor->photo = null;
        } elseif (!$doctor->exists) {
            $doctor->photo = null;
        }

        $doctor->save();

        return response()->json($this->toClient($doctor->fresh()), $doctor->wasRecentlyCreated ? 201 : 200);
    }

    private function toClient(Doctor $doctor): array
    {
        return [
            'id' => $doctor->id,
            'nama' => $doctor->name,
            'spesialisasi' => $doctor->spesialisasi,
            'bio' => $doctor->bio,
            'fotoBase64' => $doctor->photo,
            'avatar' => $doctor->avatar,
            'wa' => $doctor->whatsapp,
            'web' => $doctor->website,
            'jam_praktik' => $doctor->jam_praktik,
            'online' => (bool) $doctor->online,
        ];
    }
}