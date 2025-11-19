<?php

namespace App\Enums;

enum Permission: string
{
    // User Management
    case VIEW_USERS = 'view_users';
    case CREATE_USERS = 'create_users';
    case UPDATE_USERS = 'update_users';
    case DELETE_USERS = 'delete_users';
    case MANAGE_USER_TYPES = 'manage_user_types';

    // Test Management
    case VIEW_TESTS = 'view_tests';
    case CREATE_TESTS = 'create_tests';
    case UPDATE_TESTS = 'update_tests';
    case DELETE_TESTS = 'delete_tests';
    case TAKE_TESTS = 'take_tests';
    case VIEW_ALL_TEST_RESULTS = 'view_all_test_results';

    // Package Management
    case VIEW_PACKAGES = 'view_packages';
    case CREATE_PACKAGES = 'create_packages';
    case UPDATE_PACKAGES = 'update_packages';
    case DELETE_PACKAGES = 'delete_packages';

    // Transaction Management
    case VIEW_TRANSACTIONS = 'view_transactions';
    case CREATE_TRANSACTIONS = 'create_transactions';
    case UPDATE_TRANSACTIONS = 'update_transactions';
    case DELETE_TRANSACTIONS = 'delete_transactions';
    case APPROVE_TRANSACTIONS = 'approve_transactions';

    // Token Management
    case VIEW_TOKENS = 'view_tokens';
    case PURCHASE_TOKENS = 'purchase_tokens';
    case MANAGE_ALL_TOKENS = 'manage_all_tokens';

    // Institution Management
    case MANAGE_EMPLOYEES = 'manage_employees';
    case BULK_UPLOAD_EMPLOYEES = 'bulk_upload_employees';
    case VIEW_INSTITUTION_REPORTS = 'view_institution_reports';

    // Team Management
    case VIEW_TEAMS = 'view_teams';
    case CREATE_TEAMS = 'create_teams';
    case UPDATE_TEAMS = 'update_teams';
    case DELETE_TEAMS = 'delete_teams';
    case APPROVE_TEAMS = 'approve_teams';

    // Agenda Management
    case VIEW_AGENDAS = 'view_agendas';
    case CREATE_AGENDAS = 'create_agendas';
    case UPDATE_AGENDAS = 'update_agendas';
    case DELETE_AGENDAS = 'delete_agendas';

    // Dashboard Access
    case ACCESS_ADMIN_DASHBOARD = 'access_admin_dashboard';
    case ACCESS_PERSONAL_DASHBOARD = 'access_personal_dashboard';
    case ACCESS_INSTANSI_DASHBOARD = 'access_instansi_dashboard';

    // Profile Management
    case UPDATE_OWN_PROFILE = 'update_own_profile';
    case VIEW_OWN_RESULTS = 'view_own_results';
    case DOWNLOAD_CERTIFICATES = 'download_certificates';

    // System Administration
    case VIEW_AUDIT_LOGS = 'view_audit_logs';
    case MANAGE_SYSTEM_SETTINGS = 'manage_system_settings';
    case ACCESS_ALL_FEATURES = 'access_all_features'; // Superadmin only

    /**
     * Get permission label for display
     */
    public function label(): string
    {
        return ucwords(str_replace('_', ' ', $this->value));
    }

