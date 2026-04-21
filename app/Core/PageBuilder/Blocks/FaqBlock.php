<?php

namespace App\Core\PageBuilder\Blocks;

use App\Core\PageBuilder\Block;

class FaqBlock extends Block
{
    public function key(): string
    {
        return 'faq';
    }

    public function name(): string
    {
        return 'FAQ';
    }

    public function icon(): string
    {
        return 'question-mark-circle';
    }

    public function category(): string
    {
        return 'Content';
    }

    public function defaultData(): array
    {
        return ['title' => 'Frequently asked questions', 'items' => []];
    }

    public function fields(): array
    {
        return [
            ['key' => 'title', 'label' => 'Title', 'type' => 'text'],
            ['key' => 'items', 'label' => 'Questions', 'type' => 'repeater', 'fields' => [
                ['key' => 'q', 'label' => 'Question', 'type' => 'text'],
                ['key' => 'a', 'label' => 'Answer', 'type' => 'richtext'],
            ]],
        ];
    }

    public function view(): string
    {
        return 'page-builder::blocks.faq';
    }
}
