<?php

namespace App\Modules\Blog\Models;

use App\Concerns\HasAuditLog;
use App\Modules\Blog\Database\Factories\BlogTagFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BlogTag extends Model
{
    use HasAuditLog, HasFactory, HasUlids;

    protected $table = 'blog_tags';

    protected $guarded = ['id'];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(BlogPost::class, 'blog_post_tag');
    }

    protected static function newFactory(): BlogTagFactory
    {
        return BlogTagFactory::new();
    }
}
