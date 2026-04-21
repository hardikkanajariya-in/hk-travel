<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $key
 * @property string $label
 * @property ?string $description
 * @property ?array<int, string> $variables
 * @property bool $is_active
 * @property-read Collection<int, EmailTemplateTranslation> $translations
 */
class EmailTemplate extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'variables' => 'array',
        ];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(EmailTemplateTranslation::class);
    }

    public function translation(string $locale, string $fallback = 'en'): ?EmailTemplateTranslation
    {
        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', $fallback)
            ?? $this->translations->first();
    }
}
