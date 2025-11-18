<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestResult extends Model
{
    protected $fillable = [
        'user_id',
        'character_type_id',
        'test_type',
        'answers',
        'score',
        'institution_name',
        'test_date',
    ];

    protected $casts = [
        'answers' => 'array',
        'test_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function characterType(): BelongsTo
    {
        return $this->belongsTo(CharacterType::class);
    }
}
