<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationChannel extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_enabled' => 'bool',
        'config' => 'array',
    ];
}
