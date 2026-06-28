<?php

namespace App\Notifications;

use App\Models\ExchangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExchangeAcceptedNotification extends Notification implements ShouldQueue
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
        $otherUser = (int) $this->exchange->from_user_id === (int) $notifiable->id
            ? $this->exchange->toUser
            : $this->exchange->fromUser;

        return (new MailMessage)
            ->subject('Exchange Request Diterima')
            ->greeting("Halo {$notifiable->name},")
            ->line("{$otherUser->name} telah menerima exchange request Anda!")
            ->line("Penawaran: " . ($this->exchange->offer?->title ?? 'N/A'))
            ->line("Kebutuhan: " . ($this->exchange->need?->title ?? 'N/A'))
            ->action('Lihat Detail', route('dashboard'))
            ->line('Silahkan mulai berkomunikasi dan atur jadwal kolaborasi.')
            ->line('Semoga kolaborasi ini sukses!');
    }
}
