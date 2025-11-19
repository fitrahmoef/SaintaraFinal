<?php

namespace App\Enums;

enum Role: string
{
    case SUPERADMIN = 'superadmin';
    case ADMIN = 'admin';
    case INSTANSI = 'instansi';
    case PERSONAL = 'personal';
    case GIFT = 'gift';

    /**
     * Get all role values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get role label for display
     */
    public function label(): string
    {
        return match($this) {
            self::SUPERADMIN => 'Super Administrator',
            self::ADMIN => 'Administrator',
            self::INSTANSI => 'Instansi/Organization',
            self::PERSONAL => 'Personal User',
            self::GIFT => 'Gift User',
        };
    }

    /**
     * Get role hierarchy level (higher = more power)
     */
    public function level(): int
    {
        return match($this) {
            self::SUPERADMIN => 100,
            self::ADMIN => 50,
            self::INSTANSI => 30,
            self::PERSONAL => 10,
            self::GIFT => 5,
        };
    }

    /**
     * Check if this role has higher authority than another role
     */
    public function isHigherThan(Role $role): bool
    {
        return $this->level() > $role->level();
    }

    /**
     * Check if this role has equal or higher authority
     */
    public function isAtLeast(Role $role): bool
    {
        return $this->level() >= $role->level();
    }

    /**
     * Get dashboard route for this role
     */
    public function dashboardRoute(): string
    {
        return match($this) {
            self::SUPERADMIN => '/personal/dashboardPersonal', // Superadmin uses personal dashboard
            self::ADMIN => '/admin/dashboardAdmin',
            self::INSTANSI => '/instansi/dashboardInstansi',
            self::PERSONAL => '/personal/dashboardPersonal',
            self::GIFT => '/personal/dashboardPersonal',
        };
    }
}
