<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'module.enabled:crm'])
    ->prefix('admin/crm')
    ->name('admin.crm.')
    ->group(function (): void {
        Route::livewire('leads', 'pages::admin.leads')->middleware('can:admin.crm.leads.view')->name('leads');
        Route::livewire('kanban', 'pages::admin.leads-kanban')->middleware('can:admin.crm.leads.view')->name('kanban');
        Route::livewire('leads/{lead}', 'pages::admin.lead-detail')->middleware('can:admin.crm.leads.view')->name('leads.show');
        Route::livewire('pipelines', 'pages::admin.pipelines')->middleware('can:admin.crm.pipelines.manage')->name('pipelines');
    });
