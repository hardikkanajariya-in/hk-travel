<?php

namespace App\Modules\Tours\Models;

use App\Concerns\HasAuditLog;
use App\Core\Concerns\ProvidesSeoMeta;
use App\Core\Contracts\HasSeoMeta;
use App\Modules\Destinations\Models\Destination;
use App\Modules\Reviews\Concerns\HasReviews;
use App\Modules\Tours\Database\Factories\TourFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Tour extends Model implements HasSeoMeta
{
    use HasAuditLog, HasFactory, HasReviews, HasTranslations, HasUlids, ProvidesSeoMeta, SoftDeletes;

    protected $table = 'tours';

    protected $guarded = ['id'];

    /** @var array<int, string> */
    protected array $reviewCriteria = ['value', 'service', 'quality'];

    /** @var array<int, string> */
    public $translatable = ['name', 'description'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'duration_days' => 'integer',
            'max_group_size' => 'integer',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'gallery' => 'array',
            'inclusions' => 'array',
            'exclusions' => 'array',
            'itinerary' => 'array',
            'rating_avg' => 'decimal:2',
            'rating_count' => 'integer',
            'seo' => 'array',
        ];
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    protected static function newFactory(): TourFactory
    {
        return TourFactory::new();
    }

    public function effectivePrice(): float
    {
        return (float) ($this->discount_price ?: $this->price);
    }

    protected function buildSeoSchema(): ?array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'TouristTrip',
            'name' => $this->name,
            'description' => strip_tags((string) $this->description),
            'offers' => [
                '@type' => 'Offer',
                'price' => $this->effectivePrice(),
                'priceCurrency' => $this->currency ?: 'USD',
            ],
            'aggregateRating' => $this->reviewsAggregateSchema(),
        ];
    }
}
