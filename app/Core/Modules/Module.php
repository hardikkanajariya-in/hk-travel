<?php

namespace App\Core\Modules;

abstract class Module implements ModuleContract
{
    public function permissions(): array
    {
        return [];
    }

    public function publicRoutesPath(): ?string
    {
        return $this->path('routes/web.php');
    }

    public function adminRoutesPath(): ?string
    {
        return $this->path('routes/admin.php');
    }

    public function migrationsPath(): ?string
    {
        return $this->path('database/migrations');
    }

    public function viewsPath(): ?string
    {
        return $this->path('resources/views');
    }

    public function langPath(): ?string
    {
        return $this->path('resources/lang');
    }

    public function viewNamespace(): ?string
    {
        return $this->key();
    }

    public function provider(): ?string
    {
        return null;
    }

    /**
     * Livewire component aliases this module exposes.
     * Map of alias => fully-qualified component class.
     *
     * Aliases let views embed components with `<livewire:alias />`
     * without depending on Livewire auto-discovery, which doesn't
     * scan `app/Modules/...`.
     *
     * @return array<string, class-string>
     */
    public function livewireComponents(): array
    {
        return [];
    }

    /**
     * Admin sidebar entries contributed by this module.
     * Each: ['label' => string, 'route' => string, 'icon' => string,
     *        'permission' => string|null, 'group' => string|null].
     *
     * @return array<int, array{label:string, route:string, icon?:?string, permission?:?string, group?:?string}>
     */
    public function adminMenu(): array
    {
        return [];
    }

    /**
     * Sitemap entries contributed by this module. Implementations may
     * be heavy (DB queries) — caller is expected to cache.
     *
     * @return iterable<int, array{loc:string, lastmod?:?\DateTimeInterface, changefreq?:?string, priority?:?float}>
     */
    public function sitemapEntries(): iterable
    {
        return [];
    }

    protected function path(string $relative): ?string
    {
        $base = $this->basePath().DIRECTORY_SEPARATOR.$relative;

        return is_file($base) || is_dir($base) ? $base : null;
    }

    protected function basePath(): string
    {
        $reflection = new \ReflectionClass(static::class);

        return dirname($reflection->getFileName());
    }
}
