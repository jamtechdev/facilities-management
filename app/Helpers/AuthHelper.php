<?php

namespace App\Helpers;

use App\Http\Middleware\EnsureAccess;

/**
 * Helper class for authentication-related functions
 * Can be used in routes and controllers
 * Uses the EnsureAccess middleware directly
 */
class AuthHelper
{
    /**
     * Get the dashboard route name for the current authenticated user
     *
     * @return string
     */
    public static function getDashboardRoute(): string
    {
        $middleware = new EnsureAccess();
        $reflection = new \ReflectionClass($middleware);
        $method = $reflection->getMethod('getDashboardRoute');
        $method->setAccessible(true);
        return $method->invoke($middleware);
    }

    /**
     * Redirect to dashboard
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function redirectToDashboard()
    {
        return EnsureAccess::redirectToDashboard();
    }
}
