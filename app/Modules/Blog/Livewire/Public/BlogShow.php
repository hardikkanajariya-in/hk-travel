<?php

namespace App\Modules\Blog\Livewire\Public;

use App\Core\Concerns\EnsuresCanonicalPublicUrl;
use App\Core\Routing\PublicUrlGenerator;
use App\Core\Seo\SeoManager;
use App\Modules\Blog\Models\BlogPost;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class BlogShow extends Component
{
    use EnsuresCanonicalPublicUrl;

    public BlogPost $post;

    public function mount(string $slug, SeoManager $seo, PublicUrlGenerator $urls): void
    {
        $this->post = BlogPost::query()
            ->with(['author', 'categories', 'tags'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Best-effort, idempotent view counter; does not block render.
        BlogPost::query()->whereKey($this->post->id)->increment('view_count');

        $meta = $this->post->toSeoMeta();
        $seo->title($meta['title'])
            ->description($meta['description'])
            ->image($meta['image']);

        $this->ensureCanonicalPublicUrl('blog_post', $this->post->slug, $seo, $urls);
    }

    public function render(): View
    {
        $catIds = $this->post->categories->pluck('id');
        $tagIds = $this->post->tags->pluck('id');

        $related = BlogPost::query()
            ->published()
            ->whereKeyNot($this->post->id)
            ->where(function (Builder $q) use ($catIds, $tagIds): void {
                if ($catIds->isNotEmpty()) {
                    $q->orWhereHas('categories', fn ($c) => $c->whereIn('blog_categories.id', $catIds));
                }
                if ($tagIds->isNotEmpty()) {
                    $q->orWhereHas('tags', fn ($t) => $t->whereIn('blog_tags.id', $tagIds));
                }
            })
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('blog::public.show', [
            'post' => $this->post,
            'related' => $related,
            'toc' => $this->post->tableOfContents(),
            'bodyHtml' => $this->post->bodyWithAnchors(),
        ]);
    }
}
