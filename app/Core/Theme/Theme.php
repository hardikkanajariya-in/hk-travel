<?php

namespace App\Core\Theme;

/**
 * Manifest for a public-facing theme.
 *
 * Themes live under resources/themes/{key} and ship their own views,
 * blade components, CSS entry, and an optional config file. The active
 * theme is selected via config('hk.theme.active') (DB-overridable).
 */
class Theme
{
    public function __construct(
        public readonly string $key,
        public readonly string $name,
        public readonly string $version,
        public readonly string $author,
        public readonly string $path,
        public readonly ?string $screenshot = null,
        public readonly ?string $description = null,
        /** @var array<string, mixed> */
        public readonly array $supports = [],
    ) {}

    public function viewsPath(): string
    {
        return $this->path.DIRECTORY_SEPARATOR.'views';
    }

    public function assetsPath(): string
    {
        return $this->path.DIRECTORY_SEPARATOR.'assets';
    }

    public function configFile(): string
    {
        return $this->path.DIRECTORY_SEPARATOR.'theme.php';
    }
}
