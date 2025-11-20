<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminInstansi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'admin_instansi';

    protected $fillable = [
        'user_id',
        'nama_admin',
        'nama_instansi',
        'nomor_telepon',
        'email_instansi',
        'alamat_instansi',
        'kota_instansi',
        'provinsi_instansi',
        'kode_pos',
        'status_akun',
        'tanggal_bergabung',
        'tanggal_berakhir',
        'catatan',
    ];

    protected $casts = [
        'tanggal_bergabung' => 'date',
        'tanggal_berakhir' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = [
        'is_active',
        'days_until_expiry',
    ];

    /**
     * Relationship: AdminInstansi belongs to a User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: AdminInstansi has many employees (users with parent_instansi_id)
     */
    public function employees(): HasMany
    {
        return $this->hasMany(User::class, 'parent_instansi_id', 'user_id');
    }

    /**
     * Relationship: AdminInstansi has many token purchases through the user
     */
    public function tokenPurchases()
    {
        return $this->hasMany(TokenPurchase::class, 'user_id', 'user_id');
    }

    /**
     * Relationship: AdminInstansi has many transactions through the user
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id', 'user_id');
    }

    /**
     * Get all test results for this institution (admin + all employees)
     */
    public function allTestResults()
    {
        $employeeIds = $this->employees()->pluck('id')->toArray();
        $allUserIds = array_merge([$this->user_id], $employeeIds);

        return TestResult::whereIn('user_id', $allUserIds);
    }

    /**
     * Scope: Active institutions
     */
    public function scopeActive($query)
    {
        return $query->where('status_akun', 'aktif');
    }

    /**
     * Scope: Pending institutions
     */
    public function scopePending($query)
    {
        return $query->where('status_akun', 'pending');
    }

    /**
     * Scope: Inactive institutions
     */
    public function scopeInactive($query)
    {
        return $query->where('status_akun', 'tidak_aktif');
    }

    /**
     * Scope: Search institutions
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nama_instansi', 'like', "%{$search}%")
                ->orWhere('nama_admin', 'like', "%{$search}%")
                ->orWhere('email_instansi', 'like', "%{$search}%")
                ->orWhere('kota_instansi', 'like', "%{$search}%");
        });
    }

    /**
     * Accessor: Check if institution is active
     */
    public function getIsActiveAttribute(): bool
    {
        if ($this->status_akun !== 'aktif') {
            return false;
        }

        if ($this->tanggal_berakhir && $this->tanggal_berakhir->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Accessor: Get days until expiry
     */
    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->tanggal_berakhir) {
            return null;
        }

        return now()->diffInDays($this->tanggal_berakhir, false);
    }

    /**
     * Get total employees count
     */
    public function getEmployeesCountAttribute(): int
    {
        return $this->employees()->count();
    }

    /**
     * Get active employees count
     */
    public function getActiveEmployeesCountAttribute(): int
    {
        return $this->employees()->where('status', 'active')->count();
    }

    /**
     * Get total token balance for institution
     */
    public function getTotalTokensAttribute(): int
    {
        return $this->user->customer->saldo_token ?? 0;
    }

    /**
     * Get institution statistics
     */
    public function getStatistics(): array
    {
        $employeeIds = $this->employees()->pluck('id')->toArray();
        $allUserIds = array_merge([$this->user_id], $employeeIds);

        return [
            'total_employees' => count($employeeIds),
            'active_employees' => $this->employees()->where('status', 'active')->count(),
            'total_tests_completed' => TestResult::whereIn('user_id', $allUserIds)->count(),
            'total_tokens_used' => TokenUsage::whereIn('user_id', $allUserIds)->sum('jumlah_token'),
            'total_tokens_purchased' => $this->tokenPurchases()->sum('jumlah_token'),
            'current_token_balance' => $this->total_tokens,
            'total_transactions' => $this->transactions()->count(),
            'total_revenue' => $this->transactions()->where('status', 'paid')->sum('amount'),
        ];
    }

    /**
     * Activate institution
     */
    public function activate(): void
    {
        $this->update([
            'status_akun' => 'aktif',
            'tanggal_bergabung' => $this->tanggal_bergabung ?? now(),
        ]);
    }

    /**
     * Deactivate institution
     */
    public function deactivate(): void
    {
        $this->update(['status_akun' => 'tidak_aktif']);
    }

    /**
     * Extend expiry date
     */
    public function extendExpiry(int $days): void
    {
        $currentExpiry = $this->tanggal_berakhir ?? now();
        $this->update([
            'tanggal_berakhir' => $currentExpiry->addDays($days),
        ]);
    }
}
