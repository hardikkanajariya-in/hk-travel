<?php

namespace App\Core\PageBuilder\Blocks;

use App\Core\PageBuilder\Block;

class DividerBlock extends Block
{
    public function key(): string
    {
        return 'divider';
    }

    public function name(): string
    {
        return 'Divider';
    }

    public function icon(): string
    {
        return 'minus';
    }

    public function category(): string
    {
        return 'Layout';
    }

    public function defaultData(): array
    {
        return ['style' => 'solid'];
    }

    public function fields(): array
    {
        return [
            ['key' => 'style', 'label' => 'Style', 'type' => 'select', 'options' => ['solid' => 'Solid', 'dashed' => 'Dashed', 'dotted' => 'Dotted']],
        ];
    }

    public function view(): string
    {
        return 'page-builder::blocks.divider';
    }
}
