<?php

use App\Modules\Flights\Livewire\Public\FlightSearch;
use Illuminate\Support\Facades\Route;

$supportedLocales = implode('|', array_map('preg_quote', (array) config('hk.localization.supported', ['en'])));

Route::get('/flights', FlightSearch::class)->name('flights.search');
Route::get('/{locale}/flights', FlightSearch::class)->where('locale', $supportedLocales)->name('localized.flights.search');
