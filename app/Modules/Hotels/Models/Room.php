<?php

namespace App\Modules\Hotels\Models;

use App\Concerns\HasAuditLog;
use App\Modules\Hotels\Database\Factories\RoomFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Room extends Model
{
    use HasAuditLog, HasFactory, HasTranslations, HasUlids, SoftDeletes;

    protected $table = 'hotel_rooms';

    protected $guarded = ['id'];

    public $translatable = ['name', 'description'];

    protected function casts(): array
    {
        return [
            'price_per_night' => 'decimal:2',
            'capacity_adults' => 'integer',
            'capacity_children' => 'integer',
            'inventory' => 'integer',
            'is_available' => 'boolean',
            'amenities' => 'array',
        ];
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    protected static function newFactory(): RoomFactory
    {
        return RoomFactory::new();
    }
}
