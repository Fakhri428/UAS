@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-ink">Bookings</h1>
            <p class="mt-1 text-sm font-semibold text-ink/60">Approve atau decline booking mentoring.</p>
        </div>
        <span class="nb-badge bg-brand-yellow">{{ $bookings->total() }} booking</span>
    </div>

    <div class="nb-card overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-sm">
                <thead class="border-b-2 border-ink bg-brand-purple text-xs font-black uppercase tracking-wide text-ink">
                    <tr>
                        <th class="px-4 py-3">Room</th>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">Jadwal</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-ink/10">
                    @forelse ($bookings as $b)
                        <tr class="align-middle">
                            <td class="px-4 py-3">
                                <p class="font-black text-ink">{{ $b->room->title ?? '—' }}</p>
                                <p class="text-xs font-semibold text-ink/60">mentor: {{ $b->room->mentor->name ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3 font-bold text-ink/80">{{ $b->user->email ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs font-semibold text-ink/60">{{ $b->scheduled_at?->format('d M Y H:i') ?? '—' }}</td>
                            <td class="px-4 py-3"><x-admin-status :status="$b->status" /></td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    @if ($b->status === 'pending')
                                        <form method="POST" action="{{ route('admin.bookings.approve', $b) }}">
                                            @csrf
                                            <button type="submit" class="nb-btn nb-btn-primary">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.bookings.decline', $b) }}">
                                            @csrf
                                            <button type="submit" class="nb-btn nb-btn-pink">Decline</button>
                                        </form>
                                    @else
                                        <span class="text-xs font-bold text-ink/40">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-sm font-semibold text-ink/50">Belum ada booking.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">{{ $bookings->links() }}</div>
@endsection
