<?php

namespace App\Models;

use App\Concerns\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Pipeline extends Model
{
    use HasAuditLog;
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'stages' => 'array',
        'is_default' => 'bool',
        'is_active' => 'bool',
        'sort_order' => 'int',
    ];

    public static function default(): ?self
    {
        return static::query()->where('is_default', true)->where('is_active', true)->first()
            ?? static::query()->where('is_active', true)->orderBy('sort_order')->first();
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function stageList(): array
    {
        $stages = $this->stages ?? [];

        return array_values(array_map(fn ($s) => [
            'key' => $s['key'] ?? Str::slug((string) ($s['label'] ?? 'stage')),
            'label' => $s['label'] ?? ucfirst((string) ($s['key'] ?? 'Stage')),
            'color' => $s['color'] ?? 'neutral',
            'is_won' => (bool) ($s['is_won'] ?? false),
            'is_lost' => (bool) ($s['is_lost'] ?? false),
        ], $stages));
    }
}
