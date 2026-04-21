<?php

namespace App\Core\Concerns;

use App\Core\Contracts\HasSeoMeta;
use Illuminate\Support\Str;

/**
 * Default SEO payload extractor for module models.
 *
 * Looks for these conventional attributes on the model:
 *   - $name / $title         → SEO title
 *   - $excerpt / $summary    → meta description fallback
 *   - $description           → meta description fallback (stripped)
 *   - $cover_image / $image  → og:image
 *   - $seo (array cast)      → explicit override
 *
 * Models can override toSeoMeta() entirely or set $seoSchemaType.
 */
trait ProvidesSeoMeta
{
    public function toSeoMeta(): array
    {
        $seo = is_array($this->seo ?? null) ? $this->seo : [];

        return [
            'title' => $seo['meta_title']
                ?? $this->name
                ?? $this->title
                ?? class_basename(static::class),
            'description' => $seo['meta_description']
                ?? $this->excerpt
                ?? $this->summary
                ?? (isset($this->description) ? Str::limit(strip_tags((string) $this->description), 160, '') : null),
            'image' => $seo['og_image']
                ?? $this->cover_image
                ?? $this->image
                ?? null,
            'noindex' => (bool) ($seo['noindex'] ?? false),
            'canonical' => $seo['canonical'] ?? null,
            'schema' => $this->buildSeoSchema(),
        ];
    }

    /**
     * Override per-model to emit richer JSON-LD.
     *
     * @return array<string, mixed>|null
     */
    protected function buildSeoSchema(): ?array
    {
        return null;
    }

    public function implementsSeoMeta(): bool
    {
        return $this instanceof HasSeoMeta;
    }
}
