<?php

use App\Core\Permalink\PermalinkRouter;
use App\Modules\Destinations\Livewire\Public\DestinationIndex;
use App\Modules\Destinations\Livewire\Public\DestinationShow;
use Illuminate\Support\Facades\Route;

$pattern = ltrim(app(PermalinkRouter::class)->pattern('destination'), '/');
$supportedLocales = implode('|', array_map('preg_quote', (array) config('hk.localization.supported', ['en'])));

Route::get('/destinations', DestinationIndex::class)->name('destinations.index');
Route::get('/{locale}/destinations', DestinationIndex::class)->where('locale', $supportedLocales)->name('localized.destinations.index');
Route::get('/'.$pattern, DestinationShow::class)->name('destinations.show');
Route::get('/{locale}/'.$pattern, DestinationShow::class)->where('locale', $supportedLocales)->name('localized.destinations.show');
