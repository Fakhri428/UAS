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
            {{-- Sidebar: Daftar Obrolan --}}
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
                                       class="block p-4 rounded-xl border-2 border-ink hover:bg-brand-yellow/20 transition shadow-nb-sm {{ $conversation->id === $conv->id ? 'bg-brand-yellow/30' : 'bg-white' }}">
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
                                <p class="font-black text-lg text-ink mb-2">Belum ada obrolan</p>
                                <p class="text-ink/70 font-semibold">Mulai chat dengan user di Koukan Match!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Main: Chat Area --}}
            <div class="lg:col-span-2">
                <div class="nb-card flex flex-col h-[700px]">
                    @php
                        $otherUser = $conversation->getOtherUser(Auth::user());
                    @endphp

                    {{-- Chat Header --}}
                    <div class="border-b-2 border-ink bg-brand-purple px-6 py-4 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-brand-purple font-black text-lg border-2 border-ink shadow-nb-sm">
                            {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <p class="font-black text-xl text-white">{{ $otherUser->name }}</p>
                            <p class="text-sm text-white/80 font-semibold">Online (simulasi)</p>
                        </div>
                    </div>

                    {{-- Exchange Request Banner --}}
                    @if ($exchangeRequest)
                        <div class="border-b-2 border-ink bg-brand-lime px-6 py-4">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div>
                                    <p class="font-black text-ink text-base">
                                        <i class="fas fa-exchange-alt mr-2"></i>Exchange Request
                                    </p>
                                    <p class="text-ink/70 font-semibold text-sm mt-1">
                                        Status: 
                                        <span class="nb-badge
                                            @if($exchangeRequest->status === 'pending') bg-brand-yellow @endif
                                            @if($exchangeRequest->status === 'accepted') bg-brand-sky @endif
                                            @if($exchangeRequest->status === 'in_progress') bg-brand-purple text-white @endif
                                            @if($exchangeRequest->status === 'completed') bg-brand-lime @endif
                                            @if($exchangeRequest->status === 'reviewed') bg-white @endif
                                            @if($exchangeRequest->status === 'rejected') bg-brand-pink @endif
                                        ">
                                            {{ ucfirst(str_replace('_', ' ', $exchangeRequest->status)) }}
                                        </span>
                                    </p>
                                </div>
                                
                                <div class="flex gap-2">
                                    @if (in_array($exchangeRequest->status, ['accepted', 'in_progress']))
                                        <form action="{{ route('chat.exchange.action', $conversation) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="start">
                                            <button type="submit" class="nb-btn nb-btn-primary">
                                                <i class="fas fa-play mr-1"></i>Mulai
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('chat.exchange.action', $conversation) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="complete">
                                            <button type="submit" class="nb-btn nb-btn-yellow">
                                                <i class="fas fa-check mr-1"></i>Selesai
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if ($exchangeRequest->status === 'completed' || $exchangeRequest->status === 'reviewed')
                                        <a href="{{ route('dashboard') }}#review-section" class="nb-btn nb-btn-primary">
                                            <i class="fas fa-star mr-1"></i>Beri Review
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Messages Area --}}
                    <div id="messages-container" class="flex-1 overflow-y-auto p-6 space-y-4 bg-paper">
                        @if ($messages->count() > 0)
                            @foreach ($messages as $msg)
                                @if ($msg->type === 'system')
                                    <div class="flex justify-center">
                                        <div class="nb-badge bg-brand-yellow text-sm px-4 py-2">
                                            {{ $msg->content }}
                                        </div>
                                    </div>
                                @elseif ($msg->type === 'progress')
                                    <div class="flex {{ $msg->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                                        <div class="max-w-[80%]">
                                            <div class="px-5 py-4 rounded-xl border-2 border-ink shadow-nb-sm bg-brand-lime">
                                                <p class="font-black text-ink mb-2 text-sm">
                                                    <i class="fas fa-chart-line mr-2"></i>Progress Update
                                                </p>
                                                <p class="font-semibold text-ink">{{ $msg->metadata['progress_note'] ?? $msg->content }}</p>
                                                @if (!empty($msg->metadata['file_url']))
                                                    <a href="{{ $msg->metadata['file_url'] }}" target="_blank" class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-white border-2 border-ink rounded-lg font-black text-sm hover:bg-brand-yellow/30 transition shadow-nb-sm">
                                                        <i class="fas fa-file-alt"></i>
                                                        Lihat File
                                                    </a>
                                                @endif
                                            </div>
                                            <p class="text-xs mt-2 {{ $msg->sender_id === Auth::id() ? 'text-right text-ink/60' : 'text-left text-ink/50' }} font-semibold">
                                                {{ $msg->created_at->format('H:i') }}
                                                @if ($msg->sender_id === Auth::id())
                                                    <i class="fas fa-check-double ml-1 {{ $msg->is_read ? 'text-brand-sky' : 'text-ink/30' }}"></i>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex {{ $msg->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                                        <div class="max-w-[75%]">
                                            <div class="px-5 py-3 rounded-xl border-2 border-ink shadow-nb-sm {{ $msg->sender_id === Auth::id() ? 'bg-brand-yellow text-ink' : 'bg-white text-ink' }}">
                                                <p class="font-semibold">{{ $msg->content }}</p>
                                            </div>
                                            <p class="text-xs mt-2 {{ $msg->sender_id === Auth::id() ? 'text-right text-ink/60' : 'text-left text-ink/50' }} font-semibold">
                                                {{ $msg->created_at->format('H:i') }}
                                                @if ($msg->sender_id === Auth::id())
                                                    <i class="fas fa-check-double ml-1 {{ $msg->is_read ? 'text-brand-sky' : 'text-ink/30' }}"></i>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="text-center py-16">
                                <i class="fas fa-hand-wave text-6xl text-brand-yellow mb-6"></i>
                                <p class="font-black text-2xl text-ink mb-3">Yuk Mulai Obrolan!</p>
                                <p class="text-ink/70 font-semibold">Kirim pesan pertama ke {{ $otherUser->name }} untuk mulai exchange!</p>
                            </div>
                        @endif
                    </div>

                    {{-- Progress Input --}}
                    @if ($exchangeRequest && in_array($exchangeRequest->status, ['accepted', 'in_progress']))
                        <div class="border-t-2 border-ink bg-brand-purple/10 px-6 py-4">
                            <form action="{{ route('chat.progress.store', $conversation) }}" method="POST" class="flex gap-3">
                                @csrf
                                <input type="text" name="progress_note" 
                                       placeholder="Kirim update progress..." 
                                       class="nb-input flex-1"
                                       required>
                                <input type="url" name="file_url" 
                                       placeholder="Link file (opsional)" 
                                       class="nb-input w-1/4">
                                <button type="submit" class="nb-btn nb-btn-yellow">
                                    <i class="fas fa-upload mr-1"></i>Progress
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- Message Input --}}
                    <div class="border-t-2 border-ink bg-white p-4">
                        <form action="{{ route('chat.messages.store', $conversation) }}" method="POST" class="flex gap-3">
                            @csrf
                            <input type="text" name="content" 
                                   placeholder="Ketik pesan..." 
                                   class="nb-input flex-1"
                                   required autofocus>
                            <button type="submit" class="nb-btn nb-btn-primary">
                                <i class="fas fa-paper-plane mr-1"></i>Kirim
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const container = document.getElementById('messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    </script>
</x-app-layout>