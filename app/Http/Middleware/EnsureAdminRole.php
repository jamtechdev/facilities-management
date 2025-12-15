<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminRole
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

        // Redirect other roles to their dashboards
        if ($user->hasRole('SuperAdmin')) {
            return redirect()->route('superadmin.dashboard');
        }

        if ($user->hasRole('Staff')) {
            return redirect()->route('staff.dashboard');
        }

        if ($user->hasRole('Client')) {
            return redirect()->route('client.dashboard');
        }

        if ($user->hasRole('Lead')) {
            return redirect()->route('lead.dashboard');
        }

        if (!$user->hasAnyRole(['Admin', 'SuperAdmin'])) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
