<?php

use App\Modules\Trains\Livewire\Public\TrainSearch;
use Illuminate\Support\Facades\Route;

$supportedLocales = implode('|', array_map('preg_quote', (array) config('hk.localization.supported', ['en'])));

Route::get('/trains', TrainSearch::class)->name('trains.search');
Route::get('/{locale}/trains', TrainSearch::class)->where('locale', $supportedLocales)->name('localized.trains.search');
