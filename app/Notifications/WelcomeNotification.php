<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Selamat Datang di Saintara Platform')
            ->greeting('Halo ' . $notifiable->nama_lengkap . '!')
            ->line('Selamat datang di **Saintara Platform** - Platform Tes Karakter dan Kepribadian.')
            ->line('Akun Anda telah berhasil dibuat dan siap digunakan.')
            ->line('**Langkah Selanjutnya:**')
            ->line('1. Lengkapi profil Anda')
            ->line('2. Beli paket token untuk mengikuti tes')
            ->line('3. Mulai perjalanan mengenal diri Anda lebih dalam')
            ->action('Mulai Sekarang', url('/dashboard'))
            ->line('Terima kasih telah bergabung dengan kami!');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'welcome',
            'message' => 'Selamat datang di Saintara Platform!',
        ];
    }
}
