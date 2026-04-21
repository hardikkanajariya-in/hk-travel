<?php

namespace App\Core\PageBuilder\Blocks;

use App\Core\PageBuilder\Block;
use App\Core\Security\Sanitizer;

class CustomHtmlBlock extends Block
{
    public function key(): string
    {
        return 'custom_html';
    }

    public function name(): string
    {
        return 'Custom HTML';
    }

    public function icon(): string
    {
        return 'code-bracket';
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
        return ['html' => '<!-- raw HTML -->'];
    }

    public function fields(): array
    {
        return [
            ['key' => 'html', 'label' => 'HTML (developer profile, scripts stripped)', 'type' => 'textarea', 'rows' => 12, 'mono' => true],
        ];
    }

    public function sanitize(array $data): array
    {
        // Developer-profile sanitiser still strips <script>; this block is
        // gated behind a permission so only trusted users reach it.
        return ['html' => Sanitizer::developer((string) ($data['html'] ?? ''))];
    }

    public function view(): string
    {
        return 'page-builder::blocks.custom-html';
    }
}
