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
    ->withMiddleware(function (Middleware $middleware) {
        // Aliases de middleware
        $middleware->alias([
              'role' => \App\Http\Middleware\CheckRole::class,
            'account.status' => \App\Http\Middleware\CheckAccountStatus::class,
            'permission' => \App\Http\Middleware\CheckPermission::class, // ✅ NUEVO
    ]);
        // Aplicar a todas las rutas web
        $middleware->web(append: [
            \App\Http\Middleware\CheckAccountStatus::class,
        ]);
        
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();