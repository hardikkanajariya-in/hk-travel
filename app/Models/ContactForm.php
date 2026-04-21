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
 * @property string|null $description
 * @property array<int, array<string, mixed>> $fields
 * @property array<string, mixed>|null $settings
 * @property array<int, string>|null $notify_emails
 * @property bool $create_lead
 * @property bool $is_active
 */
class ContactForm extends Model
{
    use HasAuditLog, HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'fields' => 'array',
            'settings' => 'array',
            'notify_emails' => 'array',
            'create_lead' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(ContactSubmission::class, 'form_id');
    }

    public static function bySlug(string $slug): ?self
    {
        return static::query()->where('slug', $slug)->where('is_active', true)->first();
    }

    public function setting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }
}
