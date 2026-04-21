<?php

namespace App\Models;

use App\Concerns\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $location
 */
class Menu extends Model
{
    use HasAuditLog, HasFactory;

    protected $guarded = ['id'];

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('position');
    }

    public function rootItems(): HasMany
    {
        return $this->items()->whereNull('parent_id');
    }

    public static function forLocation(string $location): ?self
    {
        return static::query()->where('location', $location)->first();
    }
}
