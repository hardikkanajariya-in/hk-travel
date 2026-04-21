<?php

use App\Modules\Flights\Livewire\Admin\FlightOfferForm;
use App\Modules\Flights\Livewire\Admin\FlightOfferTable;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('admin/flights')
    ->name('admin.flights.')
    ->group(function (): void {
        Route::get('/', FlightOfferTable::class)->middleware('can:flights.view')->name('index');
        Route::get('/create', FlightOfferForm::class)->middleware('can:flights.create')->name('create');
        Route::get('/{id}/edit', FlightOfferForm::class)->middleware('can:flights.update')->name('edit');
    });
