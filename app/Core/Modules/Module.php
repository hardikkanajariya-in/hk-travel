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
