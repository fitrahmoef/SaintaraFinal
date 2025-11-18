<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TokenUsage extends Model
{
    protected $table = 'token_usage';

    protected $fillable = [
        'token_purchase_id',
        'test_result_id',
        'jumlah_digunakan',
        'keterangan',
        'tanggal_penggunaan',
    ];

    protected $casts = [
        'jumlah_digunakan' => 'integer',
        'tanggal_penggunaan' => 'datetime',
    ];

    public function tokenPurchase(): BelongsTo
    {
        return $this->belongsTo(TokenPurchase::class);
    }

    public function testResult(): BelongsTo
    {
        return $this->belongsTo(TestResult::class);
    }
}
