<?php

namespace App\Core\PageBuilder\Blocks;

use App\Core\PageBuilder\Block;

class CustomJsBlock extends Block
{
    public function key(): string
    {
        return 'custom_js';
    }

    public function name(): string
    {
        return 'Custom JavaScript';
    }

    public function icon(): string
    {
        return 'command-line';
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
        return ['js' => '// runs after Alpine init'];
    }

    public function fields(): array
    {
        return [
            ['key' => 'js', 'label' => 'JavaScript', 'type' => 'textarea', 'rows' => 12, 'mono' => true],
        ];
    }

    public function sanitize(array $data): array
    {
        $js = (string) ($data['js'] ?? '');
        $js = str_ireplace(['</script>'], '', $js);

        return ['js' => $js];
    }

    public function view(): string
    {
        return 'page-builder::blocks.custom-js';
    }
}
