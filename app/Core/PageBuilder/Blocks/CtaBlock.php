<?php

namespace App\Core\PageBuilder\Blocks;

use App\Core\PageBuilder\Block;

class CtaBlock extends Block
{
    public function key(): string
    {
        return 'cta';
    }

    public function name(): string
    {
        return 'Call to action';
    }

    public function icon(): string
    {
        return 'megaphone';
    }

    public function category(): string
    {
        return 'Action';
    }

    public function defaultData(): array
    {
        return [
            'heading' => 'Ready to start your next adventure?',
            'subheading' => 'Talk to one of our travel specialists.',
            'cta_label' => 'Get in touch',
            'cta_url' => '/contact',
            'variant' => 'primary',
        ];
    }

    public function fields(): array
    {
        return [
            ['key' => 'heading', 'label' => 'Heading', 'type' => 'text'],
            ['key' => 'subheading', 'label' => 'Subheading', 'type' => 'textarea'],
            ['key' => 'cta_label', 'label' => 'CTA label', 'type' => 'text'],
            ['key' => 'cta_url', 'label' => 'CTA URL', 'type' => 'url'],
            ['key' => 'variant', 'label' => 'Style', 'type' => 'select', 'options' => ['primary' => 'Primary', 'subtle' => 'Subtle']],
        ];
    }

    public function view(): string
    {
        return 'page-builder::blocks.cta';
    }
}
