@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-ink">Transactions</h1>
            <p class="mt-1 text-sm font-semibold text-ink/60">Pantau & selesaikan transaksi.</p>
        </div>
        <span class="nb-badge bg-brand-pink">{{ $transactions->total() }} transaksi</span>
    </div>

    <div class="nb-card overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-sm">
                <thead class="border-b-2 border-ink bg-brand-purple text-xs font-black uppercase tracking-wide text-ink">
                    <tr>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">Tipe</th>
                        <th class="px-4 py-3">Jumlah</th>
                        <th class="px-4 py-3">Metode</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-ink/10">
                    @forelse ($transactions as $t)
                        <tr class="align-middle">
                            <td class="px-4 py-3">
                                <p class="font-black text-ink">{{ $t->user->name ?? '—' }}</p>
                                <p class="text-xs font-semibold text-ink/60">{{ $t->user->email ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3 font-bold text-ink/80">{{ $t->type }}</td>
                            <td class="px-4 py-3">
                                <p class="font-black text-ink">Rp{{ number_format($t->amount, 0, ',', '.') }}</p>
                                @if ($t->platform_fee)
                                    <p class="text-xs font-semibold text-ink/60">fee Rp{{ number_format($t->platform_fee, 0, ',', '.') }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs font-semibold text-ink/70">{{ $t->payment_method ?? '—' }}</td>
                            <td class="px-4 py-3"><x-admin-status :status="$t->status" /></td>
                            <td class="px-4 py-3 text-right">
                                @if ($t->status !== 'completed')
                                    <form method="POST" action="{{ route('admin.transactions.complete', $t) }}">
                                        @csrf
                                        <button type="submit" class="nb-btn nb-btn-primary">Complete</button>
                                    </form>
                                @else
                                    <span class="text-xs font-bold text-ink/40">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-sm font-semibold text-ink/50">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">{{ $transactions->links() }}</div>
@endsection
