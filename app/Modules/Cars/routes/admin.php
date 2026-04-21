<?php

use App\Modules\Cars\Livewire\Admin\CarForm;
use App\Modules\Cars\Livewire\Admin\CarTable;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('admin/cars')
    ->name('admin.cars.')
    ->group(function (): void {
        Route::get('/', CarTable::class)->middleware('can:cars.view')->name('index');
        Route::get('/create', CarForm::class)->middleware('can:cars.create')->name('create');
        Route::get('/{id}/edit', CarForm::class)->middleware('can:cars.update')->name('edit');
    });
