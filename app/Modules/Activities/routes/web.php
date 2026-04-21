<?php

use App\Modules\Activities\Livewire\Public\ActivityIndex;
use App\Modules\Activities\Livewire\Public\ActivityShow;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function (): void {
    Route::get('/activities', ActivityIndex::class)->name('activities.index');
    Route::get('/activities/{slug}', ActivityShow::class)->name('activities.show');
});
