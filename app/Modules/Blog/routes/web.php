<?php

use App\Core\Permalink\PermalinkRouter;
use App\Modules\Blog\Http\Controllers\BlogRssController;
use App\Modules\Blog\Livewire\Public\BlogIndex;
use App\Modules\Blog\Livewire\Public\BlogShow;
use Illuminate\Support\Facades\Route;

$pattern = ltrim(app(PermalinkRouter::class)->pattern('blog_post'), '/');
$supportedLocales = implode('|', array_map('preg_quote', (array) config('hk.localization.supported', ['en'])));

Route::get('/blog/rss', BlogRssController::class)->name('blog.rss');
Route::get('/blog', BlogIndex::class)->name('blog.index');
Route::get('/{locale}/blog', BlogIndex::class)->where('locale', $supportedLocales)->name('localized.blog.index');
Route::get('/blog/category/{slug}', BlogIndex::class)->name('blog.category');
Route::get('/{locale}/blog/category/{slug}', BlogIndex::class)->where('locale', $supportedLocales)->name('localized.blog.category');
Route::get('/blog/tag/{slug}', BlogIndex::class)->name('blog.tag');
Route::get('/{locale}/blog/tag/{slug}', BlogIndex::class)->where('locale', $supportedLocales)->name('localized.blog.tag');
Route::get('/'.$pattern, BlogShow::class)->name('blog.show');
Route::get('/{locale}/'.$pattern, BlogShow::class)->where('locale', $supportedLocales)->name('localized.blog.show');
