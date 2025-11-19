<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class SecurityValidationProvider extends ServiceProvider
{
    /**
     * Bootstrap services and validate critical security configurations.
     *
     * @return void
     */
    public function boot(): void
    {
        // Only validate in production environment
        if (app()->environment('production')) {
            $this->validateProductionSecurity();
        }
    }

    /**
     * Validate critical security configurations for production.
     *
     * @return void
     * @throws \RuntimeException
     */
    protected function validateProductionSecurity(): void
    {
        $errors = [];

        // 1. Check DEBUG mode is disabled
        if (config('app.debug') === true) {
            $errors[] = 'APP_DEBUG must be set to false in production environment.';
        }

        // 2. Check database password is set
        $dbPassword = config('database.connections.' . config('database.default') . '.password');
        if (empty($dbPassword)) {
            $errors[] = 'Database password (DB_PASSWORD) must be set in production environment.';
        }

        // 3. Check Midtrans keys are not placeholder values
        $midtransServerKey = config('midtrans.server_key');
        $midtransClientKey = config('midtrans.client_key');

        if (empty($midtransServerKey) || $midtransServerKey === 'your-server-key-here') {
            $errors[] = 'MIDTRANS_SERVER_KEY must be set to a valid value (not placeholder).';
        }

        if (empty($midtransClientKey) || $midtransClientKey === 'your-client-key-here') {
            $errors[] = 'MIDTRANS_CLIENT_KEY must be set to a valid value (not placeholder).';
        }

        // 4. Check session encryption is enabled
        if (config('session.encrypt') !== true) {
            $errors[] = 'SESSION_ENCRYPT must be set to true in production for security.';
        }

        // 5. Check secure session cookies
        if (config('session.secure') !== true) {
            $errors[] = 'SESSION_SECURE_COOKIE must be set to true in production (requires HTTPS).';
        }

        // 6. Check session same_site is strict
        if (config('session.same_site') !== 'strict') {
            Log::warning('SESSION_SAME_SITE should be set to "strict" for maximum security.');
        }

        // 7. Check application key is set
        if (empty(config('app.key'))) {
            $errors[] = 'APP_KEY must be generated. Run: php artisan key:generate';
        }

        // If there are critical errors, throw exception
        if (!empty($errors)) {
            $errorMessage = "PRODUCTION SECURITY VALIDATION FAILED:\n\n" . implode("\n", $errors);
            $errorMessage .= "\n\nPlease fix these issues in your .env file before running in production.";

            Log::critical('Production security validation failed', ['errors' => $errors]);

            throw new \RuntimeException($errorMessage);
        }

        Log::info('Production security validation passed successfully.');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }
}
