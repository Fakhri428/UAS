<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-ink">
                <i class="fas fa-chalkboard-teacher mr-3"></i>Sesi Mentoring: {{ $booking->room->title }}
            </h2>
            <a href="{{ route('dashboard') }}" class="nb-btn nb-btn-white">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Info Kelas -->
            <div class="lg:col-span-2">
                <div class="nb-card overflow-hidden">
                    @if($booking->room->video_link)
                        <!-- Video Call Area -->
                        <div class="border-b-3 border-ink bg-brand-purple px-8 py-6">
                            <h3 class="font-black text-2xl text-white flex items-center gap-4">
                                <i class="fas fa-video text-2xl"></i>Area Video Call
                            </h3>
                        </div>
                        <div class="p-10 bg-gradient-to-br from-brand-purple/20 to-brand-sky/20">
                            <div class="bg-white border-3 border-ink rounded-2xl p-12 text-center mb-8 shadow-ink-lg">
                                <i class="fas fa-video text-9xl text-brand-purple mb-8"></i>
                                <p class="font-black text-3xl text-ink mb-4">Siap untuk Bergabung!</p>
                                <p class="text-lg text-ink/70 font-semibold">Klik tombol di bawah untuk memasuki sesi video call</p>
                            </div>
                            
                            <div class="text-center">
                                <a href="{{ $booking->room->video_link }}" target="_blank" class="nb-btn nb-btn-primary text-2xl px-12 py-6">
                                    <i class="fas fa-external-link-alt mr-3"></i>Bergabung ke Sesi Video
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="border-b-3 border-ink bg-brand-yellow px-8 py-6">
                            <h3 class="font-black text-2xl text-ink flex items-center gap-4">
                                <i class="fas fa-exclamation-circle text-2xl"></i>Link Sesi Belum Tersedia
                            </h3>
                        </div>
                        <div class="p-10 text-center bg-gradient-to-br from-brand-yellow/20 to-brand-pink/20">
                            <i class="fas fa-link-slash text-8xl text-brand-yellow mb-8"></i>
                            <p class="font-black text-2xl text-ink mb-6">Mentor belum menambahkan link video call. Silakan hubungi mentor atau cek kembali nanti!</p>
                            <a href="{{ route('chat.with', $booking->room->mentor) }}" class="nb-btn nb-btn-primary text-xl px-8 py-4">
                                <i class="fas fa-comments mr-2"></i>Chat dengan Mentor
                            </a>
                        </div>
                    @endif

                    <!-- Deskripsi Kelas -->
                    <div class="p-8 bg-white">
                        <h3 class="font-black text-2xl text-ink mb-6">Deskripsi Kelas</h3>
                        <p class="text-ink/80 text-lg leading-relaxed">{{ $booking->room->description }}</p>

                        <!-- Catatan Kelas -->
                        @if($booking->room->meeting_notes)
                            <div class="mt-10">
                                <h3 class="font-black text-2xl text-ink mb-6">
                                    <i class="fas fa-sticky-note mr-3"></i>Catatan Kelas
                                </h3>
                                <div class="bg-brand-yellow/20 rounded-2xl p-6 border-3 border-ink">
                                    <p class="text-ink/80 text-lg leading-relaxed whitespace-pre-wrap">{{ $booking->room->meeting_notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="space-y-6">
                <!-- Detail Sesi -->
                <div class="nb-card">
                    <div class="border-b-3 border-ink bg-brand-sky px-6 py-5">
                        <h3 class="font-black text-xl text-ink">
                            <i class="fas fa-info-circle mr-2"></i>Detail Sesi
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center gap-4">
                            <span class="text-ink/70 font-semibold text-lg"><i class="fas fa-user-tie mr-2"></i>Mentor:</span>
                            <span class="font-black text-xl text-ink">{{ $booking->room->mentor->name }}</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-ink/70 font-semibold text-lg"><i class="fas fa-calendar mr-2"></i>Jadwal:</span>
                            <span class="font-black text-xl text-ink">{{ $booking->scheduled_at?->format('d M Y, H:i') ?? 'Belum ditentukan' }}</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-ink/70 font-semibold text-lg"><i class="fas fa-clock mr-2"></i>Durasi:</span>
                            <span class="font-black text-xl text-ink">{{ $booking->duration_minutes }} menit</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-ink/70 font-semibold text-lg"><i class="fas fa-flag mr-2"></i>Status:</span>
                            <span class="nb-badge {{ 
                                $booking->session_status === 'scheduled' ? 'bg-brand-yellow' : 
                                ($booking->session_status === 'in_progress' ? 'bg-brand-purple text-white' : 
                                ($booking->session_status === 'completed' ? 'bg-brand-green text-white' : 'bg-brand-pink text-white')) 
                            }} text-lg">
                                {{ ucfirst(str_replace('_', ' ', $booking->session_status)) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Aksi untuk Mentor -->
                @if((int) $booking->room->mentor_id === Auth::id())
                    <div class="nb-card">
                        <div class="border-b-3 border-ink bg-brand-lime px-6 py-5">
                            <h3 class="font-black text-xl text-ink">
                                <i class="fas fa-sliders-h mr-2"></i>Kelola Kelas
                            </h3>
                        </div>
                        <div class="p-6">
                            <!-- Update Link & Catatan -->
                            <form action="{{ route('mentoring.room.update', $booking->room) }}" method="POST" class="space-y-4 mb-8">
                                @csrf
                                <div>
                                    <label class="block text-sm font-black text-ink mb-3 text-lg">Link Video Call</label>
                                    <input type="url" name="video_link" value="{{ old('video_link', $booking->room->video_link) }}" 
                                           placeholder="https://zoom.us/..." class="w-full nb-input text-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-black text-ink mb-3 text-lg">Catatan Kelas</label>
                                    <textarea name="meeting_notes" rows="4" placeholder="Tambahkan catatan untuk peserta..." 
                                              class="w-full nb-input text-lg">{{ old('meeting_notes', $booking->room->meeting_notes) }}</textarea>
                                </div>
                                <button type="submit" class="w-full nb-btn nb-btn-primary text-lg py-4">Simpan Perubahan</button>
                            </form>

                            <!-- Update Status Sesi -->
                            <form action="{{ route('mentoring.session.update-status', $booking) }}" method="POST" class="space-y-4">
                                @csrf
                                <label class="block text-sm font-black text-ink mb-3 text-lg">Ubah Status Sesi</label>
                                <select name="session_status" class="w-full nb-input text-lg" onchange="this.form.submit()">
                                    <option value="scheduled" {{ $booking->session_status === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                                    <option value="in_progress" {{ $booking->session_status === 'in_progress' ? 'selected' : '' }}>Berlangsung</option>
                                    <option value="completed" {{ $booking->session_status === 'completed' ? 'selected' : '' }}>Selesai</option>
                                    <option value="cancelled" {{ $booking->session_status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Tombol Chat dengan Mentor -->
                <div class="nb-card">
                    <div class="border-b-3 border-ink bg-brand-purple px-6 py-5">
                        <h3 class="font-black text-xl text-white">
                            <i class="fas fa-comments mr-2"></i>Hubungi Mentor
                        </h3>
                    </div>
                    <div class="p-6">
                        <a href="{{ route('chat.with', $booking->room->mentor) }}" class="w-full nb-btn nb-btn-primary flex items-center justify-center gap-2 text-lg py-4">
                            <i class="fas fa-comment-dots text-xl"></i>Mulai Chat
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>