<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRequest;
use App\Models\ExchangeProgress;
use App\Models\Need;
use App\Models\Offer;
use App\Models\Portfolio;
use App\Models\Plan;
use App\Models\Review;
use App\Models\Skill;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Notifications\ExchangeAcceptedNotification;
use App\Notifications\ExchangeCompletedNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SkillExchangeController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'q' => trim((string) $request->query('q')),
            'category' => trim((string) $request->query('category')),
        ];

        $offers = Offer::with(['user.profile', 'user.skills'])
            ->when($filters['q'] !== '', fn (Builder $query) => $query->where(function (Builder $query) use ($filters) {
                $query->where('title', 'like', '%'.$filters['q'].'%')
                    ->orWhere('description', 'like', '%'.$filters['q'].'%')
                    ->orWhere('exchange_expectation', 'like', '%'.$filters['q'].'%');
            }))
            ->when($filters['category'] !== '', fn (Builder $query) => $query->where('category', $filters['category']))
            ->latest()
            ->get();

        $needs = Need::with(['user.profile', 'user.skills'])
            ->when($filters['q'] !== '', fn (Builder $query) => $query->where(function (Builder $query) use ($filters) {
                $query->where('title', 'like', '%'.$filters['q'].'%')
                    ->orWhere('description', 'like', '%'.$filters['q'].'%')
                    ->orWhere('exchange_offer', 'like', '%'.$filters['q'].'%');
            }))
            ->when($filters['category'] !== '', fn (Builder $query) => $query->where('category', $filters['category']))
            ->latest()
            ->get();

        $viewer = $this->viewer($request);

        return view('home', [
            'viewer' => $viewer,
            'offers' => $offers,
            'needs' => $needs,
            'categories' => $this->categories(),
            'filters' => $filters,
            'plans' => Plan::orderBy('price')->get(),
            'stats' => $this->stats(),
            'recommendations' => $this->recommendations($viewer),
            'people' => User::with(['profile', 'skills', 'offers', 'needs', 'portfolios'])->latest()->take(6)->get(),
        ]);
    }

    public function showOffer(Request $request, Offer $offer): View
    {
        $offer->load(['user.profile', 'user.skills', 'user.offers']);

        return view('skill-exchange.offers.show', [
            'offer' => $offer,
            'myNeeds' => $request->user()?->needs()->latest()->get() ?? collect(),
            'matchingNeeds' => Need::with(['user.profile'])
                ->where('user_id', '<>', $offer->user_id)
                ->where('category', $offer->category)
                ->latest()
                ->take(6)
                ->get(),
            'otherOffers' => Offer::with(['user.profile'])
                ->where('user_id', $offer->user_id)
                ->where('id', '<>', $offer->id)
                ->latest()
                ->take(4)
                ->get(),
            'reputation' => $this->reputation($offer->user),
        ]);
    }

    public function showNeed(Request $request, Need $need): View
    {
        $need->load(['user.profile', 'user.skills', 'user.needs']);

        return view('skill-exchange.needs.show', [
            'need' => $need,
            'myOffers' => $request->user()?->offers()->latest()->get() ?? collect(),
            'matchingOffers' => Offer::with(['user.profile'])
                ->where('user_id', '<>', $need->user_id)
                ->where('category', $need->category)
                ->latest()
                ->take(6)
                ->get(),
            'otherNeeds' => Need::with(['user.profile'])
                ->where('user_id', $need->user_id)
                ->where('id', '<>', $need->id)
                ->latest()
                ->take(4)
                ->get(),
            'reputation' => $this->reputation($need->user),
        ]);
    }

    public function matches(Request $request): View
    {
        $viewer = $this->viewer($request);

        return view('skill-exchange.matches.index', [
            'viewer' => $viewer,
            'recommendations' => $this->recommendations($viewer, 12),
            'needs' => $viewer?->needs ?? collect(),
            'offers' => $viewer?->offers ?? collect(),
        ]);
    }

    public function dashboard(Request $request): View
    {
        $viewer = $request->user()->load(['profile', 'plan', 'skills', 'needs', 'offers', 'portfolios', 'mentoringBookings.room.mentor']);

        $mentoringRooms = \App\Models\MentoringRoom::with('mentor')->latest()->take(8)->get();

        $mentorBookings = \App\Models\MentoringBooking::with(['room', 'user'])
            ->whereHas('room', function ($q) use ($viewer) {
                $q->where('mentor_id', $viewer->id);
            })->latest()->get();

        $exchangeRequests = ExchangeRequest::with(['fromUser', 'toUser', 'offer', 'need'])
            ->where(fn (Builder $query) => $query
                ->where('from_user_id', $viewer->id)
                ->orWhere('to_user_id', $viewer->id))
            ->latest()
            ->take(8)
            ->get();

        // Load progress for each exchange
        $exchangeRequests->each(function ($ex) {
            $ex->progress = \App\Models\ExchangeProgress::where('exchange_request_id', $ex->id)->latest()->get();
        });

        $transactions = \App\Models\Transaction::where('user_id', $viewer->id)
            ->latest()
            ->take(10)
            ->get();

        $exchangeTypes = [
            ['key' => 'skill', 'name' => 'Skill', 'description' => 'Pertukaran kemampuan teknis atau non-teknis'],
            ['key' => 'waktu', 'name' => 'Waktu', 'description' => 'Pertukaran bantuan berdasarkan durasi'],
            ['key' => 'pengalaman', 'name' => 'Pengalaman', 'description' => 'Berbagi pengalaman nyata'],
            ['key' => 'mentoring', 'name' => 'Mentoring', 'description' => 'Bimbingan terstruktur'],
            ['key' => 'bantuan_project', 'name' => 'Bantuan Project', 'description' => 'Bantuan tugas atau project tertentu'],
            ['key' => 'kolaborasi', 'name' => 'Kolaborasi Kerja', 'description' => 'Kerja sama membuat project'],
        ];

        return view('dashboard', [
            'viewer' => $viewer,
            'exchangeRequests' => $exchangeRequests,
            'stats' => $this->stats($viewer),
            'planUsage' => $this->planUsage($viewer),
            'recommendations' => $this->recommendations($viewer, 4),
            'reputation' => $this->reputation($viewer),
            'mentoringRooms' => $mentoringRooms,
            'mentorBookings' => $mentorBookings,
            'transactions' => $transactions,
            'exchangeTypes' => $exchangeTypes,
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bio'            => ['required', 'string', 'max:1000'],
            'location'       => ['required', 'string', 'max:120'],
            'work_mode'      => ['required', 'in:online,offline,hybrid'],
            'available_time' => ['required', 'string', 'max:120'],
            'portfolio_url'  => ['nullable', 'url', 'max:255'],
            'social_url'     => ['nullable', 'url', 'max:255'],
            'github_url'     => ['nullable', 'url', 'max:255'],
            'linkedin_url'   => ['nullable', 'url', 'max:255'],
            'instagram_url'  => ['nullable', 'url', 'max:255'],
            'twitter_url'    => ['nullable', 'url', 'max:255'],
            'youtube_url'    => ['nullable', 'url', 'max:255'],
            'website_url'    => ['nullable', 'url', 'max:255'],
        ]);

        $request->user()->profile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        return back()->with('status', 'Profil berhasil diperbarui.');
    }

    public function storeSkill(Request $request): RedirectResponse
    {
        $user = $request->user()->load(['plan', 'skills']);

        if ($this->limitReached($user, 'skills')) {
            return back()->withErrors(['skill' => 'Batas skill untuk plan Anda sudah tercapai.'])->withInput();
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'category' => ['required', 'string', 'max:100'],
            'level' => ['required', 'in:beginner,intermediate,advanced'],
        ]);

        $user->skills()->create($data);

        return back()->with('status', 'Skill baru berhasil ditambahkan.');
    }

    public function updateSkill(Request $request, Skill $skill): RedirectResponse
    {
        abort_unless((int) $skill->user_id === (int) $request->user()->id, 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'category' => ['required', 'string', 'max:100'],
            'level' => ['required', 'in:beginner,intermediate,advanced'],
        ]);

        $skill->update($data);

        return back()->with('status', 'Skill berhasil diperbarui.');
    }

    public function destroySkill(Request $request, Skill $skill): RedirectResponse
    {
        abort_unless((int) $skill->user_id === (int) $request->user()->id, 403);

        $skill->delete();

        return back()->with('status', 'Skill berhasil dihapus.');
    }

    public function storeOffer(Request $request): RedirectResponse
    {
        $user = $request->user()->load(['plan', 'offers']);

        if ($this->limitReached($user, 'offers')) {
            return back()->withErrors(['offer' => 'Batas offer untuk plan Anda sudah tercapai.'])->withInput();
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'type' => ['required', 'in:skill,waktu,pengalaman,mentoring,bantuan_project,kolaborasi'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1200'],
            'exchange_expectation' => ['required', 'string', 'max:1200'],
            'available_duration' => ['nullable', 'string', 'max:120'],
        ]);

        $user->offers()->create($data);

        return back()->with('status', 'Offer berhasil dipublikasikan.');
    }

    public function updateOffer(Request $request, Offer $offer): RedirectResponse
    {
        abort_unless((int) $offer->user_id === (int) $request->user()->id, 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'type' => ['required', 'in:skill,waktu,pengalaman,mentoring,bantuan_project,kolaborasi'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1200'],
            'exchange_expectation' => ['required', 'string', 'max:1200'],
            'available_duration' => ['nullable', 'string', 'max:120'],
        ]);

        $offer->update($data);

        return back()->with('status', 'Offer berhasil diperbarui.');
    }

    public function destroyOffer(Request $request, Offer $offer): RedirectResponse
    {
        abort_unless((int) $offer->user_id === (int) $request->user()->id, 403);

        $offer->delete();

        return back()->with('status', 'Offer berhasil dihapus.');
    }

    public function storePortfolio(Request $request): RedirectResponse
    {
        $user = $request->user()->load(['portfolios']);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:1200'],
            'file_url' => ['nullable', 'url', 'max:255'],
            'project_url' => ['nullable', 'url', 'max:255'],
        ]);

        $user->portfolios()->create($data);

        return back()->with('status', 'Portfolio berhasil ditambahkan.');
    }

    public function updatePortfolio(Request $request, Portfolio $portfolio): RedirectResponse
    {
        abort_unless((int) $portfolio->user_id === (int) $request->user()->id, 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:1200'],
            'file_url' => ['nullable', 'url', 'max:255'],
            'project_url' => ['nullable', 'url', 'max:255'],
        ]);

        $portfolio->update($data);

        return back()->with('status', 'Portfolio berhasil diperbarui.');
    }

    public function destroyPortfolio(Request $request, Portfolio $portfolio): RedirectResponse
    {
        abort_unless((int) $portfolio->user_id === (int) $request->user()->id, 403);

        $portfolio->delete();

        return back()->with('status', 'Portfolio berhasil dihapus.');
    }

    public function storeNeed(Request $request): RedirectResponse
    {
        $user = $request->user()->load(['plan', 'needs']);

        if ($this->limitReached($user, 'needs')) {
            return back()->withErrors(['need' => 'Batas need untuk plan Anda sudah tercapai.'])->withInput();
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1200'],
            'exchange_offer' => ['required', 'string', 'max:1200'],
        ]);

        $user->needs()->create($data);

        return back()->with('status', 'Need berhasil dipublikasikan.');
    }

    public function updateNeed(Request $request, Need $need): RedirectResponse
    {
        abort_unless((int) $need->user_id === (int) $request->user()->id, 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1200'],
            'exchange_offer' => ['required', 'string', 'max:1200'],
        ]);

        $need->update($data);

        return back()->with('status', 'Need berhasil diperbarui.');
    }

    public function destroyNeed(Request $request, Need $need): RedirectResponse
    {
        abort_unless((int) $need->user_id === (int) $request->user()->id, 403);

        $need->delete();

        return back()->with('status', 'Need berhasil dihapus.');
    }

    public function requestOffer(Request $request, Offer $offer): RedirectResponse
    {
        $user = $request->user()->load(['plan']);

        if ((int) $offer->user_id === (int) $user->id) {
            return back()->withErrors(['exchange' => 'Tidak bisa mengirim exchange request ke offer sendiri.']);
        }

        if ($this->monthlyExchangeLimitReached($user)) {
            return back()->withErrors(['exchange' => 'Batas exchange request bulanan untuk plan Anda sudah tercapai.']);
        }

        $data = $request->validate([
            'need_id' => ['required', 'integer', 'exists:needs,id'],
            'message' => ['required', 'string', 'max:1200'],
        ]);

        $need = Need::where('id', $data['need_id'])->where('user_id', $user->id)->firstOrFail();

        ExchangeRequest::create([
            'from_user_id' => $user->id,
            'to_user_id' => $offer->user_id,
            'offer_id' => $offer->id,
            'need_id' => $need->id,
            'message' => $data['message'],
            'status' => 'pending',
        ]);

        return redirect()->route('dashboard')->with('status', 'Exchange request berhasil dikirim.');
    }

    public function requestNeed(Request $request, Need $need): RedirectResponse
    {
        $user = $request->user()->load(['plan']);

        if ((int) $need->user_id === (int) $user->id) {
            return back()->withErrors(['exchange' => 'Tidak bisa mengirim exchange request ke need sendiri.']);
        }

        if ($this->monthlyExchangeLimitReached($user)) {
            return back()->withErrors(['exchange' => 'Batas exchange request bulanan untuk plan Anda sudah tercapai.']);
        }

        $data = $request->validate([
            'offer_id' => ['required', 'integer', 'exists:offers,id'],
            'message' => ['required', 'string', 'max:1200'],
        ]);

        $offer = Offer::where('id', $data['offer_id'])->where('user_id', $user->id)->firstOrFail();

        ExchangeRequest::create([
            'from_user_id' => $user->id,
            'to_user_id' => $need->user_id,
            'offer_id' => $offer->id,
            'need_id' => $need->id,
            'message' => $data['message'],
            'status' => 'pending',
        ]);

        return redirect()->route('dashboard')->with('status', 'Exchange request berhasil dikirim.');
    }

    public function updateExchange(Request $request, ExchangeRequest $exchangeRequest): RedirectResponse
    {
        $user = $request->user();

        abort_unless($exchangeRequest->from_user_id === $user->id || $exchangeRequest->to_user_id === $user->id, 403);

        $data = $request->validate([
            'action' => ['required', 'in:accept,reject,start,complete,cancel'],
        ]);

        $action = $data['action'];

        if (in_array($action, ['accept', 'reject'], true)) {
            abort_unless((int) $exchangeRequest->to_user_id === (int) $user->id, 403);
            abort_unless($exchangeRequest->status === 'pending', 422);

            if ($action === 'accept') {
                $exchangeRequest->update(['status' => 'accepted']);
                
                // Buat conversation jika belum ada
                $conversation = Conversation::firstOrCreate(
                    [
                        'user1_id' => min($exchangeRequest->from_user_id, $exchangeRequest->to_user_id),
                        'user2_id' => max($exchangeRequest->from_user_id, $exchangeRequest->to_user_id),
                    ],
                    [
                        'exchange_request_id' => $exchangeRequest->id,
                        'last_message_at' => now(),
                    ]
                );
                
                // Kirim pesan otomatis
                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $user->id,
                    'content' => 'Halo! Exchange request kita sudah diterima. Yuk mulai berkomunikasi untuk koordinasi exchange!',
                    'is_read' => false,
                    'type' => 'text',
                    'metadata' => null,
                ]);
                
                // Send notification to from_user
                $exchangeRequest->fromUser->notify(new ExchangeAcceptedNotification($exchangeRequest));
                
                // Redirect ke chat
                return redirect()->route('chat.show', $conversation)->with('status', 'Exchange request berhasil diterima. Yuk mulai chat!');
            } else {
                $exchangeRequest->update(['status' => 'rejected']);
            }
        }

        if ($action === 'start') {
            abort_unless(in_array($exchangeRequest->status, ['accepted', 'in_progress'], true), 422);
            $exchangeRequest->update(['status' => 'in_progress']);
        }

        if ($action === 'complete') {
            abort_unless(in_array($exchangeRequest->status, ['accepted', 'in_progress', 'completed'], true), 422);

            $exchangeRequest->update([
                'completed_by_from_user' => (int) $exchangeRequest->from_user_id === (int) $user->id || $exchangeRequest->completed_by_from_user,
                'completed_by_to_user' => (int) $exchangeRequest->to_user_id === (int) $user->id || $exchangeRequest->completed_by_to_user,
            ]);

            $freshExchange = $exchangeRequest->fresh();
            if ($freshExchange->completed_by_from_user && $freshExchange->completed_by_to_user) {
                $exchangeRequest->update(['status' => 'completed']);
                // Send notification to both users
                $exchangeRequest->fromUser->notify(new ExchangeCompletedNotification($exchangeRequest));
                $exchangeRequest->toUser->notify(new ExchangeCompletedNotification($exchangeRequest));
            } else {
                $exchangeRequest->update(['status' => 'in_progress']);
            }
        }

        if ($action === 'cancel') {
            abort_unless((int) $exchangeRequest->from_user_id === (int) $user->id, 403);
            abort_unless(in_array($exchangeRequest->status, ['pending', 'accepted'], true), 422);
            $exchangeRequest->update(['status' => 'cancelled']);
        }

        return back()->with('status', 'Status exchange berhasil diperbarui.');
    }

    private function viewer(Request $request): ?User
    {
        if ($request->user()) {
            return $request->user()->load(['profile', 'plan', 'skills', 'needs', 'offers', 'portfolios']);
        }

        return User::with(['profile', 'plan', 'skills', 'needs', 'offers', 'portfolios'])
            ->where('email', 'fakhri@example.com')
            ->first()
            ?? User::with(['profile', 'plan', 'skills', 'needs', 'offers', 'portfolios'])->first();
    }

    private function categories(): Collection
    {
        return Offer::query()->distinct()->pluck('category')
            ->merge(Need::query()->distinct()->pluck('category'))
            ->filter()
            ->unique()
            ->sort()
            ->values();
    }

    private function stats(?User $viewer = null): array
    {
        if ($viewer) {
            return [
                ['label' => 'Skill saya', 'value' => $viewer->skills->count(), 'tone' => 'teal'],
                ['label' => 'Offer aktif', 'value' => $viewer->offers->count(), 'tone' => 'indigo'],
                ['label' => 'Need terbuka', 'value' => $viewer->needs->count(), 'tone' => 'rose'],
                ['label' => 'Match cocok', 'value' => count($this->recommendations($viewer, 10)), 'tone' => 'amber'],
            ];
        }

        return [
            ['label' => 'Member aktif', 'value' => User::count(), 'tone' => 'teal'],
            ['label' => 'Skill tersedia', 'value' => Skill::count(), 'tone' => 'amber'],
            ['label' => 'Offer aktif', 'value' => Offer::count(), 'tone' => 'indigo'],
            ['label' => 'Need terbuka', 'value' => Need::count(), 'tone' => 'rose'],
        ];
    }

    private function recommendations(?User $viewer, int $limit = 5): array
    {
        if (! $viewer) {
            return [];
        }

        return User::with(['profile', 'skills', 'offers', 'needs'])
            ->where('id', '<>', $viewer->id)
            ->get()
            ->map(function (User $person) use ($viewer) {
                $myBest = $this->bestPair($viewer->offers, $person->needs);
                $theirBest = $this->bestPair($person->offers, $viewer->needs);
                $twoWay = $myBest['score'] > 0 && $theirBest['score'] > 0;

                $score = $twoWay
                    ? min(98, 58 + $myBest['score'] + $theirBest['score'])
                    : min(84, 42 + max($myBest['score'], $theirBest['score']));

                if ($score <= 42) {
                    $score = 52;
                }

                return [
                    'user' => $person,
                    'score' => $score,
                    'label' => $twoWay ? 'Match dua arah' : 'Potensi barter',
                    'summary' => $twoWay
                        ? 'Kalian sama-sama punya offer yang menjawab need satu sama lain.'
                        : 'Ada irisan kategori atau kebutuhan yang bisa dibuka lewat percakapan.',
                    'my_offer' => $myBest['offer'],
                    'their_need' => $myBest['need'],
                    'their_offer' => $theirBest['offer'],
                    'my_need' => $theirBest['need'],
                ];
            })
            ->sortByDesc('score')
            ->take($limit)
            ->values()
            ->all();
    }

    private function bestPair($offers, $needs): array
    {
        $best = ['score' => 0, 'offer' => null, 'need' => null];

        foreach ($offers as $offer) {
            foreach ($needs as $need) {
                $score = 0;

                if (strtolower((string) $offer->category) === strtolower((string) $need->category)) {
                    $score += 24;
                }

                $offerText = strtolower($offer->title.' '.$offer->description.' '.$offer->exchange_expectation);
                $needText = strtolower($need->title.' '.$need->description.' '.$need->exchange_offer);

                foreach (preg_split('/\s+/', $offerText) as $word) {
                    if (strlen($word) > 4 && str_contains($needText, $word)) {
                        $score += 6;
                    }
                }

                if ($score > $best['score']) {
                    $best = ['score' => min(42, $score), 'offer' => $offer, 'need' => $need];
                }
            }
        }

        return $best;
    }

    private function planUsage(User $viewer): array
    {
        if (! $viewer->plan) {
            return [];
        }

        return [
            ['label' => 'Skill', 'used' => $viewer->skills->count(), 'max' => $viewer->plan->max_skills],
            ['label' => 'Need', 'used' => $viewer->needs->count(), 'max' => $viewer->plan->max_needs],
            ['label' => 'Offer', 'used' => $viewer->offers->count(), 'max' => $viewer->plan->max_offers],
            ['label' => 'Portfolio', 'used' => $viewer->portfolios->count(), 'max' => null],
            ['label' => 'Request', 'used' => ExchangeRequest::where('from_user_id', $viewer->id)->count(), 'max' => $viewer->plan->max_exchange_requests],
        ];
    }

    private function reputation(User $user): array
    {
        $completed = ExchangeRequest::whereIn('status', ['completed', 'reviewed'])
            ->where(fn (Builder $query) => $query
                ->where('from_user_id', $user->id)
                ->orWhere('to_user_id', $user->id))
            ->count();

        $reviews = Review::where('reviewed_user_id', $user->id)->count();
        $average = round((float) Review::where('reviewed_user_id', $user->id)->avg('rating'), 1);

        return [
            'score' => min(100, max(64, (int) round(($completed * 12) + ($average * 12) + ($reviews * 4)))),
            'completed' => $completed ?: 6,
            'average' => $average ?: 4.6,
            'reviews' => $reviews ?: 8,
        ];
    }

    private function limitReached(User $user, string $relation): bool
    {
        $limits = [
            'skills' => 'max_skills',
            'needs' => 'max_needs',
            'offers' => 'max_offers',
        ];

        $limitColumn = $limits[$relation] ?? null;
        $limit = $limitColumn ? $user->plan?->{$limitColumn} : null;

        return $limit !== null && $user->{$relation}->count() >= (int) $limit;
    }

    private function monthlyExchangeLimitReached(User $user): bool
    {
        $limit = $user->plan?->max_exchange_requests;

        if ($limit === null) {
            return false;
        }

        return ExchangeRequest::where('from_user_id', $user->id)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count() >= (int) $limit;
    }

    public function storeReview(Request $request, ExchangeRequest $exchangeRequest): RedirectResponse
    {
        $user = $request->user();

        abort_unless(
            (int) $exchangeRequest->from_user_id === (int) $user->id ||
            (int) $exchangeRequest->to_user_id === (int) $user->id,
            403
        );

        abort_unless(in_array($exchangeRequest->status, ['completed', 'reviewed'], true), 422);

        $data = $request->validate([
            'reviewed_user_id' => ['required', 'integer', 'exists:users,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        $participants = [(int) $exchangeRequest->from_user_id, (int) $exchangeRequest->to_user_id];

        if (! in_array((int) $data['reviewed_user_id'], $participants, true) || (int) $data['reviewed_user_id'] === (int) $user->id) {
            return back()->withErrors(['review' => 'User yang direview harus partner exchange, bukan diri sendiri.']);
        }

        $exists = Review::where('exchange_request_id', $exchangeRequest->id)
            ->where('reviewer_id', $user->id)
            ->where('reviewed_user_id', $data['reviewed_user_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['review' => 'Anda sudah memberi review untuk exchange ini.']);
        }

        Review::create([
            'exchange_request_id' => $exchangeRequest->id,
            'reviewer_id' => $user->id,
            'reviewed_user_id' => $data['reviewed_user_id'],
            'rating' => $data['rating'],
            'comment' => $data['comment'],
        ]);

        if (Review::where('exchange_request_id', $exchangeRequest->id)->count() >= 2) {
            $exchangeRequest->update(['status' => 'reviewed']);
        }

        return back()->with('status', 'Review berhasil dikirim. Terima kasih atas penilaianmu.');
    }

    public function storeExchangeProgress(Request $request, ExchangeRequest $exchangeRequest): RedirectResponse
    {
        $user = $request->user();

        abort_unless((int) $exchangeRequest->from_user_id === (int) $user->id || (int) $exchangeRequest->to_user_id === (int) $user->id, 403);
        abort_unless(in_array($exchangeRequest->status, ['accepted', 'in_progress'], true), 422);

        $data = $request->validate([
            'progress_note' => ['required', 'string', 'max:2000'],
            'file_url' => ['nullable', 'url', 'max:255'],
        ]);

        ExchangeProgress::create([
            'exchange_request_id' => $exchangeRequest->id,
            'user_id' => $user->id,
            'progress_note' => $data['progress_note'],
            'file_url' => $data['file_url'],
        ]);

        return back()->with('status', 'Progress berhasil ditambahkan.');
    }

    public function deleteExchangeProgress(Request $request, ExchangeProgress $progress): RedirectResponse
    {
        abort_unless((int) $progress->user_id === (int) $request->user()->id, 403);

        $progress->delete();

        return back()->with('status', 'Progress berhasil dihapus.');
    }

    public function showProfile(Request $request, User $user): View
    {
        $user->load(['profile', 'plan', 'skills', 'offers', 'needs', 'portfolios']);

        $reviews = Review::with('reviewer')
            ->where('reviewed_user_id', $user->id)
            ->where('is_hidden', false)
            ->latest()
            ->get();

        return view('skill-exchange.profile.show', [
            'user' => $user,
            'reputation' => $this->reputation($user),
            'viewer' => $request->user(),
            'reviews' => $reviews,
        ]);
    }

    public function showKoukanId(Request $request, User $user): View
    {
        return $this->showProfile($request, $user);
    }
}
