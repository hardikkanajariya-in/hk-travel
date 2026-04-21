<?php

use App\Modules\Tours\Livewire\Admin\TourTable;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'can:tours.view'])
    ->prefix('admin/tours')
    ->name('admin.tours.')
    ->group(function (): void {
        Route::get('/', TourTable::class)->name('index');
    });
