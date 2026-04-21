<?php

namespace App\Modules\Cruises\Models;

use App\Concerns\HasAuditLog;
use App\Core\Concerns\ProvidesSeoMeta;
use App\Core\Contracts\HasSeoMeta;
use App\Modules\Cruises\Database\Factories\CruiseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Cruise extends Model implements HasSeoMeta
{
    use HasAuditLog, HasFactory, HasTranslations, HasUlids, ProvidesSeoMeta, SoftDeletes;

    protected $table = 'cruises';

    protected $guarded = ['id'];

    public $translatable = ['title', 'description', 'highlights'];

    protected function casts(): array
    {
        return [
            'duration_nights' => 'integer',
            'departure_date' => 'date',
            'return_date' => 'date',
            'price_from' => 'decimal:2',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'cabin_types' => 'array',
            'itinerary' => 'array',
            'inclusions' => 'array',
            'exclusions' => 'array',
            'gallery' => 'array',
            'seo' => 'array',
        ];
    }

    protected static function newFactory(): CruiseFactory
    {
        return CruiseFactory::new();
    }

    protected function buildSeoSchema(): ?array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'TouristTrip',
            'name' => $this->title,
            'description' => strip_tags((string) $this->description),
            'touristType' => 'Cruise',
            'provider' => ['@type' => 'Organization', 'name' => $this->cruise_line],
            'offers' => [
                '@type' => 'Offer',
                'price' => (string) $this->price_from,
                'priceCurrency' => $this->currency,
            ],
        ];
    }
}
