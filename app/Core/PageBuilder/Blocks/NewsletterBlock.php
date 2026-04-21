<?php

namespace App\Core\PageBuilder\Blocks;

use App\Core\PageBuilder\Block;

class NewsletterBlock extends Block
{
    public function key(): string
    {
        return 'newsletter';
    }

    public function name(): string
    {
        return 'Newsletter signup';
    }

    public function icon(): string
    {
        return 'envelope';
    }

    public function category(): string
    {
        return 'Action';
    }

    public function defaultData(): array
    {
        return [
            'heading' => 'Get travel inspiration in your inbox',
            'subheading' => 'No spam, unsubscribe any time.',
            'placeholder' => 'you@example.com',
            'cta_label' => 'Subscribe',
        ];
    }

    public function fields(): array
    {
        return [
            ['key' => 'heading', 'label' => 'Heading', 'type' => 'text'],
            ['key' => 'subheading', 'label' => 'Subheading', 'type' => 'textarea'],
            ['key' => 'placeholder', 'label' => 'Email placeholder', 'type' => 'text'],
            ['key' => 'cta_label', 'label' => 'CTA label', 'type' => 'text'],
        ];
    }

    public function view(): string
    {
        return 'page-builder::blocks.newsletter';
    }
}
