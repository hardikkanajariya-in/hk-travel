<?php

use App\Modules\Taxi\Livewire\Admin\TaxiForm;
use App\Modules\Taxi\Livewire\Admin\TaxiTable;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('admin/taxi')
    ->name('admin.taxi.')
    ->group(function (): void {
        Route::get('/', TaxiTable::class)->middleware('can:taxi.view')->name('index');
        Route::get('/create', TaxiForm::class)->middleware('can:taxi.create')->name('create');
        Route::get('/{id}/edit', TaxiForm::class)->middleware('can:taxi.update')->name('edit');
    });
