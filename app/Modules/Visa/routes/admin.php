<?php

use App\Modules\Visa\Livewire\Admin\VisaServiceForm;
use App\Modules\Visa\Livewire\Admin\VisaServiceTable;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('admin/visa')
    ->name('admin.visa.')
    ->group(function (): void {
        Route::get('/', VisaServiceTable::class)->middleware('can:visa.view')->name('index');
        Route::get('/create', VisaServiceForm::class)->middleware('can:visa.create')->name('create');
        Route::get('/{id}/edit', VisaServiceForm::class)->middleware('can:visa.update')->name('edit');
    });
