<?php

use App\Core\Permalink\PermalinkRouter;
use App\Modules\Hotels\Livewire\Public\HotelIndex;
use App\Modules\Hotels\Livewire\Public\HotelShow;
use Illuminate\Support\Facades\Route;

$pattern = ltrim(app(PermalinkRouter::class)->pattern('hotel'), '/');
$supportedLocales = implode('|', array_map('preg_quote', (array) config('hk.localization.supported', ['en'])));

Route::get('/hotels', HotelIndex::class)->name('hotels.index');
Route::get('/{locale}/hotels', HotelIndex::class)->where('locale', $supportedLocales)->name('localized.hotels.index');
Route::get('/'.$pattern, HotelShow::class)->name('hotels.show');
Route::get('/{locale}/'.$pattern, HotelShow::class)->where('locale', $supportedLocales)->name('localized.hotels.show');
