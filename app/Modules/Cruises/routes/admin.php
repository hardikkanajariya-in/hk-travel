<?php

use App\Modules\Cruises\Livewire\Admin\CruiseForm;
use App\Modules\Cruises\Livewire\Admin\CruiseTable;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('admin/cruises')
    ->name('admin.cruises.')
    ->group(function (): void {
        Route::get('/', CruiseTable::class)->middleware('can:cruises.view')->name('index');
        Route::get('/create', CruiseForm::class)->middleware('can:cruises.create')->name('create');
        Route::get('/{id}/edit', CruiseForm::class)->middleware('can:cruises.update')->name('edit');
    });
