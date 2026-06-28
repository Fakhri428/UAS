@php
    $metricTone = [
        'teal' => 'bg-brand-sky',
        'amber' => 'bg-brand-yellow',
        'indigo' => 'bg-brand-purple',
        'rose' => 'bg-brand-pink',
    ];
    $statusTone = [
        'pending' => 'bg-brand-yellow',
        'accepted' => 'bg-brand-sky',
        'in_progress' => 'bg-brand-purple',
        'completed' => 'bg-brand-lime',
        'reviewed' => 'bg-white',
        'rejected' => 'bg-brand-pink',
        'cancelled' => 'bg-paper text-ink/70',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-xl font-semibold leading-tight text-ink font-black">
                Dashboard Member
            </h2>
            <p class="text-sm text-ink/60 font-semibold">Kelola skill, offer, need, dan exchange.</p>
        </div>
    </x-slot>

    <div class="bg-gradient-to-br from-brand-sky/10 via-paper to-brand-lime/10 py-8">
        <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">            @if (session('status'))
                <div class="mb-6 rounded-lg border border-brand-sky bg-brand-sky/20 px-4 py-3 text-sm font-medium text-ink">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-brand-pink bg-brand-pink/20 px-4 py-3 text-sm text-ink">
                    <p class="font-semibold">Ada data yang perlu diperbaiki.</p>
                    <ul class="mt-2 list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
                <div class="space-y-6">
                    <section class="nb-card overflow-hidden">
                        <div class="border-b-2 border-ink bg-brand-lime px-6 py-5">
                            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                                <div class="flex items-center gap-4">
                                    <img class="h-16 w-16 rounded-full border-2 border-ink object-cover shadow-nb-sm" src="{{ $viewer->profile_photo_url }}" alt="{{ $viewer->name }}">
                                    <div>
                                        <span class="nb-badge bg-brand-purple text-white">{{ $viewer->plan?->name ?? 'Gratis' }}</span>
                                        <h1 class="mt-1 text-2xl font-semibold text-ink font-black">{{ $viewer->name }}</h1>
                                        <p class="mt-1 text-sm text-ink/70 font-semibold">{{ $viewer->profile?->location ?? 'Lokasi belum diisi' }} · {{ $viewer->profile?->work_mode ?? 'online' }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('market') }}" class="nb-btn nb-btn-white">Cari Koukan Offer</a>
                                    <a href="{{ route('matches') }}" class="nb-btn nb-btn-primary">Lihat match</a>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-white">
                            <p class="text-sm font-semibold text-ink/70">{{ $viewer->profile?->bio ?? 'Bio belum diisi. Perbarui profil exchange di bawah.' }}</p>
                        </div>
                    </section>

                    @if (!empty($mentorBookings) && $mentorBookings->count())
                        <section class="nb-card overflow-hidden">
                            <div class="border-b-2 border-ink bg-brand-pink px-6 py-4">
                                <h2 class="text-base font-semibold text-ink font-black">Booking untuk saya (sebagai mentor)</h2>
                                <p class="mt-1 text-sm text-ink/70 font-semibold">Kelola booking yang masuk untuk mentoring Anda.</p>
                            </div>
                            <div class="divide-y divide-slate-100 bg-paper p-6">
                                @foreach ($mentorBookings as $mb)
                                    <article class="p-4 rounded-lg bg-white mb-4 last:mb-0 border border-ink/10">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <p class="font-semibold text-ink font-black">{{ $mb->user?->name }} — {{ $mb->room?->title }}</p>
                                                <p class="mt-1 text-sm text-ink/60 font-semibold">Jadwal: {{ optional($mb->scheduled_at)->format('Y-m-d H:i') }} · Durasi: {{ $mb->duration_minutes ?? '—' }} menit</p>
                                                <p class="mt-2 text-xs text-ink/60 font-semibold">Catatan: {{ $mb->notes ?? 'Tidak ada' }}</p>
                                            </div>
                                            <div class="flex gap-2">
                                                @if ($mb->status === 'pending')
                                                    <form method="POST" action="{{ route('mentoring-bookings.mentor.approve', $mb) }}">
                                                        @csrf
                                                        <button type="submit" class="rounded-md bg-brand-lime border-2 border-ink px-3 py-2 text-xs font-semibold text-ink">Approve</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('mentoring-bookings.mentor.decline', $mb) }}">
                                                        @csrf
                                                        <button type="submit" class="rounded-md border-2 border-ink bg-brand-pink px-3 py-2 text-xs font-semibold text-ink">Decline</button>
                                                    </form>
                                                @else
                                                    <span class="rounded-md border-2 border-ink bg-paper px-3 py-2 text-xs font-semibold">{{ $mb->status }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        @foreach ($stats as $stat)
                            <div class="nb-card p-4 {{ $metricTone[$stat['tone']] ?? 'bg-white' }}">
                                <div class="text-2xl font-semibold">{{ $stat['value'] }}</div>
                                <div class="mt-1 text-xs font-semibold uppercase tracking-normal">{{ $stat['label'] }}</div>
                            </div>
                        @endforeach
                    </section>

                    <section class="nb-card overflow-hidden">
                        <div class="border-b-2 border-ink bg-brand-sky px-6 py-4">
                            <h2 class="text-base font-semibold text-ink font-black">Profil exchange</h2>
                            <p class="mt-1 text-sm text-ink/70 font-semibold">Data ini dipakai untuk personal branding dan rekomendasi match.</p>
                        </div>
                        <div class="p-6 bg-brand-sky/5">
                        <form method="POST" action="{{ route('exchange.profile.update') }}" class="grid gap-4 lg:grid-cols-2">
                            @csrf
                            <div class="lg:col-span-2">
                                <label for="bio" class="text-sm font-semibold text-slate-700">Bio</label>
                                <textarea id="bio" name="bio" rows="3" required class="mt-2 nb-input w-full">{{ old('bio', $viewer->profile?->bio ?? '') }}</textarea>
                            </div>
                            <div>
                                <label for="location" class="text-sm font-semibold text-slate-700">Lokasi</label>
                                <input id="location" name="location" value="{{ old('location', $viewer->profile?->location ?? '') }}" required class="mt-2 nb-input w-full">
                            </div>
                            <div>
                                <label for="work_mode" class="text-sm font-semibold text-slate-700">Mode kerja</label>
                                <select id="work_mode" name="work_mode" required class="mt-2 nb-input w-full">
                                    @foreach (['online' => 'Online', 'offline' => 'Offline', 'hybrid' => 'Hybrid'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('work_mode', $viewer->profile?->work_mode ?? 'online') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="available_time" class="text-sm font-semibold text-slate-700">Waktu tersedia</label>
                                <input id="available_time" name="available_time" value="{{ old('available_time', $viewer->profile?->available_time ?? '') }}" required class="mt-2 nb-input w-full">
                            </div>
                            <div>
                                <label for="portfolio_url" class="text-sm font-semibold text-slate-700">Portfolio URL</label>
                                <input id="portfolio_url" name="portfolio_url" value="{{ old('portfolio_url', $viewer->profile?->portfolio_url ?? '') }}" type="url" class="mt-2 nb-input w-full" placeholder="https://portofolio.com/nama">
                            </div>

                            {{-- Social Links --}}
                            <div class="lg:col-span-2">
                                <p class="text-sm font-semibold text-slate-700">Link sosial & profil</p>
                                <p class="mt-1 text-xs text-ink/50 font-semibold">Isi salah satu atau semua yang kamu punya.</p>
                            </div>

                            <div>
                                <label for="github_url" class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                                    <span>🐙</span> GitHub
                                </label>
                                <input id="github_url" name="github_url" value="{{ old('github_url', $viewer->profile?->github_url ?? '') }}" type="url" class="mt-2 nb-input w-full" placeholder="https://github.com/username">
                            </div>
                            <div>
                                <label for="linkedin_url" class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                                    <span>💼</span> LinkedIn
                                </label>
                                <input id="linkedin_url" name="linkedin_url" value="{{ old('linkedin_url', $viewer->profile?->linkedin_url ?? '') }}" type="url" class="mt-2 nb-input w-full" placeholder="https://linkedin.com/in/username">
                            </div>
                            <div>
                                <label for="instagram_url" class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                                    <span>📸</span> Instagram
                                </label>
                                <input id="instagram_url" name="instagram_url" value="{{ old('instagram_url', $viewer->profile?->instagram_url ?? '') }}" type="url" class="mt-2 nb-input w-full" placeholder="https://instagram.com/username">
                            </div>
                            <div>
                                <label for="twitter_url" class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                                    <span>🐦</span> X / Twitter
                                </label>
                                <input id="twitter_url" name="twitter_url" value="{{ old('twitter_url', $viewer->profile?->twitter_url ?? '') }}" type="url" class="mt-2 nb-input w-full" placeholder="https://x.com/username">
                            </div>
                            <div>
                                <label for="youtube_url" class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                                    <span>▶️</span> YouTube
                                </label>
                                <input id="youtube_url" name="youtube_url" value="{{ old('youtube_url', $viewer->profile?->youtube_url ?? '') }}" type="url" class="mt-2 nb-input w-full" placeholder="https://youtube.com/@channel">
                            </div>
                            <div>
                                <label for="website_url" class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                                    <span>🌐</span> Website / Portfolio
                                </label>
                                <input id="website_url" name="website_url" value="{{ old('website_url', $viewer->profile?->website_url ?? '') }}" type="url" class="mt-2 nb-input w-full" placeholder="https://namakamu.dev">
                            </div>

                            <div class="lg:col-span-2 flex justify-end">
                                <button type="submit" class="nb-btn nb-btn-primary">Simpan profil</button>
                            </div>
                        </form>
                        </div>
                    </section>

                    <section class="grid gap-6 xl:grid-cols-4">
                        <article class="nb-card overflow-hidden">
                            <div class="border-b-2 border-ink bg-brand-sky px-5 py-3">
                                <h2 class="text-base font-semibold text-ink font-black">Tambah skill</h2>
                            </div>
                            <div class="p-5">
                            <form method="POST" action="{{ route('skills.store') }}" class="space-y-3">
                                @csrf
                                <input name="name" value="{{ old('name') }}" placeholder="Laravel REST API" required class="nb-input w-full">
                                <input name="category" value="{{ old('category') }}" placeholder="Programming" required class="nb-input w-full">
                                <select name="level" required class="nb-input w-full">
                                    <option value="beginner" @selected(old('level') === 'beginner')>Beginner</option>
                                    <option value="intermediate" @selected(old('level', 'intermediate') === 'intermediate')>Intermediate</option>
                                    <option value="advanced" @selected(old('level') === 'advanced')>Advanced</option>
                                </select>
                                <button type="submit" class="nb-btn nb-btn-primary w-full">Tambah skill</button>
                            </form>
                            </div>
                        </article>

                        <article class="nb-card overflow-hidden">
                            <div class="border-b-2 border-ink bg-brand-lime px-5 py-3">
                                <h2 class="text-base font-semibold text-ink font-black">Buat Koukan Offer</h2>
                            </div>
                            <div class="p-5">
                            <form method="POST" action="{{ route('offers.store') }}" class="space-y-3">
                                @csrf
                                <input name="title" value="{{ old('title') }}" placeholder="Saya bisa bantu..." required class="nb-input w-full">
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <select name="type" required class="nb-input w-full">
                                        @foreach ($exchangeTypes as $et)
                                            <option value="{{ $et['key'] }}" title="{{ $et['description'] }}" @selected(old('type') === $et['key'])>{{ $et['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <input name="category" value="{{ old('category') }}" placeholder="Design" required class="nb-input w-full">
                                </div>
                                <textarea name="description" rows="3" placeholder="Jelaskan bantuan yang bisa kamu berikan" required class="nb-input w-full">{{ old('description') }}</textarea>
                                <textarea name="exchange_expectation" rows="2" placeholder="Kamu ingin ditukar dengan apa?" required class="nb-input w-full">{{ old('exchange_expectation') }}</textarea>
                                <input name="available_duration" value="{{ old('available_duration') }}" placeholder="3 jam per minggu" class="nb-input w-full">
                                <button type="submit" class="nb-btn nb-btn-primary w-full">Publikasikan Koukan Offer</button>
                            </form>
                            </div>
                        </article>

                        <article class="nb-card overflow-hidden">
                            <div class="border-b-2 border-ink bg-brand-pink px-5 py-3">
                                <h2 class="text-base font-semibold text-ink font-black">Buat Koukan Need</h2>
                            </div>
                            <div class="p-5">
                            <form method="POST" action="{{ route('needs.store') }}" class="space-y-3">
                                @csrf
                                <input name="title" value="{{ old('title') }}" placeholder="Butuh bantuan..." required class="nb-input w-full">
                                <input name="category" value="{{ old('category') }}" placeholder="Programming" required class="nb-input w-full">
                                <textarea name="description" rows="3" placeholder="Jelaskan kebutuhanmu" required class="nb-input w-full">{{ old('description') }}</textarea>
                                <textarea name="exchange_offer" rows="2" placeholder="Apa yang bisa kamu berikan sebagai barter?" required class="nb-input w-full">{{ old('exchange_offer') }}</textarea>
                                <button type="submit" class="nb-btn nb-btn-primary w-full">Publikasikan Koukan Need</button>
                            </form>
                            </div>
                        </article>

                        <article class="nb-card overflow-hidden">
                            <div class="border-b-2 border-ink bg-brand-yellow px-5 py-3">
                                <h2 class="text-base font-semibold text-ink font-black">Tambah portfolio</h2>
                            </div>
                            <div class="p-5">
                            <form method="POST" action="{{ route('portfolios.store') }}" class="space-y-3">
                                @csrf
                                <input name="title" value="{{ old('title') }}" placeholder="Portofolio Laravel API" required class="nb-input w-full">
                                <textarea name="description" rows="3" placeholder="Jelaskan project atau hasil kerja" required class="nb-input w-full">{{ old('description') }}</textarea>
                                <input name="file_url" value="{{ old('file_url') }}" placeholder="Link file / gambar" type="url" class="nb-input w-full">
                                <input name="project_url" value="{{ old('project_url') }}" placeholder="Link project" type="url" class="nb-input w-full">
                                <button type="submit" class="nb-btn nb-btn-primary w-full">Simpan portfolio</button>
                            </form>
                            </div>
                        </article>
                    </section>

                    <section class="nb-card overflow-hidden">
                        <div class="border-b-2 border-ink bg-brand-yellow px-5 py-4">
                            <h2 class="text-base font-semibold text-ink font-black">Portfolio saya</h2>
                        </div>
                        <div class="divide-y divide-slate-100 bg-brand-yellow/5 p-6">
                            @forelse ($viewer->portfolios as $portfolio)
                                <article class="p-5 rounded-lg bg-white mb-4 last:mb-0 border border-ink/10">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <p class="font-semibold text-ink font-black">{{ $portfolio->title }}</p>
                                            <p class="mt-2 text-sm leading-6 text-ink/70 font-semibold">{{ $portfolio->description }}</p>
                                            <div class="mt-2 flex flex-wrap gap-3 text-xs text-brand-purple font-semibold">
                                                @if ($portfolio->file_url)
                                                    <a href="{{ $portfolio->file_url }}" target="_blank" class="hover:underline">Lihat file</a>
                                                @endif
                                                @if ($portfolio->project_url)
                                                    <a href="{{ $portfolio->project_url }}" target="_blank" class="hover:underline">Lihat project</a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex shrink-0 gap-2">
                                            <button
                                                type="button"
                                                onclick="document.getElementById('edit-portfolio-{{ $portfolio->id }}').classList.remove('hidden')"
                                                class="rounded-md border-2 border-ink bg-brand-sky px-3 py-2 text-xs font-semibold text-ink hover:opacity-80">Edit</button>
                                            <form method="POST" action="{{ route('portfolios.destroy', $portfolio) }}" onsubmit="return confirm('Hapus portfolio ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-md border-2 border-ink bg-brand-pink px-3 py-2 text-xs font-semibold text-ink hover:opacity-80">Hapus</button>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- Modal edit portfolio --}}
                                    <div id="edit-portfolio-{{ $portfolio->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
                                        <div class="w-full max-w-lg rounded-xl border-2 border-ink bg-white p-6 shadow-xl">
                                            <h3 class="text-base font-black text-ink">Edit Portfolio</h3>
                                            <form method="POST" action="{{ route('portfolios.update', $portfolio) }}" class="mt-4 space-y-3">
                                                @csrf
                                                @method('PUT')
                                                <input name="title" value="{{ old('title', $portfolio->title) }}" required placeholder="Judul portfolio" class="nb-input w-full">
                                                <textarea name="description" rows="3" required placeholder="Deskripsi" class="nb-input w-full">{{ old('description', $portfolio->description) }}</textarea>
                                                <input name="file_url" value="{{ old('file_url', $portfolio->file_url) }}" type="url" placeholder="Link file / gambar (opsional)" class="nb-input w-full">
                                                <input name="project_url" value="{{ old('project_url', $portfolio->project_url) }}" type="url" placeholder="Link project (opsional)" class="nb-input w-full">
                                                <div class="flex gap-2">
                                                    <button type="button"
                                                        onclick="document.getElementById('edit-portfolio-{{ $portfolio->id }}').classList.add('hidden')"
                                                        class="flex-1 rounded-md border-2 border-ink bg-paper px-3 py-2 text-xs font-semibold text-ink">Batal</button>
                                                    <button type="submit" class="flex-1 rounded-md bg-ink px-3 py-2 text-xs font-semibold text-white hover:opacity-80">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <div class="p-5 text-sm text-ink/60 font-semibold">Belum ada portfolio.</div>
                            @endforelse
                        </div>
                    </section>

                    <section class="nb-card overflow-hidden">
                        <div class="border-b-2 border-ink bg-brand-purple/20 px-5 py-4">
                            <h2 class="text-base font-semibold text-ink font-black">Mentoring</h2>
                            <p class="mt-1 text-sm text-ink/60 font-semibold">Pesan sesi mentoring dengan mentor yang tersedia.</p>
                        </div>
                        <div class="p-5">

                        @if ($viewer->canHostClass())
                            <details class="mt-4 rounded-lg border-2 border-ink bg-brand-lime/40 p-4">
                                <summary class="cursor-pointer font-black text-ink">+ Buat Kelas Online <span class="nb-badge ml-2 bg-brand-purple text-white">Pro Max</span></summary>
                                @error('mentoring_room')
                                    <p class="mt-3 rounded-md border-2 border-ink bg-brand-pink px-3 py-2 text-xs font-bold text-ink">{{ $message }}</p>
                                @enderror
                                <form method="POST" action="{{ route('mentoring-rooms.store') }}" class="mt-3 grid gap-2">
                                    @csrf
                                    <input name="title" type="text" required maxlength="180" placeholder="Judul kelas (mis. Dasar Laravel API)" class="nb-input w-full" value="{{ old('title') }}">
                                    <textarea name="description" rows="2" required placeholder="Deskripsi singkat materi kelas" class="nb-input w-full">{{ old('description') }}</textarea>
                                    <div class="grid grid-cols-2 gap-2">
                                        <input name="schedule" type="datetime-local" required class="nb-input w-full" value="{{ old('schedule') }}">
                                        <input name="duration_minutes" type="number" min="15" step="15" required placeholder="Durasi (menit)" class="nb-input w-full" value="{{ old('duration_minutes') }}">
                                    </div>
                                    <input name="price" type="number" min="0" step="1000" placeholder="Harga (Rp, kosongkan jika gratis)" class="nb-input w-full" value="{{ old('price') }}">
                                    <button type="submit" class="nb-btn nb-btn-primary w-full">Adakan Kelas</button>
                                </form>
                            </details>
                        @else
                            <div class="mt-4 rounded-lg border-2 border-dashed border-ink/40 p-4">
                                <p class="text-sm font-bold text-ink">Ingin mengadakan kelas online sendiri?</p>
                                <p class="mt-1 text-xs font-semibold text-ink/60">Fitur ini eksklusif untuk plan <strong>Pro Max</strong>.</p>
                                <a href="{{ route('plans.index') }}" class="nb-btn nb-btn-primary mt-3 inline-block">Upgrade ke Pro Max</a>
                            </div>
                        @endif

                        <div class="mt-4 grid gap-4">
                            @forelse ($mentoringRooms as $room)
                                <div class="rounded-md border p-4">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <p class="font-semibold">{{ $room->title }}</p>
                                            <p class="mt-1 text-sm text-ink/60 font-semibold">{{ $room->description }}</p>
                                            <p class="mt-2 text-xs text-ink/60 font-semibold">Mentor: {{ $room->mentor?->name ?? 'n/a' }}</p>
                                        </div>
                                        <div class="w-48">
                                            <form method="POST" action="{{ route('mentoring-bookings.store') }}" class="space-y-2">
                                                @csrf
                                                <input type="hidden" name="mentoring_room_id" value="{{ $room->id }}">
                                                <input name="scheduled_at" type="datetime-local" required class="nb-input w-full">
                                                <input name="duration_minutes" type="number" min="15" placeholder="Durasi (menit)" class="nb-input w-full">
                                                <button type="submit" class="w-full rounded-md bg-teal-600 px-3 py-2 text-xs font-semibold text-white hover:bg-teal-700">Pesan</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-sm text-ink/60 font-semibold">Belum ada mentor tersedia.</div>
                            @endforelse
                        </div>
                    </section>

                    {{-- Booking saya --}}
                    @if ($viewer->mentoringBookings->count())
                        <section class="nb-card">
                            <div class="border-b border-ink px-5 py-4">
                                <h2 class="text-base font-semibold text-ink font-black">Booking mentoring saya</h2>
                                <p class="mt-1 text-xs text-ink/60 font-semibold">Sesi mentoring yang sudah kamu pesan.</p>
                            </div>
                            <div class="divide-y divide-slate-100">
                                @foreach ($viewer->mentoringBookings->sortByDesc('created_at') as $booking)
                                    @php
                                        $bStatusTone = [
                                            'pending'  => 'bg-brand-yellow text-ink',
                                            'approved' => 'bg-brand-lime text-ink',
                                            'declined' => 'bg-rose-100 text-rose-700',
                                        ];
                                    @endphp
                                    <article class="p-5">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <p class="font-semibold text-ink font-black">{{ $booking->room?->title ?? 'Sesi Mentoring' }}</p>
                                                <p class="mt-1 text-xs text-ink/60 font-semibold">
                                                    Mentor: {{ $booking->room?->mentor?->name ?? '—' }}
                                                </p>
                                                <p class="mt-1 text-xs text-ink/60 font-semibold">
                                                    Jadwal: {{ optional($booking->scheduled_at)->format('d M Y H:i') ?? '—' }}
                                                    · Durasi: {{ $booking->duration_minutes ?? '—' }} menit
                                                </p>
                                                @if ($booking->notes)
                                                    <p class="mt-2 text-xs text-ink/70 font-semibold">{{ $booking->notes }}</p>
                                                @endif
                                            </div>
                                            <span class="shrink-0 nb-badge {{ $bStatusTone[$booking->status] ?? 'bg-paper text-ink/70' }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Jenis Exchange Types --}}
                    <section class="nb-card p-5">
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <h2 class="text-base font-semibold text-ink font-black">Jenis Exchange</h2>
                                <p class="mt-1 text-xs text-ink/60 font-semibold">Pilih tipe yang sesuai saat membuat offer.</p>
                            </div>
                            <a href="{{ url('/api/exchange-types') }}" target="_blank" class="rounded-md border border-ink px-3 py-2 text-xs font-medium text-slate-700 hover:bg-paper">JSON API</a>
                        </div>
                        <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach ($exchangeTypes as $et)
                                <div class="rounded-lg border border-ink p-3">
                                    <p class="text-xs font-black text-ink">{{ $et['name'] }}</p>
                                    <p class="mt-1 text-xs font-semibold text-ink/60">{{ $et['description'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <section class="grid gap-6 xl:grid-cols-2">
                        <article class="nb-card">
                            <div class="border-b border-ink px-5 py-4">
                                <h2 class="text-base font-semibold text-ink font-black">Koukan Offer saya</h2>
                            </div>                            <div class="divide-y divide-slate-100">
                                @forelse ($viewer->offers as $offer)
                                    <div class="p-5">
                                        <div class="flex items-start justify-between gap-3">
                                            <a href="{{ route('offers.show', $offer) }}" class="min-w-0 flex-1 hover:opacity-80">
                                                <p class="font-semibold text-ink font-black">{{ $offer->title }}</p>
                                                <p class="mt-2 text-sm leading-6 text-ink/70 font-semibold">{{ $offer->description }}</p>
                                            </a>
                                            <div class="flex shrink-0 items-center gap-2">
                                                <span class="rounded-md bg-brand-sky/10 px-2 py-1 text-xs font-semibold text-ink/70">{{ $offer->category }}</span>
                                                <button
                                                    type="button"
                                                    onclick="document.getElementById('edit-offer-{{ $offer->id }}').classList.remove('hidden')"
                                                    class="rounded-md border border-ink px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-paper">Edit</button>
                                                <form method="POST" action="{{ route('offers.destroy', $offer) }}" onsubmit="return confirm('Hapus offer ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="rounded-md border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50">Hapus</button>
                                                </form>
                                            </div>
                                        </div>

                                        {{-- Modal edit offer --}}
                                        <div id="edit-offer-{{ $offer->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
                                            <div class="w-full max-w-lg rounded-xl border-2 border-ink bg-white p-6 shadow-xl">
                                                <h3 class="text-base font-black text-ink">Edit Offer</h3>
                                                <form method="POST" action="{{ route('offers.update', $offer) }}" class="mt-4 space-y-3">
                                                    @csrf
                                                    @method('PUT')
                                                    <input name="title" value="{{ old('title', $offer->title) }}" required placeholder="Judul offer" class="nb-input w-full">
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <select name="type" required class="nb-input w-full">
                                                            @foreach (['skill' => 'Skill', 'waktu' => 'Waktu', 'pengalaman' => 'Pengalaman', 'mentoring' => 'Mentoring', 'bantuan_project' => 'Bantuan Project', 'kolaborasi' => 'Kolaborasi'] as $val => $label)
                                                                <option value="{{ $val }}" @selected($offer->type === $val)>{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                        <input name="category" value="{{ old('category', $offer->category) }}" required placeholder="Kategori" class="nb-input w-full">
                                                    </div>
                                                    <textarea name="description" rows="3" required placeholder="Deskripsi offer" class="nb-input w-full">{{ old('description', $offer->description) }}</textarea>
                                                    <textarea name="exchange_expectation" rows="2" required placeholder="Ekspektasi barter" class="nb-input w-full">{{ old('exchange_expectation', $offer->exchange_expectation) }}</textarea>
                                                    <input name="available_duration" value="{{ old('available_duration', $offer->available_duration) }}" placeholder="Durasi tersedia (opsional)" class="nb-input w-full">
                                                    <div class="flex gap-2">
                                                        <button type="button"
                                                            onclick="document.getElementById('edit-offer-{{ $offer->id }}').classList.add('hidden')"
                                                            class="flex-1 rounded-md border border-ink px-3 py-2 text-xs font-semibold text-slate-700">Batal</button>
                                                        <button type="submit" class="flex-1 rounded-md bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">Simpan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-5 text-sm text-ink/60 font-semibold">Belum ada Koukan Offer.</div>
                                @endforelse
                            </div>
                        </article>

                        <article class="nb-card">
                            <div class="border-b border-ink px-5 py-4">
                                <h2 class="text-base font-semibold text-ink font-black">Koukan Need saya</h2>
                            </div>
                            <div class="divide-y divide-slate-100">
                                @forelse ($viewer->needs as $need)
                                    <div class="p-5">
                                        <div class="flex items-start justify-between gap-3">
                                            <a href="{{ route('needs.show', $need) }}" class="min-w-0 flex-1 hover:opacity-80">
                                                <p class="font-semibold text-ink font-black">{{ $need->title }}</p>
                                                <p class="mt-2 text-sm leading-6 text-ink/70 font-semibold">{{ $need->description }}</p>
                                            </a>
                                            <div class="flex shrink-0 items-center gap-2">
                                                <span class="rounded-md bg-rose-50 px-2 py-1 text-xs font-semibold text-rose-700">{{ $need->category }}</span>
                                                <button
                                                    type="button"
                                                    onclick="document.getElementById('edit-need-{{ $need->id }}').classList.remove('hidden')"
                                                    class="rounded-md border border-ink px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-paper">Edit</button>
                                                <form method="POST" action="{{ route('needs.destroy', $need) }}" onsubmit="return confirm('Hapus need ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="rounded-md border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50">Hapus</button>
                                                </form>
                                            </div>
                                        </div>

                                        {{-- Modal edit need --}}
                                        <div id="edit-need-{{ $need->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
                                            <div class="w-full max-w-lg rounded-xl border-2 border-ink bg-white p-6 shadow-xl">
                                                <h3 class="text-base font-black text-ink">Edit Need</h3>
                                                <form method="POST" action="{{ route('needs.update', $need) }}" class="mt-4 space-y-3">
                                                    @csrf
                                                    @method('PUT')
                                                    <input name="title" value="{{ old('title', $need->title) }}" required placeholder="Judul need" class="nb-input w-full">
                                                    <input name="category" value="{{ old('category', $need->category) }}" required placeholder="Kategori" class="nb-input w-full">
                                                    <textarea name="description" rows="3" required placeholder="Deskripsi kebutuhan" class="nb-input w-full">{{ old('description', $need->description) }}</textarea>
                                                    <textarea name="exchange_offer" rows="2" required placeholder="Apa yang bisa kamu barter?" class="nb-input w-full">{{ old('exchange_offer', $need->exchange_offer) }}</textarea>
                                                    <div class="flex gap-2">
                                                        <button type="button"
                                                            onclick="document.getElementById('edit-need-{{ $need->id }}').classList.add('hidden')"
                                                            class="flex-1 rounded-md border border-ink px-3 py-2 text-xs font-semibold text-slate-700">Batal</button>
                                                        <button type="submit" class="flex-1 rounded-md bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">Simpan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-5 text-sm text-ink/60 font-semibold">Belum ada Koukan Need.</div>
                                @endforelse
                            </div>
                        </article>
                    </section>

                    <section class="nb-card">
                        <div class="border-b border-ink px-5 py-4">
                            <h2 class="text-base font-semibold text-ink font-black">Exchange request</h2>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @forelse ($exchangeRequests as $exchange)
                                <article class="p-5">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <p class="font-semibold text-ink font-black">
                                                {{ $exchange->fromUser?->name }} ke {{ $exchange->toUser?->name }}
                                            </p>
                                            <p class="mt-2 text-sm leading-6 text-ink/70 font-semibold">{{ $exchange->message }}</p>
                                            <p class="mt-2 text-xs text-ink/60 font-semibold">{{ $exchange->offer?->title ?? 'Offer belum tersedia' }} · {{ $exchange->need?->title ?? 'Need belum tersedia' }}</p>
                                        </div>
                                        <span class="shrink-0 nb-badge {{ $statusTone[$exchange->status] ?? 'bg-paper text-ink/70' }}">
                                            {{ str_replace('_', ' ', $exchange->status) }}
                                        </span>
                                    </div>

                                    <div class="mt-4 flex flex-wrap gap-2">
                                        @if ($exchange->status === 'pending' && (int) $exchange->to_user_id === (int) $viewer->id)
                                            <form method="POST" action="{{ route('exchange-requests.update', $exchange) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="action" value="accept">
                                                <button type="submit" class="rounded-md bg-teal-600 px-3 py-2 text-xs font-semibold text-white hover:bg-teal-700">Accept</button>
                                            </form>
                                            <form method="POST" action="{{ route('exchange-requests.update', $exchange) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="rounded-md border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50">Reject</button>
                                            </form>
                                        @endif

                                        @if ($exchange->status === 'pending' && (int) $exchange->from_user_id === (int) $viewer->id)
                                            <form method="POST" action="{{ route('exchange-requests.update', $exchange) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="action" value="cancel">
                                                <button type="submit" class="rounded-md border border-ink px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-paper">Cancel</button>
                                            </form>
                                        @endif

                                        @if ($exchange->status === 'accepted')
                                            <form method="POST" action="{{ route('exchange-requests.update', $exchange) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="action" value="start">
                                                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700">Mulai progress</button>
                                            </form>
                                        @endif

                                        @if (in_array($exchange->status, ['accepted', 'in_progress'], true))
                                            @php
                                                $completedByViewer = (int) $exchange->from_user_id === (int) $viewer->id
                                                    ? $exchange->completed_by_from_user
                                                    : $exchange->completed_by_to_user;
                                            @endphp

                                            @unless ($completedByViewer)
                                                <form method="POST" action="{{ route('exchange-requests.update', $exchange) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="action" value="complete">
                                                    <button type="submit" class="rounded-md border border-emerald-200 px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50">Konfirmasi selesai</button>
                                                </form>
                                            @else
                                                <span class="shrink-0 nb-badge bg-brand-yellow">Menunggu partner</span>
                                            @endunless
                                        @endif
                                    </div>

                                    @if (in_array($exchange->status, ['accepted', 'in_progress', 'completed', 'reviewed'], true))
                                        <div class="mt-4 border-t border-ink pt-4">
                                            <p class="mb-3 text-xs font-semibold text-slate-700">Progress</p>
                                            
                                            @if ($exchange->progress->count())
                                                <div class="mb-3 space-y-2">
                                                    @foreach ($exchange->progress as $prog)
                                                        <div class="rounded-md bg-paper p-3 text-sm">
                                                            <p class="font-semibold text-ink font-black">{{ $prog->user?->name ?? 'Unknown' }}</p>
                                                            <p class="mt-1 text-slate-700">{{ $prog->progress_note }}</p>
                                                            @if ($prog->file_url)
                                                                <a href="{{ $prog->file_url }}" target="_blank" class="mt-1 inline-block text-xs text-teal-700 font-semibold hover:underline">Lihat file</a>
                                                            @endif
                                                            @if ((int) $prog->user_id === (int) $viewer->id)
                                                                <form method="POST" action="{{ route('exchange-progress.destroy', $prog) }}" class="mt-1 inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="text-xs text-rose-600 font-semibold hover:underline">Hapus</button>
                                                                </form>
                                                            @endif
                                                            <p class="mt-1 text-xs text-ink/60 font-semibold">{{ $prog->created_at->format('d M Y H:i') }}</p>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            @if (in_array($exchange->status, ['accepted', 'in_progress'], true))
                                                <form method="POST" action="{{ route('exchange-requests.progress.store', $exchange) }}" class="space-y-2">
                                                    @csrf
                                                    <textarea name="progress_note" placeholder="Tambahkan catatan progress..." rows="2" required class="nb-input w-full">{{ old('progress_note') }}</textarea>
                                                    <input name="file_url" placeholder="Link file/bukti (opsional)" type="url" class="nb-input w-full">
                                                    <button type="submit" class="w-full rounded-md bg-teal-600 px-3 py-2 text-xs font-semibold text-white hover:bg-teal-700">Upload progress</button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif

                                    @if (in_array($exchange->status, ['completed', 'reviewed'], true))
                                        @php
                                            $partnerId = (int) $exchange->from_user_id === (int) $viewer->id
                                                ? $exchange->to_user_id
                                                : $exchange->from_user_id;
                                            $partnerName = (int) $exchange->from_user_id === (int) $viewer->id
                                                ? $exchange->toUser?->name
                                                : $exchange->fromUser?->name;
                                            $alreadyReviewed = \App\Models\Review::where('exchange_request_id', $exchange->id)
                                                ->where('reviewer_id', $viewer->id)
                                                ->exists();
                                        @endphp

                                        <div class="mt-4 border-t border-ink pt-4">
                                            <p class="mb-3 text-xs font-semibold text-slate-700">Review untuk partner</p>

                                            @if ($alreadyReviewed)
                                                <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-800">
                                                    Anda sudah memberikan review untuk exchange ini.
                                                </div>
                                            @else
                                                <form method="POST" action="{{ route('exchange-requests.review.store', $exchange) }}" class="space-y-3 rounded-md border-2 border-ink bg-brand-lime/30 p-4">
                                                    @csrf
                                                    <input type="hidden" name="reviewed_user_id" value="{{ $partnerId }}">

                                                    <div>
                                                        <label class="text-xs font-semibold text-slate-700">Rating untuk {{ $partnerName }}</label>
                                                        <div class="mt-2 flex gap-1" id="stars-{{ $exchange->id }}">
                                                            @for ($star = 1; $star <= 5; $star++)
                                                                <label class="cursor-pointer">
                                                                    <input type="radio" name="rating" value="{{ $star }}" required class="sr-only peer">
                                                                    <span class="text-2xl leading-none text-slate-300 peer-checked:text-amber-400 hover:text-amber-300 transition-colors">★</span>
                                                                </label>
                                                            @endfor
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label class="text-xs font-semibold text-slate-700">Komentar</label>
                                                        <textarea name="comment" rows="3" required placeholder="Bagaimana pengalaman bertukar skill dengan {{ $partnerName }}?" class="mt-2 nb-input w-full">{{ old('comment') }}</textarea>
                                                    </div>

                                                    <button type="submit" class="w-full rounded-md bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">
                                                        Kirim Review
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                </article>
                            @empty
                                <div class="p-5 text-sm text-ink/60 font-semibold">Belum ada request exchange.</div>
                            @endforelse
                        </div>
                    </section>
                </div>

                <aside class="space-y-6">
                    <section class="nb-card p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-base font-semibold text-ink font-black">Reputasi</h2>
                                <p class="mt-1 text-sm text-ink/60 font-semibold">Trust score akun.</p>
                            </div>
                            <div class="rounded-lg bg-slate-950 px-3 py-2 text-xl font-semibold text-white">{{ $reputation['score'] }}</div>
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

                    <section class="nb-card p-5">
                        <h2 class="text-base font-semibold text-ink font-black">Limit plan</h2>
                        <div class="mt-5 space-y-4">
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
                        <a href="{{ route('plans.index') }}" class="nb-btn nb-btn-primary mt-5 w-full">Upgrade Plan</a>
                    </section>

                    <section class="nb-card p-5">
                        <h2 class="text-base font-semibold text-ink font-black">Riwayat transaksi</h2>
                        <div class="mt-4 space-y-3">
                            @php
                                $statusStyle = [
                                    'completed' => 'bg-brand-lime text-ink',
                                    'pending' => 'bg-brand-yellow text-ink',
                                    'failed' => 'bg-brand-pink text-ink',
                                ];
                                $statusLabel = [
                                    'completed' => 'Berhasil',
                                    'pending' => 'Menunggu',
                                    'failed' => 'Gagal',
                                ];
                            @endphp
                            @forelse ($transactions as $trx)
                                <div class="rounded-lg border-2 border-ink p-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="text-sm font-black text-ink">
                                                {{ $trx->type === 'plan' ? 'Langganan Plan' : ucfirst($trx->type) }}
                                            </p>
                                            <p class="mt-0.5 text-xs font-semibold text-ink/60">
                                                KOUKAN-PLAN-{{ $trx->id }} · {{ $trx->created_at->format('d M Y H:i') }}
                                            </p>
                                            @if ($trx->payment_method)
                                                <p class="text-xs font-semibold text-ink/50">via {{ str_replace('_', ' ', $trx->payment_method) }}</p>
                                            @endif
                                        </div>
                                        <div class="shrink-0 text-right">
                                            <p class="text-sm font-black text-ink">Rp{{ number_format($trx->amount, 0, ',', '.') }}</p>
                                            <span class="nb-badge mt-1 {{ $statusStyle[$trx->status] ?? 'bg-brand-sky text-ink' }}">
                                                {{ $statusLabel[$trx->status] ?? ucfirst($trx->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-ink/60 font-semibold">Belum ada transaksi.</p>
                            @endforelse
                        </div>
                    </section>

                    <section class="nb-card p-5">
                        <h2 class="text-base font-semibold text-ink font-black">Skill saya</h2>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @forelse ($viewer->skills as $skill)
                                <div class="group relative flex items-center gap-1 rounded-md border border-ink px-3 py-2 text-xs font-semibold text-slate-700">
                                    <span>{{ $skill->name }} · {{ $skill->level }}</span>
                                    <button
                                        type="button"
                                        onclick="document.getElementById('edit-skill-{{ $skill->id }}').classList.remove('hidden')"
                                        class="ml-1 text-ink/40 hover:text-teal-600 transition-colors"
                                        title="Edit skill">✎</button>
                                    <form method="POST" action="{{ route('skills.destroy', $skill) }}" class="inline" onsubmit="return confirm('Hapus skill ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-ink/40 hover:text-rose-600 transition-colors" title="Hapus skill">✕</button>
                                    </form>
                                </div>

                                {{-- Modal edit skill --}}
                                <div id="edit-skill-{{ $skill->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
                                    <div class="w-full max-w-sm rounded-xl border-2 border-ink bg-white p-6 shadow-xl">
                                        <h3 class="text-base font-black text-ink">Edit Skill</h3>
                                        <form method="POST" action="{{ route('skills.update', $skill) }}" class="mt-4 space-y-3">
                                            @csrf
                                            @method('PUT')
                                            <input name="name" value="{{ old('name', $skill->name) }}" required class="nb-input w-full" placeholder="Nama skill">
                                            <input name="category" value="{{ old('category', $skill->category) }}" required class="nb-input w-full" placeholder="Kategori">
                                            <select name="level" required class="nb-input w-full">
                                                @foreach (['beginner', 'intermediate', 'advanced'] as $lv)
                                                    <option value="{{ $lv }}" @selected($skill->level === $lv)>{{ ucfirst($lv) }}</option>
                                                @endforeach
                                            </select>
                                            <div class="flex gap-2">
                                                <button type="button"
                                                    onclick="document.getElementById('edit-skill-{{ $skill->id }}').classList.add('hidden')"
                                                    class="flex-1 rounded-md border border-ink px-3 py-2 text-xs font-semibold text-slate-700">Batal</button>
                                                <button type="submit" class="flex-1 rounded-md bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <span class="text-sm text-ink/60 font-semibold">Belum ada skill.</span>
                            @endforelse
                        </div>
                    </section>

                    <section class="nb-card">
                        <div class="border-b border-ink px-5 py-4">
                            <h2 class="text-base font-semibold text-ink font-black">Match cepat</h2>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @forelse ($recommendations as $item)
                                <article class="p-5">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('users.profile', $item['user']) }}">
                                            <img class="h-10 w-10 rounded-full object-cover hover:opacity-80 transition-opacity" src="{{ $item['user']->profile_photo_url }}" alt="{{ $item['user']->name }}">
                                        </a>
                                        <div class="min-w-0 flex-1">
                                            <a href="{{ route('users.profile', $item['user']) }}" class="block truncate text-sm font-semibold text-ink font-black hover:underline">{{ $item['user']->name }}</a>
                                            <p class="truncate text-xs text-ink/60 font-semibold">{{ $item['label'] }}</p>
                                        </div>
                                        <span class="rounded-md bg-teal-50 px-2 py-1 text-xs font-semibold text-teal-700">{{ $item['score'] }}%</span>
                                    </div>
                                    <p class="mt-3 text-xs leading-5 text-ink/70 font-semibold">{{ $item['summary'] }}</p>
                                    <a href="{{ route('users.profile', $item['user']) }}" class="mt-3 inline-flex rounded-md border border-ink px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-paper">
                                        Lihat Koukan ID →
                                    </a>
                                </article>
                            @empty
                                <div class="p-5 text-sm text-ink/60 font-semibold">Belum ada match.</div>
                            @endforelse
                        </div>
                    </section>
                </aside>
            </section>
        </main>
    </div>
</x-app-layout>
