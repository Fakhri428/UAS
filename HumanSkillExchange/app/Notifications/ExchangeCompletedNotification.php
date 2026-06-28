<?php

namespace App\Notifications;

use App\Models\ExchangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExchangeCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ExchangeRequest $exchange
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Exchange Selesai')
            ->greeting("Halo {$notifiable->name},")
            ->line('Exchange Anda telah selesai. Kedua pihak telah mengkonfirmasi penyelesaian.')
            ->line('Sekarang Anda dapat memberikan review dan rating kepada partner Anda.')
            ->action('Beri Review', route('dashboard'))
            ->line('Terima kasih telah menggunakan platform kami untuk kolaborasi.');
    }
}
