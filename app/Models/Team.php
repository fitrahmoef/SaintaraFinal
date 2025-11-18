<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'department',
        'position',
        'avatar',
        'salary',
        'commission',
        'status',
        'join_date',
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'commission' => 'decimal:2',
        'join_date' => 'date',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
