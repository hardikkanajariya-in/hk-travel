<?php

namespace App\Models;

use App\Concerns\HasAuditLog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $zone
 * @property string $type
 * @property array<string, mixed>|null $data
 * @property bool $is_active
 * @property int $position
 */
class Widget extends Model
{
    use HasAuditLog, HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /** @return Collection<int, self> */
    public static function forZone(string $zone): Collection
    {
        return static::query()
            ->where('zone', $zone)
            ->where('is_active', true)
            ->orderBy('position')
            ->get();
    }
}
