<?php

namespace App\Modules\Comments\Livewire\Admin;

use App\Modules\Comments\Models\Comment;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Comments moderation')]
#[Layout('components.layouts.admin')]
class CommentModerationTable extends Component
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
        $this->authorize('comments.moderate');
        $row = Comment::query()->findOrFail($id);
        $row->forceFill([
            'status' => Comment::STATUS_APPROVED,
            'approved_at' => now(),
        ])->save();
        session()->flash('status', __('Comment approved.'));
    }

    public function reject(string $id): void
    {
        $this->authorize('comments.moderate');
        Comment::query()->whereKey($id)->update([
            'status' => Comment::STATUS_REJECTED,
            'approved_at' => null,
        ]);
    }

    public function markSpam(string $id): void
    {
        $this->authorize('comments.moderate');
        Comment::query()->whereKey($id)->update(['status' => Comment::STATUS_SPAM]);
    }

    public function delete(string $id): void
    {
        $this->authorize('comments.delete');
        Comment::query()->whereKey($id)->delete();
    }

    public function render(): View
    {
        $comments = Comment::query()
            ->with(['user', 'commentable', 'parent'])
            ->when($this->search, fn ($q) => $q->where(function ($q): void {
                $q->where('body', 'like', "%{$this->search}%")
                    ->orWhere('author_name', 'like', "%{$this->search}%")
                    ->orWhere('author_email', 'like', "%{$this->search}%");
            }))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->type, fn ($q) => $q->where('commentable_type', $this->type))
            ->orderByDesc('created_at')
            ->paginate(25);

        $types = Comment::query()
            ->select('commentable_type')
            ->distinct()
            ->pluck('commentable_type');

        return view('comments::admin.table', [
            'comments' => $comments,
            'types' => $types,
        ]);
    }
}
