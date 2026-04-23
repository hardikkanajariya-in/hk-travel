<?php

use App\Core\Permalink\PermalinkRouter;
use App\Modules\Cruises\Livewire\Public\CruiseIndex;
use App\Modules\Cruises\Livewire\Public\CruiseShow;
use Illuminate\Support\Facades\Route;

$pattern = ltrim(app(PermalinkRouter::class)->pattern('cruise'), '/');
$supportedLocales = implode('|', array_map('preg_quote', (array) config('hk.localization.supported', ['en'])));

Route::get('/cruises', CruiseIndex::class)->name('cruises.index');
Route::get('/{locale}/cruises', CruiseIndex::class)->where('locale', $supportedLocales)->name('localized.cruises.index');
Route::get('/'.$pattern, CruiseShow::class)->name('cruises.show');
Route::get('/{locale}/'.$pattern, CruiseShow::class)->where('locale', $supportedLocales)->name('localized.cruises.show');
