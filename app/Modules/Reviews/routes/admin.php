<?php

use App\Modules\Reviews\Livewire\Admin\ReviewModerationTable;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('admin/reviews')
    ->name('admin.reviews.')
    ->group(function (): void {
        Route::get('/', ReviewModerationTable::class)->middleware('can:reviews.moderate')->name('index');
    });
