<?php

namespace App\Core\PageBuilder\Blocks;

use App\Core\PageBuilder\Block;

class ImageBlock extends Block
{
    public function key(): string
    {
        return 'image';
    }

    public function name(): string
    {
        return 'Image';
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
        return ['url' => null, 'alt' => '', 'caption' => null, 'align' => 'center', 'rounded' => true];
    }

    public function fields(): array
    {
        return [
            ['key' => 'url', 'label' => 'Image URL', 'type' => 'image'],
            ['key' => 'alt', 'label' => 'Alt text', 'type' => 'text'],
            ['key' => 'caption', 'label' => 'Caption', 'type' => 'text'],
            ['key' => 'align', 'label' => 'Align', 'type' => 'select', 'options' => ['left' => 'Left', 'center' => 'Center', 'right' => 'Right']],
            ['key' => 'rounded', 'label' => 'Rounded corners', 'type' => 'toggle'],
        ];
    }

    public function view(): string
    {
        return 'page-builder::blocks.image';
    }
}
