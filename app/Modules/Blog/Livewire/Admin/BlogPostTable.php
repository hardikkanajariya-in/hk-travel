<?php

namespace App\Modules\Blog\Livewire\Admin;

use App\Modules\Blog\Models\BlogPost;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Blog posts')]
#[Layout('components.layouts.admin')]
class BlogPostTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = '';

    public function updating(string $name): void
    {
        if (in_array($name, ['search', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function publish(string $id): void
    {
        $this->authorize('blog.publish');
        $post = BlogPost::query()->findOrFail($id);
        $post->forceFill([
            'status' => BlogPost::STATUS_PUBLISHED,
            'published_at' => $post->published_at?->isPast() ? $post->published_at : now(),
        ])->save();
    }

    public function unpublish(string $id): void
    {
        $this->authorize('blog.publish');
        BlogPost::query()->whereKey($id)->update(['status' => BlogPost::STATUS_DRAFT]);
    }

    public function delete(string $id): void
    {
        $this->authorize('blog.delete');
        BlogPost::query()->whereKey($id)->delete();
    }

    public function render(): View
    {
        $posts = BlogPost::query()
            ->with(['author', 'categories'])
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('blog::admin.posts.table', compact('posts'));
    }
}
