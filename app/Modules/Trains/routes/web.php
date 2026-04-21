<?php

use App\Modules\Trains\Livewire\Public\TrainSearch;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function (): void {
    Route::get('/trains', TrainSearch::class)->name('trains.search');
});
