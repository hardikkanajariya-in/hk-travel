<?php

namespace App\Modules\Hotels\Models;

use App\Concerns\HasAuditLog;
use App\Core\Concerns\ProvidesSeoMeta;
use App\Core\Contracts\HasSeoMeta;
use App\Modules\Destinations\Models\Destination;
use App\Modules\Hotels\Database\Factories\HotelFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Hotel extends Model implements HasSeoMeta
{
    use HasAuditLog, HasFactory, HasTranslations, HasUlids, ProvidesSeoMeta, SoftDeletes;

    protected $table = 'hotels';

    protected $guarded = ['id'];

    public $translatable = ['name', 'description'];

    protected function casts(): array
    {
        return [
            'star_rating' => 'integer',
            'price_from' => 'decimal:2',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'amenities' => 'array',
            'gallery' => 'array',
            'check_in' => 'string',
            'check_out' => 'string',
            'rating_avg' => 'decimal:2',
            'rating_count' => 'integer',
            'lat' => 'float',
            'lng' => 'float',
            'seo' => 'array',
        ];
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    protected static function newFactory(): HotelFactory
    {
        return HotelFactory::new();
    }

    protected function buildSeoSchema(): ?array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Hotel',
            'name' => $this->name,
            'description' => strip_tags((string) $this->description),
            'starRating' => $this->star_rating ? ['@type' => 'Rating', 'ratingValue' => $this->star_rating] : null,
            'priceRange' => $this->price_from ? '$$' : null,
            'address' => $this->address,
            'aggregateRating' => $this->rating_count > 0 ? [
                '@type' => 'AggregateRating',
                'ratingValue' => (float) $this->rating_avg,
                'reviewCount' => $this->rating_count,
            ] : null,
        ];
    }
}
