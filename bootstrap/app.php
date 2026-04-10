<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Даёт возможность сохранять сессию для SPA
        $middleware->statefulApi();
        // Для API-запросов вывод только в JSON (редиректы не нужны)
        $middleware->redirectGuestsTo(function (Request $request) {
            return $request->is('api/*')
                ? null
                : route('login')
            ;
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
