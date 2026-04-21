<?php

namespace App\Core\PageBuilder\Blocks;

use App\Core\PageBuilder\Block;

class CustomCssBlock extends Block
{
    public function key(): string
    {
        return 'custom_css';
    }

    public function name(): string
    {
        return 'Custom CSS';
    }

    public function icon(): string
    {
        return 'paint-brush';
    }

    public function category(): string
    {
        return 'Developer';
    }

    public function permission(): ?string
    {
        return 'pages.developer-blocks';
    }

    public function defaultData(): array
    {
        return ['css' => '/* scoped CSS */'];
    }

    public function fields(): array
    {
        return [
            ['key' => 'css', 'label' => 'CSS', 'type' => 'textarea', 'rows' => 10, 'mono' => true],
        ];
    }

    public function sanitize(array $data): array
    {
        // Strip </style> closer to prevent breakouts.
        $css = (string) ($data['css'] ?? '');
        $css = str_ireplace(['</style>', '<script', '</script>'], '', $css);

        return ['css' => $css];
    }

    public function view(): string
    {
        return 'page-builder::blocks.custom-css';
    }
}
