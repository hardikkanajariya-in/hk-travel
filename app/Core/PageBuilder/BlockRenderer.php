<?php

namespace App\Core\PageBuilder;

use App\Models\PageBlock;
use App\Models\Widget;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\HtmlString;

/**
 * Renders a single block (page block or widget) to HTML.
 *
 * Centralised here so admin previews, public pages and widget zones all
 * go through the same pipeline (registry lookup → permission gate →
 * blade view). Permission-gated blocks (Custom HTML/CSS/JS) silently
 * render nothing for non-authorised viewers.
 */
class BlockRenderer
{
    public function __construct(protected BlockRegistry $registry) {}

    public function renderPageBlock(PageBlock $block, ?Authenticatable $user = null): HtmlString
    {
        return $this->render($block->type, (array) $block->data, $user, $block->visibilityClasses());
    }

    public function renderWidget(Widget $widget, ?Authenticatable $user = null): HtmlString
    {
        return $this->render($widget->type, (array) $widget->data, $user);
    }

    /** @param array<string, mixed> $data */
    public function render(string $type, array $data, ?Authenticatable $user = null, string $visibilityClasses = ''): HtmlString
    {
        if (! $this->registry->has($type)) {
            return new HtmlString('');
        }

        $block = $this->registry->get($type);

        if ($block->permission() && ! $this->allowed($user, $block->permission())) {
            return new HtmlString('');
        }

        $rendered = view($block->view(), [
            'data' => array_merge($block->defaultData(), $data),
            'block' => $block,
        ])->render();

        if ($visibilityClasses !== '') {
            $rendered = '<div class="'.e($visibilityClasses).'">'.$rendered.'</div>';
        }

        return new HtmlString($rendered);
    }

    protected function allowed(?Authenticatable $user, string $ability): bool
    {
        if (! $user) {
            return false;
        }

        return Gate::forUser($user)->check($ability);
    }
}
