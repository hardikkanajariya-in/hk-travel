<?php

namespace App\Modules\Cars\Models;

use App\Concerns\HasAuditLog;
use App\Core\Concerns\ProvidesSeoMeta;
use App\Core\Contracts\HasSeoMeta;
use App\Modules\Cars\Database\Factories\CarRentalFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class CarRental extends Model implements HasSeoMeta
{
    use HasAuditLog, HasFactory, HasTranslations, HasUlids, ProvidesSeoMeta, SoftDeletes;

    protected $table = 'car_rentals';

    protected $guarded = ['id'];

    public $translatable = ['name', 'description'];

    protected function casts(): array
    {
        return [
            'seats' => 'integer',
            'doors' => 'integer',
            'luggage' => 'integer',
            'has_ac' => 'boolean',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'daily_rate' => 'decimal:2',
            'weekly_rate' => 'decimal:2',
            'pickup_locations' => 'array',
            'features' => 'array',
            'gallery' => 'array',
            'seo' => 'array',
        ];
    }

    protected static function newFactory(): CarRentalFactory
    {
        return CarRentalFactory::new();
    }

    protected function buildSeoSchema(): ?array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Vehicle',
            'name' => $this->name,
            'description' => strip_tags((string) $this->description),
            'vehicleSeatingCapacity' => $this->seats,
            'vehicleTransmission' => $this->transmission,
            'fuelType' => $this->fuel_type,
        ];
    }
}
