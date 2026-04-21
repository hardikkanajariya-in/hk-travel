<?php

use App\Modules\Cruises\Livewire\Public\CruiseIndex;
use App\Modules\Cruises\Livewire\Public\CruiseShow;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function (): void {
    Route::get('/cruises', CruiseIndex::class)->name('cruises.index');
    Route::get('/cruises/{slug}', CruiseShow::class)->name('cruises.show');
});
