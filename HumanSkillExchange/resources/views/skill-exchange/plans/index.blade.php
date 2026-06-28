@php
    $accent = ['bg-brand-sky', 'bg-brand-lime', 'bg-brand-purple/40'];

    // Fitur per plan — 0=Gratis, 1=Pro, 2=Pro Max
    // true=centang, false=tidak tersedia, string=label
    $planFeatures = [
        0 => [
            ['label' => 'Menambahkan skill',       'value' => 'Maks. 3'],
            ['label' => 'Menambahkan need',         'value' => 'Maks. 3'],
            ['label' => 'Membuat offer',            'value' => 'Maks. 2'],
            ['label' => 'Exchange request',         'value' => 'Maks. 5/bulan'],
            ['label' => 'Upload portofolio',        'value' => 'Maks. 2 item'],
            ['label' => 'Review & rating',          'value' => true],
            ['label' => 'Matching user',            'value' => 'Basic'],
            ['label' => 'Profil publik',            'value' => 'Basic'],
            ['label' => 'Badge profil',             'value' => false],
            ['label' => 'Featured profile',        'value' => false],
            ['label' => 'Featured offer',          'value' => false],
            ['label' => 'Statistik profil',        'value' => false],
            ['label' => 'Export portofolio PDF',   'value' => false],
            ['label' => 'Prioritas pencarian',     'value' => false],
            ['label' => 'Mentoring room berbayar', 'value' => false],
            ['label' => 'Komisi platform',         'value' => 'N/A'],
        ],
        1 => [
            ['label' => 'Menambahkan skill',       'value' => 'Maks. 10'],
            ['label' => 'Menambahkan need',         'value' => 'Maks. 10'],
            ['label' => 'Membuat offer',            'value' => 'Maks. 10'],
            ['label' => 'Exchange request',         'value' => 'Maks. 30/bulan'],
            ['label' => 'Upload portofolio',        'value' => 'Maks. 10 item'],
            ['label' => 'Review & rating',          'value' => true],
            ['label' => 'Matching user',            'value' => 'Advanced'],
            ['label' => 'Profil publik',            'value' => 'Custom link'],
            ['label' => 'Badge profil',             'value' => 'Pro Badge'],
            ['label' => 'Featured profile',        'value' => '3 hari/bulan'],
            ['label' => 'Featured offer',          'value' => '3 offer/bulan'],
            ['label' => 'Statistik profil',        'value' => 'Basic analytics'],
            ['label' => 'Export portofolio PDF',   'value' => true],
            ['label' => 'Prioritas pencarian',     'value' => 'Sedang'],
            ['label' => 'Mentoring room berbayar', 'value' => true],
            ['label' => 'Komisi platform',         'value' => 'Standar'],
        ],
        2 => [
            ['label' => 'Menambahkan skill',       'value' => 'Unlimited'],
            ['label' => 'Menambahkan need',         'value' => 'Unlimited'],
            ['label' => 'Membuat offer',            'value' => 'Unlimited'],
            ['label' => 'Exchange request',         'value' => 'Unlimited'],
            ['label' => 'Upload portofolio',        'value' => 'Unlimited'],
            ['label' => 'Review & rating',          'value' => true],
            ['label' => 'Matching user',            'value' => 'Priority'],
            ['label' => 'Profil publik',            'value' => 'Custom link + tema'],
            ['label' => 'Badge profil',             'value' => 'Verified/Trusted Badge'],
            ['label' => 'Featured profile',        'value' => '15 hari/bulan'],
            ['label' => 'Featured offer',          'value' => '10 offer/bulan'],
            ['label' => 'Statistik profil',        'value' => 'Advanced analytics'],
            ['label' => 'Export portofolio PDF',   'value' => true],
            ['label' => 'Prioritas pencarian',     'value' => 'Tinggi'],
            ['label' => 'Mentoring room berbayar', 'value' => true],
            ['label' => 'Komisi platform',         'value' => 'Lebih rendah'],
        ],
    ];
@endphp

@component('layouts.public', ['title' => 'Plan Member - Koukan'])
    <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

        @if (session('status'))
            <div class="nb-card mb-6 bg-brand-lime p-4 font-bold text-ink">{{ session('status') }}</div>
        @endif

        <div class="text-center">
            <span class="nb-badge bg-brand-purple text-white">Plan Member</span>
            <h1 class="mt-4 text-3xl font-black tracking-tight text-ink sm:text-5xl">Pilih plan yang cocok untukmu</h1>
            <p class="mx-auto mt-4 max-w-2xl text-base font-medium text-ink/70">
                Tingkatkan plan untuk membuka lebih banyak fitur, prioritas matching, dan akses eksklusif.
            </p>
        </div>

        <div class="mt-10 grid gap-6 lg:grid-cols-3">
            @foreach ($plans as $index => $plan)
                @php
                    $isCurrent = $currentPlan && $currentPlan->id === $plan->id;
                    $features  = $planFeatures[$index % 3];
                @endphp

                <section class="nb-card flex flex-col p-6 {{ $accent[$index % count($accent)] }}">

                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-2xl font-black text-ink">{{ $plan->name }}</h2>
                        @if ($isCurrent)
                            <span class="nb-badge bg-ink text-white">Plan aktif</span>
                        @endif
                    </div>

                    <p class="mt-3 text-3xl font-black text-ink">
                        Rp{{ number_format($plan->price, 0, ',', '.') }}
                        <span class="text-sm font-semibold text-ink/60">/bulan</span>
                    </p>

                    <ul class="mt-6 space-y-3 text-sm font-semibold text-ink">
                        @foreach ($features as $feat)
                            @php $val = $feat['value']; @endphp
                            @if ($val !== false)
                                <li class="flex items-start gap-2">
                                    <span class="mt-0.5 shrink-0 font-black">✓</span>
                                    <span>
                                        {{ $feat['label'] }}
                                        @if ($val !== true)
                                            <span class="font-black">— {{ $val }}</span>
                                        @endif
                                    </span>
                                </li>
                            @endif
                        @endforeach
                    </ul>

                    <div class="mt-auto pt-6">
                        @auth
                            @if ($isCurrent)
                                <button type="button" disabled class="nb-btn nb-btn-white w-full cursor-not-allowed opacity-60">Plan saat ini</button>
                            @else
                                <a href="{{ route('plans.checkout', $plan) }}" class="nb-btn nb-btn-primary w-full">
                                    {{ (int) $plan->price === 0 ? 'Aktifkan Gratis' : 'Pilih Plan' }}
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="nb-btn nb-btn-primary w-full">Login untuk berlangganan</a>
                        @endauth
                    </div>

                </section>
            @endforeach
        </div>

    </main>
@endcomponent
