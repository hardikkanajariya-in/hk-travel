<?php

namespace App\Models;

use App\Concerns\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $form_id
 * @property array<string, mixed> $data
 * @property string|null $email
 * @property string|null $name
 * @property string|null $phone
 * @property string|null $subject
 * @property string|null $ip
 * @property string|null $user_agent
 * @property string|null $locale
 * @property string $status
 * @property Carbon|null $handled_at
 */
class ContactSubmission extends Model
{
    use HasAuditLog, HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'handled_at' => 'datetime',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(ContactForm::class, 'form_id');
    }
}
