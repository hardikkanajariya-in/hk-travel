<?php

use App\Core\Permalink\PermalinkRouter;
use App\Modules\Tours\Livewire\Public\TourIndex;
use App\Modules\Tours\Livewire\Public\TourShow;
use Illuminate\Support\Facades\Route;

$pattern = ltrim(app(PermalinkRouter::class)->pattern('tour'), '/');
$supportedLocales = implode('|', array_map('preg_quote', (array) config('hk.localization.supported', ['en'])));

Route::get('/tours', TourIndex::class)->name('tours.index');
Route::get('/{locale}/tours', TourIndex::class)->where('locale', $supportedLocales)->name('localized.tours.index');
Route::get('/'.$pattern, TourShow::class)->name('tours.show');
Route::get('/{locale}/'.$pattern, TourShow::class)->where('locale', $supportedLocales)->name('localized.tours.show');
