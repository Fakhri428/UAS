<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-ink">
                <i class="fas fa-comments mr-3"></i>Koukan Chat
            </h2>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div class="nb-card overflow-hidden">
                    <div class="border-b-2 border-ink bg-brand-purple px-5 py-4">
                        <h3 class="font-black text-lg text-white">Daftar Obrolan</h3>
                    </div>
                    <div class="p-4 bg-paper">
                        @if ($conversations->count() > 0)
                            <div class="space-y-3 max-h-[500px] overflow-y-auto">
                                @foreach ($conversations as $conv)
                                    @php
                                        $other = $conv->getOtherUser(Auth::user());
                                        $unread = $conv->unreadMessagesCount(Auth::user());
                                    @endphp
                                    <a href="{{ route('chat.show', $conv) }}" 
                                       class="block p-4 rounded-xl border-2 border-ink hover:bg-brand-yellow/20 transition shadow-nb-sm bg-white">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div class="w-12 h-12 rounded-full bg-brand-purple flex items-center justify-center text-white font-black text-lg border-2 border-ink flex-shrink-0 shadow-nb-sm">
                                                    {{ strtoupper(substr($other->name, 0, 1)) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="font-black text-ink truncate">{{ $other->name }}</p>
                                                    <p class="text-sm text-ink/70 truncate font-semibold">
                                                        {{ $conv->latestMessage?->content ?? 'Belum ada pesan' }}
                                                    </p>
                                                </div>
                                            </div>
                                            @if ($unread > 0)
                                                <span class="nb-badge bg-brand-pink flex-shrink-0">
                                                    {{ $unread }}
                                                </span>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="fas fa-comment-slash text-5xl text-brand-yellow mb-4"></i>
                                <p class="font-bold text-lg text-ink">Belum ada obrolan</p>
                                <p class="text-ink/70 mt-2 font-semibold">Mulai chat dengan user di Koukan Match!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="nb-card p-12 min-h-[400px] flex items-center justify-center bg-brand-sky/10">
                    <div class="text-center">
                        <i class="fas fa-hand-wave text-6xl text-brand-yellow mb-6"></i>
                        <p class="font-black text-3xl text-ink mb-3">Pilih Obrolan!</p>
                        <p class="text-lg text-ink/70 font-semibold">Pilih user di daftar obrolan atau mulai chat baru di Koukan Match!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
