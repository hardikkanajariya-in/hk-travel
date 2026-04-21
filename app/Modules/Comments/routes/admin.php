<?php

use App\Modules\Comments\Livewire\Admin\CommentModerationTable;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('admin/comments')
    ->name('admin.comments.')
    ->group(function (): void {
        Route::get('/', CommentModerationTable::class)->middleware('can:comments.moderate')->name('index');
    });
