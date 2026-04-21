<?php

namespace App\Modules\Taxi\Models;

use App\Concerns\HasAuditLog;
use App\Core\Concerns\ProvidesSeoMeta;
use App\Core\Contracts\HasSeoMeta;
use App\Modules\Taxi\Database\Factories\TaxiServiceFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class TaxiService extends Model implements HasSeoMeta
{
    use HasAuditLog, HasFactory, HasTranslations, HasUlids, ProvidesSeoMeta, SoftDeletes;

    protected $table = 'taxi_services';

    protected $guarded = ['id'];

    public $translatable = ['title', 'description'];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'luggage' => 'integer',
            'base_fare' => 'decimal:2',
            'per_km_rate' => 'decimal:2',
            'per_hour_rate' => 'decimal:2',
            'flat_rate' => 'decimal:2',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'service_areas' => 'array',
            'features' => 'array',
            'seo' => 'array',
        ];
    }

    protected static function newFactory(): TaxiServiceFactory
    {
        return TaxiServiceFactory::new();
    }

    protected function buildSeoSchema(): ?array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'TaxiService',
            'name' => $this->title,
            'description' => strip_tags((string) $this->description),
            'serviceType' => $this->service_type,
        ];
    }
}
