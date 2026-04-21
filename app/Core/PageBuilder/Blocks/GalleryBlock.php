<?php

namespace App\Core\PageBuilder\Blocks;

use App\Core\PageBuilder\Block;

class GalleryBlock extends Block
{
    public function key(): string
    {
        return 'gallery';
    }

    public function name(): string
    {
        return 'Gallery';
    }

    public function icon(): string
    {
        return 'photo';
    }

    public function category(): string
    {
        return 'Media';
    }

    public function defaultData(): array
    {
        return ['images' => [], 'columns' => 3];
    }

    public function fields(): array
    {
        return [
            ['key' => 'images', 'label' => 'Images', 'type' => 'repeater', 'fields' => [
                ['key' => 'url', 'label' => 'URL', 'type' => 'image'],
                ['key' => 'alt', 'label' => 'Alt text', 'type' => 'text'],
            ]],
            ['key' => 'columns', 'label' => 'Columns', 'type' => 'select', 'options' => ['2' => '2', '3' => '3', '4' => '4']],
        ];
    }

    public function view(): string
    {
        return 'page-builder::blocks.gallery';
    }
}
