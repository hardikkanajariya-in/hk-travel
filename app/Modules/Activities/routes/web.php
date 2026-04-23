<?php

use App\Core\Permalink\PermalinkRouter;
use App\Modules\Activities\Livewire\Public\ActivityIndex;
use App\Modules\Activities\Livewire\Public\ActivityShow;
use Illuminate\Support\Facades\Route;

$pattern = ltrim(app(PermalinkRouter::class)->pattern('activity'), '/');
$supportedLocales = implode('|', array_map('preg_quote', (array) config('hk.localization.supported', ['en'])));

Route::get('/activities', ActivityIndex::class)->name('activities.index');
Route::get('/{locale}/activities', ActivityIndex::class)->where('locale', $supportedLocales)->name('localized.activities.index');
Route::get('/'.$pattern, ActivityShow::class)->name('activities.show');
Route::get('/{locale}/'.$pattern, ActivityShow::class)->where('locale', $supportedLocales)->name('localized.activities.show');
