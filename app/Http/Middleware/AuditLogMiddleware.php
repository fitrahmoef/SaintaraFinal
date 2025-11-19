<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AuditLogger;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    /**
     * Sensitive routes that should be audited.
     *
     * @var array
     */
    protected $sensitiveRoutes = [
        'personal.tokens.purchase',
        'payment.notification',
        'payment.cancel',
        'admin.users.store',
        'admin.users.update',
        'admin.users.destroy',
        'admin.transactions.update-status',
        'admin.packages.store',
        'admin.packages.update',
        'admin.packages.destroy',
        'personal.tests.session.submit',
        'personal.profile.update',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();

        // Check if this route should be audited
        $shouldAudit = $this->shouldAuditRoute($routeName, $request);

        // Process the request
        $response = $next($request);

        // Log after successful response
        if ($shouldAudit && $response->isSuccessful()) {
            $this->logRequest($request, $routeName);
        }

        return $response;
    }

    /**
     * Determine if the route should be audited.
     *
     * @param string|null $routeName
     * @param Request $request
     * @return bool
     */
    protected function shouldAuditRoute(?string $routeName, Request $request): bool
    {
        if (!$routeName) {
            return false;
        }

        // Check if route is in sensitive routes list
        if (in_array($routeName, $this->sensitiveRoutes)) {
            return true;
        }

        // Also audit all POST/PUT/DELETE requests to admin routes
        if (str_starts_with($routeName, 'admin.') &&
            in_array($request->method(), ['POST', 'PUT', 'DELETE'])) {
            return true;
        }

        return false;
    }

    /**
     * Log the request details.
     *
     * @param Request $request
     * @param string $routeName
     * @return void
     */
    protected function logRequest(Request $request, string $routeName): void
    {
        $action = $this->getActionName($routeName, $request);
        $module = $this->getModuleName($routeName);
        $description = $this->getDescription($routeName, $request);
        $properties = $this->getProperties($request, $routeName);

        AuditLogger::log(
            action: $action,
            module: $module,
            description: $description,
            properties: $properties,
            level: $this->getLogLevel($routeName)
        );
    }

    /**
     * Get action name from route.
     *
     * @param string $routeName
     * @param Request $request
     * @return string
     */
    protected function getActionName(string $routeName, Request $request): string
    {
        // Extract action from route name (e.g., 'admin.users.store' -> 'user_created')
        $parts = explode('.', $routeName);
        $action = end($parts);

        $actionMap = [
            'store' => 'created',
            'update' => 'updated',
            'destroy' => 'deleted',
            'purchase' => 'purchased',
            'submit' => 'submitted',
            'notification' => 'payment_notification',
            'cancel' => 'cancelled',
        ];

        $suffix = $actionMap[$action] ?? $action;
        $entity = $parts[count($parts) - 2] ?? 'resource';

        return "{$entity}_{$suffix}";
    }

    /**
     * Get module name from route.
     *
     * @param string $routeName
     * @return string
     */
    protected function getModuleName(string $routeName): string
    {
        if (str_starts_with($routeName, 'admin.')) {
            return 'admin';
        }
        if (str_starts_with($routeName, 'personal.')) {
            return 'personal';
        }
        if (str_starts_with($routeName, 'payment.')) {
            return 'payment';
        }
        if (str_contains($routeName, 'token')) {
            return 'token';
        }
        if (str_contains($routeName, 'test')) {
            return 'test';
        }

        return 'general';
    }

    /**
     * Get description for the log entry.
     *
     * @param string $routeName
     * @param Request $request
     * @return string
     */
    protected function getDescription(string $routeName, Request $request): string
    {
        $user = $request->user();
        $userName = $user?->name ?? 'System';

        return "{$userName} performed action: {$routeName}";
    }

    /**
     * Get properties to log (sanitized request data).
     *
     * @param Request $request
     * @param string $routeName
     * @return array
     */
    protected function getProperties(Request $request, string $routeName): array
    {
        $data = $request->all();

        // Remove sensitive fields
        $sensitiveFields = ['password', 'password_confirmation', 'token', 'secret', 'api_key'];
        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '[REDACTED]';
            }
        }

        return [
            'route' => $routeName,
            'method' => $request->method(),
            'data' => $data,
            'user_id' => $request->user()?->id,
        ];
    }

    /**
     * Get log level based on route sensitivity.
     *
     * @param string $routeName
     * @return string
     */
    protected function getLogLevel(string $routeName): string
    {
        // Critical operations
        $criticalRoutes = [
            'payment.notification',
            'admin.users.destroy',
            'personal.tokens.purchase',
        ];

        if (in_array($routeName, $criticalRoutes)) {
            return 'warning';
        }

        return 'info';
    }
}
