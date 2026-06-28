@php
    $statusTone = [
        'pending' => 'border-amber-200 bg-amber-50 text-amber-800',
        'accepted' => 'border-indigo-200 bg-indigo-50 text-indigo-800',
        'in_progress' => 'border-blue-200 bg-blue-50 text-blue-800',
        'completed' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'reviewed' => 'border-ink bg-brand-sky/10 text-slate-700',
        'rejected' => 'border-rose-200 bg-rose-50 text-rose-800',
        'cancelled' => 'border-ink bg-paper text-ink/70 font-semibold',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-black leading-tight text-ink">
            Koukan ID - {{ $user->name }}
        </h2>
    </x-slot>

    <div class="bg-paper py-8 min-h-screen">
        <main class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <section class="nb-card p-6">
                <div class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between">
                    <div class="flex items-center gap-4">
                        <img class="h-20 w-20 rounded-full border-2 border-ink object-cover shadow-nb-sm" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                        <div>
                            <p class="mb-1 w-fit nb-badge bg-brand-yellow font-black text-ink">{{ $user->plan?->name ?? 'Gratis' }}</p>
                            <h1 class="text-2xl font-black text-ink">{{ $user->name }}</h1>
                            <p class="mt-1 text-sm font-medium text-ink">{{ $user->profile?->bio ?? 'Bio tidak tersedia' }}</p>
                            <p class="mt-1 text-xs font-bold text-ink/70">{{ $user->profile?->location ?? 'Lokasi tidak diisi' }} · {{ $user->profile?->work_mode ?? 'online' }}</p>
                        </div>
                    </div>

                    <div class="space-y-1 rounded-xl border-2 border-ink bg-brand-lime shadow-nb-sm p-4">
                        <div class="text-center">
                            <div class="text-3xl font-black text-ink">{{ $user->koukan_score ?? number_format($reputation['score']/10, 1) }}</div>
                            <div class="text-xs font-black uppercase text-ink">Koukan Score</div>
                        </div>
                        <hr class="my-2 border-ink">
                        <div class="grid grid-cols-3 gap-2 text-center text-xs">
                            <div>
                                <strong class="block text-sm font-black text-ink">{{ $reputation['completed'] }}</strong>
                                <span class="font-bold text-ink/70">Selesai</span>
                            </div>
                            <div>
                                <strong class="block text-sm font-black text-ink">{{ $reputation['average'] }}</strong>
                                <span class="font-bold text-ink/70">Rating</span>
                            </div>
                            <div>
                                <strong class="block text-sm font-black text-ink">{{ $reputation['reviews'] }}</strong>
                                <span class="font-bold text-ink/70">Review</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($user->profile?->social_url || $user->profile?->portfolio_url || $user->profile?->github_url || $user->profile?->linkedin_url || $user->profile?->instagram_url || $user->profile?->twitter_url || $user->profile?->youtube_url || $user->profile?->website_url)
                    <div class="mt-6 flex flex-wrap gap-2 border-t-2 border-ink pt-6">
                        @php
                            $links = [
                                ['url' => $user->profile?->github_url,    'label' => 'GitHub',    'icon' => '🐙', 'bg' => 'bg-gray-900 text-white'],
                                ['url' => $user->profile?->linkedin_url,  'label' => 'LinkedIn',  'icon' => '💼', 'bg' => 'bg-blue-700 text-white'],
                                ['url' => $user->profile?->instagram_url, 'label' => 'Instagram', 'icon' => '📸', 'bg' => 'bg-pink-500 text-white'],
                                ['url' => $user->profile?->twitter_url,   'label' => 'X / Twitter','icon' => '🐦', 'bg' => 'bg-black text-white'],
                                ['url' => $user->profile?->youtube_url,   'label' => 'YouTube',   'icon' => '▶️', 'bg' => 'bg-red-600 text-white'],
                                ['url' => $user->profile?->website_url,   'label' => 'Website',   'icon' => '🌐', 'bg' => 'bg-teal-600 text-white'],
                                ['url' => $user->profile?->portfolio_url, 'label' => 'Portfolio', 'icon' => '📁', 'bg' => 'nb-btn-primary'],
                                ['url' => $user->profile?->social_url,    'label' => 'Social',    'icon' => '🔗', 'bg' => 'nb-btn-white'],
                            ];
                        @endphp
                        @foreach ($links as $link)
                            @if ($link['url'])
                                <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center gap-2 rounded-lg border-2 border-ink px-3 py-2 text-sm font-black shadow-nb-sm transition-transform hover:-translate-y-0.5 {{ $link['bg'] }}">
                                    <span>{{ $link['icon'] }}</span>
                                    {{ $link['label'] }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
            </section>

            <!-- Skill Section -->
            <section class="mt-6 nb-card">
                <div class="border-b-2 border-ink px-6 py-4 bg-brand-sky rounded-t-lg">
                    <h2 class="text-base font-black text-ink">Skill</h2>
                </div>
                <div class="divide-y-2 divide-ink">
                    @forelse ($user->skills as $skill)
                        <div class="flex items-center justify-between px-6 py-4">
                            <div>
                                <p class="font-black text-ink">{{ $skill->name }}</p>
                                <p class="text-sm font-bold text-ink/70">{{ $skill->category }}</p>
                            </div>
                            <span class="nb-badge bg-white capitalize text-ink">{{ $skill->level }}</span>
                        </div>
                    @empty
                        <div class="px-6 py-4 text-sm font-bold text-ink/70">Belum ada skill yang ditambahkan.</div>
                    @endforelse
                </div>
            </section>

            <!-- Portfolio Gallery Section -->
            <section class="mt-6 nb-card">
                <div class="border-b border-ink px-6 py-4">
                    <h2 class="text-base font-semibold text-ink font-black">Portfolio</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($user->portfolios as $portfolio)
                        <article class="px-6 py-5">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex-1">
                                    <p class="font-semibold text-ink font-black">{{ $portfolio->title }}</p>
                                    <p class="mt-2 text-sm leading-6 text-ink/70 font-semibold">{{ $portfolio->description }}</p>
                                    <div class="mt-3 flex flex-wrap gap-3 text-xs">
                                        @if ($portfolio->file_url)
                                            <a href="{{ $portfolio->file_url }}" target="_blank" class="font-semibold text-teal-700 hover:underline">Lihat file</a>
                                        @endif
                                        @if ($portfolio->project_url)
                                            <a href="{{ $portfolio->project_url }}" target="_blank" class="font-semibold text-teal-700 hover:underline">Lihat project</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="px-6 py-5 text-sm text-ink/60 font-semibold">Belum ada portfolio yang ditambahkan.</div>
                    @endforelse
                </div>
            </section>

            <!-- Offer Section -->
            <section class="mt-6 nb-card">
                <div class="border-b-2 border-ink px-6 py-4 bg-brand-pink rounded-t-lg">
                    <h2 class="text-base font-black text-ink">Koukan Offer</h2>
                </div>
                <div class="divide-y-2 divide-ink">
                    @forelse ($user->offers as $offer)
                        <a href="{{ route('offers.show', $offer) }}" class="block p-6 hover:bg-brand-yellow/20 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <p class="font-black text-ink">{{ $offer->title }}</p>
                                    <p class="mt-2 text-sm leading-6 font-medium text-ink">{{ $offer->description }}</p>
                                    <p class="mt-2 text-xs font-bold text-ink/70">{{ $offer->type ?? 'skill' }} · {{ $offer->available_duration ?? '—' }}</p>
                                </div>
                                <span class="shrink-0 nb-badge bg-white text-ink">{{ $offer->category }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="px-6 py-5 text-sm font-bold text-ink/70">Belum ada Koukan Offer yang dibuat.</div>
                    @endforelse
                </div>
            </section>

            <!-- Need Section -->
            <section class="mt-6 nb-card">
                <div class="border-b-2 border-ink px-6 py-4 bg-brand-yellow rounded-t-lg">
                    <h2 class="text-base font-black text-ink">Koukan Need</h2>
                </div>
                <div class="divide-y-2 divide-ink">
                    @forelse ($user->needs as $need)
                        <a href="{{ route('needs.show', $need) }}" class="block p-6 hover:bg-brand-sky/20 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <p class="font-black text-ink">{{ $need->title }}</p>
                                    <p class="mt-2 text-sm leading-6 font-medium text-ink">{{ $need->description }}</p>
                                </div>
                                <span class="shrink-0 nb-badge bg-white text-ink">{{ $need->category }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="px-6 py-5 text-sm font-bold text-ink/70">Belum ada Koukan Need yang dibuat.</div>
                    @endforelse
                </div>
            </section>

            <!-- Review Section -->
            <section class="mt-6 nb-card">
                <div class="border-b-2 border-ink px-6 py-4 bg-brand-orange rounded-t-lg">
                    <h2 class="text-base font-black text-ink">Ulasan dari Mitra Exchange</h2>
                    <p class="mt-1 text-xs font-semibold text-ink/70">{{ $reviews->count() }} ulasan · rata-rata {{ $reputation['average'] }} ★</p>
                </div>
                <div class="divide-y-2 divide-ink/10">
                    @forelse ($reviews as $review)
                        <article class="px-6 py-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <img class="h-9 w-9 rounded-full border border-ink object-cover" src="{{ $review->reviewer?->profile_photo_url }}" alt="{{ $review->reviewer?->name }}">
                                    <div>
                                        <p class="text-sm font-black text-ink">{{ $review->reviewer?->name ?? 'Pengguna' }}</p>
                                        <p class="text-xs font-semibold text-ink/60">{{ $review->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <span class="shrink-0 rounded-md bg-brand-yellow px-2 py-1 text-xs font-black text-ink">
                                    {{ str_repeat('★', (int) $review->rating) }}{{ str_repeat('☆', max(0, 5 - (int) $review->rating)) }}
                                </span>
                            </div>
                            <p class="mt-3 rounded-lg border border-ink bg-paper px-4 py-3 text-sm font-medium leading-6 text-ink/80">{{ $review->comment }}</p>
                        </article>
                    @empty
                        <div class="px-6 py-5 text-sm font-bold text-ink/70">Belum ada ulasan untuk pengguna ini.</div>
                    @endforelse
                </div>
            </section>

            @if ($viewer && (int) $viewer->id !== (int) $user->id)
                <div class="mt-6 flex gap-3">
                    <a href="{{ route('market') }}" class="nb-btn nb-btn-white">
                        Kembali ke Koukan Market
                    </a>
                    <a href="{{ route('matches') }}" class="nb-btn nb-btn-primary">
                        Lihat Matches
                    </a>
                </div>
            @endif
        </main>
    </div>
</x-app-layout>
