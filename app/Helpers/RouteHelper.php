<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class RouteHelper
{
    /**
     * Get the route prefix based on user's dashboard permission
     * Checks which dashboard permission user has and returns corresponding route prefix
     */
    public static function getRoutePrefix(): string
    {
        $user = Auth::user();

        if (!$user) {
            return 'admin'; // Default fallback
        }

        // Check dashboard permissions (priority order)
        if ($user->can('view admin dashboard')) {
            // Check if user can also access superadmin features (has all permissions)
            // SuperAdmin typically has all permissions
            if ($user->can('view roles')) {
                return 'superadmin';
            }
            return 'admin';
        }

        if ($user->can('view staff dashboard')) {
            return 'staff';
        }

        if ($user->can('view client dashboard')) {
            return 'client';
        }

        if ($user->can('view lead dashboard')) {
            return 'lead';
        }

        return 'admin'; // Default fallback
    }

    /**
     * Get the view prefix based on current route
     * Since admin folder only has dashboard/profile, both use superadmin views
     *
     * @return string
     */
    public static function getViewPrefix(): string
    {
        // Check current route name
        $routeName = request()->route()?->getName() ?? '';

        // If route starts with superadmin, use superadmin views
        if (str_starts_with($routeName, 'superadmin.')) {
            return 'superadmin';
        }

        // For admin routes, also use superadmin views (since admin folder doesn't have all views)
        // Only dashboard and profile use admin views
        if (str_starts_with($routeName, 'admin.')) {
            $routeParts = explode('.', $routeName);
            $resource = $routeParts[1] ?? '';

            // Dashboard and profile use admin views
            if (in_array($resource, ['dashboard', 'profile'])) {
                return 'admin';
            }

            // All other resources use superadmin views
            return 'superadmin';
        }

        return 'superadmin'; // Default fallback
    }

    /**
     * Generate a route name based on user's role
     *
     * @param string $routeName The route name without prefix (e.g., 'leads.index')
     * @return string Full route name (e.g., 'superadmin.leads.index' or 'admin.leads.index')
     */
    public static function route(string $routeName): string
    {
        $prefix = self::getRoutePrefix();
        return $prefix . '.' . $routeName;
    }

    /**
     * Generate a route URL based on user's role
     *
     * @param string $routeName The route name without prefix
     * @param mixed $parameters Route parameters
     * @return string Route URL
     */
    public static function url(string $routeName, $parameters = []): string
    {
        $routeName = self::route($routeName);
        return route($routeName, $parameters);
    }

    /**
     * Check if current route matches the route pattern
     *
     * @param string $routeName Route name without prefix
     * @return bool
     */
    public static function routeIs(string $routeName): bool
    {
        $prefix = self::getRoutePrefix();
        return request()->routeIs($prefix . '.' . $routeName);
    }

    /**
     * Check if current route matches any of the route patterns (admin.* or superadmin.*)
     *
     * @param string $routeName Route name without prefix
     * @return bool
     */
    public static function routeIsAny(string $routeName): bool
    {
        return request()->routeIs('admin.' . $routeName) || request()->routeIs('superadmin.' . $routeName);
    }
}

