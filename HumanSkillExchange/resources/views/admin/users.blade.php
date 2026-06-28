@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-ink">Users</h1>
            <p class="mt-1 text-sm font-semibold text-ink/60">Kelola & verifikasi pengguna.</p>
        </div>
        <span class="nb-badge bg-brand-sky">{{ $users->total() }} user</span>
    </div>

    <div class="nb-card overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-sm">
                <thead class="border-b-2 border-ink bg-brand-purple text-xs font-black uppercase tracking-wide text-ink">
                    <tr>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Plan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Daftar</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-ink/10">
                    @forelse ($users as $user)
                        <tr class="align-middle">
                            <td class="px-4 py-3">
                                <p class="font-black text-ink">{{ $user->name }}</p>
                                <p class="text-xs font-semibold text-ink/60">{{ $user->email }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="nb-badge {{ $user->role === 'admin' ? 'bg-brand-orange' : 'bg-white' }}">{{ ucfirst($user->role) }}</span>
                            </td>
                            <td class="px-4 py-3 font-bold text-ink/80">{{ $user->plan->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="nb-badge {{ $user->is_verified ? 'bg-brand-lime' : 'bg-brand-yellow' }}">
                                    {{ $user->is_verified ? 'Verified' : 'Belum' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs font-semibold text-ink/60">{{ $user->created_at?->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                @unless ($user->is_verified)
                                    <form method="POST" action="{{ route('admin.users.verify', $user) }}">
                                        @csrf
                                        <button type="submit" class="nb-btn nb-btn-primary">Verify</button>
                                    </form>
                                @else
                                    <span class="text-xs font-bold text-ink/40">—</span>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-sm font-semibold text-ink/50">Belum ada user.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">{{ $users->links() }}</div>
@endsection
