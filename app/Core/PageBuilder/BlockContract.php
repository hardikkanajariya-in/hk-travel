<?php

namespace App\Core\PageBuilder;

/**
 * Contract every page-builder block (and widget) implements.
 *
 * Blocks are stored as rows in `page_blocks` (or `widgets`) with a
 * `type` key and a JSON `data` blob. The registry maps the type key to
 * a class implementing this contract; the class is responsible for
 * declaring its admin form schema, default data, and the blade view
 * used to render the block on the public site.
 */
interface BlockContract
{
    /** Stable, snake-case type key persisted in the DB. */
    public function key(): string;

    /** Human-readable name shown in the admin block picker. */
    public function name(): string;

    /** Heroicon name (without the `heroicon-` prefix) used in the picker. */
    public function icon(): string;

    /** Category in the admin block picker (e.g. "Layout", "Media"). */
    public function category(): string;

    /**
     * Default data when the block is first dropped into a page.
     *
     * @return array<string, mixed>
     */
    public function defaultData(): array;

    /**
     * Field schema rendered inside the admin block-editor drawer.
     * Each field: ['key' => string, 'type' => 'text|textarea|richtext|image|url|select|toggle|repeater|color', ...].
     *
     * @return array<int, array<string, mixed>>
     */
    public function fields(): array;

    /** Blade view used to render the block on the public site. */
    public function view(): string;

    /**
     * Permission gate name; null = always allowed.
     * Used by developer-only blocks (Custom HTML/CSS/JS).
     */
    public function permission(): ?string;

    /**
     * Sanitise raw form data before persistence (richtext, repeaters, etc.).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function sanitize(array $data): array;
}
