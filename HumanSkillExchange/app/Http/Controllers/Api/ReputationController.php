<?php

namespace App\Http\Controllers\Api;

use App\Models\ExchangeRequest;
use App\Models\Review;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReputationController extends BaseApiController
{
    public function forUser(Request $request, int $id): JsonResponse
    {
        $request->merge(['user_id' => $id]);

        return $this->show($request);
    }

    public function show(Request $request): JsonResponse
    {
        $userId = $request->integer('user_id') ?: $request->user()->id;
        $user = User::select('id', 'name', 'email')->find($userId);

        if (! $user) {
            return $this->fail('User tidak ditemukan', 404);
        }

        $completedExchange = ExchangeRequest::whereIn('status', ['completed', 'reviewed'])
            ->where(fn ($query) => $query->where('from_user_id', $userId)->orWhere('to_user_id', $userId))
            ->count();

        $totalReviews = Review::where('reviewed_user_id', $userId)->count();
        $averageRating = round((float) Review::where('reviewed_user_id', $userId)->avg('rating'), 2);

        $topSkillCategories = Skill::select('category', DB::raw('COUNT(*) as total'))
            ->where('user_id', $userId)
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(3)
            ->get();

        $reputationScore = min(100, ($completedExchange * 10) + ($averageRating * 10) + ($totalReviews * 3));

        return $this->success('Reputasi user berhasil dihitung', [
            'user' => $user,
            'completed_exchange' => $completedExchange,
            'total_reviews' => $totalReviews,
            'average_rating' => $averageRating,
            'top_skill_categories' => $topSkillCategories,
            'reputation_score' => (int) round($reputationScore),
        ]);
    }
}
