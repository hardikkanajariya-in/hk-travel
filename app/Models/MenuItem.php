<?php

namespace App\Models;

use App\Core\Permalink\PermalinkRouter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;

/**
 * @property int $id
 * @property int $menu_id
 * @property int|null $parent_id
 * @property string $label
 * @property string|null $url
 * @property string $target
 * @property string|null $icon
 * @property string|null $css_class
 * @property string $link_type
 * @property array<string, mixed>|null $link_target
 * @property array<string, mixed>|null $translations
 * @property int $position
 */
class MenuItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'link_target' => 'array',
            'translations' => 'array',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('position');
    }

    public function localizedLabel(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $this->translations[$locale]['label'] ?? $this->label;
    }

    public function resolveUrl(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        $localizedUrl = $this->translations[$locale]['url'] ?? null;
        if ($localizedUrl) {
            return $localizedUrl;
        }

        return match ($this->link_type) {
            'route' => $this->resolveRouteUrl(),
            'page' => $this->resolvePageUrl(),
            'permalink' => $this->resolvePermalinkUrl(),
            default => (string) ($this->url ?? '#'),
        };
    }

    protected function resolveRouteUrl(): string
    {
        $name = $this->link_target['name'] ?? null;
        if (! $name || ! Route::has($name)) {
            return '#';
        }

        return route($name, $this->link_target['params'] ?? []);
    }

    protected function resolvePageUrl(): string
    {
        $pageId = $this->link_target['page_id'] ?? null;
        if (! $pageId) {
            return '#';
        }
        $page = Page::query()->find($pageId);

        return $page ? url('/'.ltrim($page->slug, '/')) : '#';
    }

    protected function resolvePermalinkUrl(): string
    {
        $entity = $this->link_target['entity'] ?? null;
        $tokens = $this->link_target['tokens'] ?? [];
        if (! $entity) {
            return '#';
        }

        return url(app(PermalinkRouter::class)->build($entity, $tokens));
    }
}
