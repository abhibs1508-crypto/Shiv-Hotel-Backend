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
    ->withMiddleware(function (Middleware $middleware) {
        
        // 1. Enables stateful authentication for your React frontend
        $middleware->statefulApi();

        // 2. Disables CSRF protection for API routes (since we use Bearer Tokens)
        $middleware->validateCsrfTokens(except: [
            'api/*', 
        ]);

        // 3. The Custom CORS Middleware (Only keep this line if you created the ForceCors file in the last step!)
        if (class_exists(\App\Http\Middleware\ForceCors::class)) {
            $middleware->append(\App\Http\Middleware\ForceCors::class);
        }

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();