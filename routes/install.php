<?php

use App\Http\Middleware\RedirectIfInstalled;
use Illuminate\Support\Facades\Route;

/*
 * Installer routes.
 *
 * Mounted from bootstrap/app.php under the `web` middleware group. The
 * RedirectIfInstalled middleware sends visitors back to "/" once the
 * installation lock file has been written.
 */

Route::middleware(RedirectIfInstalled::class)
    ->prefix('install')
    ->name('install.')
    ->group(function (): void {
        Route::livewire('/', 'pages::installer.wizard')->name('welcome');
    });
