<?php

namespace App\Modules\Reviews;

use App\Core\Modules\Module;
use App\Modules\Reviews\Livewire\Admin\ReviewModerationTable;
use App\Modules\Reviews\Livewire\Public\ReviewForm;
use App\Modules\Reviews\Livewire\Public\ReviewList;

/**
 * Reviews module manifest.
 *
 * Polymorphic reviews with sub-criteria scoring, moderation queue, and
 * schema.org AggregateRating. Activated globally; host models opt in by
 * applying the App\Modules\Reviews\Concerns\HasReviews trait.
 *
 * Registered in config/hk-modules.php under key `reviews`.
 */
class ReviewModule extends Module
{
    public function key(): string
    {
        return 'reviews';
    }

    public function name(): string
    {
        return 'Reviews & Ratings';
    }

    public function version(): string
    {
        return '0.1.0';
    }

    public function permissions(): array
    {
        return [
            'reviews.view',
            'reviews.create',
            'reviews.update',
            'reviews.delete',
            'reviews.moderate',
        ];
    }

    public function adminMenu(): array
    {
        return [[
            'label' => 'Reviews',
            'route' => 'admin.reviews.index',
            'icon' => 'star',
            'permission' => 'reviews.moderate',
            'group' => 'Engagement',
        ]];
    }

    public function livewireComponents(): array
    {
        return [
            'reviews-public.review-form' => ReviewForm::class,
            'reviews-public.review-list' => ReviewList::class,
            'reviews-admin.review-moderation-table' => ReviewModerationTable::class,
        ];
    }
}
