<?php

namespace App\Providers;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class RolePermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->defineRolePermissions();
        $this->defineGates();
    }

    /**
     * Define role to permission mappings
     */
    private function defineRolePermissions(): void
    {
        // Superadmin has ALL permissions
        Gate::before(function (User $user, string $ability) {
            if ($user->isSuperAdmin()) {
                return true; // Superadmin bypasses all permission checks
            }
        });
    }

    /**
     * Define authorization gates for each permission
     */
    private function defineGates(): void
    {
        // User Management Permissions
        Gate::define(Permission::VIEW_USERS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        Gate::define(Permission::CREATE_USERS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        Gate::define(Permission::UPDATE_USERS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        Gate::define(Permission::DELETE_USERS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        Gate::define(Permission::MANAGE_USER_TYPES->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        // Test Management Permissions
        Gate::define(Permission::VIEW_TESTS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        Gate::define(Permission::CREATE_TESTS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        Gate::define(Permission::UPDATE_TESTS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        Gate::define(Permission::DELETE_TESTS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        Gate::define(Permission::TAKE_TESTS->value, function (User $user) {
            return $user->hasAnyRole([Role::PERSONAL, Role::GIFT]);
        });

        Gate::define(Permission::VIEW_ALL_TEST_RESULTS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN, Role::INSTANSI]);
        });

        // Package Management Permissions
        Gate::define(Permission::VIEW_PACKAGES->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN, Role::PERSONAL, Role::INSTANSI]);
        });

        Gate::define(Permission::CREATE_PACKAGES->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        Gate::define(Permission::UPDATE_PACKAGES->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        Gate::define(Permission::DELETE_PACKAGES->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        // Transaction Management Permissions
        Gate::define(Permission::VIEW_TRANSACTIONS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN, Role::PERSONAL, Role::INSTANSI]);
        });

        Gate::define(Permission::CREATE_TRANSACTIONS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN, Role::PERSONAL, Role::INSTANSI]);
        });

        Gate::define(Permission::UPDATE_TRANSACTIONS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        Gate::define(Permission::DELETE_TRANSACTIONS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        Gate::define(Permission::APPROVE_TRANSACTIONS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        // Token Management Permissions
        Gate::define(Permission::VIEW_TOKENS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN, Role::PERSONAL, Role::INSTANSI, Role::GIFT]);
        });

        Gate::define(Permission::PURCHASE_TOKENS->value, function (User $user) {
            return $user->hasAnyRole([Role::PERSONAL, Role::INSTANSI]);
        });

        Gate::define(Permission::MANAGE_ALL_TOKENS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        // Institution Management Permissions
        Gate::define(Permission::MANAGE_EMPLOYEES->value, function (User $user) {
            return $user->hasAnyRole([Role::INSTANSI, Role::ADMIN]);
        });

        Gate::define(Permission::BULK_UPLOAD_EMPLOYEES->value, function (User $user) {
            return $user->hasAnyRole([Role::INSTANSI, Role::ADMIN]);
        });

        Gate::define(Permission::VIEW_INSTITUTION_REPORTS->value, function (User $user) {
            return $user->hasAnyRole([Role::INSTANSI, Role::ADMIN]);
        });

        // Team Management Permissions
        Gate::define(Permission::VIEW_TEAMS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN, Role::PERSONAL]);
        });

        Gate::define(Permission::CREATE_TEAMS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN, Role::PERSONAL]);
        });

        Gate::define(Permission::UPDATE_TEAMS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        Gate::define(Permission::DELETE_TEAMS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        Gate::define(Permission::APPROVE_TEAMS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        // Agenda Management Permissions
        Gate::define(Permission::VIEW_AGENDAS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN, Role::PERSONAL]);
        });

        Gate::define(Permission::CREATE_AGENDAS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        Gate::define(Permission::UPDATE_AGENDAS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        Gate::define(Permission::DELETE_AGENDAS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        // Dashboard Access Permissions
        Gate::define(Permission::ACCESS_ADMIN_DASHBOARD->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        Gate::define(Permission::ACCESS_PERSONAL_DASHBOARD->value, function (User $user) {
            return $user->hasAnyRole([Role::PERSONAL, Role::GIFT]);
        });

        Gate::define(Permission::ACCESS_INSTANSI_DASHBOARD->value, function (User $user) {
            return $user->hasAnyRole([Role::INSTANSI]);
        });

        // Profile & Results Permissions
        Gate::define(Permission::UPDATE_OWN_PROFILE->value, function (User $user) {
            return true; // All authenticated users can update their own profile
        });

        Gate::define(Permission::VIEW_OWN_RESULTS->value, function (User $user) {
            return $user->hasAnyRole([Role::PERSONAL, Role::GIFT, Role::INSTANSI]);
        });

        Gate::define(Permission::DOWNLOAD_CERTIFICATES->value, function (User $user) {
            return $user->hasAnyRole([Role::PERSONAL, Role::GIFT]);
        });

        // System Administration Permissions
        Gate::define(Permission::VIEW_AUDIT_LOGS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        Gate::define(Permission::MANAGE_SYSTEM_SETTINGS->value, function (User $user) {
            return $user->hasAnyRole([Role::ADMIN]);
        });

        Gate::define(Permission::ACCESS_ALL_FEATURES->value, function (User $user) {
            return $user->isSuperAdmin();
        });
    }
}
