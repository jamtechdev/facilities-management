<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class RouteHelper
{
    /**
     * Get the route prefix based on user's role
     * Role-based access - SuperAdmin gets 'superadmin' prefix, Admin gets 'admin' prefix
     */
    public static function getRoutePrefix(): string
    {
        $user = Auth::user();

        if (!$user) {
            return 'admin';
        }

        // Get user's role
        $role = $user->roles->first();

        if ($role) {
            switch ($role->name) {
                case 'SuperAdmin':
                    return 'superadmin';
                case 'Admin':
                    return 'admin';
                case 'Staff':
                    return 'staff';
                case 'Client':
                    return 'client';
                case 'Lead':
                    return 'lead';
            }
        }

        // Fallback: Check dashboard permissions if no role assigned
        if ($user->can('view admin dashboard')) {
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
        try {
            $routeName = self::route($routeName);
            return route($routeName, $parameters);
        } catch (\Exception $e) {
            // Fallback: try superadmin first, then admin
            try {
                return route('superadmin.' . $routeName, $parameters);
            } catch (\Exception $e2) {
                try {
                    return route('admin.' . $routeName, $parameters);
                } catch (\Exception $e3) {
                    return '#';
                }
            }
        }
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

    /**
     * Get the view prefix based on current route
     * Determines which view folder to use based on the current route name
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

        // Check other route prefixes
        if (str_starts_with($routeName, 'staff.')) {
            return 'staff';
        }

        if (str_starts_with($routeName, 'client.')) {
            return 'client';
        }

        if (str_starts_with($routeName, 'lead.')) {
            return 'lead';
        }

        // Default fallback - use route prefix if available
        $prefix = self::getRoutePrefix();
        return $prefix;
    }
}
