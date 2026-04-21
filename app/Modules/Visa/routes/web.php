<?php

use App\Modules\Visa\Livewire\Public\VisaIndex;
use App\Modules\Visa\Livewire\Public\VisaShow;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function (): void {
    Route::get('/visa', VisaIndex::class)->name('visa.index');
    Route::get('/visa/{slug}', VisaShow::class)->name('visa.show');
});
