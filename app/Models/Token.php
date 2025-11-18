<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Token extends Model
{
    protected $fillable = [
        'user_id',
        'package_type',
        'token_amount',
        'price',
        'payment_status',
        'payment_method',
        'payment_proof',
        'tokens_used',
        'expiry_date',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function remainingTokens(): int
    {
        return $this->token_amount - $this->tokens_used;
    }
}
