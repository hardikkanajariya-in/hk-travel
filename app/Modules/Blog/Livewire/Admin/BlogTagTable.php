<?php

namespace App\Modules\Blog\Livewire\Admin;

use App\Modules\Blog\Models\BlogTag;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Blog tags')]
#[Layout('components.layouts.admin')]
class BlogTagTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(string $id): void
    {
        $this->authorize('blog.taxonomy.manage');
        BlogTag::query()->whereKey($id)->delete();
    }

    public function render(): View
    {
        $tags = BlogTag::query()
            ->withCount('posts')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate(50);

        return view('blog::admin.tags.table', compact('tags'));
    }
}
