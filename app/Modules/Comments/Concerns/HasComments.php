<?php

namespace App\Modules\Comments\Concerns;

use App\Modules\Comments\Models\Comment;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Mix into any Eloquent model to make it commentable.
 *
 * Host model conventions:
 *   - `allow_comments` (bool) attribute — when false the form/section
 *     is hidden client-side and requests are rejected server-side.
 *   - `comments_count` (int, optional) — auto-maintained when present.
 */
trait HasComments
{
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function approvedComments(): MorphMany
    {
        return $this->comments()->where('status', Comment::STATUS_APPROVED);
    }

    public function rootComments(): MorphMany
    {
        return $this->approvedComments()->whereNull('parent_id');
    }

    public function commentsAllowed(): bool
    {
        return (bool) ($this->allow_comments ?? false);
    }
}
