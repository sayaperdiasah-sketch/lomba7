<?php

namespace App\Http\Controllers;

use App\Models\ConsultationBooking;
use App\Models\Doctor;
use App\Models\Faq;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsultationBookingController extends Controller
{
    public function index(Request $request): View
    {
        $doctors = Doctor::query()->latest()->get()->map(fn (Doctor $doctor) => [
            'id' => $doctor->id,
            'nama' => $doctor->name,
            'spesialisasi' => $doctor->spesialisasi,
            'bio' => $doctor->bio,
            'avatar' => $doctor->photo ?: $doctor->avatar,
            'wa' => $doctor->whatsapp,
            'web' => $doctor->website,
            'jam_praktik' => $doctor->jam_praktik,
            'online' => $doctor->online,
        ]);
        $bookings = $request->user()->consultationBookings()->with('doctor')->latest()->get()->map(fn (ConsultationBooking $booking) => [
            'id' => $booking->id,
            'dokter' => $booking->doctor->name,
            'spesialisasi' => $booking->doctor->spesialisasi,
            'tanggal' => date('Y-m-d', strtotime((string) $booking->booking_date)),
            'jam' => $booking->booking_time,
            'status' => match ($booking->status) {
                'approved' => 'aktif',
                'done' => 'selesai',
                'rejected' => 'dibatalkan',
                default => 'menunggu',
            },
            'wa' => $booking->doctor->whatsapp,
            'catatan' => $booking->notes,
        ]);

        return view('pages.konsultasi', compact('doctors', 'bookings'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'doctor_id' => ['required', 'exists:doctors,id'],
            'booking_date' => ['required', 'date'],
            'booking_time' => ['required', 'string', 'max:30'],
            'notes' => ['nullable', 'string'],
        ]);

        $booking = $request->user()->consultationBookings()->create($validated);

        return response()->json($booking->load('doctor'), 201);
    }

    public function cancel(Request $request, ConsultationBooking $booking): JsonResponse
    {
        abort_unless($booking->user_id === $request->user()->id, 403);

        if (in_array($booking->status, ['done', 'rejected', 'cancelled', 'canceled'], true)) {
            return response()->json([
                'message' => 'Booking sudah selesai atau sudah dibatalkan.',
            ], 422);
        }

        $booking->update(['status' => 'rejected']);

        return response()->json($booking->fresh());
    }

    public function faqReply(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);
        $message = mb_strtolower(trim($validated['message']));

        $faq = Faq::query()
            ->where('is_active', true)
            ->orderBy('order')
            ->orderBy('id')
            ->get()
            ->first(function (Faq $faq) use ($message) {
                return collect($faq->keywords ?: [$faq->question])
                    ->filter(fn (?string $keyword) => $keyword !== null && $keyword !== '')
                    ->contains(fn (string $keyword) => str_contains($message, mb_strtolower(trim($keyword))));
            });

        return response()->json([
            'reply' => $faq?->answer ?? 'Baik, saya catat ya. Ada hal lain yang bisa saya bantu?',
            'faq_id' => $faq?->id,
        ]);
    }
}