    /**
     * Get permission description
     */
    public function description(): string
    {
        return match($this) {
            self::VIEW_USERS => 'View user list and details',
            self::CREATE_USERS => 'Create new users',
            self::UPDATE_USERS => 'Update user information',
            self::DELETE_USERS => 'Delete users from system',
            self::MANAGE_USER_TYPES => 'Change user roles/types',

            self::VIEW_TESTS => 'View test list and questions',
            self::CREATE_TESTS => 'Create new tests and questions',
            self::UPDATE_TESTS => 'Update existing tests',
            self::DELETE_TESTS => 'Delete tests from system',
            self::TAKE_TESTS => 'Take psychological tests',
            self::VIEW_ALL_TEST_RESULTS => 'View all users test results',

            self::VIEW_PACKAGES => 'View token packages',
            self::CREATE_PACKAGES => 'Create new token packages',
            self::UPDATE_PACKAGES => 'Update existing packages',
            self::DELETE_PACKAGES => 'Delete packages',

            self::VIEW_TRANSACTIONS => 'View transaction history',
            self::CREATE_TRANSACTIONS => 'Create new transactions',
            self::UPDATE_TRANSACTIONS => 'Update transaction details',
            self::DELETE_TRANSACTIONS => 'Delete transactions',
            self::APPROVE_TRANSACTIONS => 'Approve/reject transactions',

            self::VIEW_TOKENS => 'View token balance',
            self::PURCHASE_TOKENS => 'Purchase token packages',
            self::MANAGE_ALL_TOKENS => 'Manage all users tokens',

            self::MANAGE_EMPLOYEES => 'Manage institution employees',
            self::BULK_UPLOAD_EMPLOYEES => 'Upload employees via CSV',
            self::VIEW_INSTITUTION_REPORTS => 'View institution reports',

            self::VIEW_TEAMS => 'View team registrations',
            self::CREATE_TEAMS => 'Create team registrations',
            self::UPDATE_TEAMS => 'Update team information',
            self::DELETE_TEAMS => 'Delete team registrations',
            self::APPROVE_TEAMS => 'Approve/reject team requests',

            self::VIEW_AGENDAS => 'View event agendas',
            self::CREATE_AGENDAS => 'Create new agendas',
            self::UPDATE_AGENDAS => 'Update agenda details',
            self::DELETE_AGENDAS => 'Delete agendas',

            self::ACCESS_ADMIN_DASHBOARD => 'Access admin dashboard',
            self::ACCESS_PERSONAL_DASHBOARD => 'Access personal dashboard',
            self::ACCESS_INSTANSI_DASHBOARD => 'Access institution dashboard',

            self::UPDATE_OWN_PROFILE => 'Update own profile',
            self::VIEW_OWN_RESULTS => 'View own test results',
            self::DOWNLOAD_CERTIFICATES => 'Download certificates',

            self::VIEW_AUDIT_LOGS => 'View system audit logs',
            self::MANAGE_SYSTEM_SETTINGS => 'Manage system settings',
            self::ACCESS_ALL_FEATURES => 'Full system access (Superadmin)',
        };
    }

    /**
     * Group permissions by category
     */
    public static function grouped(): array
    {
        return [
            'User Management' => [
                self::VIEW_USERS,
                self::CREATE_USERS,
                self::UPDATE_USERS,
                self::DELETE_USERS,
                self::MANAGE_USER_TYPES,
            ],
            'Test Management' => [
                self::VIEW_TESTS,
                self::CREATE_TESTS,
                self::UPDATE_TESTS,
                self::DELETE_TESTS,
                self::TAKE_TESTS,
                self::VIEW_ALL_TEST_RESULTS,
            ],
            'Package Management' => [
                self::VIEW_PACKAGES,
                self::CREATE_PACKAGES,
                self::UPDATE_PACKAGES,
                self::DELETE_PACKAGES,
            ],
            'Transaction Management' => [
                self::VIEW_TRANSACTIONS,
                self::CREATE_TRANSACTIONS,
                self::UPDATE_TRANSACTIONS,
                self::DELETE_TRANSACTIONS,
                self::APPROVE_TRANSACTIONS,
            ],
            'Token Management' => [
                self::VIEW_TOKENS,
                self::PURCHASE_TOKENS,
                self::MANAGE_ALL_TOKENS,
            ],
            'Institution Management' => [
                self::MANAGE_EMPLOYEES,
                self::BULK_UPLOAD_EMPLOYEES,
                self::VIEW_INSTITUTION_REPORTS,
            ],
            'Team Management' => [
                self::VIEW_TEAMS,
                self::CREATE_TEAMS,
                self::UPDATE_TEAMS,
                self::DELETE_TEAMS,
                self::APPROVE_TEAMS,
            ],
            'Agenda Management' => [
                self::VIEW_AGENDAS,
                self::CREATE_AGENDAS,
                self::UPDATE_AGENDAS,
                self::DELETE_AGENDAS,
            ],
            'Dashboard Access' => [
                self::ACCESS_ADMIN_DASHBOARD,
                self::ACCESS_PERSONAL_DASHBOARD,
                self::ACCESS_INSTANSI_DASHBOARD,
            ],
            'Profile & Results' => [
                self::UPDATE_OWN_PROFILE,
                self::VIEW_OWN_RESULTS,
                self::DOWNLOAD_CERTIFICATES,
            ],
            'System Administration' => [
                self::VIEW_AUDIT_LOGS,
                self::MANAGE_SYSTEM_SETTINGS,
                self::ACCESS_ALL_FEATURES,
            ],
        ];
    }
}
