<?php

use App\Modules\Destinations\Livewire\Admin\DestinationForm;
use App\Modules\Destinations\Livewire\Admin\DestinationTable;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('admin/destinations')
    ->name('admin.destinations.')
    ->group(function (): void {
        Route::get('/', DestinationTable::class)->middleware('can:destinations.view')->name('index');
        Route::get('/create', DestinationForm::class)->middleware('can:destinations.create')->name('create');
        Route::get('/{id}/edit', DestinationForm::class)->middleware('can:destinations.update')->name('edit');
    });
