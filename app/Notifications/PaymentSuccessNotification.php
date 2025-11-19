<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Transaction;

class PaymentSuccessNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $transaction;
    protected $tokenPurchase;

    public function __construct(Transaction $transaction, $tokenPurchase)
    {
        $this->transaction = $transaction;
        $this->tokenPurchase = $tokenPurchase;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Pembayaran Berhasil - ' . $this->transaction->kode_transaksi)
            ->greeting('Halo ' . $notifiable->nama_lengkap . '!')
            ->line('Pembayaran Anda telah berhasil diproses.')
            ->line('**Detail Transaksi:**')
            ->line('Kode Transaksi: ' . $this->transaction->kode_transaksi)
            ->line('Paket: ' . $this->transaction->package->nama_paket)
            ->line('Jumlah Token: ' . $this->tokenPurchase->jumlah_token)
            ->line('Jumlah Bayar: Rp ' . number_format($this->transaction->jumlah_bayar, 0, ',', '.'))
            ->line('Tanggal Pembayaran: ' . $this->transaction->waktu_dibayar->format('d F Y H:i'))
            ->line('Token Anda sekarang sudah dapat digunakan untuk mengikuti tes.')
            ->action('Lihat Token Saya', url('/dashboard/tokens'))
            ->line('Terima kasih telah menggunakan layanan kami!');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'payment_success',
            'transaction_id' => $this->transaction->id,
            'kode_transaksi' => $this->transaction->kode_transaksi,
            'package_name' => $this->transaction->package->nama_paket,
            'amount' => $this->transaction->jumlah_bayar,
            'tokens' => $this->tokenPurchase->jumlah_token,
            'message' => 'Pembayaran untuk paket ' . $this->transaction->package->nama_paket . ' berhasil diproses',
        ];
    }
}
