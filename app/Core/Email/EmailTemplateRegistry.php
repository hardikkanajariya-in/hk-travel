<?php

namespace App\Core\Email;

/**
 * Catalog of all known email template keys + their advertised variables.
 *
 * Code that sends mail registers the key here so the admin UI knows
 * what placeholders are available, and so the seeder can pre-create
 * the row with sensible defaults.
 */
class EmailTemplateRegistry
{
    /** @var array<string, array{label: string, description?: string, variables: array<int, string>}> */
    protected array $templates = [];

    public function register(string $key, string $label, array $variables = [], ?string $description = null): void
    {
        $this->templates[$key] = [
            'label' => $label,
            'description' => $description,
            'variables' => $variables,
        ];
    }

    /** @return array<string, array{label: string, description?: string, variables: array<int, string>}> */
    public function all(): array
    {
        return $this->templates;
    }

    /** @return array{label: string, description?: string, variables: array<int, string>}|null */
    public function get(string $key): ?array
    {
        return $this->templates[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return isset($this->templates[$key]);
    }
}
