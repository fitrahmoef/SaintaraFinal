<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CharacterType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'strengths',
        'challenges',
        'communication_style',
        'image_path',
    ];

    protected $casts = [
        'strengths' => 'array',
        'challenges' => 'array',
    ];

    public function testResults(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }
}
