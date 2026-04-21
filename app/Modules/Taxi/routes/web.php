<?php

use App\Modules\Taxi\Livewire\Public\TaxiIndex;
use App\Modules\Taxi\Livewire\Public\TaxiShow;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function (): void {
    Route::get('/taxi', TaxiIndex::class)->name('taxi.index');
    Route::get('/taxi/{slug}', TaxiShow::class)->name('taxi.show');
});
