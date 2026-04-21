<?php

use App\Modules\Buses\Livewire\Admin\BusRouteForm;
use App\Modules\Buses\Livewire\Admin\BusRouteTable;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('admin/buses')
    ->name('admin.buses.')
    ->group(function (): void {
        Route::get('/', BusRouteTable::class)->middleware('can:buses.view')->name('index');
        Route::get('/create', BusRouteForm::class)->middleware('can:buses.create')->name('create');
        Route::get('/{id}/edit', BusRouteForm::class)->middleware('can:buses.update')->name('edit');
    });
