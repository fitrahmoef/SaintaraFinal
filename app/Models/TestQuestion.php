<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestQuestion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'test_id',
        'nomor_soal',
        'pertanyaan',
        'tipe_soal',
        'pilihan_jawaban',
        'bobot_karakter',
        'is_active',
    ];

    protected $casts = [
        'pilihan_jawaban' => 'array',
        'bobot_karakter' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the test that owns the question
     */
    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }
}
