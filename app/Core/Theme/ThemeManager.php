<?php

namespace App\Core\Theme;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use RuntimeException;

/**
 * Discovers and activates public themes.
 *
 * On boot, scans `resources/themes/*` for `theme.php` manifests,
 * loads the active one (per config/hk.theme.active), and tells Laravel's
 * view finder to look inside that theme's `views/` folder first.
 *
 * Module views still resolve through their module namespaces; the active
 * theme can override them via {moduleKey}/* subfolders inside the theme.
 */
class ThemeManager
{
    /** @var Collection<string, Theme> */
    protected Collection $themes;

    protected ?Theme $active = null;

    public function __construct(
        protected Application $app,
        protected Filesystem $files,
    ) {
        $this->themes = collect();
    }

    public function discover(): self
    {
        $base = (string) config('hk.theme.path', resource_path('themes'));

        if (! $this->files->isDirectory($base)) {
            return $this;
        }

        foreach ($this->files->directories($base) as $dir) {
            $manifest = $dir.DIRECTORY_SEPARATOR.'theme.php';

            if (! $this->files->exists($manifest)) {
                continue;
            }

            $data = require $manifest;

            if (! is_array($data) || empty($data['key'])) {
                continue;
            }

            $this->themes->put($data['key'], new Theme(
                key: $data['key'],
                name: $data['name'] ?? $data['key'],
                version: $data['version'] ?? '0.0.0',
                author: $data['author'] ?? 'Unknown',
                path: $dir,
                screenshot: $data['screenshot'] ?? null,
                description: $data['description'] ?? null,
                supports: $data['supports'] ?? [],
            ));
        }

        return $this;
    }

    public function activate(?string $key = null): void
    {
        $key ??= (string) config('hk.theme.active', 'default');

        $theme = $this->themes->get($key);

        if (! $theme) {
            return; // Fail soft: no theme = use bare app views (e.g. fresh install).
        }

        $this->active = $theme;

        $finder = $this->app['view']->getFinder();

        if ($this->files->isDirectory($theme->viewsPath())) {
            $finder->prependLocation($theme->viewsPath());
        }
    }

    /** @return Collection<string, Theme> */
    public function all(): Collection
    {
        return $this->themes;
    }

    public function active(): ?Theme
    {
        return $this->active;
    }

    public function get(string $key): Theme
    {
        return $this->themes->get($key)
            ?? throw new RuntimeException("Theme [$key] not found.");
    }
}
