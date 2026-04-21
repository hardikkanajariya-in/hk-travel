<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $from_path
 * @property string $to_path
 * @property int $status_code
 * @property bool $is_active
 * @property int $hit_count
 * @property ?Carbon $last_hit_at
 */
class PermalinkRedirect extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'status_code' => 'integer',
            'hit_count' => 'integer',
            'last_hit_at' => 'datetime',
        ];
    }
}
