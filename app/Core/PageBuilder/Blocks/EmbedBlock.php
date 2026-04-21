<?php

namespace App\Core\PageBuilder\Blocks;

use App\Core\PageBuilder\Block;

class EmbedBlock extends Block
{
    public function key(): string
    {
        return 'embed';
    }

    public function name(): string
    {
        return 'Embed (oEmbed URL)';
    }

    public function icon(): string
    {
        return 'code-bracket-square';
    }

    public function category(): string
    {
        return 'Media';
    }

    public function defaultData(): array
    {
        return ['url' => null, 'caption' => null];
    }

    public function fields(): array
    {
        return [
            ['key' => 'url', 'label' => 'URL', 'type' => 'url'],
            ['key' => 'caption', 'label' => 'Caption', 'type' => 'text'],
        ];
    }

    public function view(): string
    {
        return 'page-builder::blocks.embed';
    }
}
