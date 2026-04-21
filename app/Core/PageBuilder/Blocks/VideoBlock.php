<?php

namespace App\Core\PageBuilder\Blocks;

use App\Core\PageBuilder\Block;

class VideoBlock extends Block
{
    public function key(): string
    {
        return 'video';
    }

    public function name(): string
    {
        return 'Video';
    }

    public function icon(): string
    {
        return 'film';
    }

    public function category(): string
    {
        return 'Media';
    }

    public function defaultData(): array
    {
        return ['url' => null, 'caption' => null, 'aspect' => '16/9'];
    }

    public function fields(): array
    {
        return [
            ['key' => 'url', 'label' => 'YouTube / Vimeo URL', 'type' => 'url'],
            ['key' => 'caption', 'label' => 'Caption', 'type' => 'text'],
            ['key' => 'aspect', 'label' => 'Aspect', 'type' => 'select', 'options' => ['16/9' => '16:9', '4/3' => '4:3', '1/1' => '1:1']],
        ];
    }

    public function view(): string
    {
        return 'page-builder::blocks.video';
    }
}
