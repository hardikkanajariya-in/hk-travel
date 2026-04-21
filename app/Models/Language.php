<?php

namespace App\Models;

use App\Core\Localization\LocaleManager;
use Database\Factories\LanguageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property ?string $native_name
 * @property ?string $flag
 * @property bool $is_rtl
 * @property bool $is_default
 * @property bool $is_active
 * @property int $sort_order
 */
class Language extends Model
{
    /** @use HasFactory<LanguageFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        $flush = static function (): void {
            if (app()->bound(LocaleManager::class)) {
                app(LocaleManager::class)->flush();
            }
        };

        static::saved($flush);
        static::deleted($flush);
    }

    protected function casts(): array
    {
        return [
            'is_rtl' => 'boolean',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }
}
