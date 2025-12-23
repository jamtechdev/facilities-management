<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    /**
     * Handle an incoming request.
     *
     * Usage: middleware('permission:view leads') or middleware('permission:view leads|create leads')
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permissions  Pipe-separated permissions (user needs at least one)
     */
    public function handle(Request $request, Closure $next, string $permissions): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Parse permissions (can be single permission or pipe-separated for OR logic)
        $requiredPermissions = array_map('trim', explode('|', $permissions));

        // Check if user has any of the required permissions
        $hasPermission = false;
        foreach ($requiredPermissions as $permission) {
            if ($user->can($permission)) {
                $hasPermission = true;
                break;
            }
        }

        // If user doesn't have required permission, redirect to their dashboard or abort
        if (!$hasPermission) {
            // For AJAX/API requests, return JSON response
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to perform this action. Required permission(s): ' . implode(', ', $requiredPermissions)
                ], 403);
            }

            // For regular requests, redirect with flash message based on dashboard permissions
            $dashboardRoute = 'admin.dashboard';
            if ($user->can('view admin dashboard')) {
                if ($user->can('view roles')) {
                    $dashboardRoute = 'superadmin.dashboard';
                } else {
                    $dashboardRoute = 'admin.dashboard';
                }
            } elseif ($user->can('view staff dashboard')) {
                $dashboardRoute = 'staff.dashboard';
            } elseif ($user->can('view client dashboard')) {
                $dashboardRoute = 'client.dashboard';
            } elseif ($user->can('view lead dashboard')) {
                $dashboardRoute = 'lead.dashboard';
            }

            return redirect()->route($dashboardRoute)->with('error', 'You do not have permission to access this page. Required permission(s): ' . implode(', ', $requiredPermissions));
        }

        return $next($request);
    }
}

