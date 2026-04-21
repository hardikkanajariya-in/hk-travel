<?php

namespace App\Modules\Reviews\Observers;

use App\Modules\Reviews\Models\Review;

/**
 * Keeps each host model's `rating_avg` / `rating_count` roll-up in sync
 * with approved-review changes. The trait HasReviews on the host model
 * provides the recalculation method.
 */
class ReviewObserver
{
    public function saved(Review $review): void
    {
        if (! $review->wasChanged(['status', 'rating'])
            && ! $review->wasRecentlyCreated) {
            return;
        }

        $this->refreshHost($review);
    }

    public function deleted(Review $review): void
    {
        $this->refreshHost($review);
    }

    public function restored(Review $review): void
    {
        $this->refreshHost($review);
    }

    protected function refreshHost(Review $review): void
    {
        $host = $review->reviewable;

        if ($host && method_exists($host, 'recalculateReviewAggregate')) {
            $host->recalculateReviewAggregate();
        }
    }
}
