<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Koukan' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="overflow-x-hidden bg-paper font-sans text-ink antialiased">
    <div class="min-h-screen">
        <header class="sticky top-0 z-30 border-b-2 border-ink bg-brand-yellow">
            <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-lg border-2 border-ink bg-ink text-sm font-black text-white shadow-nb-sm">K</span>
                    <span>
                        <span class="block text-base font-black tracking-tight text-ink">Koukan</span>
                        <span class="block text-xs font-semibold text-ink/70">Exchange Skills, Build Connections.</span>
                    </span>
                </a>

                <nav class="flex flex-wrap items-center gap-2 text-sm">
                    <a href="{{ route('market') }}" class="nb-btn {{ request()->routeIs('home', 'market', 'preview', 'offers.show', 'needs.show') ? 'nb-btn-pink' : 'nb-btn-white' }}">Koukan Market</a>
                    <a href="{{ route('matches') }}" class="nb-btn {{ request()->routeIs('matches') ? 'nb-btn-pink' : 'nb-btn-white' }}">Koukan Match</a>

                    @auth
                        <a href="{{ route('dashboard') }}" class="nb-btn nb-btn-primary">Koukan Board</a>
                        <a href="{{ route('user.show', auth()->user()->name) }}" class="nb-btn nb-btn-sky">Koukan ID</a>
                    @else
                        <a href="{{ route('login') }}" class="nb-btn nb-btn-white">Login</a>
                        <a href="{{ route('register') }}" class="nb-btn nb-btn-sky">Daftar</a>
                    @endauth
                </nav>
            </div>
        </header>

        {{ $slot }}
    </div>
</body>
</html>
