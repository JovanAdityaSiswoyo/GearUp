<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle unauthenticated access to protected routes
        $exceptions->render(function (Throwable $e, $request) {
            // Handle authentication exception for all guards
            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                // For JSON requests (API), return JSON response
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Unauthenticated'
                    ], 401);
                }
                
                // For HTML requests, show the custom 401 page
                return response()->view('errors.401', [], 401);
            }
        });
    })->create();
