<?php

namespace App\Http\Middleware;

use App\Enums\Permission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Superadmin bypasses all permission checks
        if ($request->user()->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user has any of the required permissions
        foreach ($permissions as $permissionString) {
            $permission = Permission::tryFrom($permissionString);

            if ($permission && Gate::allows($permission->value)) {
                return $next($request);
            }
        }

        // User doesn't have required permissions
        abort(403, 'Unauthorized action.');
    }
}
