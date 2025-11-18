<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestResult extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'test_id',
        'customer_id',
        'token_purchase_id',
        'hasil_karakter',
        'deskripsi_hasil',
        'skor',
        'jawaban',
        'analisis',
        'tanggal_tes',
        'waktu_mulai',
        'waktu_selesai',
        'ip_address',
    ];

    protected $casts = [
        'jawaban' => 'array',
        'analisis' => 'array',
        'tanggal_tes' => 'datetime',
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'skor' => 'integer',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function tokenPurchase(): BelongsTo
    {
        return $this->belongsTo(TokenPurchase::class);
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }

    public function tokenUsage(): HasOne
    {
        return $this->hasOne(TokenUsage::class);
    }

    public function getDurationInMinutes(): ?int
    {
        if ($this->waktu_mulai && $this->waktu_selesai) {
            return (int) $this->waktu_mulai->diffInMinutes($this->waktu_selesai);
        }
        return null;
    }
}
