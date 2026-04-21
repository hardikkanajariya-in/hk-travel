<?php

namespace App\Modules\Tours\Models;

use App\Concerns\HasAuditLog;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Tour extends Model
{
    use HasAuditLog, HasTranslations, HasUlids, SoftDeletes;

    protected $table = 'tours';

    protected $guarded = ['id'];

    /** @var array<int, string> */
    public $translatable = ['name', 'description'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'duration_days' => 'integer',
            'max_group_size' => 'integer',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }
}
