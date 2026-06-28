<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRequest;
use App\Models\Need;
use App\Models\Offer;
use App\Models\Plan;
use App\Models\Review;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PreviewController extends Controller
{
    public function home(): View
    {
        return view('home', $this->payload());
    }

    public function dashboard(Request $request): View
    {
        return view('dashboard', $this->payload($request->user()));
    }

    private function payload(?User $activeUser = null): array
    {
        $viewer = $activeUser?->load(['profile', 'plan', 'skills', 'needs', 'offers'])
            ?? User::with(['profile', 'plan', 'skills', 'needs', 'offers'])
                ->where('email', 'fakhri@example.com')
                ->first()
            ?? User::with(['profile', 'plan', 'skills', 'needs', 'offers'])->first();

        $people = User::with(['profile', 'plan', 'skills', 'needs', 'offers'])
            ->when($viewer, fn ($query) => $query->where('id', '<>', $viewer->id))
            ->orderBy('name')
            ->take(8)
            ->get();

        $offers = Offer::with(['user.profile'])
            ->latest('id')
            ->take(6)
            ->get();

        $needs = Need::with(['user.profile'])
            ->latest('id')
            ->take(6)
            ->get();

        return [
            'viewer' => $viewer,
            'people' => $people,
            'offers' => $offers,
            'needs' => $needs,
            'plans' => Plan::orderBy('price')->get(),
            'metrics' => [
                ['label' => 'User Aktif', 'value' => User::count(), 'tone' => 'teal'],
                ['label' => 'Skill Tersedia', 'value' => Skill::count(), 'tone' => 'amber'],
                ['label' => 'Need Terbuka', 'value' => Need::count(), 'tone' => 'rose'],
                ['label' => 'Offer Aktif', 'value' => Offer::count(), 'tone' => 'indigo'],
            ],
            'recommendations' => $this->recommendations($viewer, $people),
            'activity' => $this->activity(),
            'planUsage' => $this->planUsage($viewer),
            'reputation' => $this->reputation($viewer),
        ];
    }

    private function recommendations(?User $viewer, $people): array
    {
        if (! $viewer) {
            return [];
        }

        return $people->map(function (User $person) use ($viewer) {
            $viewerOffer = $viewer->offers->first();
            $viewerNeed = $viewer->needs->first();
            $personOffer = $person->offers->first();
            $personNeed = $person->needs->first();

            $twoWay = $viewerOffer && $personNeed && $personOffer && $viewerNeed
                && strtolower($viewerOffer->category) === strtolower($personNeed->category)
                && strtolower($personOffer->category) === strtolower($viewerNeed->category);

            $oneWay = $viewerOffer && $personNeed
                && strtolower($viewerOffer->category) === strtolower($personNeed->category);

            $score = $twoWay ? 90 : ($oneWay ? 72 : 54);

            return [
                'user' => $person,
                'score' => $score,
                'label' => $twoWay ? 'Match dua arah' : ($oneWay ? 'Offer cocok' : 'Potensi kolaborasi'),
                'reason' => $twoWay
                    ? 'Offer Anda cocok dengan need mereka, dan offer mereka cocok dengan need Anda.'
                    : 'Ada kemiripan kategori, lokasi, atau mode kerja yang bisa dibuka dengan request.',
                'suggested_offer' => $viewerOffer,
                'suggested_need' => $personNeed ?? $viewerNeed,
            ];
        })->sortByDesc('score')->values()->take(3)->all();
    }

    private function activity(): array
    {
        $requests = ExchangeRequest::with(['fromUser', 'toUser', 'offer', 'need'])
            ->latest('id')
            ->take(4)
            ->get();

        if ($requests->isNotEmpty()) {
            return $requests->map(fn (ExchangeRequest $request) => [
                'title' => $request->fromUser?->name.' ke '.$request->toUser?->name,
                'description' => $request->offer?->title ?? $request->need?->title ?? $request->message,
                'status' => $request->status,
            ])->all();
        }

        return [
            [
                'title' => 'Fakhri ke Raka',
                'description' => 'Minta bantuan desain UI dashboard, barter dengan REST API Laravel.',
                'status' => 'suggested',
            ],
            [
                'title' => 'Raka ke Fakhri',
                'description' => 'Butuh backend API untuk portofolio UI.',
                'status' => 'draft',
            ],
        ];
    }

    private function planUsage(?User $viewer): array
    {
        if (! $viewer || ! $viewer->plan) {
            return [];
        }

        return [
            ['label' => 'Skill', 'used' => $viewer->skills->count(), 'max' => $viewer->plan->max_skills],
            ['label' => 'Need', 'used' => $viewer->needs->count(), 'max' => $viewer->plan->max_needs],
            ['label' => 'Offer', 'used' => $viewer->offers->count(), 'max' => $viewer->plan->max_offers],
        ];
    }

    private function reputation(?User $viewer): array
    {
        if (! $viewer) {
            return ['score' => 0, 'average' => 0, 'reviews' => 0, 'completed' => 0];
        }

        $completed = ExchangeRequest::whereIn('status', ['completed', 'reviewed'])
            ->where(fn ($query) => $query
                ->where('from_user_id', $viewer->id)
                ->orWhere('to_user_id', $viewer->id))
            ->count();

        $reviews = Review::where('reviewed_user_id', $viewer->id)->count();
        $average = round((float) Review::where('reviewed_user_id', $viewer->id)->avg('rating'), 1);
        $score = min(100, ($completed * 10) + ($average * 10) + ($reviews * 3));

        return [
            'score' => (int) round($score ?: 64),
            'average' => $average ?: 4.6,
            'reviews' => $reviews ?: 8,
            'completed' => $completed ?: 6,
        ];
    }
}
