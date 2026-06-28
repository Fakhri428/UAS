@component('layouts.public', ['title' => $offer->title.' - Koukan'])
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
                            <div class="flex flex-wrap gap-2">
                                <span class="rounded-md bg-teal-50 px-2 py-1 text-xs font-semibold text-teal-700">{{ $offer->category }}</span>
                                <span class="rounded-md bg-brand-sky/10 px-2 py-1 text-xs font-semibold text-ink/70 font-semibold">{{ $offer->type }}</span>
                            </div>
                            <h1 class="mt-4 text-3xl font-semibold text-ink font-black">{{ $offer->title }}</h1>
                            <p class="mt-3 max-w-3xl text-base leading-7 text-ink/70 font-semibold">{{ $offer->description }}</p>
                        </div>
                        @guest
                            <a href="{{ route('login') }}" class="inline-flex shrink-0 items-center justify-center rounded-md bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">
                                Login untuk exchange
                            </a>
                        @else
                            @if (auth()->id() === $offer->user_id)
                                <a href="{{ route('market') }}" class="inline-flex shrink-0 items-center justify-center nb-btn nb-btn-white">
                                    Cari offer lain
                                </a>
                            @else
                                <a href="#exchange-form" class="inline-flex shrink-0 items-center justify-center rounded-md bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">
                                    Isi form request
                                </a>
                            @endif
                        @endguest
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-3">
                        <div class="nb-card bg-brand-lime p-4">
                            <p class="text-xs font-semibold uppercase tracking-normal text-ink/60 font-semibold">Durasi</p>
                            <p class="mt-2 text-sm font-semibold text-ink font-black">{{ $offer->available_duration ?? 'Fleksibel' }}</p>
                        </div>
                        <div class="nb-card bg-brand-lime p-4">
                            <p class="text-xs font-semibold uppercase tracking-normal text-ink/60 font-semibold">Mode</p>
                            <p class="mt-2 text-sm font-semibold text-ink font-black">{{ $offer->user->profile?->work_mode ?? 'online' }}</p>
                        </div>
                        <div class="nb-card bg-brand-lime p-4">
                            <p class="text-xs font-semibold uppercase tracking-normal text-ink/60 font-semibold">Lokasi</p>
                            <p class="mt-2 text-sm font-semibold text-ink font-black">{{ $offer->user->profile?->location ?? 'Fleksibel' }}</p>
                        </div>
                    </div>
                </article>

                <section class="nb-card p-6">
                    <h2 class="text-lg font-semibold text-ink font-black">Yang ingin ditukar</h2>
                    <p class="mt-3 text-sm leading-6 text-ink/70 font-semibold">{{ $offer->exchange_expectation }}</p>
                </section>

                <section id="exchange-form" class="scroll-mt-24 nb-card p-6">
                    <h2 class="text-lg font-semibold text-ink font-black">Ajukan exchange</h2>
                    @guest
                        <p class="mt-3 text-sm leading-6 text-ink/70 font-semibold">Login untuk mengirim request ke pemilik offer ini.</p>
                        <a href="{{ route('login') }}" class="mt-4 inline-flex rounded-md bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">Login</a>
                    @else
                        @if (auth()->id() === $offer->user_id)
                            <p class="mt-3 text-sm leading-6 text-ink/70 font-semibold">Ini offer milik Anda sendiri. Untuk mencoba fitur exchange, buka offer milik user lain dari halaman market.</p>
                            <a href="{{ route('market') }}" class="mt-4 inline-flex rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Cari offer lain</a>
                        @elseif ($myNeeds->isEmpty())
                            <p class="mt-3 text-sm leading-6 text-ink/70 font-semibold">Buat need terlebih dahulu agar partner tahu bantuan apa yang Anda cari.</p>
                            <a href="{{ route('dashboard') }}" class="mt-4 inline-flex rounded-md bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">Buat need</a>
                        @else
                            <form method="POST" action="{{ route('offers.request', $offer) }}" class="mt-4 grid gap-4 md:grid-cols-[minmax(0,260px)_minmax(0,1fr)_auto] md:items-end">
                                @csrf
                                <div>
                                    <label for="need_id" class="text-sm font-semibold text-slate-700">Need saya</label>
                                    <select id="need_id" name="need_id" required class="mt-2 nb-input w-full">
                                        @foreach ($myNeeds as $myNeed)
                                            <option value="{{ $myNeed->id }}">{{ $myNeed->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="message" class="text-sm font-semibold text-slate-700">Pesan request</label>
                                    <textarea id="message" name="message" rows="3" required class="mt-2 nb-input w-full">Halo, saya tertarik exchange. Saya butuh bantuan ini dan bisa menawarkan kontribusi sesuai need saya.</textarea>
                                </div>
                                <button type="submit" class="rounded-md bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">Kirim request</button>
                            </form>
                        @endif
                    @endguest
                </section>

                <section class="nb-card">
                    <div class="border-b border-ink px-6 py-4">
                        <h2 class="text-lg font-semibold text-ink font-black">Need yang cocok</h2>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse ($matchingNeeds as $need)
                            <article class="p-6">
                                <div class="flex items-start gap-3">
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ $need->user->profile_photo_url }}" alt="{{ $need->user->name }}">
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('needs.show', $need) }}" class="font-semibold text-ink font-black hover:text-teal-700">{{ $need->title }}</a>
                                        <p class="mt-1 text-xs text-ink/60 font-semibold">{{ $need->user->name }} · {{ $need->user->profile?->location ?? 'Lokasi fleksibel' }}</p>
                                        <p class="mt-3 text-sm leading-6 text-ink/70 font-semibold">{{ $need->description }}</p>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="p-6 text-sm text-ink/60 font-semibold">Belum ada need dengan kategori yang sama.</div>
                        @endforelse
                    </div>
                </section>
            </div>

            <aside class="space-y-6">
                <section class="nb-card p-5">
                    <div class="flex items-center gap-3">
                        <img class="h-14 w-14 rounded-full object-cover" src="{{ $offer->user->profile_photo_url }}" alt="{{ $offer->user->name }}">
                        <div class="min-w-0">
                            <h2 class="truncate text-base font-semibold text-ink font-black">{{ $offer->user->name }}</h2>
                            <p class="truncate text-sm text-ink/60 font-semibold">{{ $offer->user->profile?->location ?? 'Lokasi fleksibel' }}</p>
                        </div>
                    </div>
                    <p class="mt-4 text-sm leading-6 text-ink/70 font-semibold">{{ $offer->user->profile?->bio ?? 'Member Koukan' }}</p>

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
                    <h2 class="text-base font-semibold text-ink font-black">Skill pemilik offer</h2>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @forelse ($offer->user->skills as $skill)
                            <span class="rounded-md border border-ink px-3 py-2 text-xs font-semibold text-slate-700">{{ $skill->name }} · {{ $skill->level }}</span>
                        @empty
                            <span class="text-sm text-ink/60 font-semibold">Belum ada skill.</span>
                        @endforelse
                    </div>
                </section>

                <section class="nb-card p-5">
                    <h2 class="text-base font-semibold text-ink font-black">Offer lain</h2>
                    <div class="mt-4 space-y-3">
                        @forelse ($otherOffers as $otherOffer)
                            <a href="{{ route('offers.show', $otherOffer) }}" class="block rounded-lg border border-ink p-4 hover:bg-paper">
                                <p class="text-sm font-semibold text-ink font-black">{{ $otherOffer->title }}</p>
                                <p class="mt-1 text-xs text-ink/60 font-semibold">{{ $otherOffer->category }}</p>
                            </a>
                        @empty
                            <p class="text-sm text-ink/60 font-semibold">Belum ada offer lain.</p>
                        @endforelse
                    </div>
                </section>
            </aside>
        </section>
    </main>
@endcomponent
