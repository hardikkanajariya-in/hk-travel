<?php

use App\Modules\Blog\Http\Controllers\BlogRssController;
use App\Modules\Blog\Livewire\Public\BlogIndex;
use App\Modules\Blog\Livewire\Public\BlogShow;
use Illuminate\Support\Facades\Route;

Route::get('/blog/rss', BlogRssController::class)->name('blog.rss');
Route::get('/blog', BlogIndex::class)->name('blog.index');
Route::get('/blog/category/{slug}', BlogIndex::class)->name('blog.category');
Route::get('/blog/tag/{slug}', BlogIndex::class)->name('blog.tag');
Route::get('/blog/{slug}', BlogShow::class)->name('blog.show');
