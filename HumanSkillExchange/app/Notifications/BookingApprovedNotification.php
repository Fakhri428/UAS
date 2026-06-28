<?php

namespace App\Notifications;

use App\Models\MentoringBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public MentoringBooking $booking
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $room = $this->booking->room;
        $mentor = $room->mentor;

        return (new MailMessage)
            ->subject('Booking Mentoring Disetujui')
            ->greeting("Halo {$notifiable->name},")
            ->line("Booking mentoring Anda untuk sesi \"{$room->title}\" telah disetujui oleh mentor {$mentor->name}.")
            ->line("Jadwal: " . optional($this->booking->scheduled_at)->format('d F Y H:i'))
            ->line("Durasi: {$this->booking->duration_minutes} menit")
            ->action('Lihat Detail', route('dashboard'))
            ->line('Terima kasih telah menggunakan platform kami.');
    }
}
