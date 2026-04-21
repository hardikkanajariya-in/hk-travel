<?php

use Illuminate\Support\Facades\Route;

/*
 * Admin routes — protected by auth + verified, then mounted under /admin.
 *
 * Module routes can also register under this namespace via their own
 * adminRoutesPath() (see ModuleServiceProvider::bootModule).
 */

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::livewire('/', 'pages::admin.dashboard')->name('dashboard');
    });
