@component('layouts.public', ['title' => $need->title.' - Koukan'])
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('market') }}" class="text-sm font-semibold text-teal-700 hover:text-teal-800">Kembali ke market</a>
        </div>

        @if (session('status'))
            <div class="mb-6 rounded-lg border border-teal-200 bg-teal-50 px-4 py-3 text-sm font-medium text-teal-900">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                <p class="font-semibold">Exchange belum bisa dikirim.</p>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <div class="space-y-6">
                <article class="nb-card p-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <span class="rounded-md bg-rose-50 px-2 py-1 text-xs font-semibold text-rose-700">{{ $need->category }}</span>
                            <h1 class="mt-4 text-3xl font-semibold text-ink font-black">{{ $need->title }}</h1>
                            <p class="mt-3 max-w-3xl text-base leading-7 text-ink/70 font-semibold">{{ $need->description }}</p>
                        </div>
                        @guest
                            <a href="{{ route('login') }}" class="inline-flex shrink-0 items-center justify-center rounded-md bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">
                                Login untuk exchange
                            </a>
                        @else
                            @if (auth()->id() === $need->user_id)
                                <a href="{{ route('market') }}" class="inline-flex shrink-0 items-center justify-center nb-btn nb-btn-white">
                                    Cari need lain
                                </a>
                            @else
                                <a href="#exchange-form" class="inline-flex shrink-0 items-center justify-center rounded-md bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">
                                    Isi form bantuan
                                </a>
                            @endif
                        @endguest
                    </div>

                    <div class="mt-6 nb-card bg-brand-lime p-4">
                        <p class="text-xs font-semibold uppercase tracking-normal text-ink/60 font-semibold">Barter dari pemilik need</p>
                        <p class="mt-2 text-sm leading-6 text-slate-700">{{ $need->exchange_offer }}</p>
                    </div>
                </article>

                <section id="exchange-form" class="scroll-mt-24 nb-card p-6">
                    <h2 class="text-lg font-semibold text-ink font-black">Tawarkan bantuan</h2>
                    @guest
                        <p class="mt-3 text-sm leading-6 text-ink/70 font-semibold">Login untuk mengirim request ke pemilik need ini.</p>
                        <a href="{{ route('login') }}" class="mt-4 inline-flex rounded-md bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">Login</a>
                    @else
                        @if (auth()->id() === $need->user_id)
                            <p class="mt-3 text-sm leading-6 text-ink/70 font-semibold">Ini need milik Anda sendiri. Untuk mencoba fitur exchange, buka need milik user lain dari halaman market.</p>
                            <a href="{{ route('market') }}" class="mt-4 inline-flex rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Cari need lain</a>
                        @elseif ($myOffers->isEmpty())
                            <p class="mt-3 text-sm leading-6 text-ink/70 font-semibold">Buat offer terlebih dahulu agar partner tahu kontribusi apa yang Anda berikan.</p>
                            <a href="{{ route('dashboard') }}" class="mt-4 inline-flex rounded-md bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">Buat offer</a>
                        @else
                            <form method="POST" action="{{ route('needs.request', $need) }}" class="mt-4 grid gap-4 md:grid-cols-[minmax(0,260px)_minmax(0,1fr)_auto] md:items-end">
                                @csrf
                                <div>
                                    <label for="offer_id" class="text-sm font-semibold text-slate-700">Offer saya</label>
                                    <select id="offer_id" name="offer_id" required class="mt-2 nb-input w-full">
                                        @foreach ($myOffers as $myOffer)
                                            <option value="{{ $myOffer->id }}">{{ $myOffer->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="message" class="text-sm font-semibold text-slate-700">Pesan request</label>
                                    <textarea id="message" name="message" rows="3" required class="mt-2 nb-input w-full">Halo, saya bisa membantu need ini. Saya ingin mengajukan exchange dengan offer yang saya punya.</textarea>
                                </div>
                                <button type="submit" class="rounded-md bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">Kirim request</button>
                            </form>
                        @endif
                    @endguest
                </section>

                <section class="nb-card">
                    <div class="border-b border-ink px-6 py-4">
                        <h2 class="text-lg font-semibold text-ink font-black">Offer yang cocok</h2>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse ($matchingOffers as $offer)
                            <article class="p-6">
                                <div class="flex items-start gap-3">
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ $offer->user->profile_photo_url }}" alt="{{ $offer->user->name }}">
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('offers.show', $offer) }}" class="font-semibold text-ink font-black hover:text-teal-700">{{ $offer->title }}</a>
                                        <p class="mt-1 text-xs text-ink/60 font-semibold">{{ $offer->user->name }} · {{ $offer->available_duration ?? 'Fleksibel' }}</p>
                                        <p class="mt-3 text-sm leading-6 text-ink/70 font-semibold">{{ $offer->description }}</p>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="p-6 text-sm text-ink/60 font-semibold">Belum ada offer dengan kategori yang sama.</div>
                        @endforelse
                    </div>
                </section>
            </div>

            <aside class="space-y-6">
                <section class="nb-card p-5">
                    <div class="flex items-center gap-3">
                        <img class="h-14 w-14 rounded-full object-cover" src="{{ $need->user->profile_photo_url }}" alt="{{ $need->user->name }}">
                        <div class="min-w-0">
                            <h2 class="truncate text-base font-semibold text-ink font-black">{{ $need->user->name }}</h2>
                            <p class="truncate text-sm text-ink/60 font-semibold">{{ $need->user->profile?->location ?? 'Lokasi fleksibel' }}</p>
                        </div>
                    </div>
                    <p class="mt-4 text-sm leading-6 text-ink/70 font-semibold">{{ $need->user->profile?->bio ?? 'Member Koukan' }}</p>

                    <div class="mt-5 grid grid-cols-3 gap-2 text-center text-xs">
                        <div class="rounded-lg border border-ink p-3">
                            <strong class="block text-sm text-ink font-black">{{ $reputation['completed'] }}</strong>
                            <span class="text-ink/60 font-semibold">Selesai</span>
                        </div>
                        <div class="rounded-lg border border-ink p-3">
                            <strong class="block text-sm text-ink font-black">{{ $reputation['average'] }}</strong>
                            <span class="text-ink/60 font-semibold">Rating</span>
                        </div>
                        <div class="rounded-lg border border-ink p-3">
                            <strong class="block text-sm text-ink font-black">{{ $reputation['score'] }}</strong>
                            <span class="text-ink/60 font-semibold">Trust</span>
                        </div>
                    </div>
                </section>

                <section class="nb-card p-5">
                    <h2 class="text-base font-semibold text-ink font-black">Skill pemilik need</h2>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @forelse ($need->user->skills as $skill)
                            <span class="rounded-md border border-ink px-3 py-2 text-xs font-semibold text-slate-700">{{ $skill->name }} · {{ $skill->level }}</span>
                        @empty
                            <span class="text-sm text-ink/60 font-semibold">Belum ada skill.</span>
                        @endforelse
                    </div>
                </section>

                <section class="nb-card p-5">
                    <h2 class="text-base font-semibold text-ink font-black">Need lain</h2>
                    <div class="mt-4 space-y-3">
                        @forelse ($otherNeeds as $otherNeed)
                            <a href="{{ route('needs.show', $otherNeed) }}" class="block rounded-lg border border-ink p-4 hover:bg-paper">
                                <p class="text-sm font-semibold text-ink font-black">{{ $otherNeed->title }}</p>
                                <p class="mt-1 text-xs text-ink/60 font-semibold">{{ $otherNeed->category }}</p>
                            </a>
                        @empty
                            <p class="text-sm text-ink/60 font-semibold">Belum ada need lain.</p>
                        @endforelse
                    </div>
                </section>
            </aside>
        </section>
    </main>
@endcomponent
