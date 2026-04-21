<?php

use App\Http\Middleware\EnsureAppInstalled;
use App\Http\Middleware\RedirectIfInstalled;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::middleware('web')
                ->group(__DIR__.'/../routes/install.php');

            Route::middleware('web')
                ->group(__DIR__.'/../routes/admin.php');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'installed' => EnsureAppInstalled::class,
            'not-installed' => RedirectIfInstalled::class,
            'locale' => SetLocale::class,
        ]);

        $middleware->web(append: [
            EnsureAppInstalled::class,
            SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
