<?php

use App\Core\Permalink\PermalinkRouter;
use App\Modules\Buses\Livewire\Public\BusIndex;
use App\Modules\Buses\Livewire\Public\BusShow;
use Illuminate\Support\Facades\Route;

$pattern = ltrim(app(PermalinkRouter::class)->pattern('bus'), '/');
$supportedLocales = implode('|', array_map('preg_quote', (array) config('hk.localization.supported', ['en'])));

Route::get('/buses', BusIndex::class)->name('buses.index');
Route::get('/{locale}/buses', BusIndex::class)->where('locale', $supportedLocales)->name('localized.buses.index');
Route::get('/'.$pattern, BusShow::class)->name('buses.show');
Route::get('/{locale}/'.$pattern, BusShow::class)->where('locale', $supportedLocales)->name('localized.buses.show');
