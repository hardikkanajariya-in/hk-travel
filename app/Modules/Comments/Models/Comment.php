<?php

namespace App\Modules\Comments\Models;

use App\Concerns\HasAuditLog;
use App\Models\User;
use App\Modules\Comments\Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasAuditLog, HasFactory, HasUlids, SoftDeletes;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_SPAM = 'spam';

    public const MAX_DEPTH = 4;

    protected $table = 'comments';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'depth' => 'integer',
            'is_pinned' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('created_at');
    }

    public function approvedReplies(): HasMany
    {
        return $this->replies()->where('status', self::STATUS_APPROVED);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function authorName(): string
    {
        return $this->user?->name ?? $this->author_name ?? __('Anonymous');
    }

    protected static function newFactory(): CommentFactory
    {
        return CommentFactory::new();
    }
}
