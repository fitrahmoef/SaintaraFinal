<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama_paket',
        'harga',
        'deskripsi',
        'tipe_paket',
        'jumlah_token',
        'masa_aktif_hari',
        'is_active',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'is_active' => 'boolean',
        'jumlah_token' => 'integer',
        'masa_aktif_hari' => 'integer',
    ];

    public function tokenPurchases(): HasMany
    {
        return $this->hasMany(TokenPurchase::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
