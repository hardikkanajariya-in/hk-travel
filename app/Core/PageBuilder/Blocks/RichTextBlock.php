<?php

namespace App\Core\PageBuilder\Blocks;

use App\Core\PageBuilder\Block;

class RichTextBlock extends Block
{
    public function key(): string
    {
        return 'rich_text';
    }

    public function name(): string
    {
        return 'Rich text';
    }

    public function icon(): string
    {
        return 'document-text';
    }

    public function category(): string
    {
        return 'Text';
    }

    public function defaultData(): array
    {
        return ['html' => '<p>Write something inspiring…</p>'];
    }

    public function fields(): array
    {
        return [
            ['key' => 'html', 'label' => 'Content', 'type' => 'richtext'],
        ];
    }

    public function view(): string
    {
        return 'page-builder::blocks.rich-text';
    }
}
