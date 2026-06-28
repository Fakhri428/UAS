<?php

namespace App\Http\Controllers\Api;

use App\Models\ExchangeRequest;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $userId = $request->integer('user_id');

        if (! $userId) {
            return $this->fail('Parameter user_id wajib dikirim', 400);
        }

        return $this->forUser($userId);
    }

    public function forUser(int $userId): JsonResponse
    {
        $reviews = Review::query()
            ->select('reviews.*', 'reviewer.name as reviewer_name')
            ->join('users as reviewer', 'reviewer.id', '=', 'reviews.reviewer_id')
            ->where('reviewed_user_id', $userId)
            ->where('reviews.is_hidden', false)
            ->latest('reviews.id')
            ->get();

        return $this->success('Review user berhasil diambil', $reviews);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request, [
            'exchange_request_id' => ['required', 'integer', 'exists:exchange_requests,id'],
            'reviewed_user_id' => ['required', 'integer', 'exists:users,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string'],
        ]);

        if ($data instanceof JsonResponse) {
            return $data;
        }

        $exchange = ExchangeRequest::where('id', $data['exchange_request_id'])
            ->whereIn('status', ['completed', 'reviewed'])
            ->where(fn ($query) => $query
                ->where('from_user_id', $request->user()->id)
                ->orWhere('to_user_id', $request->user()->id))
            ->first();

        if (! $exchange) {
            return $this->fail('Exchange completed tidak ditemukan atau bukan milik Anda', 404);
        }

        $participants = [(int) $exchange->from_user_id, (int) $exchange->to_user_id];

        if (! in_array((int) $data['reviewed_user_id'], $participants, true) || (int) $data['reviewed_user_id'] === $request->user()->id) {
            return $this->fail('User yang direview harus partner exchange', 422);
        }

        $exists = Review::where('exchange_request_id', $exchange->id)
            ->where('reviewer_id', $request->user()->id)
            ->where('reviewed_user_id', $data['reviewed_user_id'])
            ->exists();

        if ($exists) {
            return $this->fail('Anda sudah memberi review untuk exchange ini', 409);
        }

        $review = Review::create($data + ['reviewer_id' => $request->user()->id]);

        if (Review::where('exchange_request_id', $exchange->id)->count() >= 2) {
            $exchange->update(['status' => 'reviewed']);
        }

        return $this->success('Review berhasil dikirim', $review, 201);
    }
}
