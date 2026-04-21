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
        Route::livewire('modules', 'pages::admin.modules')->middleware('can:admin.modules.manage')->name('modules');
        Route::livewire('settings', 'pages::admin.settings')->middleware('can:admin.settings.manage')->name('settings');
        Route::livewire('security', 'pages::admin.security')->middleware('can:admin.security.manage')->name('security');
        Route::livewire('captcha', 'pages::admin.captcha')->middleware('can:admin.captcha.manage')->name('captcha');
        Route::livewire('audit', 'pages::admin.audit')->middleware('can:admin.audit.view')->name('audit');
        Route::livewire('branding', 'pages::admin.branding')->middleware('can:admin.branding.manage')->name('branding');
        Route::livewire('languages', 'pages::admin.languages')->middleware('can:admin.languages.manage')->name('languages');
        Route::livewire('permalinks', 'pages::admin.permalinks')->middleware('can:admin.permalinks.manage')->name('permalinks');
        Route::livewire('email-templates', 'pages::admin.email-templates')->middleware('can:admin.email-templates.manage')->name('email-templates');
        Route::livewire('notifications', 'pages::admin.notifications')->middleware('can:admin.notifications.manage')->name('notifications');
    });
