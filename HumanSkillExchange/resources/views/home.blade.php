@php
    $metricTone = [
        'teal' => 'bg-brand-sky',
        'amber' => 'bg-brand-yellow',
        'indigo' => 'bg-brand-purple',
        'rose' => 'bg-brand-pink',
    ];
@endphp

@component('layouts.public', ['title' => 'Koukan - Exchange Skills, Build Connections.'])
    <main>
        <section class="border-b-2 border-ink bg-brand-lime">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[minmax(0,1fr)_380px] lg:px-8">
                <div class="flex flex-col justify-center">
                    <span class="nb-badge w-fit bg-brand-purple text-white">Koukan Market</span>
                    <h1 class="mt-4 max-w-3xl text-4xl font-black leading-[1.05] tracking-tight text-ink sm:text-6xl">
                        Tukarkan skill, bangun koneksi, kumpulkan Koukan Score.
                    </h1>
                    <p class="mt-5 max-w-2xl text-base font-medium leading-7 text-ink/80">
                        Buat <strong>Koukan Offer</strong> untuk apa yang bisa kamu tawarkan, dan temukan <strong>Koukan Need</strong> dari pengguna lain yang membutuhkan bantuanmu.
                    </p>

                    <form method="GET" action="{{ route('market') }}" class="mt-6 grid gap-3 nb-panel bg-white p-4 sm:grid-cols-[minmax(0,1fr)_220px_auto]">
                        <label class="sr-only" for="q">Cari skill</label>
                        <input
                            id="q"
                            name="q"
                            value="{{ $filters['q'] }}"
                            type="search"
                            placeholder="Cari Laravel, Figma, copywriting..."
                            class="nb-input"
                        >

                        <label class="sr-only" for="category">Kategori</label>
                        <select id="category" name="category" class="nb-input">
                            <option value="">Semua kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected($filters['category'] === $category)>{{ $category }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="nb-btn nb-btn-primary">Cari</button>
                    </form>
                </div>

                <aside class="nb-panel bg-ink p-5 text-white">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-bold text-brand-yellow">Match tercepat</p>
                            <h2 class="mt-2 text-2xl font-black">Barter dua arah</h2>
                        </div>
                        <span class="nb-badge bg-brand-yellow">{{ count($recommendations) }} match</span>
                    </div>

                    <div class="mt-5 space-y-3">
                        @forelse ($recommendations as $item)
                            <a href="{{ route('matches') }}" class="block rounded-lg border-2 border-white/80 bg-white/5 p-4 transition-colors hover:bg-white/15">
                                <div class="flex items-center gap-3">
                                    <img class="h-10 w-10 rounded-full border-2 border-white object-cover" src="{{ $item['user']->profile_photo_url }}" alt="{{ $item['user']->name }}">
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-bold">{{ $item['user']->name }}</p>
                                        <p class="truncate text-xs text-white/70">{{ $item['label'] }}</p>
                                    </div>
                                    <span class="text-sm font-black text-brand-yellow">{{ $item['score'] }}%</span>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-lg border-2 border-dashed border-white/40 bg-white/5 p-4 text-sm font-medium text-white/70">
                                Match akan muncul setelah member menambahkan offer dan need.
                            </div>
                        @endforelse
                    </div>
                </aside>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($stats as $stat)
                    <div class="nb-card p-4 {{ $metricTone[$stat['tone']] ?? 'bg-white' }}">
                        <div class="text-3xl font-black">{{ $stat['value'] }}</div>
                        <div class="mt-1 text-xs font-bold uppercase tracking-wide text-ink/80">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="mx-auto grid max-w-7xl gap-6 px-4 pb-12 sm:px-6 lg:grid-cols-[minmax(0,1fr)_380px] lg:px-8">
            <div class="space-y-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-black tracking-tight text-ink">Koukan Offer Populer</h2>
                        <p class="mt-1 text-sm font-medium text-ink/70">Pilih kontribusi dari member lain dan mulai exchange.</p>
                    </div>
                    <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="nb-btn nb-btn-yellow">Buat Koukan Offer</a>
                </div>

                <div class="grid gap-5 xl:grid-cols-2">
                    @forelse ($offers as $offer)
                        <article class="nb-card p-5">
                            <div class="flex items-start gap-3">
                                <a href="{{ route('users.profile', $offer->user) }}">
                                    <img class="h-11 w-11 rounded-full border-2 border-ink object-cover hover:opacity-80 transition-opacity" src="{{ $offer->user->profile_photo_url }}" alt="{{ $offer->user->name }}">
                                </a>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ route('offers.show', $offer) }}" class="text-base font-black text-ink hover:underline">
                                            {{ $offer->title }}
                                        </a>
                                        <span class="nb-badge bg-brand-sky">{{ $offer->category }}</span>
                                    </div>
                                    <p class="mt-1 text-xs font-semibold text-ink/60">
                                        <a href="{{ route('users.profile', $offer->user) }}" class="hover:underline">{{ $offer->user->name }}</a>
                                        · {{ $offer->user->profile?->location ?? 'Lokasi fleksibel' }} · {{ $offer->user->profile?->work_mode ?? 'online' }}
                                    </p>
                                </div>
                            </div>

                            <p class="mt-4 text-sm font-medium leading-6 text-ink/80">{{ $offer->description }}</p>
                            <div class="mt-4 rounded-lg border-2 border-ink bg-brand-yellow/40 p-3">
                                <p class="text-xs font-black uppercase tracking-wide text-ink">Ekspektasi barter</p>
                                <p class="mt-1 text-sm font-medium leading-6 text-ink/80">{{ $offer->exchange_expectation }}</p>
                            </div>

                            <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                                <span class="nb-badge bg-white">{{ $offer->available_duration ?? 'Fleksibel' }}</span>
                                <a href="{{ route('offers.show', $offer) }}" class="nb-btn nb-btn-white">Detail</a>
                            </div>
                        </article>
                    @empty
                        <div class="nb-card border-dashed bg-white p-6 text-sm font-medium text-ink/60 xl:col-span-2">
                            Belum ada offer yang cocok dengan filter saat ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <aside class="space-y-6">
                <section class="nb-card overflow-hidden">
                    <div class="border-b-2 border-ink bg-brand-pink px-5 py-4">
                        <h2 class="text-lg font-black text-ink">Koukan Need Terbuka</h2>
                        <p class="mt-1 text-sm font-medium text-ink/70">Permintaan bantuan dari member.</p>
                    </div>
                    <div class="divide-y-2 divide-ink/10">
                        @forelse ($needs->take(5) as $need)
                            <article class="p-5">
                                <div class="flex gap-3">
                                    <a href="{{ route('users.profile', $need->user) }}">
                                        <img class="h-10 w-10 rounded-full border-2 border-ink object-cover hover:opacity-80 transition-opacity" src="{{ $need->user->profile_photo_url }}" alt="{{ $need->user->name }}">
                                    </a>
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('needs.show', $need) }}" class="font-black text-ink hover:underline">{{ $need->title }}</a>
                                        <p class="mt-1 text-xs font-semibold text-ink/60">
                                            {{ $need->category }} ·
                                            <a href="{{ route('users.profile', $need->user) }}" class="hover:underline">{{ $need->user->name }}</a>
                                        </p>
                                        <p class="mt-2 text-sm font-medium leading-6 text-ink/80">{{ $need->description }}</p>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="p-5 text-sm font-medium text-ink/60">Belum ada need yang cocok.</div>
                        @endforelse
                    </div>
                </section>

                <section class="nb-card p-5">
                    <h2 class="text-lg font-black text-ink">Member aktif</h2>
                    <div class="mt-4 space-y-3">
                        @foreach ($people as $person)
                            <a href="{{ route('users.profile', $person) }}" class="flex items-center gap-3 rounded-lg border-2 border-transparent p-2 hover:border-ink hover:bg-paper transition">
                                <img class="h-10 w-10 rounded-full border-2 border-ink object-cover" src="{{ $person->profile_photo_url }}" alt="{{ $person->name }}">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-bold text-ink">{{ $person->name }}</p>
                                    <p class="truncate text-xs font-medium text-ink/60">{{ $person->profile?->bio ?? 'Member Koukan' }}</p>
                                </div>
                                <span class="nb-badge bg-brand-lime">{{ $person->skills->count() }} skill</span>
                            </a>
                        @endforeach
                    </div>
                </section>

                <section class="nb-card p-5">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-lg font-black text-ink">Plan member</h2>
                        <a href="{{ route('plans.index') }}" class="text-xs font-bold text-brand-purple underline">Lihat semua</a>
                    </div>
                    <div class="mt-4 space-y-3">
                        @foreach ($plans as $plan)
                            <a href="{{ route('plans.index') }}" class="block rounded-lg border-2 border-ink bg-brand-purple/30 p-4 transition hover:bg-brand-purple/50">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-black text-ink">{{ $plan->name }}</p>
                                        <p class="mt-1 text-xs font-semibold text-ink/60">{{ $plan->max_exchange_requests ? $plan->max_exchange_requests.' request/bulan' : 'Request tanpa batas' }}</p>
                                    </div>
                                    <p class="text-sm font-black text-ink">Rp{{ number_format($plan->price, 0, ',', '.') }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <a href="{{ route('plans.index') }}" class="nb-btn nb-btn-primary mt-4 w-full">Join Plan Member</a>
                </section>
            </aside>
        </section>
    </main>
@endcomponent
