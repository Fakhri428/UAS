<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Selamat Datang - Koukan</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-paper font-sans text-ink antialiased">

    {{-- Navbar --}}
    <header class="border-b-2 border-ink bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg border-2 border-ink bg-ink text-sm font-black text-white">K</span>
                <span class="text-base font-black text-ink">Koukan</span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" class="nb-btn nb-btn-white text-sm">Market</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="nb-btn nb-btn-primary text-sm">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="nb-btn nb-btn-white text-sm">Login</a>
                    <a href="{{ route('register') }}" class="nb-btn nb-btn-primary text-sm">Daftar</a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        {{-- Hero --}}
        <section class="border-b-2 border-ink bg-brand-lime">
            <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:py-24">
                <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
                    <div>
                        <span class="nb-badge bg-brand-purple text-white">Platform Barter Skill #1</span>
                        <h1 class="mt-4 text-5xl font-black leading-[1.05] tracking-tight text-ink sm:text-6xl">
                            Tukarkan <span class="underline decoration-brand-pink decoration-4">skill</span>-mu,<br>
                            bangun koneksi nyata.
                        </h1>
                        <p class="mt-5 max-w-lg text-lg font-semibold leading-7 text-ink/80">
                            Koukan mempertemukan orang-orang yang saling butuh. Kamu punya skill A, orang lain punya skill B — swap, tanpa uang.
                        </p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            @auth
                                <a href="{{ route('dashboard') }}" class="nb-btn nb-btn-primary text-base">Masuk ke Dashboard</a>
                                <a href="{{ route('home') }}" class="nb-btn nb-btn-white text-base">Jelajahi Market</a>
                            @else
                                <a href="{{ route('register') }}" class="nb-btn nb-btn-primary text-base">Mulai Gratis</a>
                                <a href="{{ route('home') }}" class="nb-btn nb-btn-white text-base">Lihat Market</a>
                            @endauth
                        </div>
                        @guest
                            <p class="mt-4 text-sm font-semibold text-ink/60">
                                Sudah punya akun? <a href="{{ route('login') }}" class="font-black text-brand-purple underline">Login di sini</a>
                            </p>
                        @endguest
                    </div>

                    {{-- Visual kanan --}}
                    <div class="relative hidden lg:block">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="nb-card bg-brand-sky p-4 rotate-1">
                                <p class="text-xs font-black uppercase text-ink/60">Offer</p>
                                <p class="mt-2 text-sm font-black text-ink">Saya bisa bantu Laravel REST API</p>
                                <span class="mt-3 inline-block nb-badge bg-white">Programming</span>
                            </div>
                            <div class="nb-card bg-brand-pink p-4 -rotate-1 mt-6">
                                <p class="text-xs font-black uppercase text-ink/60">Need</p>
                                <p class="mt-2 text-sm font-black text-ink">Butuh bantuan desain UI di Figma</p>
                                <span class="mt-3 inline-block nb-badge bg-white">Design</span>
                            </div>
                            <div class="nb-card bg-brand-yellow p-4 -rotate-1">
                                <p class="text-xs font-black uppercase text-ink/60">Match 94%</p>
                                <p class="mt-2 text-sm font-black text-ink">Fakhri ↔ Raka — barter dua arah</p>
                                <span class="mt-3 inline-block nb-badge bg-ink text-white">Exchange Active</span>
                            </div>
                            <div class="nb-card bg-brand-purple/20 p-4 rotate-1 mt-6">
                                <p class="text-xs font-black uppercase text-ink/60">Review</p>
                                <p class="mt-2 text-sm font-black text-ink">★★★★★ — Sangat responsif!</p>
                                <span class="mt-3 inline-block nb-badge bg-brand-lime">Reviewed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Stats bar --}}
        <section class="border-b-2 border-ink bg-brand-yellow">
            <div class="mx-auto max-w-6xl px-4 py-6 sm:px-6">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    @foreach ([
                        ['value' => '100+', 'label' => 'Member Aktif'],
                        ['value' => '200+', 'label' => 'Skill Terdaftar'],
                        ['value' => '50+',  'label' => 'Exchange Selesai'],
                        ['value' => '4.8★', 'label' => 'Rata-rata Rating'],
                    ] as $s)
                        <div class="text-center">
                            <p class="text-3xl font-black text-ink">{{ $s['value'] }}</p>
                            <p class="mt-1 text-xs font-bold uppercase tracking-wide text-ink/70">{{ $s['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Cara kerja --}}
        <section class="border-b-2 border-ink bg-white">
            <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6">
                <div class="text-center">
                    <span class="nb-badge bg-brand-sky">Cara Kerja</span>
                    <h2 class="mt-3 text-3xl font-black text-ink">Dari daftar sampai exchange, 4 langkah</h2>
                </div>
                <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ([
                        ['step' => '01', 'bg' => 'bg-brand-sky',    'title' => 'Buat Profil',        'desc' => 'Isi bio, lokasi, mode kerja, dan link sosialmu. Ini tampil di Koukan ID-mu.'],
                        ['step' => '02', 'bg' => 'bg-brand-lime',   'title' => 'Tambah Skill & Offer','desc' => 'Tulis apa yang bisa kamu bantu dan apa yang kamu butuhkan.'],
                        ['step' => '03', 'bg' => 'bg-brand-yellow', 'title' => 'Temukan Match',       'desc' => 'Sistem menghitung kecocokan berdasarkan kategori, kata kunci, dan reputasi.'],
                        ['step' => '04', 'bg' => 'bg-brand-pink',   'title' => 'Exchange & Review',   'desc' => 'Setuju, jalankan exchange, catat progress, dan beri rating setelah selesai.'],
                    ] as $i => $step)
                        <div class="nb-card p-5 {{ $step['bg'] }}">
                            <span class="text-4xl font-black text-ink/20">{{ $step['step'] }}</span>
                            <h3 class="mt-2 text-base font-black text-ink">{{ $step['title'] }}</h3>
                            <p class="mt-2 text-sm font-semibold leading-6 text-ink/70">{{ $step['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Fitur utama --}}
        <section class="border-b-2 border-ink bg-paper">
            <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6">
                <div class="text-center">
                    <span class="nb-badge bg-brand-purple text-white">Fitur Unggulan</span>
                    <h2 class="mt-3 text-3xl font-black text-ink">Semua yang kamu butuhkan untuk barter skill</h2>
                </div>
                <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ([
                        ['icon' => '🤝', 'bg' => 'bg-brand-sky',       'title' => 'Rule-Based Matching',    'desc' => 'Algoritma mencocokkan offer dan need berdasarkan kategori, kata kunci, dan mode kerja.'],
                        ['icon' => '🔄', 'bg' => 'bg-brand-lime',      'title' => 'Exchange Request',        'desc' => 'Kirim request, setujui, catat progress bersama, dan konfirmasi selesai dari dua pihak.'],
                        ['icon' => '⭐', 'bg' => 'bg-brand-yellow',    'title' => 'Review & Reputasi',       'desc' => 'Setiap exchange yang selesai menghasilkan review dan membangun Koukan Score.'],
                        ['icon' => '🎓', 'bg' => 'bg-brand-purple/20', 'title' => 'Mentoring Room',          'desc' => 'Mentor bisa membuka kelas online berbayar. Peserta booking langsung dari platform.'],
                        ['icon' => '📁', 'bg' => 'bg-brand-pink',      'title' => 'Koukan ID & Portfolio',   'desc' => 'Profil publik dengan skill, offer, need, portfolio, review, dan link sosial media.'],
                        ['icon' => '🔒', 'bg' => 'bg-brand-orange/20', 'title' => 'Plan Member',             'desc' => 'Gratis untuk mulai. Upgrade ke Pro atau Pro Max untuk membuka lebih banyak fitur.'],
                    ] as $feat)
                        <div class="nb-card p-5 {{ $feat['bg'] }}">
                            <span class="text-3xl">{{ $feat['icon'] }}</span>
                            <h3 class="mt-3 text-base font-black text-ink">{{ $feat['title'] }}</h3>
                            <p class="mt-2 text-sm font-semibold leading-6 text-ink/70">{{ $feat['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Testimonial --}}
        <section class="border-b-2 border-ink bg-white">
            <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6">
                <div class="text-center">
                    <span class="nb-badge bg-brand-lime">Testimoni</span>
                    <h2 class="mt-3 text-3xl font-black text-ink">Apa kata mereka tentang Koukan?</h2>
                </div>
                <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ([
                        ['name' => 'Fakhri R.', 'role' => 'Web Developer', 'bg' => 'bg-brand-sky/20', 'text' => 'Bisa barter skill desain UI dengan Laravel, sangat membantu!'],
                        ['name' => 'Raka A.', 'role' => 'UI Designer', 'bg' => 'bg-brand-lime/20', 'text' => 'Platformnya simpel, matchingnya akurat, dan komunitasnya ramah.'],
                        ['name' => 'Dinda S.', 'role' => 'Content Writer', 'bg' => 'bg-brand-yellow/20', 'text' => 'Dapat mentor programming dengan cara barter tulisan, worth it banget!'],
                    ] as $testi)
                        <div class="nb-card p-6 {{ $testi['bg'] }}">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-brand-yellow">★★★★★</span>
                            </div>
                            <p class="text-sm font-semibold text-ink/80 italic">"{{ $testi['text'] }}"</p>
                            <div class="mt-4">
                                <p class="font-black text-ink">{{ $testi['name'] }}</p>
                                <p class="text-xs text-ink/60 font-semibold">{{ $testi['role'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Kategori Skill Populer --}}
        <section class="border-b-2 border-ink bg-brand-pink/10">
            <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6">
                <div class="text-center">
                    <span class="nb-badge bg-brand-pink text-white">Skill Populer</span>
                    <h2 class="mt-3 text-3xl font-black text-ink">Skill yang banyak di-exchange</h2>
                </div>
                <div class="mt-10 grid gap-3 sm:grid-cols-3 lg:grid-cols-6">
                    @foreach ([
                        ['name' => 'Web Development', 'icon' => '💻', 'bg' => 'bg-brand-sky'],
                        ['name' => 'UI/UX Design', 'icon' => '🎨', 'bg' => 'bg-brand-lime'],
                        ['name' => 'Mobile App', 'icon' => '📱', 'bg' => 'bg-brand-yellow'],
                        ['name' => 'Data Science', 'icon' => '📊', 'bg' => 'bg-brand-purple'],
                        ['name' => 'Content Writing', 'icon' => '✍️', 'bg' => 'bg-brand-pink'],
                        ['name' => 'Digital Marketing', 'icon' => '🚀', 'bg' => 'bg-brand-orange/20'],
                    ] as $cat)
                        <div class="nb-card p-5 text-center {{ $cat['bg'] }}">
                            <span class="text-3xl">{{ $cat['icon'] }}</span>
                            <p class="mt-2 text-sm font-black text-ink">{{ $cat['name'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- CTA bawah --}}
        <section class="border-b-2 border-ink bg-brand-purple/10">
            <div class="mx-auto max-w-3xl px-4 py-16 text-center sm:px-6">
                <h2 class="text-3xl font-black text-ink sm:text-4xl">Siap mulai exchange?</h2>
                <p class="mx-auto mt-4 max-w-xl text-base font-semibold text-ink/70">
                    Daftar gratis, isi profil, tambah skill, dan temukan partner barter pertamamu hari ini.
                </p>
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="nb-btn nb-btn-primary text-base">Buka Dashboard</a>
                        <a href="{{ route('home') }}" class="nb-btn nb-btn-white text-base">Jelajahi Market</a>
                    @else
                        <a href="{{ route('register') }}" class="nb-btn nb-btn-primary text-base">Daftar Sekarang — Gratis</a>
                        <a href="{{ route('home') }}" class="nb-btn nb-btn-white text-base">Lihat Market Dulu</a>
                    @endauth
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t-2 border-ink bg-ink py-6 text-center">
        <p class="text-sm font-semibold text-white/70">Koukan — Platform Barter Skill. Praktikum Web Service.</p>
    </footer>

</body>
</html>
