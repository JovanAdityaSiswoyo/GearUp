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
        // Kecualikan webhook Midtrans dari CSRF — request datang dari server Midtrans
        $middleware->validateCsrfTokens(except: [
            'webhooks/midtrans',
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, $request) {
            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Unauthenticated'
                    ], 401);
                }

                return response()->view('errors.401', [], 401);
            }
        });
    })->create();