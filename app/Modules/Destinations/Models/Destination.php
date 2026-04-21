<?php

namespace App\Modules\Destinations\Models;

use App\Concerns\HasAuditLog;
use App\Core\Concerns\ProvidesSeoMeta;
use App\Core\Contracts\HasSeoMeta;
use App\Modules\Destinations\Database\Factories\DestinationFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Destination extends Model implements HasSeoMeta
{
    use HasAuditLog, HasFactory, HasTranslations, HasUlids, ProvidesSeoMeta, SoftDeletes;

    protected $table = 'destinations';

    protected $guarded = ['id'];

    /** @var array<int, string> */
    public $translatable = ['name', 'description', 'highlights'];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'lat' => 'float',
            'lng' => 'float',
            'seo' => 'array',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    protected static function newFactory(): DestinationFactory
    {
        return DestinationFactory::new();
    }

    protected function buildSeoSchema(): ?array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'TouristDestination',
            'name' => $this->name,
            'description' => strip_tags((string) $this->description),
            'geo' => $this->lat && $this->lng ? [
                '@type' => 'GeoCoordinates',
                'latitude' => $this->lat,
                'longitude' => $this->lng,
            ] : null,
        ];
    }
}
