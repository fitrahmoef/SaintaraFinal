<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\TestResult;

class TestCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $testResult;

    public function __construct(TestResult $testResult)
    {
        $this->testResult = $testResult;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Hasil Tes Anda Sudah Tersedia')
            ->greeting('Halo ' . $notifiable->nama_lengkap . '!')
            ->line('Terima kasih telah menyelesaikan tes **' . $this->testResult->test->nama_tes . '**')
            ->line('Hasil tes Anda sudah tersedia dan dapat dilihat di dashboard.')
            ->line('**Detail Hasil:**')
            ->line('Tipe Karakter Dominan: ' . ($this->testResult->tipe_karakter_dominan ?? 'Sedang diproses'))
            ->line('Tanggal Tes: ' . $this->testResult->tanggal_tes->format('d F Y H:i'))
            ->action('Lihat Hasil Lengkap', url('/dashboard/results/' . $this->testResult->id))
            ->line('Anda dapat mengunduh sertifikat dari halaman hasil tes.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'test_completed',
            'test_result_id' => $this->testResult->id,
            'test_name' => $this->testResult->test->nama_tes,
            'character_type' => $this->testResult->tipe_karakter_dominan,
            'message' => 'Hasil tes ' . $this->testResult->test->nama_tes . ' sudah tersedia',
        ];
    }
}
