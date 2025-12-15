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
        $middleware->alias([
            'role.superadmin' => \App\Http\Middleware\EnsureSuperAdminRole::class,
            'role.admin' => \App\Http\Middleware\EnsureAdminRole::class,
            'role.staff' => \App\Http\Middleware\EnsureStaffRole::class,
            'role.client' => \App\Http\Middleware\EnsureClientRole::class,
            'role.lead' => \App\Http\Middleware\EnsureLeadRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
