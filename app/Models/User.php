<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Permission;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Gate;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'namapanggilan',
        'email',
        'notelp',
        'negara',
        'kota',
        'password',
        // 'user_type', // SECURITY: Removed to prevent privilege escalation via mass assignment
    ];

    /**
     * Set the user type (protected method)
     * Only use this in controlled contexts (registration, admin operations)
     */
    public function setUserType(string $type): void
    {
        if (!in_array($type, ['personal', 'admin', 'instansi', 'gift', 'superadmin'])) {
            throw new \InvalidArgumentException('Invalid user type');
        }
        $this->user_type = $type;
    }

    /**
     * Get the user's role as an enum
     */
    public function getRole(): ?Role
    {
        if (empty($this->user_type)) {
            return null;
        }

        return Role::tryFrom($this->user_type);
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(Role $role): bool
    {
        return $this->user_type === $role->value;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($role instanceof Role && $this->hasRole($role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user is superadmin
     */
    public function isSuperAdmin(): bool
    {
        return $this->user_type === Role::SUPERADMIN->value;
    }

    /**
     * Check if user is admin or superadmin
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole([Role::ADMIN, Role::SUPERADMIN]);
    }

    /**
     * Check if user is instansi
     */
    public function isInstansi(): bool
    {
        return $this->user_type === Role::INSTANSI->value;
    }

    /**
     * Check if user is personal
     */
    public function isPersonal(): bool
    {
        return $this->user_type === Role::PERSONAL->value;
    }

    /**
     * Check if user is gift user
     */
    public function isGift(): bool
    {
        return $this->user_type === Role::GIFT->value;
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(Permission $permission): bool
    {
        return Gate::forUser($this)->allows($permission->value);
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($permission instanceof Permission && $this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($permission instanceof Permission && !$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get the user's dashboard route based on their role
     */
    public function getDashboardRoute(): string
    {
        $role = $this->getRole();

        if (!$role) {
            return '/personal/dashboardPersonal'; // Default fallback
        }

        return $role->dashboardRoute();
    }

    /**
     * Check if this user has higher authority than another user
     */
    public function hasHigherAuthorityThan(User $user): bool
    {
        $myRole = $this->getRole();
        $theirRole = $user->getRole();

        if (!$myRole || !$theirRole) {
            return false;
        }

        return $myRole->isHigherThan($theirRole);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function testResults(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }

    // DEPRECATED: Old Token model - use customer->tokenPurchases instead
    // public function tokens(): HasMany
    // {
    //     return $this->hasMany(Token::class);
    // }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function latestTestResult()
    {
        return $this->testResults()->latest()->with('characterType')->first();
    }
}
