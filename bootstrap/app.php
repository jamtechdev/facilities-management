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
        // Unified middleware for authentication, role access, and permissions
        $middleware->alias([
            'access' => \App\Http\Middleware\EnsureAccess::class,
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

        // For web requests, redirect to dashboard with error message based on role
        $user = auth()->user();
        if ($user) {
            $middleware = new \App\Http\Middleware\EnsureAccess();
            $reflection = new \ReflectionClass($middleware);
            $method = $reflection->getMethod('getDashboardRoute');
            $method->setAccessible(true);
            $dashboardRoute = $method->invoke($middleware, $user);

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
