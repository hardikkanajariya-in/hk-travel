<?php

namespace App\Modules\Reviews\Concerns;

use App\Modules\Reviews\Models\Review;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Schema;

/**
 * Mix into any Eloquent model to make it reviewable.
 *
 * Host model conventions:
 *   - `rating_avg` (decimal) and `rating_count` (int) columns are
 *     auto-recalculated by the ReviewObserver whenever a review is
 *     approved/unapproved/deleted.
 *   - Optionally declare `protected array $reviewCriteria = ['value', ...]`
 *     to expose sub-criteria fields on the review form. Defaults are
 *     read from config('hk.reviews.criteria').
 */
trait HasReviews
{
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function approvedReviews(): MorphMany
    {
        return $this->reviews()->where('status', Review::STATUS_APPROVED);
    }

    /**
     * @return array<int, string>
     */
    public function reviewCriteria(): array
    {
        return property_exists($this, 'reviewCriteria') && is_array($this->reviewCriteria)
            ? $this->reviewCriteria
            : (array) config('hk.reviews.default_criteria', ['value', 'service', 'quality']);
    }

    public function recalculateReviewAggregate(): void
    {
        if (! Schema::hasColumns($this->getTable(), ['rating_avg', 'rating_count'])) {
            return;
        }

        $stats = $this->reviews()
            ->where('status', Review::STATUS_APPROVED)
            ->selectRaw('AVG(rating) as avg, COUNT(*) as cnt')
            ->first();

        $this->forceFill([
            'rating_avg' => round((float) ($stats->avg ?? 0), 2),
            'rating_count' => (int) ($stats->cnt ?? 0),
        ])->saveQuietly();
    }

    /**
     * Snippet of schema.org AggregateRating to merge into the host
     * model's JSON-LD payload. Returns null when there are no reviews.
     *
     * @return array<string, mixed>|null
     */
    public function reviewsAggregateSchema(): ?array
    {
        $count = (int) ($this->rating_count ?? 0);

        if ($count <= 0) {
            return null;
        }

        return [
            '@type' => 'AggregateRating',
            'ratingValue' => (float) ($this->rating_avg ?? 0),
            'reviewCount' => $count,
            'bestRating' => 5,
            'worstRating' => 1,
        ];
    }
}
