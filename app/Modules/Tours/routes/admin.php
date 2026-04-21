<?php

use App\Modules\Tours\Livewire\Admin\TourForm;
use App\Modules\Tours\Livewire\Admin\TourTable;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('admin/tours')
    ->name('admin.tours.')
    ->group(function (): void {
        Route::get('/', TourTable::class)->middleware('can:tours.view')->name('index');
        Route::get('/create', TourForm::class)->middleware('can:tours.create')->name('create');
        Route::get('/{id}/edit', TourForm::class)->middleware('can:tours.update')->name('edit');
    });
