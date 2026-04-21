<?php

use App\Modules\Flights\Livewire\Public\FlightSearch;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function (): void {
    Route::get('/flights', FlightSearch::class)->name('flights.search');
});
