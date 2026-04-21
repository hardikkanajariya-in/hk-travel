<?php

namespace App\Core\PageBuilder\Blocks;

use App\Core\PageBuilder\Block;

class ButtonBlock extends Block
{
    public function key(): string
    {
        return 'button';
    }

    public function name(): string
    {
        return 'Button';
    }

    public function icon(): string
    {
        return 'cursor-arrow-rays';
    }

    public function category(): string
    {
        return 'Action';
    }

    public function defaultData(): array
    {
        return ['label' => 'Click me', 'url' => '#', 'variant' => 'primary', 'size' => 'md', 'align' => 'left'];
    }

    public function fields(): array
    {
        return [
            ['key' => 'label', 'label' => 'Label', 'type' => 'text'],
            ['key' => 'url', 'label' => 'URL', 'type' => 'url'],
            ['key' => 'variant', 'label' => 'Style', 'type' => 'select', 'options' => ['primary' => 'Primary', 'secondary' => 'Secondary', 'outline' => 'Outline', 'ghost' => 'Ghost']],
            ['key' => 'size', 'label' => 'Size', 'type' => 'select', 'options' => ['sm' => 'Small', 'md' => 'Medium', 'lg' => 'Large']],
            ['key' => 'align', 'label' => 'Align', 'type' => 'select', 'options' => ['left' => 'Left', 'center' => 'Center', 'right' => 'Right']],
        ];
    }

    public function view(): string
    {
        return 'page-builder::blocks.button';
    }
}
