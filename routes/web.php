<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

$supportedLocales = implode('|', array_map('preg_quote', (array) config('hk.localization.supported', ['en'])));

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/{locale}', [PageController::class, 'home'])
    ->where('locale', $supportedLocales)
    ->name('localized.home');

Route::get('robots.txt', RobotsController::class)->name('robots');
Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('sitemaps/{name}', [SitemapController::class, 'child'])
    ->where('name', '[A-Za-z0-9_\-]+\.xml')
    ->name('sitemap.child');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', 'pages::customer.dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';

// Catch-all CMS page route — must be LAST so it does not shadow
// module routes registered via ModuleServiceProvider. The constraint
// blocks reserved admin/dashboard/settings/install prefixes.
Route::get('{slug}', [PageController::class, 'show'])
    ->where('slug', '^(?!admin|dashboard|settings|install|livewire|api|storage|build|sitemap|sitemaps|robots|up)[A-Za-z0-9\-_/]+$')
    ->name('page.show');

Route::get('{locale}/{slug}', [PageController::class, 'show'])
    ->where('locale', $supportedLocales)
    ->where('slug', '^(?!admin|dashboard|settings|install|livewire|api|storage|build|sitemap|sitemaps|robots|up)[A-Za-z0-9\-_/]+$')
    ->name('localized.page.show');
