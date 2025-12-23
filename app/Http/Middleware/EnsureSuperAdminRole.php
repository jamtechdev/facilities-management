<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Check if user has view roles permission (SuperAdmin typically has this)
        if (!$user->can('view roles')) {
            // For AJAX/API requests, return JSON response
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to access this area. Required permission: view roles'
                ], 403);
            }
            
            // For regular requests, redirect with flash message based on dashboard permissions
            $dashboardRoute = 'admin.dashboard';
            if ($user->can('view admin dashboard')) {
                $dashboardRoute = 'admin.dashboard';
            } elseif ($user->can('view staff dashboard')) {
                $dashboardRoute = 'staff.dashboard';
            } elseif ($user->can('view client dashboard')) {
                $dashboardRoute = 'client.dashboard';
            } elseif ($user->can('view lead dashboard')) {
                $dashboardRoute = 'lead.dashboard';
            }
            
            return redirect()->route($dashboardRoute)->with('error', 'You do not have permission to access this area. Required permission: view roles');
        }

        return $next($request);
    }
}

