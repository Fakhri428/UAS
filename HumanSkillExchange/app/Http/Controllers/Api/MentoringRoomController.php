<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MentoringRoom;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MentoringRoomController extends Controller
{
    public function index(): JsonResponse
    {
        $rooms = MentoringRoom::with(['user', 'mentor'])->latest()->get();
        return response()->json(['data' => $rooms]);
    }

    public function show(MentoringRoom $mentoringRoom): JsonResponse
    {
        return response()->json(['data' => $mentoringRoom->load(['user', 'mentor'])]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // Hanya plan Pro Max yang boleh mengadakan kelas online.
        if (! $user->canHostClass()) {
            $message = 'Mengadakan kelas online hanya tersedia untuk plan Pro Max. Silakan upgrade plan kamu.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 403);
            }

            return back()->withErrors(['mentoring_room' => $message])->withInput();
        }

        $data = $request->validate([
            'title' => 'required|string|max:180',
            'description' => 'required|string',
            'schedule' => 'nullable|date',
            'duration_minutes' => 'required|integer|min:15',
            'price' => 'nullable|numeric|min:0',
        ]);

        // Pembuat kelas otomatis menjadi mentornya.
        $data['mentor_id'] = $user->id;
        $data['price'] = $data['price'] ?? 0;
        $data['status'] = 'open';
        $room = MentoringRoom::create($data);

        if ($request->expectsJson()) {
            return response()->json(['data' => $room], 201);
        }

        return back()->with('status', 'Kelas online berhasil dibuat.');
    }

    public function update(Request $request, MentoringRoom $mentoringRoom): JsonResponse
    {
        $this->authorize('update', $mentoringRoom);
        $data = $request->validate([
            'title' => 'sometimes|string|max:191',
            'description' => 'nullable|string',
            'scheduled_at' => 'sometimes|date',
            'duration_minutes' => 'sometimes|integer',
            'price' => 'sometimes|numeric',
            'status' => 'sometimes|string',
        ]);
        $mentoringRoom->update($data);
        return response()->json(['data' => $mentoringRoom]);
    }

    public function destroy(MentoringRoom $mentoringRoom): JsonResponse
    {
        $this->authorize('delete', $mentoringRoom);
        $mentoringRoom->delete();
        return response()->json([], 204);
    }
}
