<?php

namespace App\Core\Branding;

use App\Core\Settings\SettingsRepository;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

/**
 * Resolves runtime branding (colors, fonts, logos) into HTML the
 * layouts can drop in. Reads everything through SettingsRepository
 * so changes take effect on the next request without a deploy.
 *
 * Color tokens are emitted as CSS variables under :root and consumed
 * by the Tailwind v4 theme via `@theme { --color-hk-primary-*: var(...) }`.
 */
class BrandingService
{
    public function __construct(protected SettingsRepository $settings) {}

    public function siteName(): string
    {
        return (string) $this->settings->get('brand.name', config('hk.brand.name'));
    }

    public function tagline(): ?string
    {
        return $this->settings->get('brand.tagline', config('hk.brand.tagline'));
    }

    public function logoUrl(): ?string
    {
        return $this->settings->get('brand.logo', config('hk.brand.logo'));
    }

    public function darkLogoUrl(): ?string
    {
        return $this->settings->get('brand.logo_dark', null);
    }

    public function faviconUrl(): ?string
    {
        return $this->settings->get('brand.favicon', config('hk.brand.favicon'));
    }

    public function primaryColor(): string
    {
        return (string) $this->settings->get('brand.primary_color', config('hk.brand.primary_color', '#2563eb'));
    }

    public function accentColor(): string
    {
        return (string) $this->settings->get('brand.accent_color', config('hk.brand.accent_color', '#f97316'));
    }

    public function fontFamily(): string
    {
        return (string) $this->settings->get('brand.font_family', 'Inter');
    }

    public function fontUrl(): ?string
    {
        $family = $this->fontFamily();
        if ($family === '' || $family === 'system') {
            return null;
        }

        $encoded = str_replace(' ', '+', $family);

        // Bunny CDN (privacy-friendly Google Fonts mirror) — already allowed by CSP.
        return "https://fonts.bunny.net/css2?family={$encoded}:wght@400;500;600;700&display=swap";
    }

    public function showHeader(): bool
    {
        return (bool) $this->settings->get('brand.show_header', true);
    }

    public function showFooter(): bool
    {
        return (bool) $this->settings->get('brand.show_footer', true);
    }

    /**
     * Inline <style> + <link> tags for the document head.
     */
    public function headTags(): Htmlable
    {
        $primary = $this->hexToRgbVars($this->primaryColor());
        $accent = $this->hexToRgbVars($this->accentColor());
        $fontUrl = $this->fontUrl();
        $favicon = $this->faviconUrl();
        $family = $this->fontFamily();

        $css = ":root{--hk-primary:{$this->primaryColor()};--hk-primary-rgb:{$primary};--hk-accent:{$this->accentColor()};--hk-accent-rgb:{$accent};--hk-font-sans:'{$family}',ui-sans-serif,system-ui,sans-serif;}";

        $html = "<style>{$css}</style>";

        if ($fontUrl) {
            $html .= '<link rel="preconnect" href="https://fonts.bunny.net" crossorigin>';
            $html .= '<link rel="stylesheet" href="'.e($fontUrl).'">';
        }

        if ($favicon) {
            $html .= '<link rel="icon" href="'.e($favicon).'">';
        }

        return new HtmlString($html);
    }

    protected function hexToRgbVars(string $hex): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (strlen($hex) !== 6 || ! ctype_xdigit($hex)) {
            return '37 99 235';
        }

        return hexdec(substr($hex, 0, 2)).' '.hexdec(substr($hex, 2, 2)).' '.hexdec(substr($hex, 4, 2));
    }
}
