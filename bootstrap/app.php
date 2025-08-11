<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Listeners\LogUserLogin;
use App\Listeners\LogUserLogout;
use App\Http\Middleware\ActivityLogMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            ActivityLogMiddleware::class,
        ]);
    })
    ->withEvents([
        Login::class => [LogUserLogin::class],
        Logout::class => [LogUserLogout::class],
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
