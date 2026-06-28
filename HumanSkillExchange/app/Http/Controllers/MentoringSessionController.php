<?php

namespace App\Http\Controllers;

use App\Models\MentoringBooking;
use App\Models\MentoringRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MentoringSessionController extends Controller
{
    public function show(MentoringBooking $booking)
    {
        // Check if user has access to this booking
        $user = Auth::user();
        abort_unless(
            (int) $booking->user_id === (int) $user->id || (int) $booking->room->mentor_id === (int) $user->id,
            403
        );

        return view('mentoring.session', compact('booking'));
    }

    public function updateStatus(Request $request, MentoringBooking $booking)
    {
        $user = Auth::user();
        abort_unless((int) $booking->room->mentor_id === (int) $user->id, 403);

        $data = $request->validate([
            'session_status' => ['required', 'in:scheduled,in_progress,completed,cancelled'],
        ]);

        $booking->update($data);

        return back()->with('status', 'Status sesi berhasil diperbarui!');
    }

    public function updateRoom(Request $request, MentoringRoom $room)
    {
        $user = Auth::user();
        abort_unless((int) $room->mentor_id === (int) $user->id, 403);

        $data = $request->validate([
            'video_link' => ['nullable', 'url', 'max:255'],
            'meeting_notes' => ['nullable', 'string'],
        ]);

        $room->update($data);

        return back()->with('status', 'Link dan catatan kelas berhasil diperbarui!');
    }
}
