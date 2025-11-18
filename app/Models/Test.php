<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Test extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama_tes',
        'deskripsi_tes',
        'jenis_tes',
        'jumlah_soal',
        'durasi_menit',
        'token_required',
        'metadata',
        'is_active',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'jumlah_soal' => 'integer',
        'durasi_menit' => 'integer',
        'token_required' => 'integer',
    ];

    public function testResults(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
