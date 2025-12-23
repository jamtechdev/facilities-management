<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
    // Single permission-based middleware - manages all access by permissions
    $middleware->alias([
        'permission' => \App\Http\Middleware\EnsurePermission::class,
        'role.superadmin' => \App\Http\Middleware\EnsureSuperAdminRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
    // Handle 403 Forbidden errors with custom message
    $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, \Illuminate\Http\Request $request) {
        if ($request->expectsJson() || $request->ajax()) {
            $message = $e->getMessage();
            // If message is the default Laravel message, provide a better one
            if (empty($message) || $message === 'This action is unauthorized.') {
                $message = 'You do not have permission to perform this action. Please contact your administrator if you need this access.';
            }
            return response()->json([
                'success' => false,
                'message' => $message
            ], 403);
        }

        // For web requests, redirect to dashboard with error message based on permissions
        $user = auth()->user();
        if ($user) {
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

            $message = $e->getMessage();
            if (empty($message) || $message === 'This action is unauthorized.') {
                $message = 'You do not have permission to access this page. Please contact your administrator if you need this access.';
            }
            return redirect()->route($dashboardRoute)->with('error', $message);
        }

        $message = $e->getMessage();
        if (empty($message) || $message === 'This action is unauthorized.') {
            $message = 'You do not have permission to access this page. Please contact your administrator if you need this access.';
        }
        return redirect()->route('login')->with('error', $message);
    });
    })->create();
