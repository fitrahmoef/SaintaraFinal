<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TokenPurchase extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'transaction_id',
        'package_id',
        'kode_token',
        'jumlah_token',
        'jumlah_terpakai',
        'status',
        'tanggal_pembelian',
        'tanggal_kadaluarsa',
    ];

    protected $casts = [
        'jumlah_token' => 'integer',
        'jumlah_terpakai' => 'integer',
        'tanggal_pembelian' => 'datetime',
        'tanggal_kadaluarsa' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function tokenUsages(): HasMany
    {
        return $this->hasMany(TokenUsage::class);
    }

    public function remainingTokens(): int
    {
        return $this->jumlah_token - $this->jumlah_terpakai;
    }

    public function isExpired(): bool
    {
        return $this->tanggal_kadaluarsa && now()->gt($this->tanggal_kadaluarsa);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'aktif'
            && $this->remainingTokens() > 0
            && !$this->isExpired();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'aktif')
            ->where('jumlah_terpakai', '<', 'jumlah_token')
            ->where(function($q) {
                $q->whereNull('tanggal_kadaluarsa')
                    ->orWhere('tanggal_kadaluarsa', '>', now());
            });
    }

    public static function generateKodeToken(): string
    {
        $year = now()->year;
        $lastToken = self::whereYear('tanggal_pembelian', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastToken ? ((int) substr($lastToken->kode_token, -5)) + 1 : 1;

        return sprintf('TKN-%d-%05d', $year, $number);
    }
}
