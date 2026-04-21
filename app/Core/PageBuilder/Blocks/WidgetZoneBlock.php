<?php

namespace App\Core\PageBuilder\Blocks;

use App\Core\PageBuilder\Block;

class WidgetZoneBlock extends Block
{
    public function key(): string
    {
        return 'widget_zone';
    }

    public function name(): string
    {
        return 'Widget zone';
    }

    public function icon(): string
    {
        return 'rectangle-group';
    }

    public function category(): string
    {
        return 'Layout';
    }

    public function defaultData(): array
    {
        return ['zone' => 'sidebar'];
    }

    public function fields(): array
    {
        return [
            ['key' => 'zone', 'label' => 'Zone key', 'type' => 'text'],
        ];
    }

    public function view(): string
    {
        return 'page-builder::blocks.widget-zone';
    }
}
