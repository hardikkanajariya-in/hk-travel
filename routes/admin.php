<?php

use App\Http\Controllers\Admin\MediaUploadController;
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
        Route::livewire('permalinks', 'pages::admin.permalinks')->middleware('can:admin.permalinks.manage')->name('permalinks');
        Route::livewire('email-templates', 'pages::admin.email-templates')->middleware('can:admin.email-templates.manage')->name('email-templates');
        Route::livewire('notifications', 'pages::admin.notifications')->middleware('can:admin.notifications.manage')->name('notifications');
        Route::livewire('themes', 'pages::admin.themes')->middleware('can:admin.themes.manage')->name('themes');
        Route::livewire('menus', 'pages::admin.menus')->middleware('can:admin.menus.manage')->name('menus');
        Route::livewire('widgets', 'pages::admin.widgets')->middleware('can:admin.widgets.manage')->name('widgets');
        Route::livewire('pages', 'pages::admin.pages')->middleware('can:admin.pages.manage')->name('pages');
        Route::livewire('pages/{page}/edit', 'pages::admin.page-editor')->middleware('can:admin.pages.manage')->name('pages.edit');
        Route::livewire('seo', 'pages::admin.seo')->middleware('can:admin.seo.manage')->name('seo');
        Route::livewire('contact-forms', 'pages::admin.contact-forms')->middleware('can:admin.forms.manage')->name('contact-forms');
        Route::livewire('contact-forms/{form}/edit', 'pages::admin.contact-form-builder')->middleware('can:admin.forms.manage')->name('contact-forms.edit');
        Route::livewire('contact-submissions', 'pages::admin.contact-submissions')->middleware('can:admin.forms.submissions.view')->name('contact-submissions');

        Route::prefix('crm')->name('crm.')->group(function () {
            Route::livewire('leads', 'pages::admin.leads')->middleware('can:admin.crm.leads.view')->name('leads');
            Route::livewire('kanban', 'pages::admin.leads-kanban')->middleware('can:admin.crm.leads.view')->name('kanban');
            Route::livewire('leads/{lead}', 'pages::admin.lead-detail')->middleware('can:admin.crm.leads.view')->name('leads.show');
            Route::livewire('pipelines', 'pages::admin.pipelines')->middleware('can:admin.crm.pipelines.manage')->name('pipelines');
        });

        // Shared media upload endpoint used by <x-ui.image-picker>. Saves
        // through the public disk (StorageManager) so adding S3/Spaces/GCS
        // later requires no view changes.
        Route::post('media/upload-image', [MediaUploadController::class, 'image'])
            ->name('media.upload-image');
    });
