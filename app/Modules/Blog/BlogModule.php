<?php

namespace App\Modules\Blog;

use App\Core\Modules\Module;
use App\Modules\Blog\Livewire\Admin\BlogCategoryForm;
use App\Modules\Blog\Livewire\Admin\BlogCategoryTable;
use App\Modules\Blog\Livewire\Admin\BlogPostForm;
use App\Modules\Blog\Livewire\Admin\BlogPostTable;
use App\Modules\Blog\Livewire\Admin\BlogTagForm;
use App\Modules\Blog\Livewire\Admin\BlogTagTable;
use App\Modules\Blog\Livewire\Public\BlogIndex;
use App\Modules\Blog\Livewire\Public\BlogShow;
use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\BlogPost;
use Illuminate\Support\Facades\Schema;

/**
 * Blog module manifest.
 *
 * Provides categories, tags, scheduled publishing, related-posts logic,
 * an RSS feed, and an auto-generated table of contents on the public
 * detail page. Posts opt-in to the Comments module via `allow_comments`.
 *
 * Registered in config/hk-modules.php under key `blog`.
 */
class BlogModule extends Module
{
    public function key(): string
    {
        return 'blog';
    }

    public function name(): string
    {
        return 'Blog & Travel Guides';
    }

    public function version(): string
    {
        return '0.1.0';
    }

    public function permissions(): array
    {
        return [
            'blog.view',
            'blog.create',
            'blog.update',
            'blog.delete',
            'blog.publish',
            'blog.taxonomy.manage',
        ];
    }

    public function adminMenu(): array
    {
        return [
            [
                'label' => 'Posts',
                'route' => 'admin.blog.posts.index',
                'icon' => 'file-text',
                'permission' => 'blog.view',
                'group' => 'Blog',
            ],
            [
                'label' => 'Categories',
                'route' => 'admin.blog.categories.index',
                'icon' => 'folder',
                'permission' => 'blog.taxonomy.manage',
                'group' => 'Blog',
            ],
            [
                'label' => 'Tags',
                'route' => 'admin.blog.tags.index',
                'icon' => 'tag',
                'permission' => 'blog.taxonomy.manage',
                'group' => 'Blog',
            ],
        ];
    }

    public function livewireComponents(): array
    {
        return [
            'blog-public.blog-index' => BlogIndex::class,
            'blog-public.blog-show' => BlogShow::class,
            'blog-admin.blog-post-table' => BlogPostTable::class,
            'blog-admin.blog-post-form' => BlogPostForm::class,
            'blog-admin.blog-category-table' => BlogCategoryTable::class,
            'blog-admin.blog-category-form' => BlogCategoryForm::class,
            'blog-admin.blog-tag-table' => BlogTagTable::class,
            'blog-admin.blog-tag-form' => BlogTagForm::class,
        ];
    }

    public function sitemapEntries(): iterable
    {
        if (! Schema::hasTable('blog_posts')) {
            return [];
        }

        foreach (BlogPost::query()->published()->get(['slug', 'updated_at']) as $row) {
            yield [
                'loc' => route('blog.show', $row->slug),
                'lastmod' => $row->updated_at,
                'changefreq' => 'weekly',
                'priority' => 0.7,
            ];
        }

        if (Schema::hasTable('blog_categories')) {
            foreach (BlogCategory::query()->get(['slug', 'updated_at']) as $row) {
                yield [
                    'loc' => route('blog.category', $row->slug),
                    'lastmod' => $row->updated_at,
                    'changefreq' => 'weekly',
                    'priority' => 0.5,
                ];
            }
        }
    }
}
