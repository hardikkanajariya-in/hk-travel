<?php

namespace App\Core\PageBuilder\Blocks;

use App\Core\PageBuilder\Block;

class ColumnsBlock extends Block
{
    public function key(): string
    {
        return 'columns';
    }

    public function name(): string
    {
        return 'Columns';
    }

    public function icon(): string
    {
        return 'view-columns';
    }

    public function category(): string
    {
        return 'Layout';
    }

    public function defaultData(): array
    {
        return ['layout' => '1-1', 'left_html' => '<p>Left column</p>', 'right_html' => '<p>Right column</p>'];
    }

    public function fields(): array
    {
        return [
            ['key' => 'layout', 'label' => 'Split', 'type' => 'select', 'options' => ['1-1' => '50 / 50', '1-2' => '33 / 67', '2-1' => '67 / 33']],
            ['key' => 'left_html', 'label' => 'Left content', 'type' => 'richtext'],
            ['key' => 'right_html', 'label' => 'Right content', 'type' => 'richtext'],
        ];
    }

    public function view(): string
    {
        return 'page-builder::blocks.columns';
    }
}
