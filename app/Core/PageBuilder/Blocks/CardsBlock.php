<?php

namespace App\Core\PageBuilder\Blocks;

use App\Core\PageBuilder\Block;

class CardsBlock extends Block
{
    public function key(): string
    {
        return 'cards';
    }

    public function name(): string
    {
        return 'Cards / Grid';
    }

    public function icon(): string
    {
        return 'squares-2x2';
    }

    public function category(): string
    {
        return 'Layout';
    }

    public function defaultData(): array
    {
        return ['columns' => 3, 'cards' => []];
    }

    public function fields(): array
    {
        return [
            ['key' => 'columns', 'label' => 'Columns', 'type' => 'select', 'options' => ['2' => '2', '3' => '3', '4' => '4']],
            ['key' => 'cards', 'label' => 'Cards', 'type' => 'repeater', 'fields' => [
                ['key' => 'image', 'label' => 'Image', 'type' => 'image'],
                ['key' => 'title', 'label' => 'Title', 'type' => 'text'],
                ['key' => 'body', 'label' => 'Body', 'type' => 'textarea'],
                ['key' => 'cta_label', 'label' => 'CTA label', 'type' => 'text'],
                ['key' => 'cta_url', 'label' => 'CTA URL', 'type' => 'url'],
            ]],
        ];
    }

    public function view(): string
    {
        return 'page-builder::blocks.cards';
    }
}
