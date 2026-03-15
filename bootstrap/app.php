<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__.'/../routes/web.php',
            __DIR__.'/../routes/web_routes/auth/auth_routes.php',
        ],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Redireccionar usuarios no autenticados
        $middleware->redirectGuestsTo('/login');
        
        // Redireccionar usuarios autenticados al dashboard
        $middleware->redirectUsersTo('/dashboard');
        
        // Registrar aliases de middlewares
        $middleware->alias([
            'auth'      => \App\Http\Middleware\Authenticate::class,
            'guest'     => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'developer' => \App\Http\Middleware\DeveloperAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
