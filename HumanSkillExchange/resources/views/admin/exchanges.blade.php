@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-ink">Exchanges</h1>
            <p class="mt-1 text-sm font-semibold text-ink/60">Pantau permintaan barter skill antar user.</p>
        </div>
        <span class="nb-badge bg-brand-lime">{{ $exchanges->total() }} request</span>
    </div>

    <div class="nb-card overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-sm">
                <thead class="border-b-2 border-ink bg-brand-purple text-xs font-black uppercase tracking-wide text-ink">
                    <tr>
                        <th class="px-4 py-3">Dari → Ke</th>
                        <th class="px-4 py-3">Koukan Offer / Koukan Need</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Progress</th>
                        <th class="px-4 py-3">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-ink/10">
                    @forelse ($exchanges as $ex)
                        <tr class="align-middle">
                            <td class="px-4 py-3">
                                <p class="font-black text-ink">{{ $ex->fromUser->name ?? '—' }}</p>
                                <p class="text-xs font-bold text-ink/50">→ {{ $ex->toUser->name ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                @if ($ex->offer)
                                    <span class="nb-badge bg-brand-sky">Offer</span>
                                    <span class="block mt-1 text-xs font-semibold text-ink/70">{{ $ex->offer->title }}</span>
                                @endif
                                @if ($ex->need)
                                    <span class="nb-badge bg-brand-yellow">Need</span>
                                    <span class="block mt-1 text-xs font-semibold text-ink/70">{{ $ex->need->title }}</span>
                                @endif
                                @if (!$ex->offer && !$ex->need)
                                    <span class="text-xs font-bold text-ink/40">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3"><x-admin-status :status="$ex->status" /></td>
                            <td class="px-4 py-3">
                                <div class="flex gap-1">
                                    <span class="nb-badge {{ $ex->completed_by_from_user ? 'bg-brand-lime' : 'bg-white' }}">From</span>
                                    <span class="nb-badge {{ $ex->completed_by_to_user ? 'bg-brand-lime' : 'bg-white' }}">To</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs font-semibold text-ink/60">{{ $ex->created_at?->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-sm font-semibold text-ink/50">Belum ada exchange request.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">{{ $exchanges->links() }}</div>
@endsection
