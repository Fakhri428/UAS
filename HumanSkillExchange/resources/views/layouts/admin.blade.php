<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin — Koukan' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-paper font-sans text-ink antialiased">
    @php
        $nav = [
            ['route' => 'admin.dashboard',    'label' => 'Dashboard',    'icon' => '▦'],
            ['route' => 'admin.users',        'label' => 'Users',        'icon' => '◉'],
            ['route' => 'admin.exchanges',    'label' => 'Exchanges',    'icon' => '⇄'],
            ['route' => 'admin.reviews',      'label' => 'Reviews',      'icon' => '★'],
            ['route' => 'admin.transactions', 'label' => 'Transactions', 'icon' => '$'],
        ];
    @endphp

    <div class="min-h-screen">
        {{-- Top bar --}}
        <header class="sticky top-0 z-30 border-b-2 border-ink bg-brand-yellow">
            <div class="mx-auto flex max-w-[1400px] items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg border-2 border-ink bg-ink text-sm font-black text-white shadow-nb-sm">K</span>
                    <span>
                        <span class="block text-base font-black tracking-tight text-ink">Admin Panel</span>
                        <span class="block text-xs font-semibold text-ink/70">Koukan</span>
                    </span>
                </a>

                <div class="flex items-center gap-2">
                    <span class="nb-badge hidden bg-white sm:inline-flex">{{ auth()->user()->name ?? 'Admin' }}</span>
                    <a href="{{ route('home') }}" class="nb-btn nb-btn-white">← Situs</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nb-btn nb-btn-primary">Logout</button>
                    </form>
                </div>
            </div>
        </header>

        <div class="mx-auto flex max-w-[1400px] flex-col gap-6 px-4 py-6 sm:px-6 lg:flex-row lg:px-8">
            {{-- Sidebar --}}
            <aside class="lg:w-60 lg:shrink-0">
                <nav class="nb-card overflow-hidden p-0 lg:sticky lg:top-24">
                    <div class="border-b-2 border-ink bg-brand-purple px-4 py-3">
                        <p class="text-xs font-black uppercase tracking-wide text-ink">Menu</p>
                    </div>
                    <div class="flex flex-row flex-wrap gap-2 p-3 lg:flex-col">
                        @foreach ($nav as $item)
                            @php $active = request()->routeIs($item['route']); @endphp
                            <a href="{{ route($item['route']) }}"
                               class="nb-btn w-full justify-start {{ $active ? 'nb-btn-pink' : 'nb-btn-white' }}">
                                <span class="text-base leading-none">{{ $item['icon'] }}</span>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </nav>
            </aside>

            {{-- Main content --}}
            <main class="min-w-0 flex-1">
                @if (session('status'))
                    <div class="nb-panel mb-5 bg-brand-lime px-4 py-3 text-sm font-bold text-ink">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
