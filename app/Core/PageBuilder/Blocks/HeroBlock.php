<?php

namespace App\Core\PageBuilder\Blocks;

use App\Core\PageBuilder\Block;

class HeroBlock extends Block
{
    public function key(): string
    {
        return 'hero';
    }

    public function name(): string
    {
        return 'Hero';
    }

    public function icon(): string
    {
        return 'photo';
    }

    public function category(): string
    {
        return 'Hero';
    }

    public function defaultData(): array
    {
        return [
            'eyebrow' => null,
            'heading' => 'Discover the world, your way.',
            'subheading' => 'Curated tours, hand-picked hotels, all in one place.',
            'image' => null,
            'overlay' => 'dark',
            'align' => 'center',
            'cta_label' => 'Browse tours',
            'cta_url' => '#',
            'cta2_label' => null,
            'cta2_url' => null,
        ];
    }

    public function fields(): array
    {
        return [
            ['key' => 'eyebrow', 'label' => 'Eyebrow text', 'type' => 'text'],
            ['key' => 'heading', 'label' => 'Heading', 'type' => 'text'],
            ['key' => 'subheading', 'label' => 'Subheading', 'type' => 'textarea'],
            ['key' => 'image', 'label' => 'Background image URL', 'type' => 'image'],
            ['key' => 'overlay', 'label' => 'Overlay', 'type' => 'select', 'options' => ['none' => 'None', 'dark' => 'Dark', 'light' => 'Light']],
            ['key' => 'align', 'label' => 'Alignment', 'type' => 'select', 'options' => ['left' => 'Left', 'center' => 'Center', 'right' => 'Right']],
            ['key' => 'cta_label', 'label' => 'Primary CTA label', 'type' => 'text'],
            ['key' => 'cta_url', 'label' => 'Primary CTA URL', 'type' => 'url'],
            ['key' => 'cta2_label', 'label' => 'Secondary CTA label', 'type' => 'text'],
            ['key' => 'cta2_url', 'label' => 'Secondary CTA URL', 'type' => 'url'],
        ];
    }

    public function view(): string
    {
        return 'page-builder::blocks.hero';
    }
}
