<?php

use App\Modules\Hotels\Livewire\Admin\HotelForm;
use App\Modules\Hotels\Livewire\Admin\HotelTable;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('admin/hotels')
    ->name('admin.hotels.')
    ->group(function (): void {
        Route::get('/', HotelTable::class)->middleware('can:hotels.view')->name('index');
        Route::get('/create', HotelForm::class)->middleware('can:hotels.create')->name('create');
        Route::get('/{id}/edit', HotelForm::class)->middleware('can:hotels.update')->name('edit');
    });
