<?php

namespace App\Models;

use App\Concerns\HasAuditLog;
use App\Modules\Comments\Concerns\HasComments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $slug
 * @property string $title
 * @property string $layout
 * @property string $status
 * @property bool $is_homepage
 * @property bool $allow_comments
 * @property array<string, mixed>|null $seo
 * @property array<string, mixed>|null $translations
 * @property Carbon|null $published_at
 */
class Page extends Model
{
    use HasAuditLog, HasComments, HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_homepage' => 'boolean',
            'allow_comments' => 'boolean',
            'seo' => 'array',
            'translations' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(PageBlock::class)->orderBy('position');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published'
            && (! $this->published_at || $this->published_at->isPast());
    }

    public function localizedTitle(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $this->translations[$locale]['title'] ?? $this->title;
    }
}
