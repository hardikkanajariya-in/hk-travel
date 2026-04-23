<?php

use App\Core\Permalink\PermalinkRouter;
use App\Modules\Taxi\Livewire\Public\TaxiIndex;
use App\Modules\Taxi\Livewire\Public\TaxiShow;
use Illuminate\Support\Facades\Route;

$pattern = ltrim(app(PermalinkRouter::class)->pattern('taxi'), '/');
$supportedLocales = implode('|', array_map('preg_quote', (array) config('hk.localization.supported', ['en'])));

Route::get('/taxi', TaxiIndex::class)->name('taxi.index');
Route::get('/{locale}/taxi', TaxiIndex::class)->where('locale', $supportedLocales)->name('localized.taxi.index');
Route::get('/'.$pattern, TaxiShow::class)->name('taxi.show');
Route::get('/{locale}/'.$pattern, TaxiShow::class)->where('locale', $supportedLocales)->name('localized.taxi.show');
