<?php

use App\Modules\Blog\Livewire\Admin\BlogCategoryForm;
use App\Modules\Blog\Livewire\Admin\BlogCategoryTable;
use App\Modules\Blog\Livewire\Admin\BlogPostForm;
use App\Modules\Blog\Livewire\Admin\BlogPostTable;
use App\Modules\Blog\Livewire\Admin\BlogTagForm;
use App\Modules\Blog\Livewire\Admin\BlogTagTable;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('admin/blog')
    ->name('admin.blog.')
    ->group(function (): void {
        Route::prefix('posts')->name('posts.')->group(function (): void {
            Route::get('/', BlogPostTable::class)->middleware('can:blog.view')->name('index');
            Route::get('/create', BlogPostForm::class)->middleware('can:blog.create')->name('create');
            Route::get('/{id}/edit', BlogPostForm::class)->middleware('can:blog.update')->name('edit');
        });

        Route::prefix('categories')->name('categories.')->group(function (): void {
            Route::get('/', BlogCategoryTable::class)->middleware('can:blog.taxonomy.manage')->name('index');
            Route::get('/create', BlogCategoryForm::class)->middleware('can:blog.taxonomy.manage')->name('create');
            Route::get('/{id}/edit', BlogCategoryForm::class)->middleware('can:blog.taxonomy.manage')->name('edit');
        });

        Route::prefix('tags')->name('tags.')->group(function (): void {
            Route::get('/', BlogTagTable::class)->middleware('can:blog.taxonomy.manage')->name('index');
            Route::get('/create', BlogTagForm::class)->middleware('can:blog.taxonomy.manage')->name('create');
            Route::get('/{id}/edit', BlogTagForm::class)->middleware('can:blog.taxonomy.manage')->name('edit');
        });
    });
