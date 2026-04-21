<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $email_template_id
 * @property string $locale
 * @property string $subject
 * @property string $body_html
 * @property ?string $body_text
 */
class EmailTemplateTranslation extends Model
{
    protected $guarded = ['id'];

    public function template(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class, 'email_template_id');
    }
}
