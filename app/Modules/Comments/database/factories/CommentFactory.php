<?php

namespace App\Modules\Comments\Database\Factories;

use App\Modules\Comments\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'commentable_type' => 'App\\Modules\\Blog\\Models\\BlogPost',
            'commentable_id' => (string) Str::ulid(),
            'parent_id' => null,
            'user_id' => null,
            'author_name' => $this->faker->name(),
            'author_email' => $this->faker->safeEmail(),
            'author_url' => null,
            'body' => $this->faker->paragraph(),
            'status' => Comment::STATUS_PENDING,
            'depth' => 0,
            'is_pinned' => false,
            'locale' => 'en',
            'ip' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'approved_at' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => Comment::STATUS_APPROVED,
            'approved_at' => now(),
        ]);
    }
}
