<?php

namespace App\Core\PageBuilder;

use App\Models\Page;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\HtmlString;

/**
 * Renders a published Page (or a draft preview) by walking its blocks
 * in order through the BlockRenderer. The result is a single HtmlString
 * the active theme drops into its `page` layout.
 */
class PageRenderer
{
    public function __construct(protected BlockRenderer $blocks) {}

    public function render(Page $page, ?Authenticatable $user = null): HtmlString
    {
        $html = '';
        foreach ($page->blocks as $block) {
            $html .= $this->blocks->renderPageBlock($block, $user)->toHtml();
        }

        return new HtmlString($html);
    }

    public function homepage(): ?Page
    {
        return Page::query()
            ->where('is_homepage', true)
            ->where('status', 'published')
            ->first();
    }

    public function findPublished(string $slug): ?Page
    {
        return Page::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();
    }
}
