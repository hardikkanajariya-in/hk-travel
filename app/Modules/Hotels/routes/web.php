<?php

use App\Modules\Hotels\Livewire\Public\HotelIndex;
use App\Modules\Hotels\Livewire\Public\HotelShow;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function (): void {
    Route::get('/hotels', HotelIndex::class)->name('hotels.index');
    Route::get('/hotels/{slug}', HotelShow::class)->name('hotels.show');
});
