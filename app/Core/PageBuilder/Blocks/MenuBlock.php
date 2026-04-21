<?php

namespace App\Core\PageBuilder\Blocks;

use App\Core\PageBuilder\Block;

class MenuBlock extends Block
{
    public function key(): string
    {
        return 'menu';
    }

    public function name(): string
    {
        return 'Menu';
    }

    public function icon(): string
    {
        return 'bars-3';
    }

    public function category(): string
    {
        return 'Navigation';
    }

    public function defaultData(): array
    {
        return ['location' => 'primary', 'orientation' => 'horizontal'];
    }

    public function fields(): array
    {
        return [
            ['key' => 'location', 'label' => 'Menu location', 'type' => 'text'],
            ['key' => 'orientation', 'label' => 'Orientation', 'type' => 'select', 'options' => ['horizontal' => 'Horizontal', 'vertical' => 'Vertical']],
        ];
    }

    public function view(): string
    {
        return 'page-builder::blocks.menu';
    }
}
