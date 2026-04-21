<?php

use App\Modules\Tours\Livewire\Public\TourIndex;
use App\Modules\Tours\Livewire\Public\TourShow;
use Illuminate\Support\Facades\Route;

Route::get('/tours', TourIndex::class)->name('tours.index');
Route::get('/tours/{slug}', TourShow::class)->name('tours.show');
