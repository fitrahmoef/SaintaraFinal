<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'nama_panggilan',
        'nomor_telepon',
        'tanggal_lahir',
        'jenis_kelamin',
        'golongan_darah',
        'negara',
        'kota',
        'free_tokens_granted',
        'free_token_count',
        'free_tokens_granted_at',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'free_tokens_granted' => 'boolean',
        'free_token_count' => 'integer',
        'free_tokens_granted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tokenPurchases(): HasMany
    {
        return $this->hasMany(TokenPurchase::class);
    }

    public function testResults(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }
}
