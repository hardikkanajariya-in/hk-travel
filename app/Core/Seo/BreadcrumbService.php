<?php

namespace App\Core\Seo;

/**
 * Per-request collector for breadcrumb trails. Layouts pull the
 * accumulated trail at render time; controllers and Livewire
 * components push to it during the request lifecycle.
 */
class BreadcrumbService
{
    /** @var array<int, array{name:string, url:?string}> */
    protected array $crumbs = [];

    public function push(string $name, ?string $url = null): self
    {
        $this->crumbs[] = ['name' => $name, 'url' => $url];

        return $this;
    }

    public function clear(): self
    {
        $this->crumbs = [];

        return $this;
    }

    /** @return array<int, array{name:string, url:?string}> */
    public function all(): array
    {
        return $this->crumbs;
    }

    public function isEmpty(): bool
    {
        return empty($this->crumbs);
    }
}
