<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $page_id
 * @property string $type
 * @property array<string, mixed>|null $data
 * @property bool $visible_mobile
 * @property bool $visible_tablet
 * @property bool $visible_desktop
 * @property int $position
 */
class PageBlock extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'visible_mobile' => 'boolean',
            'visible_tablet' => 'boolean',
            'visible_desktop' => 'boolean',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Tailwind responsive-visibility classes derived from the per-breakpoint flags.
     */
    public function visibilityClasses(): string
    {
        $classes = [];
        if (! $this->visible_mobile) {
            $classes[] = 'hidden';
        }
        if (! $this->visible_tablet) {
            $classes[] = $this->visible_mobile ? 'md:hidden' : 'md:hidden';
        } else {
            if (! $this->visible_mobile) {
                $classes[] = 'md:block';
            }
        }
        if (! $this->visible_desktop) {
            $classes[] = 'lg:hidden';
        } else {
            if (! $this->visible_tablet) {
                $classes[] = 'lg:block';
            }
        }

        return implode(' ', $classes);
    }
}
