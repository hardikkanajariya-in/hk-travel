<?php

use App\Modules\Trains\Livewire\Admin\TrainOfferForm;
use App\Modules\Trains\Livewire\Admin\TrainOfferTable;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('admin/trains')
    ->name('admin.trains.')
    ->group(function (): void {
        Route::get('/', TrainOfferTable::class)->middleware('can:trains.view')->name('index');
        Route::get('/create', TrainOfferForm::class)->middleware('can:trains.create')->name('create');
        Route::get('/{id}/edit', TrainOfferForm::class)->middleware('can:trains.update')->name('edit');
    });
