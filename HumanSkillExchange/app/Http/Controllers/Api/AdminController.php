<?php

namespace App\Http\Controllers\Api;

use App\Models\ExchangeRequest;
use App\Models\Review;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends BaseApiController
{
    public function users(Request $request): JsonResponse
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $users = User::query()
            ->select('id', 'name', 'email', 'role', 'is_verified', 'plan_id', 'created_at')
            ->with('plan:id,name')
            ->latest('id')
            ->get();

        return $this->success('Data semua user berhasil diambil', $users);
    }

    public function exchanges(Request $request): JsonResponse
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $exchanges = ExchangeRequest::query()
            ->select([
                'exchange_requests.*',
                'from_user.name as from_user_name',
                'to_user.name as to_user_name',
            ])
            ->join('users as from_user', 'from_user.id', '=', 'exchange_requests.from_user_id')
            ->join('users as to_user', 'to_user.id', '=', 'exchange_requests.to_user_id')
            ->latest('exchange_requests.id')
            ->get();

        return $this->success('Data semua exchange request berhasil diambil', $exchanges);
    }

    public function reviews(Request $request): JsonResponse
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        // Admin melihat semua review termasuk yang disembunyikan.
        $reviews = Review::query()
            ->select('reviews.*', 'reviewer.name as reviewer_name', 'reviewed.name as reviewed_user_name')
            ->join('users as reviewer', 'reviewer.id', '=', 'reviews.reviewer_id')
            ->join('users as reviewed', 'reviewed.id', '=', 'reviews.reviewed_user_id')
            ->latest('reviews.id')
            ->get();

        return $this->success('Data semua review berhasil diambil', $reviews);
    }

    public function transactions(Request $request): JsonResponse
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $transactions = Transaction::with('user:id,name,email')->latest('id')->get();

        return $this->success('Data semua transaksi berhasil diambil', $transactions);
    }

    public function verifyUser(Request $request, int $id): JsonResponse
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $user = User::find($id);

        if (! $user) {
            return $this->fail('User tidak ditemukan', 404);
        }

        $user->update(['is_verified' => true]);

        return $this->success('User berhasil diverifikasi', [
            'id' => (int) $user->id,
            'is_verified' => true,
        ]);
    }

    public function hideReview(Request $request, int $id): JsonResponse
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $review = Review::find($id);

        if (! $review) {
            return $this->fail('Review tidak ditemukan', 404);
        }

        $review->update(['is_hidden' => true]);

        return $this->success('Review berhasil disembunyikan', [
            'id' => (int) $review->id,
            'is_hidden' => true,
        ]);
    }

    private function ensureAdmin(Request $request): ?JsonResponse
    {
        return $request->user()?->role === 'admin'
            ? null
            : $this->fail('Akses ditolak. Hanya admin yang diizinkan', 403);
    }
}
