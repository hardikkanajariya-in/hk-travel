<?php

namespace App\Modules\Activities\Models;

use App\Concerns\HasAuditLog;
use App\Core\Concerns\ProvidesSeoMeta;
use App\Core\Contracts\HasSeoMeta;
use App\Modules\Activities\Database\Factories\ActivityFactory;
use App\Modules\Destinations\Models\Destination;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Activity extends Model implements HasSeoMeta
{
    use HasAuditLog, HasFactory, HasTranslations, HasUlids, ProvidesSeoMeta, SoftDeletes;

    protected $table = 'activities';

    protected $guarded = ['id'];

    public $translatable = ['name', 'short_description', 'description'];

    protected function casts(): array
    {
        return [
            'duration_hours' => 'float',
            'price' => 'decimal:2',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'highlights' => 'array',
            'included' => 'array',
            'gallery' => 'array',
            'schedule' => 'array',
            'min_age' => 'integer',
            'max_group_size' => 'integer',
            'rating_avg' => 'decimal:2',
            'rating_count' => 'integer',
            'seo' => 'array',
        ];
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    protected static function newFactory(): ActivityFactory
    {
        return ActivityFactory::new();
    }

    protected function buildSeoSchema(): ?array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'TouristAttraction',
            'name' => $this->name,
            'description' => strip_tags((string) $this->description),
            'image' => $this->cover_image,
            'aggregateRating' => $this->rating_count > 0 ? [
                '@type' => 'AggregateRating',
                'ratingValue' => (float) $this->rating_avg,
                'reviewCount' => $this->rating_count,
            ] : null,
        ];
    }
}
