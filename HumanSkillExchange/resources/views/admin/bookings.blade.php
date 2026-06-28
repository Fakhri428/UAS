@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-ink">Monitoring Booking</h1>
            <p class="mt-1 text-sm font-semibold text-ink/60">Approve atau decline booking mentoring.</p>
        </div>
        <span class="nb-badge bg-brand-yellow">{{ $bookings->total() }} booking</span>
    </div>

    <div class="nb-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-sm">
                <thead class="border-b-2 border-ink bg-brand-purple">
                    <tr>
                        <th class="px-5 py-3 text-xs font-black uppercase tracking-wide text-white">Room</th>
                        <th class="px-5 py-3 text-xs font-black uppercase tracking-wide text-white">User</th>
                        <th class="px-5 py-3 text-xs font-black uppercase tracking-wide text-white">Jadwal</th>
                        <th class="px-5 py-3 text-xs font-black uppercase tracking-wide text-white">Status</th>
                        <th class="px-5 py-3 text-xs font-black uppercase tracking-wide text-white text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-ink/10 bg-white">
                    @forelse ($bookings as $b)
                        <tr class="align-middle hover:bg-brand-yellow/10 transition">
                            <td class="px-5 py-4">
                                <p class="font-black text-ink">{{ $b->room->title ?? '—' }}</p>
                                <p class="text-xs font-semibold text-ink/60 mt-1">mentor: {{ $b->room->mentor->name ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-bold text-ink">{{ $b->user->name ?? '—' }}</p>
                                <p class="text-xs font-semibold text-ink/60">{{ $b->user->email ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm font-semibold text-ink/70">{{ $b->scheduled_at?->format('d M Y H:i') ?? '—' }}</td>
                            <td class="px-5 py-4"><x-admin-status :status="$b->status" /></td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    @if ($b->status === 'pending')
                                        <form method="POST" action="{{ route('admin.bookings.approve', $b) }}">
                                            @csrf
                                            <button type="submit" class="nb-btn nb-btn-primary text-xs">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.bookings.decline', $b) }}">
                                            @csrf
                                            <button type="submit" class="nb-btn nb-btn-pink text-xs">Decline</button>
                                        </form>
                                    @else
                                        <span class="text-xs font-bold text-ink/40">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-sm font-semibold text-ink/50">Belum ada booking.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">{{ $bookings->links() }}</div>
@endsection
