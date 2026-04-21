<?php

use App\Modules\Destinations\Livewire\Public\DestinationIndex;
use App\Modules\Destinations\Livewire\Public\DestinationShow;
use Illuminate\Support\Facades\Route;

Route::get('/destinations', DestinationIndex::class)->name('destinations.index');
Route::get('/destinations/{slug}', DestinationShow::class)->name('destinations.show');
