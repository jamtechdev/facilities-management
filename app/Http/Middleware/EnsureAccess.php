<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Unified Middleware for Authentication, Role Access, and Permission Checks
 *
 * This middleware handles:
 * 1. Authentication check - ensures user is logged in
 * 2. Role-based route access - prevents cross-role access (e.g., Staff can't access Client routes)
 * 3. Permission checks - validates user has required permissions (optional parameter)
 * 4. Dashboard routing - redirects users to their appropriate dashboard
 *
 * Usage:
 * - middleware('access') - Only checks authentication
 * - middleware('access:view leads') - Checks authentication and permission
 * - middleware('access:view leads|create leads') - Checks authentication and any of the permissions (OR logic)
 */
class EnsureAccess
{

    /**
     * Route prefixes mapped to their allowed roles
     * This prevents users from accessing routes of other roles
     * Each role can ONLY access their own routes
     */
    private array $roleRouteMap = [
        'superadmin' => ['SuperAdmin'],
        'admin' => ['Admin'], // Only Admin can access admin routes
        'staff' => ['Staff'],
        'client' => ['Client'],
        'lead' => ['Lead'],
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $permissions  Optional: Pipe-separated permissions (user needs at least one)
     */
    public function handle(Request $request, Closure $next, ?string $permissions = null): Response
    {
        // 1. Check if user is authenticated
        if (!auth()->check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required. Please login to continue.'
                ], 401);
            }
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = auth()->user();
        $role = $user->roles->first();
        $routeName = $request->route()?->getName() ?? '';
        $routePrefix = $this->getRoutePrefix($routeName);

        // 2. Check role-based route access (prevent cross-role access)
        // Each role can only access routes prefixed with their own role name
        if ($routePrefix && $role) {
            $allowedRoles = $this->roleRouteMap[$routePrefix] ?? [];

            // Check if user's role is allowed for this route prefix
            if (!empty($allowedRoles) && !in_array($role->name, $allowedRoles)) {
                return $this->denyAccess($request, "You do not have permission to access {$routePrefix} routes.");
            }
        }

        // 3. Check permissions if specified
        if ($permissions) {
            $requiredPermissions = array_map('trim', explode('|', $permissions));
            $hasPermission = false;

            foreach ($requiredPermissions as $permission) {
                if ($user->can($permission)) {
                    $hasPermission = true;
                    break;
                }
            }

            if (!$hasPermission) {
                $message = 'You do not have permission to perform this action. Required permission(s): ' . implode(', ', $requiredPermissions);
                return $this->denyAccess($request, $message);
            }
        }

        return $next($request);
    }

    /**
     * Extract route prefix from route name
     * e.g., 'admin.dashboard' -> 'admin', 'staff.timesheet' -> 'staff'
     */
    private function getRoutePrefix(string $routeName): ?string
    {
        if (empty($routeName)) {
            return null;
        }

        $parts = explode('.', $routeName);
        $prefix = $parts[0] ?? null;

        // Check if it's a valid role prefix
        return isset($this->roleRouteMap[$prefix]) ? $prefix : null;
    }

    /**
     * Deny access and return appropriate response
     */
    private function denyAccess(Request $request, string $message): Response
    {
        // For AJAX/API requests, return JSON response
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message
            ], 403);
        }

        // For regular requests, redirect to user's dashboard
        $dashboardRoute = $this->getDashboardRoute();
        return redirect()->route($dashboardRoute)->with('error', $message);
    }

    /**
     * Get the dashboard route name based on user's role or permissions
     *
     * @param \App\Models\User|null $user
     * @return string
     */
    protected function getDashboardRoute($user = null): string
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return 'login';
        }

        $role = $user->roles->first();

        // Redirect based on role (priority order)
        if ($role) {
            switch ($role->name) {
                case 'SuperAdmin':
                    return 'superadmin.dashboard';
                case 'Admin':
                    return 'admin.dashboard';
                case 'Staff':
                    return 'staff.dashboard';
                case 'Client':
                    return 'client.dashboard';
                case 'Lead':
                    return 'lead.dashboard';
            }
        }

        // Fallback: Redirect based on dashboard permissions if no role assigned
        if ($user->can('view admin dashboard')) {
            // Check if SuperAdmin by checking for 'view roles' permission
            if ($user->can('view roles')) {
                return 'superadmin.dashboard';
            }
            return 'admin.dashboard';
        } elseif ($user->can('view staff dashboard')) {
            return 'staff.dashboard';
        } elseif ($user->can('view client dashboard')) {
            return 'client.dashboard';
        } elseif ($user->can('view lead dashboard')) {
            return 'lead.dashboard';
        }

        return 'login';
    }

    /**
     * Redirect user to their appropriate dashboard
     *
     * @param \App\Models\User|null $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function redirectToDashboard($user = null)
    {
        $instance = new self();
        $dashboardRoute = $instance->getDashboardRoute($user);

        if ($dashboardRoute === 'login') {
            auth()->logout();
            return redirect()->route('login')->with('error', 'You do not have permission to access this system.');
        }

        return redirect()->route($dashboardRoute);
    }
}
