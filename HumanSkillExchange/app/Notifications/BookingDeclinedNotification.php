<?php

namespace App\Notifications;

use App\Models\MentoringBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingDeclinedNotification extends Notification implements ShouldQueue
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
            ->subject('Booking Mentoring Ditolak')
            ->greeting("Halo {$notifiable->name},")
            ->line("Sayangnya, booking mentoring Anda untuk sesi \"{$room->title}\" telah ditolak oleh mentor {$mentor->name}.")
            ->line('Silahkan coba booking sesi lain atau hubungi mentor untuk informasi lebih lanjut.')
            ->action('Lihat Sesi Lain', route('dashboard'))
            ->line('Terima kasih telah menggunakan platform kami.');
    }
}
