<?php

namespace App\Modules\Blog\Livewire\Admin;

use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\BlogPost;
use App\Modules\Blog\Models\BlogTag;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Edit post')]
#[Layout('components.layouts.admin')]
class BlogPostForm extends Component
{
    public ?BlogPost $post = null;

    #[Validate('required|string|max:200')]
    public string $title = '';

    #[Validate('required|string|max:240')]
    public string $slug = '';

    #[Validate('nullable|string|max:500')]
    public ?string $excerpt = null;

    #[Validate('nullable|string|max:200000')]
    public ?string $body = null;

    #[Validate('nullable|string|max:255')]
    public ?string $cover_image = null;

    #[Validate('required|in:draft,scheduled,published,archived')]
    public string $status = 'draft';

    #[Validate('nullable|date')]
    public ?string $published_at = null;

    public bool $is_featured = false;

    public bool $allow_comments = true;

    public bool $show_toc = true;

    /** @var array<int, string> */
    public array $categoryIds = [];

    /** @var array<int, string> */
    public array $tagIds = [];

    public string $newTag = '';

    public function mount(?string $id = null): void
    {
        if ($id) {
            $this->authorize('blog.update');
            $this->post = BlogPost::query()->with(['categories', 'tags'])->findOrFail($id);
            $this->fill($this->post->only([
                'title', 'slug', 'excerpt', 'body', 'cover_image', 'status',
                'is_featured', 'allow_comments', 'show_toc',
            ]));
            $this->published_at = $this->post->published_at?->format('Y-m-d\TH:i');
            $this->categoryIds = $this->post->categories->pluck('id')->all();
            $this->tagIds = $this->post->tags->pluck('id')->all();
        } else {
            $this->authorize('blog.create');
        }
    }

    public function updatedTitle(string $value): void
    {
        if (! $this->slug || $this->post === null) {
            $this->slug = Str::slug($value);
        }
    }

    public function addTag(): void
    {
        $name = trim($this->newTag);
        if ($name === '') {
            return;
        }

        $tag = BlogTag::query()->firstOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name],
        );

        if (! in_array($tag->id, $this->tagIds, true)) {
            $this->tagIds[] = $tag->id;
        }

        $this->newTag = '';
    }

    public function removeTag(string $id): void
    {
        $this->tagIds = array_values(array_filter($this->tagIds, fn ($tid) => $tid !== $id));
    }

    public function save(): void
    {
        $this->validate();

        $publishedAt = $this->published_at ? Carbon::parse($this->published_at) : null;

        // Auto-coerce status when published_at is set in the future.
        $status = $this->status;
        if ($publishedAt && $publishedAt->isFuture() && $status === 'published') {
            $status = BlogPost::STATUS_SCHEDULED;
        }
        if ($status === BlogPost::STATUS_PUBLISHED && ! $publishedAt) {
            $publishedAt = now();
        }

        $data = [
            'author_id' => auth()->id(),
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'cover_image' => $this->cover_image,
            'status' => $status,
            'is_featured' => $this->is_featured,
            'allow_comments' => $this->allow_comments,
            'show_toc' => $this->show_toc,
            'published_at' => $publishedAt,
        ];

        if ($this->post) {
            $this->post->update($data);
        } else {
            $this->post = BlogPost::create($data);
        }

        $this->post->reading_minutes = $this->post->calculateReadingMinutes();
        $this->post->saveQuietly();

        $this->post->categories()->sync($this->categoryIds);
        $this->post->tags()->sync($this->tagIds);

        session()->flash('status', __('Post saved.'));
        $this->redirectRoute('admin.blog.posts.index', navigate: true);
    }

    public function render(): View
    {
        return view('blog::admin.posts.form', [
            'categories' => BlogCategory::query()->orderBy('name')->get(['id', 'name']),
            'tags' => BlogTag::query()->whereIn('id', $this->tagIds)->get(['id', 'name']),
        ]);
    }
}
