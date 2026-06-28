<?php

namespace App\Http\Controllers\Api;

use App\Models\Need;
use App\Models\Offer;
use App\Models\Profile;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MatchController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        if ($request->filled('offer_id')) {
            return $this->matchesForOffer($request, (int) $request->query('offer_id'));
        }

        if ($request->filled('need_id')) {
            return $this->matchesForNeed($request, (int) $request->query('need_id'));
        }

        return $this->userMatches($request);
    }

    public function offerMatches(Request $request, int $offerId): JsonResponse
    {
        return $this->matchesForOffer($request, $offerId);
    }

    public function needMatches(Request $request, int $needId): JsonResponse
    {
        return $this->matchesForNeed($request, $needId);
    }

    private function matchesForOffer(Request $request, int $offerId): JsonResponse
    {
        $offer = Offer::where('id', $offerId)->where('user_id', $request->user()->id)->first();

        if (! $offer) {
            return $this->fail('Offer tidak ditemukan atau bukan milik Anda', 404);
        }

        $needs = Need::query()
            ->select('needs.*', 'users.name as user_name', 'profiles.work_mode', 'profiles.location')
            ->join('users', 'users.id', '=', 'needs.user_id')
            ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->where('needs.user_id', '<>', $request->user()->id)
            ->latest('needs.id')
            ->limit(50)
            ->get();

        $matches = [];

        foreach ($needs as $need) {
            $needData = $need->toArray();
            $score = $this->scoreOfferNeed($offer->toArray(), $needData, $this->averageRating((int) $need->user_id));

            if ($score <= 0) {
                continue;
            }

            $matches[] = [
                'user_id' => (int) $need->user_id,
                'name' => $need->user_name,
                'need_id' => (int) $need->id,
                'need_title' => $need->title,
                'work_mode' => $need->work_mode,
                'location' => $need->location,
                'match_score' => $score,
                'reason' => $this->buildOfferNeedReason($offer->toArray(), $needData),
            ];
        }

        usort($matches, fn ($a, $b) => $b['match_score'] <=> $a['match_score']);

        return $this->success('Rekomendasi match untuk offer berhasil diambil', [
            'offer_id' => $offerId,
            'matches' => $matches,
        ]);
    }

    private function matchesForNeed(Request $request, int $needId): JsonResponse
    {
        $need = Need::where('id', $needId)->where('user_id', $request->user()->id)->first();

        if (! $need) {
            return $this->fail('Need tidak ditemukan atau bukan milik Anda', 404);
        }

        $offers = Offer::query()
            ->select('offers.*', 'users.name as user_name', 'profiles.work_mode', 'profiles.location')
            ->join('users', 'users.id', '=', 'offers.user_id')
            ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->where('offers.user_id', '<>', $request->user()->id)
            ->latest('offers.id')
            ->limit(50)
            ->get();

        $matches = [];

        foreach ($offers as $offer) {
            $offerData = $offer->toArray();
            $score = $this->scoreOfferNeed($offerData, $need->toArray(), $this->averageRating((int) $offer->user_id));

            if ($score <= 0) {
                continue;
            }

            $matches[] = [
                'user_id' => (int) $offer->user_id,
                'name' => $offer->user_name,
                'offer_id' => (int) $offer->id,
                'offer_title' => $offer->title,
                'type' => $offer->type,
                'work_mode' => $offer->work_mode,
                'location' => $offer->location,
                'match_score' => $score,
                'reason' => $this->buildOfferNeedReason($offerData, $need->toArray()),
            ];
        }

        usort($matches, fn ($a, $b) => $b['match_score'] <=> $a['match_score']);

        return $this->success('Rekomendasi match untuk need berhasil diambil', [
            'need_id' => $needId,
            'matches' => $matches,
        ]);
    }

    private function userMatches(Request $request): JsonResponse
    {
        $currentUserId = $request->user()->id;
        $myOffers = Offer::where('user_id', $currentUserId)->get()->toArray();
        $myNeeds = Need::where('user_id', $currentUserId)->get()->toArray();

        $users = User::query()
            ->select('users.id', 'users.name', 'profiles.work_mode', 'profiles.location')
            ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->where('users.id', '<>', $currentUserId)
            ->latest('users.id')
            ->limit(50)
            ->get();

        $matches = [];

        foreach ($users as $user) {
            $theirNeeds = Need::where('user_id', $user->id)->get()->toArray();
            $theirOffers = Offer::where('user_id', $user->id)->get()->toArray();

            $bestMyOffer = $this->bestOfferNeedScore($myOffers, $theirNeeds);
            $bestTheirOffer = $this->bestOfferNeedScore($theirOffers, $myNeeds);

            if ($bestMyOffer['score'] <= 0 && $bestTheirOffer['score'] <= 0) {
                continue;
            }

            $ratingScore = min(10, (int) round($this->averageRating((int) $user->id) * 2));
            $score = $bestMyOffer['score'] > 0 && $bestTheirOffer['score'] > 0 ? 60 : 35;
            $score += min(20, (int) round(($bestMyOffer['score'] + $bestTheirOffer['score']) / 8));
            $score += $this->compatibleWorkMode($currentUserId, (int) $user->id) ? 10 : 0;
            $score += $ratingScore;
            $score = min(100, $score);

            $matches[] = [
                'user_id' => (int) $user->id,
                'name' => $user->name,
                'work_mode' => $user->work_mode,
                'location' => $user->location,
                'match_score' => $score,
                'reason' => $this->buildUserMatchReason($bestMyOffer, $bestTheirOffer),
                'suggested_offer_id' => $bestMyOffer['offer_id'],
                'suggested_need_id' => $bestTheirOffer['need_id'],
            ];
        }

        usort($matches, fn ($a, $b) => $b['match_score'] <=> $a['match_score']);

        return $this->success('Daftar match berhasil diambil', [
            'user_id' => $currentUserId,
            'matches' => $matches,
        ]);
    }

    private function scoreOfferNeed(array $offer, array $need, float $averageRating = 0): int
    {
        $score = 0;

        if (strtolower((string) $offer['category']) === strtolower((string) $need['category'])) {
            $score += 40;
        }

        $offerText = implode(' ', [
            $offer['title'] ?? '',
            $offer['category'] ?? '',
            $offer['description'] ?? '',
            $offer['exchange_expectation'] ?? '',
        ]);

        $needText = implode(' ', [
            $need['title'] ?? '',
            $need['category'] ?? '',
            $need['description'] ?? '',
            $need['exchange_offer'] ?? '',
        ]);

        if ($this->hasKeywordOverlap($offerText, $needText)) {
            $score += 45;
        }

        $score += min(10, (int) round($averageRating * 2));

        return min(100, $score);
    }

    private function bestOfferNeedScore(array $offers, array $needs): array
    {
        $best = [
            'score' => 0,
            'offer_id' => null,
            'offer_title' => null,
            'need_id' => null,
            'need_title' => null,
        ];

        foreach ($offers as $offer) {
            foreach ($needs as $need) {
                $score = $this->scoreOfferNeed($offer, $need);

                if ($score > $best['score']) {
                    $best = [
                        'score' => $score,
                        'offer_id' => (int) $offer['id'],
                        'offer_title' => $offer['title'],
                        'need_id' => (int) $need['id'],
                        'need_title' => $need['title'],
                    ];
                }
            }
        }

        return $best;
    }

    private function hasKeywordOverlap(string $firstText, string $secondText): bool
    {
        return count(array_intersect($this->textTokens($firstText), $this->textTokens($secondText))) > 0;
    }

    private function textTokens(string $text): array
    {
        $normalized = strtolower((string) preg_replace('/[^a-z0-9]+/i', ' ', $text));
        $tokens = preg_split('/\s+/', trim($normalized));

        return array_values(array_unique(array_filter($tokens, fn ($token) => strlen($token) >= 3)));
    }

    private function compatibleWorkMode(int $firstUserId, int $secondUserId): bool
    {
        $profiles = Profile::whereIn('user_id', [$firstUserId, $secondUserId])->get()->keyBy('user_id');

        if ($profiles->count() < 2) {
            return false;
        }

        $first = $profiles[$firstUserId];
        $second = $profiles[$secondUserId];

        if ($first->work_mode === 'online' || $second->work_mode === 'online') {
            return true;
        }

        if ($first->work_mode === 'hybrid' || $second->work_mode === 'hybrid') {
            return true;
        }

        return strtolower((string) $first->location) === strtolower((string) $second->location);
    }

    private function buildOfferNeedReason(array $offer, array $need): string
    {
        if (strtolower((string) $offer['category']) === strtolower((string) $need['category'])) {
            return 'Kategori offer dan need sama, yaitu '.$offer['category'].'.';
        }

        return 'Terdapat kemiripan kata kunci antara offer dan need.';
    }

    private function buildUserMatchReason(array $bestMyOffer, array $bestTheirOffer): string
    {
        if ($bestMyOffer['score'] > 0 && $bestTheirOffer['score'] > 0) {
            return 'Ada potensi match dua arah: offer Anda cocok dengan need mereka, dan offer mereka cocok dengan need Anda.';
        }

        if ($bestMyOffer['score'] > 0) {
            return 'Offer Anda berpotensi membantu need user tersebut.';
        }

        return 'Offer user tersebut berpotensi membantu need Anda.';
    }

    private function averageRating(int $userId): float
    {
        return (float) Review::where('reviewed_user_id', $userId)->avg('rating');
    }
}
