<?php

use App\Modules\Cars\Livewire\Public\CarIndex;
use App\Modules\Cars\Livewire\Public\CarShow;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function (): void {
    Route::get('/cars', CarIndex::class)->name('cars.index');
    Route::get('/cars/{slug}', CarShow::class)->name('cars.show');
});
