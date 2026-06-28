@extends('layouts.admin', ['title' => 'Admin Dashboard — Koukan'])

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-ink">Admin Dashboard</h1>
            <p class="mt-1 text-sm font-semibold text-ink/70">Overview of system activity.</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="nb-card">
            <div class="border-b-2 border-ink px-5 py-4">
                <h2 class="text-lg font-black text-ink">Recent Users</h2>
            </div>
            <div class="divide-y-2 divide-ink/10">
                @foreach($users->take(5) as $u)
                    <div class="p-4 flex items-center justify-between">
                        <div>
                            <p class="font-bold text-ink">{{ $u->name }}</p>
                            <p class="text-sm font-semibold text-ink/70">{{ $u->email }}</p>
                        </div>
                        <span class="nb-badge bg-brand-lime">User</span>
                    </div>
                @endforeach
            </div>
            <div class="border-t-2 border-ink bg-paper px-5 py-3">
                <span class="text-sm font-bold text-ink/50">View all users &rarr;</span>
            </div>
        </section>

        <section class="nb-card">
            <div class="border-b-2 border-ink bg-brand-pink px-5 py-4">
                <h2 class="text-lg font-black text-ink">Recent Bookings</h2>
            </div>
            <div class="divide-y-2 divide-ink/10">
                @foreach($bookings->take(5) as $b)
                    <div class="p-4">
                        <div class="flex justify-between items-start gap-4">
                            <div>
                                <p class="font-bold text-ink">{{ $b->room->title ?? 'n/a' }}</p>
                                <p class="text-sm font-semibold text-ink/70">by {{ $b->user->email ?? 'n/a' }}</p>
                            </div>
                            <span class="nb-badge {{ $b->status === 'approved' ? 'bg-brand-lime' : ($b->status === 'declined' ? 'bg-rose-400' : 'bg-brand-yellow') }}">
                                {{ $b->status }}
                            </span>
                        </div>
                        @if($b->status === 'pending')
                            @can('admin')
                                <div class="mt-3 flex gap-2">
                                    <form action="{{ route('admin.bookings.approve', $b) }}" method="post" class="inline">
                                        @csrf
                                        <button class="nb-btn border-2 border-ink bg-brand-lime px-3 py-1 text-xs" type="submit">Approve</button>
                                    </form>
                                    <form action="{{ route('admin.bookings.decline', $b) }}" method="post" class="inline">
                                        @csrf
                                        <button class="nb-btn border-2 border-ink bg-rose-400 px-3 py-1 text-xs" type="submit">Decline</button>
                                    </form>
                                </div>
                            @endcan
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="border-t-2 border-ink bg-paper px-5 py-3">
                <span class="text-sm font-bold text-ink/50">View all bookings &rarr;</span>
            </div>
        </section>

        <section class="nb-card">
            <div class="border-b-2 border-ink bg-brand-sky px-5 py-4">
                <h2 class="text-lg font-black text-ink">Recent Transactions</h2>
            </div>
            <div class="divide-y-2 divide-ink/10">
                @foreach($transactions->take(5) as $t)
                    <div class="p-4 flex items-center justify-between">
                        <div>
                            <p class="font-bold text-ink">{{ $t->user->name ?? 'n/a' }}</p>
                            <p class="text-sm font-semibold text-ink/70">{{ $t->user->email ?? 'n/a' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-black text-ink">{{ $t->amount }} {{ $t->currency }}</p>
                            <span class="nb-badge {{ $t->status === 'SUCCESS' ? 'bg-brand-lime' : 'bg-brand-yellow' }} line-clamp-1">{{ $t->status }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        
        <section class="nb-card">
            <div class="border-b-2 border-ink bg-brand-purple px-5 py-4">
                <h2 class="text-lg font-black text-ink">Mentoring Rooms</h2>
            </div>
            <div class="divide-y-2 divide-ink/10">
                @foreach($rooms->take(5) as $r)
                    <div class="p-4 flex items-center justify-between">
                        <div>
                            <p class="font-bold text-ink">{{ $r->title }}</p>
                            <p class="text-sm font-semibold text-ink/70">mentor: {{ $r->mentor->name ?? 'n/a' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</div>
@endsection
