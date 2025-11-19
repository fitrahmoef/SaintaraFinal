<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TokenExpiringNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $expiringTokensCount;
    protected $daysRemaining;

    public function __construct($expiringTokensCount, $daysRemaining)
    {
        $this->expiringTokensCount = $expiringTokensCount;
        $this->daysRemaining = $daysRemaining;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Pengingat: Token Anda Akan Segera Kadaluarsa')
            ->greeting('Halo ' . $notifiable->nama_lengkap . '!')
            ->line('Anda memiliki **' . $this->expiringTokensCount . ' token** yang akan kadaluarsa dalam **' . $this->daysRemaining . ' hari**.')
            ->line('Segera gunakan token Anda sebelum kadaluarsa untuk mengikuti tes yang tersedia.')
            ->action('Gunakan Token Sekarang', url('/dashboard/tests'))
            ->line('Jika ada pertanyaan, jangan ragu untuk menghubungi kami.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'token_expiring',
            'tokens_count' => $this->expiringTokensCount,
            'days_remaining' => $this->daysRemaining,
            'message' => $this->expiringTokensCount . ' token akan kadaluarsa dalam ' . $this->daysRemaining . ' hari',
        ];
    }
}
