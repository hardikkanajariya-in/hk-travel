<?php

use App\Core\Permalink\PermalinkRouter;
use App\Modules\Cars\Livewire\Public\CarIndex;
use App\Modules\Cars\Livewire\Public\CarShow;
use Illuminate\Support\Facades\Route;

$pattern = ltrim(app(PermalinkRouter::class)->pattern('car'), '/');
$supportedLocales = implode('|', array_map('preg_quote', (array) config('hk.localization.supported', ['en'])));

Route::get('/cars', CarIndex::class)->name('cars.index');
Route::get('/{locale}/cars', CarIndex::class)->where('locale', $supportedLocales)->name('localized.cars.index');
Route::get('/'.$pattern, CarShow::class)->name('cars.show');
Route::get('/{locale}/'.$pattern, CarShow::class)->where('locale', $supportedLocales)->name('localized.cars.show');
