@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-ink">Reviews</h1>
            <p class="mt-1 text-sm font-semibold text-ink/60">Moderasi ulasan pengguna.</p>
        </div>
        <span class="nb-badge bg-brand-orange">{{ $reviews->total() }} review</span>
    </div>

    <div class="grid gap-4">
        @forelse ($reviews as $review)
            <article class="nb-card p-4 {{ $review->is_hidden ? 'bg-paper' : 'bg-white' }}">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-black text-ink">{{ $review->reviewer->name ?? '—' }}</span>
                            <span class="text-xs font-bold text-ink/50">→</span>
                            <span class="text-sm font-black text-ink">{{ $review->reviewedUser->name ?? '—' }}</span>
                        </div>
                        <div class="mt-1 flex items-center gap-2">
                            <span class="nb-badge bg-brand-yellow">{{ str_repeat('★', (int) $review->rating) }}{{ str_repeat('☆', max(0, 5 - (int) $review->rating)) }}</span>
                            @if ($review->is_hidden)
                                <span class="nb-badge bg-brand-pink">Hidden</span>
                            @endif
                        </div>
                    </div>

                    <div class="shrink-0">
                        @if ($review->is_hidden)
                            <form method="POST" action="{{ route('admin.reviews.unhide', $review) }}">
                                @csrf
                                <button type="submit" class="nb-btn nb-btn-white">Tampilkan</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.reviews.hide', $review) }}">
                                @csrf
                                <button type="submit" class="nb-btn nb-btn-pink">Sembunyikan</button>
                            </form>
                        @endif
                    </div>
                </div>

                <p class="mt-3 rounded-lg border-2 border-ink bg-brand-sky/20 p-3 text-sm font-medium leading-6 text-ink/80">
                    {{ $review->comment ?: '—' }}
                </p>
            </article>
        @empty
            <div class="nb-card bg-white px-4 py-8 text-center text-sm font-semibold text-ink/50">Belum ada review.</div>
        @endforelse
    </div>

    <div class="mt-5">{{ $reviews->links() }}</div>
@endsection
