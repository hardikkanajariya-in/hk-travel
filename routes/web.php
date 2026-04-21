<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';

// Catch-all CMS page route — must be LAST so it does not shadow
// module routes registered via ModuleServiceProvider. The constraint
// blocks reserved admin/dashboard/settings prefixes.
Route::get('{slug}', [PageController::class, 'show'])
    ->where('slug', '^(?!admin|dashboard|settings|livewire|api|storage|build)[A-Za-z0-9\-_/]+$')
    ->name('page.show');
