<?php

use App\Modules\Buses\Livewire\Public\BusIndex;
use App\Modules\Buses\Livewire\Public\BusShow;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function (): void {
    Route::get('/buses', BusIndex::class)->name('buses.index');
    Route::get('/buses/{slug}', BusShow::class)->name('buses.show');
});
