<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'package_id',
        'payment_gateway_id',
        'kode_transaksi',
        'jumlah_bayar',
        'status_pembayaran',
        'metode_pembayaran',
        'gateway_transaction_id',
        'payment_url',
        'payment_metadata',
        'waktu_dibuat',
        'waktu_dibayar',
        'waktu_kadaluarsa',
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'payment_metadata' => 'array',
        'waktu_dibuat' => 'datetime',
        'waktu_dibayar' => 'datetime',
        'waktu_kadaluarsa' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function paymentGateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class);
    }

    public function tokenPurchase(): HasOne
    {
        return $this->hasOne(TokenPurchase::class);
    }

    public function isPaid(): bool
    {
        return $this->status_pembayaran === 'dibayar';
    }

    public function isPending(): bool
    {
        return $this->status_pembayaran === 'pending';
    }

    public function isExpired(): bool
    {
        return $this->status_pembayaran === 'kadaluarsa'
            || ($this->waktu_kadaluarsa && now()->gt($this->waktu_kadaluarsa));
    }

    public static function generateKodeTransaksi(): string
    {
        $year = now()->year;
        $lastTransaction = self::whereYear('waktu_dibuat', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastTransaction ? ((int) substr($lastTransaction->kode_transaksi, -5)) + 1 : 1;

        return sprintf('TRX-%d-%05d', $year, $number);
    }
}
