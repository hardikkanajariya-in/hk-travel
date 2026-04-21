<?php

namespace App\Core\Seo;

use App\Core\Branding\BrandingService;
use App\Core\Settings\SettingsRepository;
use App\Models\Page;
use Illuminate\Support\Str;

/**
 * Single source of truth for the meta-tag stack rendered into every
 * public-theme `<head>` block. Pages, modules and ad-hoc views all
 * pour into one instance so that the final markup is consistent and
 * deduplicated.
 */
class SeoManager
{
    protected ?string $title = null;

    protected ?string $description = null;

    protected ?string $canonical = null;

    protected ?string $image = null;

    protected bool $noindex = false;

    /** @var array<string, string> */
    protected array $extra = [];

    /** @var array<int, string> */
    protected array $hreflangs = [];

    public function __construct(
        protected SettingsRepository $settings,
        protected BrandingService $branding,
    ) {}

    public function title(?string $title): self
    {
        $this->title = $title ?: null;

        return $this;
    }

    public function description(?string $description): self
    {
        $this->description = $description ? Str::limit(strip_tags($description), 160, '') : null;

        return $this;
    }

    public function canonical(?string $url): self
    {
        $this->canonical = $url;

        return $this;
    }

    public function image(?string $url): self
    {
        $this->image = $url;

        return $this;
    }

    public function noindex(bool $value = true): self
    {
        $this->noindex = $value;

        return $this;
    }

    public function meta(string $name, string $content): self
    {
        $this->extra[$name] = $content;

        return $this;
    }

    public function fromPage(Page $page, ?string $locale = null): self
    {
        $locale = $locale ?: app()->getLocale();
        $seo = (array) $page->seo;

        $this->title($seo['meta_title'] ?? $page->title);
        $this->description($seo['meta_description'] ?? null);
        $this->image($seo['og_image'] ?? null);
        $this->noindex((bool) ($seo['noindex'] ?? false));
        $this->canonical(url('/'.$page->slug));

        return $this;
    }

    public function resolvedTitle(): string
    {
        $site = $this->branding->siteName();
        $explicit = $this->title ?: $this->settings->get('seo.default_title');

        if ($explicit) {
            return $explicit.' · '.$site;
        }

        $tagline = $this->settings->get('seo.default_tagline', $this->branding->tagline());

        return $tagline ? $site.' — '.$tagline : $site;
    }

    public function resolvedDescription(): ?string
    {
        return $this->description ?: $this->settings->get('seo.default_description');
    }

    public function resolvedImage(): ?string
    {
        return $this->image ?: $this->settings->get('seo.default_image');
    }

    public function isNoindex(): bool
    {
        return $this->noindex || (bool) $this->settings->get('seo.noindex_site', false);
    }

    /** @return array<string, string|null> */
    public function snapshot(): array
    {
        return [
            'title' => $this->resolvedTitle(),
            'description' => $this->resolvedDescription(),
            'canonical' => $this->canonical,
            'image' => $this->resolvedImage(),
            'noindex' => $this->isNoindex(),
            'extra' => $this->extra,
        ];
    }
}
