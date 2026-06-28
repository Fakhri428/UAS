@component('layouts.public', ['title' => 'Match - Koukan'])
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <section class="grid gap-6 lg:grid-cols-[360px_minmax(0,1fr)]">
            <aside class="space-y-6">
                <section class="nb-card p-5">
                    <div class="flex items-center gap-3">
                        @if ($viewer)
                            <img class="h-14 w-14 rounded-full border-2 border-ink shadow-nb-sm object-cover" src="{{ $viewer->profile_photo_url }}" alt="{{ $viewer->name }}">
                        @else
                            <div class="flex h-14 w-14 items-center justify-center rounded-full border-2 border-ink bg-brand-yellow font-black text-ink shadow-nb-sm">HS</div>
                        @endif
                        <div class="min-w-0">
                            <h1 class="truncate text-xl font-semibold text-ink font-black">{{ $viewer?->name ?? 'Guest User' }}</h1>
                            <p class="truncate text-sm text-ink/60 font-semibold">{{ $viewer?->profile?->location ?? 'Pilih partner barter' }}</p>
                        </div>
                    </div>
                    <p class="mt-4 text-sm leading-6 text-ink/70 font-semibold">
                        {{ $viewer?->profile?->bio ?? 'Match dihitung dari kategori offer, need, kata kunci, mode kerja, dan riwayat reputasi.' }}
                    </p>
                </section>

                <section class="nb-card p-5">
                    <h2 class="text-base font-semibold text-ink font-black">Offer saya</h2>
                    <div class="mt-4 space-y-3">
                        @forelse ($offers as $offer)
                            <a href="{{ route('offers.show', $offer) }}" class="block rounded-lg border-2 border-ink p-4 bg-white hover:bg-brand-sky hover:shadow-nb-sm transition">
                                <p class="text-sm font-black text-ink uppercase">{{ $offer->title }}</p>
                                <p class="mt-1 text-xs text-ink/70 font-bold uppercase">{{ $offer->category }}</p>
                            </a>
                        @empty
                            <p class="text-sm text-ink/60 font-semibold">Belum ada offer.</p>
                        @endforelse
                    </div>
                </section>

                <section class="nb-card p-5">
                    <h2 class="text-base font-semibold text-ink font-black">Need saya</h2>
                    <div class="mt-4 space-y-3">
                        @forelse ($needs as $need)
                            <a href="{{ route('needs.show', $need) }}" class="block rounded-lg border-2 border-ink p-4 bg-white hover:bg-brand-pink hover:shadow-nb-sm transition">
                                <p class="text-sm font-black text-ink uppercase">{{ $need->title }}</p>
                                <p class="mt-1 text-xs text-ink/70 font-bold uppercase">{{ $need->category }}</p>
                            </a>
                        @empty
                            <p class="text-sm text-ink/60 font-semibold">Belum ada need.</p>
                        @endforelse
                    </div>
                </section>
            </aside>

            <section class="space-y-6">
                <div class="mb-5 rounded-xl border-2 border-ink bg-brand-yellow p-6 shadow-nb">
                    <p class="text-sm font-black uppercase text-ink">Rekomendasi Partner</p>
                    <h2 class="mt-2 text-3xl font-black text-ink">Match yang paling dekat dengan profilmu</h2>
                    <p class="mt-3 max-w-2xl text-sm font-bold text-ink/70">
                        Prioritas tertinggi diberikan ke partner yang bisa menerima offer kamu dan punya offer untuk need kamu.
                    </p>
                </div>

                <div class="grid gap-4 xl:grid-cols-2">
                    @forelse ($recommendations as $item)
                        <article class="nb-card p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex min-w-0 items-center gap-3">
                                    <a href="{{ route('users.profile', $item['user']) }}">
                                        <img class="h-12 w-12 rounded-full object-cover hover:opacity-80 transition-opacity" src="{{ $item['user']->profile_photo_url }}" alt="{{ $item['user']->name }}">
                                    </a>
                                    <div class="min-w-0">
                                        <a href="{{ route('users.profile', $item['user']) }}" class="block truncate text-base font-semibold text-ink font-black hover:underline">{{ $item['user']->name }}</a>
                                        <p class="truncate text-sm text-ink/60 font-semibold">{{ $item['user']->profile?->location ?? 'Lokasi fleksibel' }} · {{ $item['user']->profile?->work_mode ?? 'online' }}</p>
                                    </div>
                                </div>
                                <div class="rounded-lg border-2 border-ink bg-brand-sky px-3 py-2 text-center shadow-nb-sm transform -rotate-2">
                                    <strong class="block text-xl font-black text-ink">{{ $item['score'] }}%</strong>
                                    <span class="text-xs font-bold uppercase text-ink">Match</span>
                                </div>
                            </div>

                            <p class="mt-4 text-sm leading-6 text-ink/70 font-semibold">{{ $item['summary'] }}</p>

                            <div class="mt-4 grid gap-3">
                                @if ($item['my_offer'] && $item['their_need'])
                                    <div class="nb-card bg-brand-lime p-4">
                                        <p class="text-xs font-semibold uppercase tracking-normal text-ink/60 font-semibold">Offer kamu cocok untuk</p>
                                        <p class="mt-1 text-sm font-semibold text-ink font-black">{{ $item['their_need']->title }}</p>
                                    </div>
                                @endif

                                @if ($item['their_offer'] && $item['my_need'])
                                    <div class="nb-card bg-brand-lime p-4">
                                        <p class="text-xs font-semibold uppercase tracking-normal text-ink/60 font-semibold">Offer mereka cocok untuk</p>
                                        <p class="mt-1 text-sm font-semibold text-ink font-black">{{ $item['my_need']->title }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-5 flex flex-wrap gap-2">
                                @foreach ($item['user']->skills->take(3) as $skill)
                                    <span class="rounded-md border-2 border-ink bg-white shadow-nb-sm px-2 py-1 text-xs font-black uppercase text-ink">{{ $skill->name }}</span>
                                @endforeach
                            </div>

                            <div class="mt-5 flex flex-wrap gap-2">
                                <a href="{{ route('users.profile', $item['user']) }}" class="nb-btn nb-btn-white">
                                    <i class="fas fa-id-card mr-2"></i>Lihat Koukan ID
                                </a>
                                @if(auth()->check())
                                    @if($item['user']->offers->count() > 0)
                                        <a href="{{ route('offers.show', $item['user']->offers->first()) }}" class="nb-btn nb-btn-primary">
                                            <i class="fas fa-handshake mr-2"></i>Request Exchange
                                        </a>
                                    @elseif($item['user']->needs->count() > 0)
                                        <a href="{{ route('needs.show', $item['user']->needs->first()) }}" class="nb-btn nb-btn-primary">
                                            <i class="fas fa-handshake mr-2"></i>Request Exchange
                                        </a>
                                    @else
                                        <a href="{{ route('users.profile', $item['user']) }}" class="nb-btn nb-btn-primary">
                                            <i class="fas fa-handshake mr-2"></i>Mulai Exchange
                                        </a>
                                    @endif
                                    <a href="{{ route('chat.with', $item['user']) }}" class="nb-btn nb-btn-secondary">
                                        <i class="fas fa-comments mr-2"></i>Chat
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="nb-btn nb-btn-primary">
                                        Login untuk Mulai Exchange
                                    </a>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="rounded-lg border border-dashed border-ink bg-white p-6 text-sm text-ink/60 font-semibold">
                            Belum ada match yang bisa ditampilkan.
                        </div>
                    @endforelse
                </div>
            </section>
        </section>
    </main>
@endcomponent
