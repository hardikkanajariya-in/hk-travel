<?php

namespace App\Modules\Reviews\Models;

use App\Concerns\HasAuditLog;
use App\Models\User;
use App\Modules\Reviews\Database\Factories\ReviewFactory;
use App\Modules\Reviews\Observers\ReviewObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Polymorphic review row attached to any module model that uses the
 * App\Modules\Reviews\Concerns\HasReviews trait.
 *
 * Sub-criteria scores (e.g. cleanliness, value, service) live in the
 * `criteria` JSON column. The numeric `rating` column holds the
 * arithmetic mean of those sub-scores (or the standalone overall rating
 * when no sub-criteria are supplied) and is what feeds aggregate
 * roll-ups on the host model.
 */
#[ObservedBy([ReviewObserver::class])]
class Review extends Model
{
    use HasAuditLog, HasFactory, HasUlids, SoftDeletes;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_SPAM = 'spam';

    protected $table = 'reviews';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'rating' => 'decimal:2',
            'criteria' => 'array',
            'is_verified' => 'boolean',
            'helpful_count' => 'integer',
            'reported_count' => 'integer',
            'approved_at' => 'datetime',
        ];
    }

    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeFor(Builder $query, Model $reviewable): Builder
    {
        return $query
            ->where('reviewable_type', $reviewable->getMorphClass())
            ->where('reviewable_id', $reviewable->getKey());
    }

    public function authorName(): string
    {
        return $this->user?->name ?? $this->author_name ?? __('Anonymous');
    }

    protected static function newFactory(): ReviewFactory
    {
        return ReviewFactory::new();
    }
}
