<?php

namespace App\Modules\Flights\Models;

use App\Concerns\HasAuditLog;
use App\Modules\Flights\Database\Factories\FlightOfferFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlightOffer extends Model
{
    use HasAuditLog, HasFactory, HasUlids, SoftDeletes;

    protected $table = 'flight_offers';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'stops' => 'integer',
            'price' => 'decimal:2',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'segments' => 'array',
        ];
    }

    protected static function newFactory(): FlightOfferFactory
    {
        return FlightOfferFactory::new();
    }
}
