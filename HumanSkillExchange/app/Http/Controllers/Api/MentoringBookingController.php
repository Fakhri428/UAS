<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MentoringBooking;
use App\Models\MentoringRoom;
use App\Notifications\BookingApprovedNotification;
use App\Notifications\BookingDeclinedNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MentoringBookingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->role === 'admin') {
            $bookings = MentoringBooking::with(['room', 'user'])->latest()->get();
        } else {
            $bookings = MentoringBooking::with('room')->where('user_id', $user->id)->latest()->get();
        }

        return response()->json(['data' => $bookings]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'mentoring_room_id' => 'required|integer|exists:mentoring_rooms,id',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';

        $booking = MentoringBooking::create($data);
        return response()->json(['data' => $booking], 201);
    }

    public function book(Request $request, MentoringRoom $mentoringRoom): JsonResponse
    {
        $request->merge(['mentoring_room_id' => $mentoringRoom->id]);

        return $this->store($request);
    }

    public function update(Request $request, MentoringBooking $mentoringBooking): JsonResponse
    {
        $this->authorize('update', $mentoringBooking);
        $data = $request->validate([
            'status' => 'sometimes|string',
            'scheduled_at' => 'sometimes|date',
            'duration_minutes' => 'sometimes|integer',
        ]);

        $mentoringBooking->update($data);
        return response()->json(['data' => $mentoringBooking]);
    }

    public function mentorApprove(Request $request, MentoringBooking $booking)
    {
        $user = $request->user();
        $room = $booking->room;
        if (!$room || $room->mentor_id !== $user->id) {
            abort(403);
        }

        $booking->update(['status' => 'approved']);

        // Send notification to user
        $booking->user->notify(new BookingApprovedNotification($booking));

        if ($request->wantsJson()) {
            return response()->json(['data' => $booking]);
        }

        return redirect()->route('dashboard')->with('status', 'Booking disetujui dan notifikasi dikirim');
    }

    public function mentorDecline(Request $request, MentoringBooking $booking)
    {
        $user = $request->user();
        $room = $booking->room;
        if (!$room || $room->mentor_id !== $user->id) {
            abort(403);
        }

        $booking->update(['status' => 'declined']);

        // Send notification to user
        $booking->user->notify(new BookingDeclinedNotification($booking));

        if ($request->wantsJson()) {
            return response()->json(['data' => $booking]);
        }

        return redirect()->route('dashboard')->with('status', 'Booking ditolak dan notifikasi dikirim');
    }
}
