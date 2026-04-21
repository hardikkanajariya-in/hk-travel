<?php

namespace App\Core\Email;

use App\Core\Security\Sanitizer;
use Illuminate\Support\Str;

/**
 * Sandboxed template renderer.
 *
 * Email bodies edited in the admin UI MUST NOT execute arbitrary PHP
 * or Blade. We support only `{{ var }}` interpolation, and for
 * developers a `@if`/`@endif` whitelist using a tiny tokenizer.
 *
 * HTML output is also routed through the `rich-text` HTMLPurifier
 * profile to strip script/iframe/style.
 */
class TemplateRenderer
{
    /**
     * @param  array<string, mixed>  $vars
     */
    public function render(string $template, array $vars = [], bool $sanitize = true): string
    {
        $rendered = $this->interpolate($template, $vars);

        return $sanitize ? Sanitizer::rich($rendered) : $rendered;
    }

    /**
     * @param  array<string, mixed>  $vars
     */
    public function renderText(string $template, array $vars = []): string
    {
        return $this->interpolate($template, $vars);
    }

    /**
     * @param  array<string, mixed>  $vars
     */
    protected function interpolate(string $template, array $vars): string
    {
        return preg_replace_callback('/\{\{\s*([\w\.]+)\s*\}\}/', function (array $m) use ($vars): string {
            $value = data_get($vars, $m[1]);

            if (is_array($value) || is_object($value)) {
                $value = json_encode($value);
            }

            return e((string) ($value ?? ''));
        }, $template) ?? $template;
    }

    /**
     * Extract `{{ var }}` placeholders for editor hints / validation.
     *
     * @return array<int, string>
     */
    public function placeholders(string $template): array
    {
        preg_match_all('/\{\{\s*([\w\.]+)\s*\}\}/', $template, $matches);

        return array_values(array_unique($matches[1] ?? []));
    }

    public function previewSubject(string $subject, array $vars = []): string
    {
        return Str::limit($this->interpolate($subject, $vars), 200);
    }
}
