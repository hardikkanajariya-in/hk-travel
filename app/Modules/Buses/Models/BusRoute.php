<?php

namespace App\Modules\Buses\Models;

use App\Concerns\HasAuditLog;
use App\Core\Concerns\ProvidesSeoMeta;
use App\Core\Contracts\HasSeoMeta;
use App\Modules\Buses\Database\Factories\BusRouteFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class BusRoute extends Model implements HasSeoMeta
{
    use HasAuditLog, HasFactory, HasTranslations, HasUlids, ProvidesSeoMeta, SoftDeletes;

    protected $table = 'bus_routes';

    protected $guarded = ['id'];

    public $translatable = ['title', 'description'];

    protected function casts(): array
    {
        return [
            'fare' => 'decimal:2',
            'duration_minutes' => 'integer',
            'distance_km' => 'integer',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'schedule_days' => 'array',
            'amenities' => 'array',
            'stops' => 'array',
            'seo' => 'array',
        ];
    }

    protected static function newFactory(): BusRouteFactory
    {
        return BusRouteFactory::new();
    }

    protected function buildSeoSchema(): ?array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BusTrip',
            'name' => $this->title,
            'description' => strip_tags((string) $this->description),
            'departureBusStop' => ['@type' => 'BusStation', 'name' => $this->origin],
            'arrivalBusStop' => ['@type' => 'BusStation', 'name' => $this->destination],
            'provider' => ['@type' => 'Organization', 'name' => $this->operator],
        ];
    }
}
