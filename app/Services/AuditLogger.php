<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    /**
     * Log an activity with automatic request context.
     *
     * @param string $action
     * @param string $module
     * @param string|null $description
     * @param array $properties
     * @param string $level
     * @param mixed $user
     * @return ActivityLog
     */
    public static function log(
        string $action,
        string $module,
        ?string $description = null,
        array $properties = [],
        string $level = 'info',
        $user = null
    ): ActivityLog {
        $user = $user ?? Auth::user();
        $request = request();

        return ActivityLog::create([
            'user_type' => $user ? get_class($user) : null,
            'user_id' => $user?->id,
            'action' => $action,
            'description' => $description,
            'module' => $module,
            'ip_address' => self::getIpAddress($request),
            'user_agent' => $request?->userAgent(),
            'request_url' => $request?->fullUrl(),
            'request_method' => $request?->method(),
            'properties' => $properties,
            'log_level' => $level,
        ]);
    }

    /**
     * Log authentication activities.
     *
     * @param string $action
     * @param string|null $description
     * @param array $properties
     * @param mixed $user
     * @return ActivityLog
     */
    public static function auth(
        string $action,
        ?string $description = null,
        array $properties = [],
        $user = null
    ): ActivityLog {
        return self::log($action, 'auth', $description, $properties, 'info', $user);
    }

    /**
     * Log payment/transaction activities.
     *
     * @param string $action
     * @param string|null $description
     * @param array $properties
     * @param string $level
     * @return ActivityLog
     */
    public static function payment(
        string $action,
        ?string $description = null,
        array $properties = [],
        string $level = 'info'
    ): ActivityLog {
        return self::log($action, 'payment', $description, $properties, $level);
    }

    /**
     * Log token purchase/usage activities.
     *
     * @param string $action
     * @param string|null $description
     * @param array $properties
     * @return ActivityLog
     */
    public static function token(
        string $action,
        ?string $description = null,
        array $properties = []
    ): ActivityLog {
        return self::log($action, 'token', $description, $properties);
    }

    /**
     * Log test-related activities.
     *
     * @param string $action
     * @param string|null $description
     * @param array $properties
     * @return ActivityLog
     */
    public static function test(
        string $action,
        ?string $description = null,
        array $properties = []
    ): ActivityLog {
        return self::log($action, 'test', $description, $properties);
    }

    /**
     * Log admin operations.
     *
     * @param string $action
     * @param string|null $description
     * @param array $properties
     * @param string $level
     * @return ActivityLog
     */
    public static function admin(
        string $action,
        ?string $description = null,
        array $properties = [],
        string $level = 'info'
    ): ActivityLog {
        return self::log($action, 'admin', $description, $properties, $level);
    }

    /**
     * Log security-related events.
     *
     * @param string $action
     * @param string|null $description
     * @param array $properties
     * @param string $level
     * @return ActivityLog
     */
    public static function security(
        string $action,
        ?string $description = null,
        array $properties = [],
        string $level = 'warning'
    ): ActivityLog {
        return self::log($action, 'security', $description, $properties, $level);
    }

    /**
     * Log critical errors or security violations.
     *
     * @param string $action
     * @param string|null $description
     * @param array $properties
     * @return ActivityLog
     */
    public static function critical(
        string $action,
        ?string $description = null,
        array $properties = []
    ): ActivityLog {
        return self::log($action, 'security', $description, $properties, 'critical');
    }

    /**
     * Get the real IP address from the request.
     *
     * @param Request|null $request
     * @return string|null
     */
    protected static function getIpAddress(?Request $request): ?string
    {
        if (!$request) {
            return null;
        }

        // Check for IP in various headers (for proxies/load balancers)
        $ipHeaders = [
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR',
        ];

        foreach ($ipHeaders as $header) {
            $ip = $request->server($header);
            if ($ip) {
                // X-Forwarded-For might contain multiple IPs, take the first one
                if (str_contains($ip, ',')) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                return $ip;
            }
        }

        return $request->ip();
    }
}
