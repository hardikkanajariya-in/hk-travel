<?php

namespace App\Core\PageBuilder;

use App\Core\PageBuilder\Blocks\ButtonBlock;
use App\Core\PageBuilder\Blocks\CardsBlock;
use App\Core\PageBuilder\Blocks\ColumnsBlock;
use App\Core\PageBuilder\Blocks\ContactFormBlock;
use App\Core\PageBuilder\Blocks\CtaBlock;
use App\Core\PageBuilder\Blocks\CustomCssBlock;
use App\Core\PageBuilder\Blocks\CustomHtmlBlock;
use App\Core\PageBuilder\Blocks\CustomJsBlock;
use App\Core\PageBuilder\Blocks\DividerBlock;
use App\Core\PageBuilder\Blocks\EmbedBlock;
use App\Core\PageBuilder\Blocks\FaqBlock;
use App\Core\PageBuilder\Blocks\GalleryBlock;
use App\Core\PageBuilder\Blocks\HeadingBlock;
use App\Core\PageBuilder\Blocks\HeroBlock;
use App\Core\PageBuilder\Blocks\ImageBlock;
use App\Core\PageBuilder\Blocks\MenuBlock;
use App\Core\PageBuilder\Blocks\NewsletterBlock;
use App\Core\PageBuilder\Blocks\RichTextBlock;
use App\Core\PageBuilder\Blocks\SpacerBlock;
use App\Core\PageBuilder\Blocks\TestimonialsBlock;
use App\Core\PageBuilder\Blocks\VideoBlock;
use App\Core\PageBuilder\Blocks\WidgetZoneBlock;
use Illuminate\Support\Collection;
use RuntimeException;

/**
 * Central registry of available page-builder blocks.
 *
 * Resolved as a singleton in HkCoreServiceProvider so modules may push
 * additional blocks at boot time via `register()`.
 */
class BlockRegistry
{
    /** @var Collection<string, BlockContract> */
    protected Collection $blocks;

    public function __construct()
    {
        $this->blocks = collect();
        $this->bootBuiltIns();
    }

    public function register(BlockContract $block): self
    {
        $this->blocks->put($block->key(), $block);

        return $this;
    }

    public function get(string $key): BlockContract
    {
        return $this->blocks->get($key)
            ?? throw new RuntimeException("Page-builder block [$key] is not registered.");
    }

    public function has(string $key): bool
    {
        return $this->blocks->has($key);
    }

    /** @return Collection<string, BlockContract> */
    public function all(): Collection
    {
        return $this->blocks;
    }

    /** @return Collection<string, Collection<string, BlockContract>> */
    public function grouped(): Collection
    {
        return $this->blocks->groupBy(fn (BlockContract $b) => $b->category());
    }

    protected function bootBuiltIns(): void
    {
        foreach ([
            HeroBlock::class,
            HeadingBlock::class,
            RichTextBlock::class,
            ImageBlock::class,
            GalleryBlock::class,
            VideoBlock::class,
            ButtonBlock::class,
            CardsBlock::class,
            ColumnsBlock::class,
            SpacerBlock::class,
            DividerBlock::class,
            FaqBlock::class,
            CtaBlock::class,
            TestimonialsBlock::class,
            NewsletterBlock::class,
            EmbedBlock::class,
            MenuBlock::class,
            WidgetZoneBlock::class,
            CustomHtmlBlock::class,
            CustomCssBlock::class,
            CustomJsBlock::class,
            ContactFormBlock::class,
        ] as $class) {
            $this->register(app($class));
        }
    }
}
