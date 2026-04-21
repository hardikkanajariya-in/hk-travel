<?php

namespace App\Modules\Blog\Livewire\Admin;

use App\Modules\Blog\Models\BlogCategory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Blog categories')]
#[Layout('components.layouts.admin')]
class BlogCategoryTable extends Component
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
        BlogCategory::query()->whereKey($id)->delete();
    }

    public function render(): View
    {
        $categories = BlogCategory::query()
            ->withCount('posts')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('position')
            ->orderBy('name')
            ->paginate(25);

        return view('blog::admin.categories.table', compact('categories'));
    }
}
