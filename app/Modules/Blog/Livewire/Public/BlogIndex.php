<?php

namespace App\Modules\Blog\Livewire\Public;

use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\BlogPost;
use App\Modules\Blog\Models\BlogTag;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Blog')]
#[Layout('components.layouts.public')]
class BlogIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public ?string $categorySlug = null;

    public ?string $tagSlug = null;

    public function mount(?string $slug = null): void
    {
        $route = request()->route()?->getName();
        if ($route === 'blog.category') {
            $this->categorySlug = $slug;
        } elseif ($route === 'blog.tag') {
            $this->tagSlug = $slug;
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $category = $this->categorySlug
            ? BlogCategory::query()->where('slug', $this->categorySlug)->first()
            : null;
        $tag = $this->tagSlug
            ? BlogTag::query()->where('slug', $this->tagSlug)->first()
            : null;

        $posts = BlogPost::query()
            ->with(['author', 'categories', 'tags'])
            ->published()
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($category, fn ($q) => $q->whereHas('categories', fn ($q) => $q->whereKey($category->id)))
            ->when($tag, fn ($q) => $q->whereHas('tags', fn ($q) => $q->whereKey($tag->id)))
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->paginate(9);

        return view('blog::public.index', [
            'posts' => $posts,
            'category' => $category,
            'tag' => $tag,
            'categories' => BlogCategory::query()->withCount(['posts' => fn ($q) => $q->where('status', BlogPost::STATUS_PUBLISHED)])
                ->orderBy('name')->limit(20)->get(),
            'popularTags' => BlogTag::query()->withCount(['posts' => fn ($q) => $q->where('status', BlogPost::STATUS_PUBLISHED)])
                ->orderByDesc('posts_count')->limit(20)->get(),
        ]);
    }
}
