<?php

namespace App\Modules\Blog\Models;

use App\Concerns\HasAuditLog;
use App\Core\Concerns\ProvidesSeoMeta;
use App\Core\Contracts\HasSeoMeta;
use App\Core\Routing\PublicUrlGenerator;
use App\Models\User;
use App\Modules\Blog\Database\Factories\BlogPostFactory;
use App\Modules\Comments\Concerns\HasComments;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BlogPost extends Model implements HasSeoMeta
{
    use HasAuditLog, HasComments, HasFactory, HasUlids, ProvidesSeoMeta, SoftDeletes;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_ARCHIVED = 'archived';

    protected $table = 'blog_posts';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
            'show_toc' => 'boolean',
            'view_count' => 'integer',
            'reading_minutes' => 'integer',
            'gallery' => 'array',
            'seo' => 'array',
            'translations' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(BlogCategory::class, 'blog_post_category');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_post_tag');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where(function ($q): void {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function scopeScheduledDue(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SCHEDULED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED
            && (! $this->published_at || $this->published_at->isPast());
    }

    public function calculateReadingMinutes(): int
    {
        $words = max(1, str_word_count(strip_tags((string) $this->body)));

        return max(1, (int) ceil($words / 220));
    }

    /**
     * Parse the rendered body into a flat heading list for the auto TOC.
     * Skips when the post has show_toc=false or there are no <h2>/<h3>.
     *
     * @return array<int, array{id:string, level:int, text:string}>
     */
    public function tableOfContents(): array
    {
        if (! $this->show_toc || blank($this->body)) {
            return [];
        }

        if (! preg_match_all('/<h([23])(?:[^>]*)>(.+?)<\/h\1>/is', (string) $this->body, $matches, PREG_SET_ORDER)) {
            return [];
        }

        $items = [];
        foreach ($matches as $m) {
            $text = trim(strip_tags($m[2]));
            if ($text === '') {
                continue;
            }
            $items[] = [
                'id' => Str::slug($text),
                'level' => (int) $m[1],
                'text' => $text,
            ];
        }

        return $items;
    }

    /**
     * Body with anchor IDs injected on h2/h3 tags so TOC links jump.
     */
    public function bodyWithAnchors(): string
    {
        if (blank($this->body)) {
            return '';
        }

        return preg_replace_callback(
            '/<h([23])([^>]*)>(.+?)<\/h\1>/is',
            function (array $m): string {
                $text = trim(strip_tags($m[3]));
                if ($text === '') {
                    return $m[0];
                }
                $id = Str::slug($text);
                $existingAttrs = $m[2];
                if (str_contains($existingAttrs, 'id=')) {
                    return $m[0];
                }

                return '<h'.$m[1].$existingAttrs.' id="'.$id.'">'.$m[3].'</h'.$m[1].'>';
            },
            (string) $this->body
        ) ?? (string) $this->body;
    }

    protected function buildSeoSchema(): ?array
    {
        $urls = app(PublicUrlGenerator::class);

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $this->title,
            'description' => $this->excerpt,
            'image' => $this->cover_image,
            'datePublished' => $this->published_at?->toIso8601String(),
            'dateModified' => $this->updated_at?->toIso8601String(),
            'author' => $this->author ? [
                '@type' => 'Person',
                'name' => $this->author->name,
            ] : null,
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $urls->entity('blog_post', ['slug' => $this->slug]),
            ],
        ];
    }

    protected static function newFactory(): BlogPostFactory
    {
        return BlogPostFactory::new();
    }
}
