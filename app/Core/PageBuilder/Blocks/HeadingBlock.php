<?php

namespace App\Core\PageBuilder\Blocks;

use App\Core\PageBuilder\Block;

class HeadingBlock extends Block
{
    public function key(): string
    {
        return 'heading';
    }

    public function name(): string
    {
        return 'Heading';
    }

    public function icon(): string
    {
        return 'h1';
    }

    public function category(): string
    {
        return 'Text';
    }

    public function defaultData(): array
    {
        return ['text' => 'Section heading', 'level' => 'h2', 'align' => 'left'];
    }

    public function fields(): array
    {
        return [
            ['key' => 'text', 'label' => 'Text', 'type' => 'text'],
            ['key' => 'level', 'label' => 'Level', 'type' => 'select', 'options' => ['h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4']],
            ['key' => 'align', 'label' => 'Align', 'type' => 'select', 'options' => ['left' => 'Left', 'center' => 'Center', 'right' => 'Right']],
        ];
    }

    public function view(): string
    {
        return 'page-builder::blocks.heading';
    }
}
