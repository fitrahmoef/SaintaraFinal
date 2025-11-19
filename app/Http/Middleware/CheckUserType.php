<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $type  The required user type (personal, admin, instansi, superadmin, gift)
     */
    public function handle(Request $request, Closure $next, string $type): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user_type is null or empty - redirect to login if so
        if (empty($user->user_type)) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'User type not set. Please contact administrator.');
        }

        // Validate that user_type is one of the allowed values
        if (!in_array($user->user_type, ['personal', 'admin', 'instansi', 'gift', 'superadmin'])) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Invalid user type. Please contact administrator.');
        }

        // Superadmin can access all dashboards
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if ($user->user_type !== $type) {
            // Redirect to appropriate dashboard based on user's actual type
            $dashboardRoute = $user->user_type . '.dashboard';

            // Verify the route exists before redirecting
            if (\Illuminate\Support\Facades\Route::has($dashboardRoute)) {
                return redirect()->route($dashboardRoute);
            }

            // If route doesn't exist, logout and redirect to login
            auth()->logout();
            return redirect()->route('login')->with('error', 'Invalid user type. Please contact administrator.');
        }

        return $next($request);
    }
}
