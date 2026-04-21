<?php

namespace App\Modules\Reviews\Livewire\Public;

use App\Modules\Reviews\Models\Review;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Approved-reviews list embedded on host module Show pages. Listens
 * for the `review-submitted` event so a freshly auto-approved review
 * appears immediately without a full page reload.
 */
class ReviewList extends Component
{
    use WithPagination;

    public ?string $reviewableType = null;

    public ?string $reviewableId = null;

    #[Url(as: 'reviews_sort')]
    public string $sort = 'newest';

    public function mount(Model $reviewable): void
    {
        $this->reviewableType = $reviewable->getMorphClass();
        $this->reviewableId = (string) $reviewable->getKey();
    }

    #[On('review-submitted')]
    public function refresh(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $query = Review::query()
            ->where('reviewable_type', $this->reviewableType)
            ->where('reviewable_id', $this->reviewableId)
            ->where('status', Review::STATUS_APPROVED);

        $query = match ($this->sort) {
            'rating_desc' => $query->orderByDesc('rating'),
            'rating_asc' => $query->orderBy('rating'),
            'helpful' => $query->orderByDesc('helpful_count'),
            default => $query->orderByDesc('approved_at'),
        };

        $reviews = $query->paginate(10);

        $stats = Review::query()
            ->where('reviewable_type', $this->reviewableType)
            ->where('reviewable_id', $this->reviewableId)
            ->where('status', Review::STATUS_APPROVED)
            ->selectRaw('AVG(rating) as avg, COUNT(*) as cnt')
            ->first();

        $distribution = [];
        foreach (range(5, 1) as $star) {
            $distribution[$star] = Review::query()
                ->where('reviewable_type', $this->reviewableType)
                ->where('reviewable_id', $this->reviewableId)
                ->where('status', Review::STATUS_APPROVED)
                ->whereBetween('rating', [$star - 0.5, $star + 0.4999])
                ->count();
        }

        return view('reviews::public.list', [
            'reviews' => $reviews,
            'avg' => round((float) ($stats->avg ?? 0), 1),
            'count' => (int) ($stats->cnt ?? 0),
            'distribution' => $distribution,
        ]);
    }
}
