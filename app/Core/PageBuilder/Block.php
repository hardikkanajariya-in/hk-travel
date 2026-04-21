<?php

namespace App\Core\PageBuilder;

use App\Core\Security\Sanitizer;

abstract class Block implements BlockContract
{
    public function permission(): ?string
    {
        return null;
    }

    public function category(): string
    {
        return 'Content';
    }

    public function defaultData(): array
    {
        return [];
    }

    public function fields(): array
    {
        return [];
    }

    /**
     * Sanitize submitted data — by default, runs HTML through HTMLPurifier
     * for any field declared as `richtext`, leaves the rest untouched, and
     * drops any keys not in the field schema.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function sanitize(array $data): array
    {
        $allowed = collect($this->fields())->pluck('key')->all();
        $clean = [];

        foreach ($this->fields() as $field) {
            $key = $field['key'];
            $value = $data[$key] ?? null;

            $clean[$key] = match ($field['type'] ?? 'text') {
                'richtext' => $value === null ? null : Sanitizer::rich((string) $value),
                'toggle' => (bool) $value,
                'repeater' => $this->normalizeRepeater($value),
                default => $value,
            };
        }

        // Preserve any extras already saved that the schema doesn't define.
        foreach ($data as $k => $v) {
            if (! in_array($k, $allowed, true)) {
                $clean[$k] = $v;
            }
        }

        return $clean;
    }

    /**
     * Coerce a repeater field value into a clean indexed array.
     *
     * Accepts arrays, JSON strings (admin UI uses a JSON textarea
     * for repeaters) and falls back to an empty array on bad input.
     */
    protected function normalizeRepeater(mixed $value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : [];
        }

        return is_array($value) ? array_values($value) : [];
    }
}
