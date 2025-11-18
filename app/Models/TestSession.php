<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TestSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'test_id',
        'token_purchase_id',
        'session_token',
        'status',
        'jawaban',
        'current_question',
        'waktu_mulai',
        'waktu_selesai',
        'waktu_expired',
        'token_locked',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'jawaban' => 'array',
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'waktu_expired' => 'datetime',
        'token_locked' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function tokenPurchase()
    {
        return $this->belongsTo(TokenPurchase::class);
    }

    /**
     * Scopes
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'in_progress')
                    ->where(function($q) {
                        $q->whereNull('waktu_expired')
                          ->orWhere('waktu_expired', '>', now());
                    });
    }

    /**
     * Generate unique session token
     */
    public static function generateSessionToken(): string
    {
        do {
            $token = 'TST-' . strtoupper(Str::random(20));
        } while (self::where('session_token', $token)->exists());

        return $token;
    }

    /**
     * Check if session is expired
     */
    public function isExpired(): bool
    {
        if (!$this->waktu_expired) {
            return false;
        }

        return now()->isAfter($this->waktu_expired);
    }

    /**
     * Check if session is still active
     */
    public function isActive(): bool
    {
        return $this->status === 'in_progress' && !$this->isExpired();
    }

    /**
     * Mark session as completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'waktu_selesai' => now(),
            'token_locked' => false,
        ]);
    }

    /**
     * Mark session as abandoned
     */
    public function markAsAbandoned(): void
    {
        $this->update([
            'status' => 'abandoned',
            'token_locked' => false,
        ]);
    }

    /**
     * Mark session as expired
     */
    public function markAsExpired(): void
    {
        $this->update([
            'status' => 'expired',
            'token_locked' => false,
        ]);
    }

    /**
     * Update progress
     */
    public function updateProgress(int $questionIndex, array $jawaban): void
    {
        $this->update([
            'current_question' => $questionIndex,
            'jawaban' => $jawaban,
        ]);
    }

    /**
     * Calculate remaining time in seconds
     */
    public function getRemainingTimeAttribute(): int
    {
        if (!$this->waktu_expired) {
            return 0;
        }

        $remaining = now()->diffInSeconds($this->waktu_expired, false);
        return max(0, $remaining);
    }

    /**
     * Calculate progress percentage
     */
    public function getProgressPercentageAttribute(): float
    {
        if (!$this->test) {
            return 0;
        }

        $totalQuestions = $this->test->jumlah_soal;
        if ($totalQuestions === 0) {
            return 0;
        }

        return round(($this->current_question / $totalQuestions) * 100, 2);
    }
}
