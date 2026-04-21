<?php

namespace App\Core\PageBuilder\Blocks;

use App\Core\PageBuilder\Block;

class TestimonialsBlock extends Block
{
    public function key(): string
    {
        return 'testimonials';
    }

    public function name(): string
    {
        return 'Testimonials';
    }

    public function icon(): string
    {
        return 'chat-bubble-left-right';
    }

    public function category(): string
    {
        return 'Social proof';
    }

    public function defaultData(): array
    {
        return ['title' => 'What travellers say', 'items' => []];
    }

    public function fields(): array
    {
        return [
            ['key' => 'title', 'label' => 'Title', 'type' => 'text'],
            ['key' => 'items', 'label' => 'Testimonials', 'type' => 'repeater', 'fields' => [
                ['key' => 'quote', 'label' => 'Quote', 'type' => 'textarea'],
                ['key' => 'author', 'label' => 'Author', 'type' => 'text'],
                ['key' => 'role', 'label' => 'Role / location', 'type' => 'text'],
                ['key' => 'avatar', 'label' => 'Avatar URL', 'type' => 'image'],
            ]],
        ];
    }

    public function view(): string
    {
        return 'page-builder::blocks.testimonials';
    }
}
