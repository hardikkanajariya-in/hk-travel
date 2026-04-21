<?php

use App\Modules\Activities\Livewire\Admin\ActivityForm;
use App\Modules\Activities\Livewire\Admin\ActivityTable;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('admin/activities')
    ->name('admin.activities.')
    ->group(function (): void {
        Route::get('/', ActivityTable::class)->middleware('can:activities.view')->name('index');
        Route::get('/create', ActivityForm::class)->middleware('can:activities.create')->name('create');
        Route::get('/{id}/edit', ActivityForm::class)->middleware('can:activities.update')->name('edit');
    });
