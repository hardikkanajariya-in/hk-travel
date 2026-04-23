<?php

use App\Core\Permalink\PermalinkRouter;
use App\Modules\Visa\Livewire\Public\VisaIndex;
use App\Modules\Visa\Livewire\Public\VisaShow;
use Illuminate\Support\Facades\Route;

$pattern = ltrim(app(PermalinkRouter::class)->pattern('visa'), '/');
$supportedLocales = implode('|', array_map('preg_quote', (array) config('hk.localization.supported', ['en'])));

Route::get('/visa', VisaIndex::class)->name('visa.index');
Route::get('/{locale}/visa', VisaIndex::class)->where('locale', $supportedLocales)->name('localized.visa.index');
Route::get('/'.$pattern, VisaShow::class)->name('visa.show');
Route::get('/{locale}/'.$pattern, VisaShow::class)->where('locale', $supportedLocales)->name('localized.visa.show');
