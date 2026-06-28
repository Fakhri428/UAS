@php
    $embedded = $embedded ?? false;
    $initials = function (?string $name) {
        $name = trim((string) $name);
        if ($name === '') {
            return 'HS';
        }

        $parts = preg_split('/\s+/', $name);
        return strtoupper(substr($parts[0] ?? 'H', 0, 1).substr($parts[1] ?? $parts[0] ?? 'S', 0, 1));
    };
    $metricTone = [
        'teal' => 'border-teal-200 bg-teal-50 text-teal-900',
        'amber' => 'border-amber-200 bg-amber-50 text-amber-900',
        'rose' => 'border-rose-200 bg-rose-50 text-rose-900',
        'indigo' => 'border-indigo-200 bg-indigo-50 text-indigo-900',
    ];
    $statusTone = [
        'suggested' => 'bg-teal-50 text-teal-800 border-teal-200',
        'draft' => 'bg-amber-50 text-amber-800 border-amber-200',
        'pending' => 'bg-amber-50 text-amber-800 border-amber-200',
        'accepted' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
        'in_progress' => 'bg-blue-50 text-blue-800 border-blue-200',
        'completed' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
        'reviewed' => 'bg-brand-sky/10 text-slate-700 border-ink',
    ];
@endphp

<div class="{{ $embedded ? 'bg-paper' : 'min-h-screen bg-paper' }}">
    @unless ($embedded)
        <header class="border-b border-ink bg-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg border-2 border-ink bg-ink text-sm font-black text-white shadow-nb-sm">K</span>
                    <span>
                        <span class="block text-sm font-semibold text-ink font-black">Koukan</span>
                        <span class="block text-xs text-ink/60 font-semibold">Skill barter workspace</span>
                    </span>
                </a>

                <nav class="flex flex-wrap items-center gap-2 text-sm">
                    <a href="#market" class="rounded-md px-3 py-2 text-ink/70 font-semibold hover:bg-brand-sky/10 hover:text-ink font-black">Market</a>
                    <a href="#match" class="rounded-md px-3 py-2 text-ink/70 font-semibold hover:bg-brand-sky/10 hover:text-ink font-black">Match</a>
                    <a href="{{ route('docs') }}" class="rounded-md px-3 py-2 text-ink/70 font-semibold hover:bg-brand-sky/10 hover:text-ink font-black">API Docs</a>
                    <a href="{{ route('login') }}" class="rounded-md border border-ink px-3 py-2 font-medium text-slate-800 hover:bg-brand-sky/10">Login</a>
                </nav>
            </div>
        </header>
    @endunless

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <div class="space-y-6">
                <div class="nb-card p-5">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="max-w-2xl">
                            <p class="text-sm font-medium text-teal-700">Preview aplikasi</p>
                            <h1 class="mt-2 text-3xl font-semibold text-ink font-black sm:text-4xl">Dashboard pertukaran skill</h1>
                            <p class="mt-3 text-sm leading-6 text-ink/70 font-semibold">
                                User bisa menampilkan profil, mencari offer, mencatat need, menerima rekomendasi match, lalu menjalankan exchange sampai review.
                            </p>
                        </div>

                        <div class="grid min-w-0 grid-cols-2 gap-2 sm:min-w-[260px]">
                            @foreach ($metrics as $metric)
                                <div class="rounded-lg border p-3 {{ $metricTone[$metric['tone']] ?? 'border-ink bg-paper text-ink font-black' }}">
                                    <div class="text-2xl font-semibold">{{ $metric['value'] }}</div>
                                    <div class="mt-1 text-xs font-medium">{{ $metric['label'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <section id="market" class="grid gap-6 xl:grid-cols-2">
                    <div class="nb-card">
                        <div class="flex items-center justify-between border-b border-ink px-5 py-4">
                            <div>
                                <h2 class="text-base font-semibold text-ink font-black">Offer terbaru</h2>
                                <p class="mt-1 text-xs text-ink/60 font-semibold">Kontribusi yang bisa ditukar.</p>
                            </div>
                            <a href="{{ url('/api/offers') }}" class="rounded-md border border-ink px-3 py-2 text-xs font-medium text-slate-700 hover:bg-paper">JSON</a>
                        </div>

                        <div class="divide-y divide-slate-100">
                            @forelse ($offers as $offer)
                                <article class="p-5">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-teal-100 text-sm font-semibold text-teal-900">
                                            {{ $initials($offer->user?->name) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h3 class="text-sm font-semibold text-ink font-black">{{ $offer->title }}</h3>
                                                <span class="rounded-md bg-brand-sky/10 px-2 py-1 text-xs font-medium text-ink/70 font-semibold">{{ $offer->type }}</span>
                                            </div>
                                            <p class="mt-2 text-sm leading-6 text-ink/70 font-semibold">{{ $offer->description }}</p>
                                            <div class="mt-3 flex flex-wrap gap-2 text-xs">
                                                <span class="rounded-md border border-ink px-2 py-1 text-ink/70 font-semibold">{{ $offer->category }}</span>
                                                <span class="rounded-md border border-ink px-2 py-1 text-ink/70 font-semibold">{{ $offer->available_duration ?? 'Fleksibel' }}</span>
                                                <span class="rounded-md border border-ink px-2 py-1 text-ink/70 font-semibold">{{ $offer->user?->profile?->work_mode ?? 'online' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <div class="p-5 text-sm text-ink/60 font-semibold">Belum ada offer.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="nb-card">
                        <div class="flex items-center justify-between border-b border-ink px-5 py-4">
                            <div>
                                <h2 class="text-base font-semibold text-ink font-black">Need terbuka</h2>
                                <p class="mt-1 text-xs text-ink/60 font-semibold">Kebutuhan bantuan dari user.</p>
                            </div>
                            <a href="{{ url('/api/needs') }}" class="rounded-md border border-ink px-3 py-2 text-xs font-medium text-slate-700 hover:bg-paper">JSON</a>
                        </div>

                        <div class="divide-y divide-slate-100">
                            @forelse ($needs as $need)
                                <article class="p-5">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-rose-100 text-sm font-semibold text-rose-900">
                                            {{ $initials($need->user?->name) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h3 class="text-sm font-semibold text-ink font-black">{{ $need->title }}</h3>
                                                <span class="rounded-md bg-rose-50 px-2 py-1 text-xs font-medium text-rose-700">{{ $need->category }}</span>
                                            </div>
                                            <p class="mt-2 text-sm leading-6 text-ink/70 font-semibold">{{ $need->description }}</p>
                                            <p class="mt-3 text-xs leading-5 text-ink/60 font-semibold">Barter: {{ $need->exchange_offer }}</p>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <div class="p-5 text-sm text-ink/60 font-semibold">Belum ada need.</div>
                            @endforelse
                        </div>
                    </div>
                </section>

                <section class="nb-card">
                    <div class="border-b border-ink px-5 py-4">
                        <h2 class="text-base font-semibold text-ink font-black">Alur exchange</h2>
                        <p class="mt-1 text-xs text-ink/60 font-semibold">Gambaran status yang akan dilalui user.</p>
                    </div>
                    <div class="grid gap-3 p-5 md:grid-cols-4">
                        @foreach ([
                            ['title' => 'Request', 'copy' => 'Pilih offer dan need, lalu kirim pesan ke partner.', 'tone' => 'border-teal-200 bg-teal-50'],
                            ['title' => 'Accept', 'copy' => 'Partner menerima atau menolak request exchange.', 'tone' => 'border-amber-200 bg-amber-50'],
                            ['title' => 'Progress', 'copy' => 'Kedua user mencatat perkembangan dan file pendukung.', 'tone' => 'border-indigo-200 bg-indigo-50'],
                            ['title' => 'Review', 'copy' => 'Exchange selesai setelah dua pihak konfirmasi dan memberi rating.', 'tone' => 'border-rose-200 bg-rose-50'],
                        ] as $step)
                            <article class="rounded-lg border p-4 {{ $step['tone'] }}">
                                <h3 class="text-sm font-semibold text-ink font-black">{{ $step['title'] }}</h3>
                                <p class="mt-2 text-xs leading-5 text-ink/70 font-semibold">{{ $step['copy'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>
            </div>

            <aside class="space-y-6">
                <section class="nb-card p-5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white">
                            {{ $initials($viewer?->name) }}
                        </div>
                        <div class="min-w-0">
                            <h2 class="truncate text-base font-semibold text-ink font-black">{{ $viewer?->name ?? 'Guest User' }}</h2>
                            <p class="truncate text-sm text-ink/60 font-semibold">{{ $viewer?->email ?? 'guest@example.com' }}</p>
                        </div>
                    </div>

                    <p class="mt-4 text-sm leading-6 text-ink/70 font-semibold">
                        {{ $viewer?->profile?->bio ?? 'Profil user akan menampilkan personal branding, lokasi, mode kerja, dan ketersediaan waktu.' }}
                    </p>

                    <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                        <div class="rounded-lg border border-ink p-3">
                            <span class="block text-ink/60 font-semibold">Plan</span>
                            <strong class="mt-1 block text-ink font-black">{{ $viewer?->plan?->name ?? 'Gratis' }}</strong>
                        </div>
                        <div class="rounded-lg border border-ink p-3">
                            <span class="block text-ink/60 font-semibold">Mode</span>
                            <strong class="mt-1 block text-ink font-black">{{ $viewer?->profile?->work_mode ?? 'online' }}</strong>
                        </div>
                    </div>

                    @if ($planUsage)
                        <div class="mt-5 space-y-3">
                            @foreach ($planUsage as $usage)
                                @php
                                    $max = $usage['max'];
                                    $percent = $max ? min(100, (int) round(($usage['used'] / $max) * 100)) : 100;
                                @endphp
                                <div>
                                    <div class="flex justify-between text-xs text-ink/60 font-semibold">
                                        <span>{{ $usage['label'] }}</span>
                                        <span>{{ $usage['used'] }} / {{ $max ?? '∞' }}</span>
                                    </div>
                                    <div class="mt-2 h-2 rounded-full bg-brand-sky/10">
                                        <div class="h-2 rounded-full bg-teal-600" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>

                <section id="match" class="nb-card">
                    <div class="border-b border-ink px-5 py-4">
                        <h2 class="text-base font-semibold text-ink font-black">Rekomendasi match</h2>
                        <p class="mt-1 text-xs text-ink/60 font-semibold">Berdasarkan offer, need, kategori, dan profil.</p>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse ($recommendations as $item)
                            <article class="p-5">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-900">
                                            {{ $initials($item['user']->name) }}
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="truncate text-sm font-semibold text-ink font-black">{{ $item['user']->name }}</h3>
                                            <p class="truncate text-xs text-ink/60 font-semibold">{{ $item['label'] }}</p>
                                        </div>
                                    </div>
                                    <span class="rounded-md bg-teal-50 px-2 py-1 text-xs font-semibold text-teal-700">{{ $item['score'] }}%</span>
                                </div>
                                <p class="mt-3 text-xs leading-5 text-ink/70 font-semibold">{{ $item['reason'] }}</p>
                                <a href="{{ url('/api/matches') }}" class="mt-4 inline-flex rounded-md bg-slate-900 px-3 py-2 text-xs font-medium text-white hover:bg-slate-700">Lihat API match</a>
                            </article>
                        @empty
                            <div class="p-5 text-sm text-ink/60 font-semibold">Belum ada rekomendasi.</div>
                        @endforelse
                    </div>
                </section>

                <section class="nb-card p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-base font-semibold text-ink font-black">Reputasi</h2>
                            <p class="mt-1 text-xs text-ink/60 font-semibold">Ringkasan trust user.</p>
                        </div>
                        <div class="rounded-lg bg-slate-900 px-3 py-2 text-xl font-semibold text-white">{{ $reputation['score'] }}</div>
                    </div>
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
                            <strong class="block text-sm text-ink font-black">{{ $reputation['reviews'] }}</strong>
                            <span class="text-ink/60 font-semibold">Review</span>
                        </div>
                    </div>
                </section>

                <section class="nb-card">
                    <div class="border-b border-ink px-5 py-4">
                        <h2 class="text-base font-semibold text-ink font-black">Aktivitas exchange</h2>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @foreach ($activity as $activityItem)
                            <article class="p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="text-sm font-semibold text-ink font-black">{{ $activityItem['title'] }}</h3>
                                        <p class="mt-1 text-xs leading-5 text-ink/70 font-semibold">{{ $activityItem['description'] }}</p>
                                    </div>
                                    <span class="shrink-0 rounded-md border px-2 py-1 text-xs font-medium {{ $statusTone[$activityItem['status']] ?? 'border-ink bg-paper text-slate-700' }}">
                                        {{ str_replace('_', ' ', $activityItem['status']) }}
                                    </span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            </aside>
        </section>
    </main>
</div>
