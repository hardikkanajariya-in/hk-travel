<?php

namespace App\Core\Seo;

use App\Core\Branding\BrandingService;
use App\Core\Settings\SettingsRepository;
use Illuminate\Support\HtmlString;

/**
 * Builds tiny, hand-rolled JSON-LD blobs for the schemas that pay
 * back the most in SEO: Organization, WebSite (sitelinks search),
 * BreadcrumbList and Article. We deliberately avoid pulling in a
 * heavyweight schema library — the surface area here is small and
 * the structures are stable.
 */
class JsonLd
{
    /** @var array<int, array<string, mixed>> */
    protected array $graph = [];

    public function __construct(
        protected SettingsRepository $settings,
        protected BrandingService $branding,
    ) {}

    public function organization(): self
    {
        $this->graph[] = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $this->branding->siteName(),
            'url' => url('/'),
            'logo' => $this->branding->logoUrl(),
            'sameAs' => array_values(array_filter([
                $this->settings->get('contact.social.facebook'),
                $this->settings->get('contact.social.instagram'),
                $this->settings->get('contact.social.twitter'),
                $this->settings->get('contact.social.linkedin'),
                $this->settings->get('contact.social.youtube'),
            ])),
        ]);

        return $this;
    }

    public function website(): self
    {
        $this->graph[] = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $this->branding->siteName(),
            'url' => url('/'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => url('/search?q={search_term_string}'),
                'query-input' => 'required name=search_term_string',
            ],
        ];

        return $this;
    }

    /** @param array<int, array{name:string, url:?string}> $crumbs */
    public function breadcrumbs(array $crumbs): self
    {
        if (empty($crumbs)) {
            return $this;
        }

        $this->graph[] = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_values(array_map(fn (array $c, int $i): array => array_filter([
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $c['name'],
                'item' => $c['url'] ?? null,
            ]), $crumbs, array_keys($crumbs))),
        ];

        return $this;
    }

    /** @param array<string, mixed> $data */
    public function article(array $data): self
    {
        $this->graph[] = array_filter(array_merge([
            '@context' => 'https://schema.org',
            '@type' => 'Article',
        ], $data));

        return $this;
    }

    /** @param array<string, mixed> $node */
    public function add(array $node): self
    {
        $this->graph[] = $node;

        return $this;
    }

    public function render(): HtmlString
    {
        if (empty($this->graph)) {
            return new HtmlString('');
        }

        $json = json_encode(
            count($this->graph) === 1 ? $this->graph[0] : ['@context' => 'https://schema.org', '@graph' => $this->graph],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        if ($json === false) {
            return new HtmlString('');
        }

        return new HtmlString('<script type="application/ld+json">'.$json.'</script>');
    }
}
