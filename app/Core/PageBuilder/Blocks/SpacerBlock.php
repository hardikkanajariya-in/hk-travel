<?php

namespace App\Core\PageBuilder\Blocks;

use App\Core\PageBuilder\Block;

class SpacerBlock extends Block
{
    public function key(): string
    {
        return 'spacer';
    }

    public function name(): string
    {
        return 'Spacer';
    }

    public function icon(): string
    {
        return 'arrows-up-down';
    }

    public function category(): string
    {
        return 'Layout';
    }

    public function defaultData(): array
    {
        return ['size' => 'md'];
    }

    public function fields(): array
    {
        return [
            ['key' => 'size', 'label' => 'Size', 'type' => 'select', 'options' => ['sm' => 'Small', 'md' => 'Medium', 'lg' => 'Large', 'xl' => 'Extra large']],
        ];
    }

    public function view(): string
    {
        return 'page-builder::blocks.spacer';
    }
}
