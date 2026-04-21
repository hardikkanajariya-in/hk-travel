<?php

namespace App\Modules\Reviews\Livewire\Admin;

use App\Modules\Reviews\Models\Review;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Reviews moderation')]
#[Layout('components.layouts.admin')]
class ReviewModerationTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = 'pending';

    #[Url(as: 'type')]
    public string $type = '';

    public function updating(string $name): void
    {
        if (in_array($name, ['search', 'status', 'type'], true)) {
            $this->resetPage();
        }
    }

    public function approve(string $id): void
    {
        $this->authorize('reviews.moderate');
        $review = Review::query()->findOrFail($id);
        $review->forceFill([
            'status' => Review::STATUS_APPROVED,
            'approved_at' => now(),
        ])->save();
        session()->flash('status', __('Review approved.'));
    }

    public function reject(string $id): void
    {
        $this->authorize('reviews.moderate');
        $review = Review::query()->findOrFail($id);
        $review->forceFill([
            'status' => Review::STATUS_REJECTED,
            'approved_at' => null,
        ])->save();
    }

    public function markSpam(string $id): void
    {
        $this->authorize('reviews.moderate');
        Review::query()->whereKey($id)->update(['status' => Review::STATUS_SPAM]);
    }

    public function delete(string $id): void
    {
        $this->authorize('reviews.delete');
        Review::query()->whereKey($id)->delete();
    }

    public function render(): View
    {
        $reviews = Review::query()
            ->with(['user', 'reviewable'])
            ->when($this->search, fn ($q) => $q->where(function ($q): void {
                $q->where('body', 'like', "%{$this->search}%")
                    ->orWhere('title', 'like', "%{$this->search}%")
                    ->orWhere('author_name', 'like', "%{$this->search}%")
                    ->orWhere('author_email', 'like', "%{$this->search}%");
            }))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->type, fn ($q) => $q->where('reviewable_type', $this->type))
            ->orderByDesc('created_at')
            ->paginate(25);

        $types = Review::query()
            ->select('reviewable_type')
            ->distinct()
            ->pluck('reviewable_type');

        return view('reviews::admin.table', [
            'reviews' => $reviews,
            'types' => $types,
        ]);
    }
}
