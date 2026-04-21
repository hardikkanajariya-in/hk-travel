<?php

namespace App\Modules\Trains\Models;

use App\Concerns\HasAuditLog;
use App\Modules\Trains\Database\Factories\TrainOfferFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainOffer extends Model
{
    use HasAuditLog, HasFactory, HasUlids, SoftDeletes;

    protected $table = 'train_offers';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'changes' => 'integer',
            'price' => 'decimal:2',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'refundable' => 'boolean',
            'segments' => 'array',
        ];
    }

    protected static function newFactory(): TrainOfferFactory
    {
        return TrainOfferFactory::new();
    }
}
